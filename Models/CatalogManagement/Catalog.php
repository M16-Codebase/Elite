<?php
namespace Models\CatalogManagement;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Rules\RuleAggregator;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
/**
 * Модель кталога реализует фабрику в методе {@link Catalog::factory()}
 * В каталоге используются следующие сущности и названия:<ul>
      <li>variant - вариация позиции каталога
      <li>item - структурный элемент каталога, который может объеденять несколько реально существующих и слабо разделяемых вариантов (variant)
      <li>itemType - тип элемента каталога
      <li>typeProperty - свойство типа, описывает какими параметрами должны обладать элементы данного типа и какие на них накладываются ограничения
      <li>search - поиск элементов каталога</ul>
 * @see Catalog::factory()
 */


class Catalog {
    /**
     * @see self::search()
     */
    const S_ITEM = 1;
    /**
     * @see self::search()
     */
    const S_VARIANT  = 2;

    const CACHE_ITEMS_ENABLE = 1; //механизм кеширования в базе данных
    /**
     * Правила для поиска значений свойств
     */
    const SEARCH_AUTOCOMPLETE = 'autocomplete';//правило для автокомплита
    const SEARCH_REGEXP = 'regexp';//правило для регулярки

    const MAX_RECREATE_VIEWS_ON_UPDATE = 500;//максимальное количество товаров для пересчета view свойств
    
    const SORT_RANDOM = "random";
    const SORT_IN_ORDER = "order";
    /**
     * Экземпляр класса каталога
     * @var Catalog
     */
    static protected $instance = null;
    /**
     * Маркер для определения, первый раз ли используется темповая таблица
     * вообще это мега проблема MySql, он не может за один запрос больше одного раза обращаться к темповой таблице, они сделали это фичей(задокументировали) и походу забили.
     * @var type 
     */
    private $usedTmpTable = 0;
    /**
	 * @param $segment_id
     * @return Catalog
     */
    public static function factory($type_key, $segment_id = NULL){
        if (is_null($segment_id)){
            $segment_id = 0;
        }
        $params_hash = serialize(func_get_args());
        if (empty (self::$instance[$params_hash])){
            self::$instance[$params_hash] = new Catalog($type_key, $segment_id);
        }
        return self::$instance[$params_hash];
    }

    public static function onSegmentDelete($segment_id){
        if (empty($segment_id)){
            throw new \LogicException('Empty segment_id');
        }
        $db = \App\Builder::getInstance()->getDB();
        foreach (array(Item::TABLE_PROP_INT, Item::TABLE_PROP_STRING, Item::TABLE_PROP_FLOAT, Variant::TABLE_PROP_INT, Variant::TABLE_PROP_STRING, Variant::TABLE_PROP_FLOAT, CatalogHelpers\Type\SegmentVisible::TABLE_SEGMENT_VISIBLE) as $table){
            $db->query('DELETE FROM `'.$table.'` WHERE `segment_id` = ?d', $segment_id);
        }
    }
    /**
     * @var \MysqlSimple\Controller
     */
	protected $db = NULL;
    /**
     *
     * @var int
     */
    protected $segment_id = NULL;
    /**
     * Категория верхнего уровня (каталог)
     * @var Type
     */
    protected $catalog = NULL;
    /**
     * @see Catalog::factory()
     */
	protected function __construct($catalog_key, $segment_id = NULL){
		$this->db = \App\Builder::getInstance()->getDB();
        $this->segment_id = $segment_id;
        $this->catalog = is_numeric($catalog_key) ? Type::getbyId($catalog_key) : Type::getByKey($catalog_key);
        if (!$this->catalog->isCatalog() && $this->catalog['id'] != Type::DEFAULT_TYPE_ID){
            throw new \Exception('В объект поиска по каталогу должен быть передан ключ категории первого уровня. «' . $catalog_key . '» не является категорией первого уровня');
        }
	}

    /**
     * Проверка массива на разрешенные ключи и полноту
     * @param array $params
     * @param string $allowParamsGroup
     * @param bool $having_all <ul>
         <li>FALSE  - ключи массива $params должны быть в массиве <b>$allowParams</b>, других быть не может</li>
         <li>TRUE  - ВСЕ ключи массива $params должны быть в массиве <b>$allowParams</b>, других быть не может</li></ul>
     * @param bool $filter <ul>
     *  <li>FALSE - не фильтровать и выводить ошибки</li>
     *  <li>TRUE - отдать только те значения, которые можно изменять</li></ul>
     * @return bool|array
     */
    public static function checkAllowed($params, $allowParamsGroup, $having_all = FALSE, $filter = FALSE){
        if (is_array($allowParamsGroup)){
            $allowParams = $allowParamsGroup;
        }else{
            $allowParams = self::$allowParams[$allowParamsGroup];
        }
        $checked_params = array();
//        var_dump($params);
        foreach ($params as $key=>$val){
            if (in_array($key, $allowParams)){
				$checked_params[$key] = $val;
                unset ($params[$key]);
                unset ($allowParams[array_search($key, $allowParams)]);
            }elseif (!$filter){
                throw new \Exception('Incorrect params key:'.$key); //ошибка логическая, так что валимся смело
                return false;
            }
        }
        if ($having_all && count($allowParams)){
            trigger_error('Params have not some keys: "'.  implode('", "', $allowParams)).'"';//ошибка логическая, так что валимся смело
            return false;
        }
        return $checked_params;
    }
    /**
     * При переносе из multiple в не multiple остались значения у вариантов
     * @param bool $delete
     */
    public static function checkBase($delete = FALSE){
        $db = \App\Builder::getInstance()->getDB();
        $tables = array(
            \Models\CatalogManagement\Item::TABLE_PROP_FLOAT => 0,
            \Models\CatalogManagement\Item::TABLE_PROP_INT => 0,
            \Models\CatalogManagement\Item::TABLE_PROP_STRING => 0,
            \Models\CatalogManagement\Variant::TABLE_PROP_FLOAT => 1,
            \Models\CatalogManagement\Variant::TABLE_PROP_INT => 1,
            \Models\CatalogManagement\Variant::TABLE_PROP_STRING => 1);
        $error_str = '';
        $var_ids = array();
        $item_ids = array();
        foreach ($tables as $t => $m){
            //расщепляемость
            $result = $db->query('SELECT `'.($m ? 'variant_id' : 'item_id').'` AS `obj_id`, `property_id` FROM `'.$t.'` WHERE `property_id` IN (SELECT `id` FROM `'.\Models\CatalogManagement\Properties\Factory::TABLE.'` WHERE `multiple` != ?d)', $m)->select('obj_id', 'property_id');
            if (!empty($result)){
                foreach ($result as $o_id => $p){
                    foreach ($p as $p_id => $data){
                        $error_str .= 'Проблемы расщепляемости в таблице "'.$t.'" - ' . ($m ? 'variant_id' : 'item_id') . ': ' . $o_id . ' property_id: ' . $p_id;
                    }
                    if ($m){
                        $var_ids[] = $o_id;
                    }else{
                        $item_ids[] = $o_id;
                    }
                }
            }
            if (!empty($result) && $delete){
                $db->query('DELETE FROM `'.$t.'` WHERE `property_id` IN (SELECT `id` FROM `'.\Models\CatalogManagement\Properties\Factory::TABLE.'` WHERE `multiple` != ?d)', $m);
            }
            //сегментированность
            $result = $db->query('SELECT `'.($m ? 'variant_id' : 'item_id').'` AS `obj_id`, `property_id` FROM `'.$t.'` WHERE `property_id` IN (SELECT `id` FROM `'.\Models\CatalogManagement\Properties\Factory::TABLE.'` WHERE `segment` = 1) AND `segment_id` = 0')->select('obj_id', 'property_id');
            if (!empty($result)){
                foreach ($result as $o_id => $p){
                    foreach ($p as $p_id => $data){
                        $error_str .= 'Проблемы сегментов в таблице "'.$t.'" - ' . ($m ? 'variant_id' : 'item_id') . ': ' . $o_id . ' property_id: ' . $p_id;
                    }
                    if ($m){
                        $var_ids[] = $o_id;
                    }else{
                        $item_ids[] = $o_id;
                    }
                }
            }
            if (!empty($result) && $delete){
                $db->query('DELETE FROM `'.$t.'` WHERE `property_id` IN (SELECT `id` FROM `'.\Models\CatalogManagement\Properties\Factory::TABLE.'` WHERE `segment` = 1) AND `segment_id` = 0');
            }
            //не тот тип
            if ($m){
                $result = $db->query('SELECT `v`.`id` AS `variant_id`, `p`.`id` AS `property_id` FROM `'.$t.'` AS `val`
                INNER JOIN `variants` AS `v` ON (`v`.`id` = `val`.`variant_id` AND `v`.`status` IN (2,3))
                INNER JOIN `items` AS `i` ON (`v`.`item_id` = `i`.`id` AND `i`.`status` IN (2,3))
                INNER JOIN `properties` AS `p` ON (`val`.`property_id` = `p`.`id`)
                INNER JOIN `item_types` AS `t` ON (`t`.`id` = `i`.`type_id`)
                WHERE `p`.`type_id` != `t`.`id` AND `t`.`parents` NOT LIKE CONCAT("%.", `p`.`type_id`, ".%")')->select('variant_id', 'property_id');
                if (!empty($result)){
                    foreach ($result as $v_id => $p){
                        foreach ($p as $p_id => $data){
                            if ($delete){
                                $db->query('DELETE FROM `'.$t.'` WHERE `variant_id` = ?d AND `property_id` = ?d', $v_id, $p_id);
                            }
                            $error_str .= 'Не тот тип в таблице "'.$t.'" - variant_id: ' . $v_id . ' property_id: ' . $p_id . "\n";
                        }
                        $var_ids[] = $v_id;
                    }
                }
            }else{
                $result = $db->query('SELECT `i`.`id` AS `item_id`, `p`.`id` AS `property_id` FROM `'.$t.'` AS `val`
                INNER JOIN `items` AS `i` ON (`val`.`item_id` = `i`.`id` AND `i`.`status` IN (2,3))
                INNER JOIN `properties` AS `p` ON (`val`.`property_id` = `p`.`id`)
                INNER JOIN `item_types` AS `t` ON (`t`.`id` = `i`.`type_id`)
                WHERE `p`.`type_id` != `t`.`id` AND `t`.`parents` NOT LIKE CONCAT("%.", `p`.`type_id`, ".%")')->select('item_id', 'property_id');
                if (!empty($result)){
                    foreach ($result as $i_id => $p){
                        foreach ($p as $p_id => $data){
                            if ($delete){
                                $db->query('DELETE FROM `'.$t.'` WHERE `variant_id` = ?d AND `property_id` = ?d', $i_id, $p_id);
                            }
                            $error_str .= 'Не тот тип в таблице "'.$t.'" - item_id: ' . $i_id . ' property_id: ' . $p_id . "\n";
                        }
                        $item_ids[] = $i_id;
                    }
                }
            }
            //множественные значения
            $result = $db->query('SELECT `val`.`'.($m ? 'variant_id' : 'item_id').'` AS `obj_id`, `val`.`property_id`, `val`.`segment_id` FROM `'.$t.'` AS `val`'
                . 'INNER JOIN `properties` AS `p` ON (`p`.`id` = `val`.`property_id`)'
                . 'WHERE (`p`.`set` = 0 OR `p`.`set` IS NULL) '
                . 'GROUP BY '.($m ? '`variant_id`' : '`item_id`').', `property_id`, `segment_id`, `value`'
                . 'HAVING COUNT(*) > 1')->select('obj_id', 'property_id', 'segment_id');
            if (!empty($result)){
                foreach ($result as $o_id => $p){
                    foreach ($p as $p_id => $data){
                        foreach ($data as $s_id => $d){
                            if ($delete){//удаляем по одному, чтобы не переборщить
                                $query = 'DELETE FROM `'.$t.'` '
                                    . 'WHERE `'.($m ? 'variant_id' : 'item_id').'` = ?d '
                                    . 'AND `property_id` = ?d '
                                    . '{ AND `segment_id` = ?d}'
                                    . '{ AND (`segment_id` IS NULL OR `segment_id` = 0) AND ?d} '
                                    . 'LIMIT 1';
                                $db->query($query, $o_id, $p_id, $s_id ? $s_id : $db->skipIt(), empty($s_id) ? 1 : $db->skipIt());
                            }
                            $error_str .= 'Проблемы с множественными значениями в таблице "'.$t.'" - ' . ($m ? 'variant_id' : 'item_id') . ': ' . $o_id . ' property_id: ' . $p_id . ' segment_id: ' . $s_id . "\n";
                        }
                    }
                    if ($m){
                        $var_ids[] = $o_id;
                    }else{
                        $item_ids[] = $o_id;
                    }
                }
            }
            // Значения пропертей в таблицах другого типа данных
            $wrong_type_keys = self::getWrongTypesByTableAndMultiple($t, $m);
            $result = $db->query('SELECT `val`.`'.($m ? 'variant_id' : 'item_id').'` AS `obj_id`, `val`.`property_id`, `val`.`segment_id` FROM `'.$t.'` AS `val`'
                . 'INNER JOIN `properties` AS `p` ON (`p`.`id` = `val`.`property_id`)'
                . 'WHERE `p`.`data_type` IN (?l) '
                . 'GROUP BY '.($m ? '`variant_id`' : '`item_id`').', `property_id`, `segment_id`, `value`'
                . 'HAVING COUNT(*) > 1', $wrong_type_keys)->select('obj_id', 'property_id', 'segment_id');
            if (!empty($result)){
                foreach ($result as $o_id => $p){
                    foreach ($p as $p_id => $data){
                        foreach ($data as $s_id => $d){
                            if ($delete){//удаляем по одному, чтобы не переборщить
                                $query = 'DELETE FROM `'.$t.'` '
                                    . 'WHERE `'.($m ? 'variant_id' : 'item_id').'` = ?d '
                                    . 'AND `property_id` = ?d '
                                    . '{ AND `segment_id` = ?d}'
                                    . '{ AND (`segment_id` IS NULL OR `segment_id` = 0) AND ?d} '
                                    . 'LIMIT 1';
                                $db->query($query, $o_id, $p_id, $s_id ? $s_id : $db->skipIt(), empty($s_id) ? 1 : $db->skipIt());
                            }
                            $error_str .= 'Значения свойства находятся в таблице другого типа данных "'.$t.'" - ' . ($m ? 'variant_id' : 'item_id') . ': ' . $o_id . ' property_id: ' . $p_id . ' segment_id: ' . $s_id . "\n";
                        }
                    }
                    if ($m){
                        $var_ids[] = $o_id;
                    }else{
                        $item_ids[] = $o_id;
                    }
                }
            }
        }
        if ($delete){
            if (!empty($var_ids)){
                $db->query('UPDATE `'.\Models\CatalogManagement\Variant::TABLE.'` SET `recreate_view` = 1 WHERE `id` IN (?i)', $var_ids);
                foreach ($var_ids as $v_id){
                    \Models\CatalogManagement\Variant::clearCache($v_id);
                }
            }
            if (!empty($item_ids)){
                $db->query('UPDATE `'.\Models\CatalogManagement\Item::TABLE.'` SET `recreate_view` = 1 WHERE `id` IN (?i)', $item_ids);
                foreach ($item_ids as $i_id){
                    \Models\CatalogManagement\Variant::clearCache($i_id);
                }
            }
        }
        if (!empty($error_str)){//отправляем на мыло или кидаем exception?
            $mail = new \LPS\Container\WebContentContainer('mails/simple.tpl');
            $mail->add('message', nl2br($error_str));
            \Models\Email::send($mail, \LPS\Config::getParametr('email', 'developers_email'));
            if ($delete){
                self::checkBase(TRUE);
            }
        }
    }

    /**
     * Возвращает список пропертей, не соответствующих указанной таблице
     * @param string $target_table — имя таблицы
     * @param int $multiple — расщепляемость
     * @return string[]
     */
    public static function getWrongTypesByTableAndMultiple($target_table, $multiple){
        $types = \Models\CatalogManagement\Properties\Factory::getPropertyTypesList();
        $result = array();
        foreach($types as $data_type){
            $table = \Models\CatalogManagement\Properties\Property::getValuesTable($data_type, $multiple);
            if ($table != $target_table){
                $result[$data_type] = $data_type;
            }
        }
        return $result;
    }
    /**
     * Почистить все реестры созданных объектов каталога
     */
    public static function clearAllRegistry(){
        Type::clearCache();
        PropertyFactory::clearCache();
        Item::clearCache(null, null, false);
        Variant::clearCache(null, null, false);
    }
    /**
     * Пересоздает все view свойства всех товаров и вариантов
     * @return boolean
     */
    public static function recreateAllViews($max_count = self::MAX_RECREATE_VIEWS_ON_UPDATE, $segment_id = NULL){
        $db = \App\Builder::getInstance()->getDB();
        $count_items = $db->query('SELECT COUNT(*) FROM `'.Item::TABLE.'` WHERE `recreate_view` = 1 AND `status` IN (?l)', array(Item::S_PUBLIC, Item::S_HIDE))->getCell();
        $count_variants = $db->query('SELECT COUNT(*) FROM `'.  Variant::TABLE.'` WHERE `recreate_view` = 1 AND `status` IN (?l)', array(Variant::S_PUBLIC, Variant::S_HIDE))->getCell();
        if ($count_items + $count_variants > $max_count){
            return FALSE;
        }
        $catalogs = Type::getCatalogs();
        foreach ($catalogs as $c){
            //пересоздание view вызывается при создании объектов
            $items = CatalogSearch::factory($c->getKey(),$segment_id)->setRules(array('recreate_view' => 1))->setPublicOnly(FALSE)->searchItems(0, $max_count);
            if (count($items)){
                foreach ($items as $i){
                    $i->recreateViews();
                }
            }
            $variants = CatalogSearch::factory($c->getKey(), $segment_id)->setRules(array('variant.recreate_view' => 1))->setPublicOnly(FALSE)->searchVariants(0, $max_count);
            if (count($variants)){
                foreach ($variants as $v){
                    $v->recreateViews();
                }
            }
        }
        return TRUE;
    }
	public static function recreateCache(){
		$db = \App\Builder::getInstance()->getDB();
		$segments = \App\Segment::getInstance()->getAll();
		foreach ($segments as $s){
			$item_ids = $db->query('SELECT `id` FROM `'.\Models\CatalogManagement\Item::TABLE.'` WHERE `data_cache` IS NULL')->getCol('id', 'id');
			if (!empty($item_ids)){
				$items = \Models\CatalogManagement\Item::factory($item_ids, $s['id']);//пересоздали все кэши
				unset($item_ids);
				unset($items);
			}
			$variant_ids = $db->query('SELECT `id` FROM `'.\Models\CatalogManagement\Variant::TABLE.'` WHERE `data_cache` IS NULL')->getCol('id', 'id');
			if (!empty($variant_ids)){
				$variants = \Models\CatalogManagement\Variant::factory($variant_ids, $s['id']);
				unset($variant_ids);
				unset($variants);
			}
		}
	}
    /**
     * Отчистка базы данных от старых хвостов.
     *
     * @return bool
     */
    public static function cleanup(){
        Item::cleanup();
        PropertyFactory::cleanup();
        return true;
    }
	
	/**
	 * Получить отсортированные значения свойств
	 * @param string $prop_sort метод сортировки
	 * @param string $prop_data_type тип свойства
	 * @param array $values массив значений
	 */
	public static function getSortedValues($prop_sort, $prop_data_type, $values, $prop_values = NULL){
		if (empty($prop_sort)){
			$prop_sort = CatalogConfig::SORT_VALUES_DEF;
		}
		CatalogConfig::getPropertyFieldsData('sort', $prop_sort);//простая проверка, есть ли такая сортировка
		CatalogConfig::getPropertyFieldsData('data_type', $prop_data_type);//простая проверка, есть ли такой тип данных
		if ($prop_data_type == Properties\Enum::TYPE_NAME && ($prop_sort == CatalogConfig::SORT_VALUES_DEF || empty($prop_sort)) && !isset($prop_values)){
			throw new \Exception('Для типа "перечисление" и сортировки "по умолчанию" должны передаваться значения свойства');
		}
		//если тип свойства не перечисление и не строка или значения пустые или сортирвка по алфавиту
		if (!in_array($prop_data_type, array(Properties\Enum::TYPE_NAME, Properties\String::TYPE_NAME)) || 
			empty($values) || 
			($prop_sort == CatalogConfig::SORT_VALUES_ALF))
		{
			asort($values);//по умолчанию  == по алфавиту
			return $values;
		}
		//если сортировка не установлена
		if (empty($prop_sort) || $prop_sort == CatalogConfig::SORT_VALUES_DEF){
			if ($prop_data_type == Properties\Enum::TYPE_NAME){//у enum своя сортировка, по заданному порядку
				$temp_search_vals = array();
                if (!empty($prop_values)){
                    foreach ($prop_values as $e_id => $v){
                        if (!empty($values[$e_id])){
                            $temp_search_vals[$e_id] = $values[$e_id];
                        }
                    }
                }
                return $temp_search_vals;
			}
			asort($values);//строки по умолчанию по алфавиту
			return $values;
		}
//		if ($prop_sort == CatalogConfig::SORT_VALUES_ALF){
//			asort($values);
//			return $values;
//		}
		//сортировка по алфавиту, бывшая финансовая сортировка
		if ($prop_sort == CatalogConfig::SORT_VALUES_ALF){
			$temp_search_vals = array();
			//сначала вытаскиваем все числа
			foreach ($values as $key => $val){
				preg_match('~[^0-9]*([0-9]*).*~', $val, $out);
				$temp_search_vals[$key] = $out[1];
			}
			asort($temp_search_vals);
			foreach ($temp_search_vals as $k => $v){
				$return_values[$k] = $values[$k];
			}
			return $return_values;
		}
		throw new \Exception('Возникла необрабатываемая ситуация: prop_sort: ' . $prop_sort . '; prop_data_type: ' . $prop_data_type);
	}

    /*
     ****************** Управление свойствами элементов *******************
     */

    /**
     * расчет шага для поиска
     * @param bool $is_int считаем шаг для интов?
     * @param int $delta длина шкалы
     * @param int $degrees_count сколько всего вариантов которые можно найти
     * @return mixed(float,int)
     */
    private function calcStep($is_int, $delta, $degrees_count){
        foreach (array(100,50,30,20,10,5,3) as $num){
            if ($num * 3 < $degrees_count ){
                $degrees_count = $num;
                break;
            }
        }
        $step = round($delta/$degrees_count, $is_int ? 0 : 2);
        if ($step <= 1){
            $step = $is_int ? 1 : 0.1;
        }
        return $step;
    }

    /**
     * Поиск проходит во всех дочерних конечных типах переданных типов
     * @param int|array $type_id
     * @param bool $publicOnly
     * @return Type[] все дочерние типы
     */
    private function getTypes($type_id, $publicOnly = TRUE){
        $type_ids = array();//в каких типах будем искать
        if (!is_array($type_id)){
            $type_id = array($type_id);
        }
        $types = Type::factory($type_id);
        $allow_type_status = $publicOnly ? array(Type::STATUS_VISIBLE) : array(Type::STATUS_VISIBLE, Type::STATUS_HIDDEN);
        foreach ($types as $type){
            if (in_array($type['status'], $allow_type_status)){
                if (!$type['allow_children']){
                    $type_ids[] = $type['id'];
                }else{
                    $children = $type->getAllChildren($allow_type_status);
                    if (!empty($children)){
                        foreach ($children as $child){
                            if (empty($child['allow_children'])){
                                $type_ids = array_merge($type_ids, !empty($child) ? array_keys($child) : array());
                            }
                        }
                    }
                }
            }
        }
        return $type_ids;
    }

    /**
     * Список свойств и значений доступных для поиска для указанного типа или сквозные
     * @param int $type_id идентификатор типа
     * @param string $status "public", "exist", null фильтр статусов среди каких позиций искать
     * @param int[] $prop_ids список допустимых свойств
	 * @param array $params доп параметры для поска в нужных свойствах
	 * @param array $item_rules Rule[] из каких товаров делать выборку
	 * @param string $sort сортировка нужных свойств
	 * @param bool $getByKey забирать по ключу или по id
     * @param bool $kustik_search — искать про проброшенным свойствам в каталоге с наследуемыми айтемами
     * @return array
     */
    public function getSearchableProperties($type_id = Type::DEFAULT_TYPE_ID, $status='public', $prop_ids = null, $params = array(), $item_rules = array(), $sort = 'type_group', $getByKey = FALSE, $kustik_search = false){
        $db = \App\Builder::getInstance()->getDB();
        if (is_null($this->segment_id)){
            $segment = \App\Segment::getInstance()->getDefault();
            $this->segment_id = !empty($segment) ? $segment['id'] : NULL;
        }
        $type = Type::getById($type_id);
        $filter_type_id = $type_id;
        $catalog = $type->getCatalog();
        if (!empty($prop_ids)){
            $properties = PropertyFactory::get($prop_ids, $this->segment_id);
        }else{
//            $properties = PropertyFactory::search($type_id, PropertyFactory::P_SEARCH, 'id', $sort, 'parents', $params);
            if ($kustik_search && $catalog['nested_in']) {
                // Для поиска в кустике забираем свойства поиска по всей ветке
                $final_types = $type->getNestedInFinalTypes();
                if (count($final_types) > 1) {
                    $properties = PropertyFactory::search($type_id, PropertyFactory::P_SEARCH, 'id', $sort, 'parents', $params, $this->segment_id);
                } else {
                    $search_type = reset($final_types);
                    $filter_type_id = $type['nested_in_final'] ? $type['id'] : $search_type['id'];
                    $origin_type_ids = array($type['id']);
                    while(!empty($search_type) && $search_type['nested_in'] && $search_type['id'] != $type['id']) {
                        $origin_type_ids[] = $search_type['id'];
                        $search_type = Type::getById($search_type['nested_in'], $this->segment_id);
                    }
                    $params['origin_type_id'] = $origin_type_ids;
                    $properties = PropertyFactory::search($filter_type_id, PropertyFactory::P_SEARCH, 'id', $sort, 'parents', $params, $this->segment_id);
                }
            } else {
                $properties = PropertyFactory::search($type_id, PropertyFactory::P_SEARCH, 'id', $sort, 'parents', $params, $this->segment_id);
            }
        }
        $props_values = array(); // Возможные значения характеристик, имеющиеся в базе на данный момент
        $prop_extrim = array(); //крайние значения характеристик
        $table = null;
		$join_variants = FALSE;
        foreach ($properties as $p_id => $p_prop){ // для всех свойств определяется таблица и оперделяется нужно ли считать список доступных вариантов или крайних значений
            $table = $p_prop['table'];
            if ($p_prop['search_type'] == 'check' || $p_prop['search_type'] == 'select'){
                $props_values[$table][] = $p_id; //сгруппированные по таблицам идентификаторы свойств, для которых нужно искать все значения
            }elseif($p_prop['search_type'] == 'between'){
				if ($p_prop instanceof Properties\Diapason){
					$add_props[$p_prop['id']] = $p_prop->getAddProperties();
					$prop_extrim[$add_props[$p_prop['id']]['min']['table']][] = $add_props[$p_prop['id']]['min']['id'];
					$prop_extrim[$add_props[$p_prop['id']]['min']['table']][] = $add_props[$p_prop['id']]['max']['id'];
				}else{
					$prop_extrim[$table][] = $p_id;//сгруппированные по таблицам идентификаторы свойств, для которых нужно искать экстримальные значения
				}
            }
			if ($p_prop['multiple']){
				$join_variants = TRUE;
			}
        }
        $item_rules['item_type_id'] = Rule::make('type_id')->setValue($filter_type_id);
		$catalogSearch = CatalogSearch::factory($catalog->getKey(), $this->segment_id);
		$catalogSearch->setRules($item_rules)
            ->enableTotalCount(FALSE)
			->setPublicOnly($status == 'public');
		if ($join_variants){
			$sql_result = $catalogSearch->getVariantSqlQuery();
		}else{
			$sql_result = $catalogSearch->getItemSqlQuery();
		}
		if (empty($sql_result)){
			return array();
		}
//		$tmp_table_name = 'search_res_tmp' . str_replace(array(',','.',' '), '', microtime());
//		$db->nakedQuery('CREATE TEMPORARY TABLE `'.$tmp_table_name.'` ENGINE=MEMORY ('.$sql_result.')');
        $sql_array = array();
        unset ($table);
        foreach (array(Item::TABLE_PROP_FLOAT, Item::TABLE_PROP_INT, Item::TABLE_PROP_STRING) as $table){//порядок должен быть такой же, как и у переменных в плэйсхолдерах. см. $props_values
            if (!empty($props_values[$table])){
                $sql_array[] = $this->getSqlPartForSearchProps('item', $table, $props_values[$table], FALSE, $sql_result, $join_variants) . "\n";
            }
        }
        foreach (array(Variant::TABLE_PROP_FLOAT, Variant::TABLE_PROP_INT, Variant::TABLE_PROP_STRING) as $table){
            if (!empty($props_values[$table])){
                $sql_array[] = $this->getSqlPartForSearchProps('variant', $table, $props_values[$table], FALSE, $sql_result, $join_variants) . "\n";
            }
        }
        if (!empty($sql_array)){
//            var_dump(implode(' UNION ', $sql_array) . "\n".'ORDER BY `property_id`, `value`' . "\n");
            $res = $db->query(implode(' UNION ', $sql_array) . "\n".'ORDER BY `property_id`, `value`');
            //Поиск всех возможных значений (и их количества) для построения поиска
            /* @var $props_values array Формат переменной array(property_id=>array(property_value=>count))*/
            $props_values = $res->getCol(array('property_id', 'value'), 'count');
        }
        unset($sql);
        $sql_array = array();
        unset($table);
        foreach (array(Item::TABLE_PROP_FLOAT, Item::TABLE_PROP_INT, Item::TABLE_PROP_STRING) as $table){//порядок должен быть такой же, как и у переменных в плэйсхолдерах. см. $props_values
            if (!empty($prop_extrim[$table])){
                $sql_array[] = $this->getSqlPartForSearchProps('item', $table, $prop_extrim[$table], TRUE, $sql_result, $join_variants) . "\n";
            }
        }
        foreach (array(Variant::TABLE_PROP_FLOAT, Variant::TABLE_PROP_INT, Variant::TABLE_PROP_STRING) as $table){
            if (!empty($prop_extrim[$table])){
                $sql_array[] = $this->getSqlPartForSearchProps('variant', $table, $prop_extrim[$table], TRUE, $sql_result, $join_variants) . "\n";
            }
        }
        $this->usedTmpTable = 0;
        if (!empty($sql_array)){
//            var_dump(implode(' UNION ', $sql_array) . "\n");
            $res = $db->query(implode(' UNION ', $sql_array) . "\n");
            //Поиск максимальных и минимальных значений для построения поиска
            /* @var $prop_extrim array Формат переменной array(property_id=>array(min=>count, max=>count, count=>count)) */
            $prop_extrim = $res->select('property_id');
        }
        $result = array();
        foreach ($properties as $id => $prop){ //Полученный массив параметров свойств дополняется информацией о сортировках
            // Для кустика — $final_prop содержит проброшенное свойство, если мы в кустике, и свойство проброшено, либо исходное во всех остальных случаях
            $searchValues = array();
            if ($prop['data_type'] == 'flag'){
                if (isset($props_values[$id][0])){
                    $searchValues[0] = $prop['values']['no'];
                }
                if (isset($props_values[$id][1])){
                    $searchValues[1] = $prop['values']['yes'];
                }
            }elseif ($prop['search_type'] == 'check' || $prop['search_type'] == 'select'){
                if (isset($props_values[$id])){ //если есть значения для данного свойства
                    foreach ($props_values[$id] as $val => $count){
                        $searchValues[$val] = $prop->formatValue($prop->explicitType($val), true, true, $this->segment_id); //$searchValues = array("значение свойства" => "значение свойства для вывода")
                    }
                }
            }elseif($prop['search_type']=='between'){
				if ($prop instanceof Properties\Diapason){
					$min = $add_props[$id]['min'];
					$max = $add_props[$id]['max'];
					$searchValues['min']   = isset ($prop_extrim[$min['id']]['min']) ? ($min instanceof Properties\Date ? date('d.m.Y', $prop_extrim[$min['id']]['min']) : floatval($prop_extrim[$min['id']]['min']))   : FALSE;
					$searchValues['max']   = isset ($prop_extrim[$max['id']]['max'])   ? ($max instanceof Properties\Date ? date('d.m.Y', $prop_extrim[$max['id']]['max']) : floatval($prop_extrim[$max['id']]['max']))   : FALSE;
					$searchValues['count'] = isset ($prop_extrim[$min['id']]['count']) && isset($prop_extrim[$max['id']]['count']) ? ($prop_extrim[$min['id']]['count'] + $prop_extrim[$max['id']]['count'])/2 : FALSE;
				}else{
					$searchValues['max']   = isset ($prop_extrim[$id]['max'])   ? ($prop['data_type'] == Properties\Date::TYPE_NAME ? date('d.m.Y', $prop_extrim[$id]['max']) : floatval($prop_extrim[$id]['max']))   : FALSE;
					$searchValues['min']   = isset ($prop_extrim[$id]['min'])   ? ($prop['data_type'] == Properties\Date::TYPE_NAME ? date('d.m.Y', $prop_extrim[$id]['min']) : floatval($prop_extrim[$id]['min']))   : FALSE;
					$searchValues['count'] = isset ($prop_extrim[$id]['count']) ? $prop_extrim[$id]['count'] : FALSE;
				}
                $searchValues['step'] = 0;
                if (!empty($prop['values']['step']) || $prop['data_type'] == Properties\Date::TYPE_NAME){
                    $searchValues['step'] =  $prop['values']['step'];
                }elseif ($searchValues['max'] > $searchValues['min'] && !empty($searchValues['count'])){
                    $searchValues['step'] = $this->calcStep($prop['data_type'], $searchValues['max'] - $searchValues['min'], $searchValues['count']);
                }
            }
            $search_objects = array();
            if ($prop instanceof Properties\Entity){//если сущность, надо её подготовить перед тем как вытащить
                foreach ($searchValues as $v){
                    $prop->getFinalValue($v, $this->segment_id);
                }
                foreach ($searchValues as $v){
                    $complete_value = $prop->getCompleteValue(array('value' => $v), $this->segment_id);
                    $complete_value = is_array($complete_value) ? reset($complete_value) : $complete_value;
                    $searchValues[$v] = $complete_value[$prop::FILTER_VIEW_KEY];
                }
            }
            //сортировка значений
            if ($prop['data_type'] == Properties\Enum::TYPE_NAME && $prop['sort'] == 'default'){//у enum свой порядок
				$this->getSortedValues(CatalogConfig::SORT_VALUES_ALF, Properties\Enum::TYPE_NAME, $searchValues, $prop['values']);
                $temp_search_vals = array();
                $enum_values = $prop['values'];
                if (!empty($enum_values)){
                    foreach ($enum_values as $e_id => $v){
                        if (!empty($searchValues[$e_id])){
                            $temp_search_vals[$e_id] = $searchValues[$e_id];
                        }
                    }
                }
                $searchValues = $temp_search_vals;
            }else{//у остальных по алфавиту
                asort($searchValues);
            }
			$searchValues = $this->getSortedValues($prop['sort'], $prop['data_type'], $searchValues, $prop['values']);
            if (!$kustik_search || empty($prop['origin_type_id'])) {
                $origin_prop = $prop;
            } else {
                $origin_type = Type::getById($prop['origin_type_id']);
                $origin_prop_key = preg_replace('~^'.$origin_type['key'] . '_~', '', $prop['key']);
                $origin_prop = PropertyFactory::getSingleByKey($origin_prop_key, $prop['origin_type_id'], 'self');
            }
			if (!$getByKey){
				$result[$id] = new PropertyExtension($origin_prop, array('search_values' => $searchValues, 'search_objects' => $search_objects), $this->segment_id);
			}else{
				$result[$prop['key']] = new PropertyExtension($origin_prop, array('search_values' => $searchValues, 'search_objects' => $search_objects), $this->segment_id);
			}
        }
        return $result;
    }
    /**
     * Забираем sql для запроса значений вариантов в фильтре (потом объединяется через UNION)
     * @param string $search_type тип сущности (item | variant)
     * @param string $table таблица значений
     * @param array $prop_ids id типов
     * @param bool $getMaxMin брать только экстремумы
     * @param string|bool $sql_result
     * @param bool $join_variants используются ли правила на айтемы\варианты
     * @return type
     */
    private function getSqlPartForSearchProps($search_type, $table, $prop_ids, $getMaxMin = FALSE, $sql_result = FALSE, $join_variants){
        $sql = '(SELECT `pv`.`property_id`, `pv`.`value`, COUNT(*) AS `count`'. ($getMaxMin ? ', MIN(`pv`.`value`) AS `min`, MAX(`pv`.`value`) AS `max`' : ''). "\n" . ' FROM `'.$table. '` AS `pv`';
        if (!empty($sql_result)){
            $sql .= PHP_EOL . ' INNER JOIN ('.$sql_result.') AS `items` 
                '. PHP_EOL .' 
                ON (`items`.`'.($join_variants && $search_type == 'item' ? 'id' : 'obj_id').'` = `pv`.`'.$search_type.'_id`)';
        }
//        if ($tmp_table_name){
//            if (!empty($this->usedTmpTable)){
//                $tmp_table_name_old = $tmp_table_name;
//                $tmp_table_name = $tmp_table_name . $this->usedTmpTable;
//                $db = \App\Builder::getInstance()->getDB();
//                $db->nakedQuery('CREATE TEMPORARY TABLE `'.$tmp_table_name.'` LIKE `'.$tmp_table_name_old.'`');
//                $db->nakedQuery('INSERT INTO `'.$tmp_table_name.'` SELECT * FROM `'.$tmp_table_name_old.'`');
//            }
//            $this->usedTmpTable++;
//			if ($join_variants && $search_type == 'item'){
//				$sql .= "\n" . 'INNER JOIN `'.$tmp_table_name.'` AS `tmp` ON (`tmp`.`id` = `pv`.`'.$search_type.'_id`)';
//			}else{
//				$sql .= "\n" . 'INNER JOIN `'.$tmp_table_name.'` AS `tmp` ON (`tmp`.`obj_id` = `pv`.`'.$search_type.'_id`)';
//			}
//        }
        
        $sql .= "\n" . ' WHERE `pv`.`property_id` IN ('.implode(',', $prop_ids).') AND `pv`.`value` != ""  AND (`pv`.`segment_id` IS NULL' . (!empty($this->segment_id) ? (' OR `pv`.`segment_id` = '.$this->segment_id) : '') . ')';
        $sql .= "\n" . ($getMaxMin ? 'GROUP BY `pv`.`property_id`' : 'GROUP BY `pv`.`property_id`, `pv`.`value`') . ')';
        return $sql;
    }
    /**
     * Возвращает список уникальных значений свойства по ключу сквозным образом через все типы
     * @param array $prop_key ключ свойства
     * @param array $type_ids id типов, в которых искать
     * @param bool $publicOnly искать только в видимых (1) или и в скрытых тоже (0)
     * @return array ('value' => 'количество Items')
     * @todo нужно разделить поиск по вариантам и итемам через флаг
     * @TODO выпилить oldDB
     */

    public function getDistinctValues($prop_key, $type_ids = null, $publicOnly = true){
        if (empty($type_ids)) {
            $children = $this->catalog->getAllChildren();
            $type_ids = array($this->catalog['id']);
            foreach($children as $list) {
                $type_ids = array_merge($type_ids, array_keys($list));
            }
        }
        $props = PropertyFactory::getByKey($prop_key, $type_ids);
        if (empty($props)){
            return array();
        }
        $propTables = array();
        foreach ($props as $p){
            $propTable = $p->getTable();
            $propTables[$propTable][] = $p['id'];
        }

       // $propTables = array(self::TABLE_ITEMS_PROP_FLOAT, self::TABLE_ITEMS_PROP_INT, self::TABLE_ITEMS_PROP_STRING, self::TABLE_VARIANT_PROP_FLOAT, self::TABLE_VARIANT_PROP_INT, self::TABLE_VARIANT_PROP_STRING);
        $variantPropTables = array(Variant::TABLE_PROP_FLOAT, Variant::TABLE_PROP_INT, Variant::TABLE_PROP_STRING);
        $sql = array();
        $sqlVars = array();
        if (is_null($this->segment_id)){
            $segment = \App\Segment::getInstance()->getDefault();
            $this->segment_id = !empty($segment) ? $segment['id'] : NULL;
        }
        foreach ($propTables as $propTable => $propList){
            $sql[] = '
                (SELECT `p`.`value`, `p`.`property_id`, COUNT(`p`.`value`) AS `count_items`
                FROM `'.$propTable.'` AS `p` '.
                (!in_array($propTable, $variantPropTables) ? '
                    INNER JOIN `'.Item::TABLE.'` AS `i` ON (`p`.`item_id` = `i`.`id` AND `i`.`status`' . ( $publicOnly ? (' = '.Item::S_PUBLIC) : (' NOT IN ('.Item::S_TMP.', '.Item::S_DELETE.')')).')'
                : '
                    INNER JOIN `'.Variant::TABLE.'` AS `v` ON (`p`.`variant_id` = `v`.`id` AND `v`.`status`' . ( $publicOnly ? (' AND `v`.`status` = '.  Variant::S_PUBLIC) : (' NOT IN ('.Variant::S_DELETE.', '.Variant::S_TMP.')')).')
                    INNER JOIN `'.Item::TABLE.'` AS `i` ON (`v`.`item_id` = `i`.`id` AND `i`.`status` NOT IN ('.Item::S_TMP.', '.Item::S_DELETE.') ' . ( $publicOnly ? ' AND `i`.`status` = '.Item::S_PUBLIC : '').')'
                ).'
                WHERE `i`.`type_id` IN (?l) AND `p`.`property_id` IN (?l) AND (`p`.`segment_id` IS NULL{ OR `p`.`segment_id` = ?d})
                GROUP BY `p`.`value`)';
            $sqlVars[] = $type_ids;
            $sqlVars[] = $propList;
            $sqlVars[] = !empty($this->segment_id) ? $this->segment_id : $this->db->skipIt();
        }
        $sql = implode("\n".'                 UNION ', $sql);
        array_unshift($sqlVars, $sql);
        $distincts_props = call_user_func_array(array($this->db, 'query'), $sqlVars);
        $distincts_props = $distincts_props->select();
        $propertyList = array();
        foreach ($distincts_props as $data){ //можно было бы использовать и $props_ids, но там могут быть лишние property_id, потому что не у всех могут иметься значения
            $propertyList[$data['property_id']] = $data['property_id'];
        }
        $propertyList = PropertyFactory::get($propertyList);
        $distincts = array();
        foreach ($distincts_props as $data){
            $property = $propertyList[$data['property_id']];
            if ($property['data_type'] == 'enum' || $property['data_type'] == 'flag'){
                $name = $property->formatValue($data['value']);
            }else{
                $name = $data['value'];
            }
            if ($name != '')
                $distincts[$name] = isset($distincts[$name]) ? ($distincts[$name] + $data['count_items']) : $data['count_items'];
        }
        return $distincts;
    }

    /**
     * ищет экстремальные значения одного сквозного не расщепляемого  свойства в пределах каждого указанного типа
     * @todo нужно разделить поиск по вариантам и итемам через флаг
     * @param string $prop_key
     * @param int[] $type_ids
     * @param bool $no_empty
     * @param bool $public
     * @return array|mixed
     * @TODO выпилить oldDB
     */
   public function getExtremumValues($prop_key, $type_ids, $no_empty = false, $public = true){
        $props = PropertyFactory::getByKey($prop_key, $type_ids);
        if (empty($props)){
            return array();
        }
        $propTables = array();
        foreach ($props as $p){
            $propTable = $p->getTable();
            $propTables[$propTable][] = $p['id'];
        }
        $extremum = array();
       // $propTables = array(self::TABLE_ITEMS_PROP_FLOAT, self::TABLE_ITEMS_PROP_INT, self::TABLE_ITEMS_PROP_STRING, self::TABLE_VARIANT_PROP_FLOAT, self::TABLE_VARIANT_PROP_INT, self::TABLE_VARIANT_PROP_STRING);
        $variantPropTables = array(Variant::TABLE_PROP_FLOAT, Variant::TABLE_PROP_INT, Variant::TABLE_PROP_STRING);
        $sql = array();
        $sqlVars = array();
        if (is_null($this->segment_id)){
            $segment = \App\Segment::getInstance()->getDefault();
            $this->segment_id = !empty($segment) ? $segment['id'] : NULL;
        }
        foreach ($propTables as $propTable => $propList){
            $sql[] = '
            (SELECT `i`.`type_id` AS ARRAY_KEY, MIN(`v`.`value`) AS `min`, MAX(`v`.`value`) AS `max`
            FROM '.$propTable.' AS `v` '.
               (!in_array($propTable, $variantPropTables) ? '
               INNER JOIN `'.Item::TABLE.'` AS `i` ON (`v`.`item_id` = `i`.`id` AND `i`.`type_id` IN (?a) AND `i`.`status` '.( $public ? ' = '.  Item::S_PUBLIC : ' != '.Item::S_DELETE).') '
                : 'INNER JOIN `'.Variant::TABLE.'` AS `var` ON (`v`.`variant_id` = `var`.`id` AND `var`.`status`  '.( $public ? ' = '.  Variant::S_PUBLIC : ' != '.Variant::S_DELETE).')
                INNER JOIN `'.Item::TABLE.'` AS `i` ON (`var`.`item_id` = `i`.`id` AND `i`.`type_id` IN (?a) AND `i`.`status` '.( $public ? ' = '.Item::S_PUBLIC : ' != '.Item::S_DELETE).')'
            ).'
            WHERE `v`.`property_id` IN (?a)'. ($no_empty ? ' AND `v`.`value`!=0 AND `v`.`value`!="" AND !ISNULL(`v`.`value`)' : '') . '
                 AND (`p`.`segment_id` IS NULL{ OR `p`.`segment_id` = ?d})
            GROUP BY `i`.`type_id`)';
            $sqlVars[] = $type_ids;
            $sqlVars[] = $propList;
            $sqlVars[] = !empty($this->segment_id) ? $this->segment_id : DBSIMPLE_SKIP;
        }
        $sql = implode("\n".'                 UNION ', $sql);
        array_unshift($sqlVars, $sql);
        $extremum = call_user_func_array(array($this->db, 'select'), $sqlVars);
        return $extremum;
   }
    /**
     *
     * @param CatalogPosition $item
     * @param int $count - нужное количество
     * @param array $params
     * @param string $search_type
     * @param bool $publicOnly
	 * @param array $major_params передается по ссылке для того, чтобы быть в курсе, по каким параметрам происходит поиск
     * @return CatalogPosition[]
     */
   public function getConcurrents($type_id, CatalogPosition $entity, $count = 1, $params = array(), $search_type = self::S_ITEM, $publicOnly = true, &$major_params = array()){
        $type_properties = PropertyFactory::search($type_id, PropertyFactory::P_MAJOR, 'key', 'position', 'parents', array(), $this->segment_id);
        if (!empty($type_properties)){
            /** @var Rule[] $major_params */
            $major_params = array();
            foreach ($type_properties as $prop_key => $prop_data){
				if ($prop_data['multiple'] == 1 && $entity instanceof Item){
//					throw new \Exception('Неверно заданы параметры. Для поиска похожих items не должно быть расщепляемых свойств похожести');
					continue;//@TODO что лучше? пропускать или выкидывать ошибку?
				}
				if ($prop_data['multiple'] != 1 && $entity instanceof Variant){
					$item = $entity->getItem();
					$value = $item['properties'][$prop_key]['value'];
				}else{
					$value = $entity['properties'][$prop_key]['value'];
				}
                if (is_null($value)){
                    continue;
                }
                $range_string = $prop_data['major'];
                //парсим строку
                if ($range_string == '0' || ($prop_data['data_type'] != 'int' && $prop_data['data_type'] != 'float')){//если 0, то ищем точное совпадение
                    $major_params[$prop_key]['value'] = $value;
                }else{//иначе ищем диапазон
                    $range_from = $value;
                    $range_to = $value;
                    $plus_position = strpos($range_string, '+');
                    $minus_position = strpos($range_string, '-');
                    $has_procent = strpos($range_string, '%');
                    //если есть и плюc и минус
                    if ($plus_position !== FALSE && $minus_position !== FALSE){
                        if (!preg_match('~^\-([0-9]+\%?)\+([0-9]+\%?)$~', $range_string, $out) || $out[1] == '' || $out[2] == ''){
                            continue;
                        }
                        $minus = str_replace('-', '', $out[1]);
						if ($out[1] == '0' || $out[1] == '0%'){
							$range_from = NULL;
						}elseif (strpos($out[1], '%')){
                            $range_from -= $value*str_replace('%', '', $minus)/100;
                        }else{
                            $range_from -= $minus;
                        }
                        $plus_value = str_replace('+', '', $out[2]);
                        if ($out[2] == '0' || $out[2] == '0%'){
							$range_to = NULL;
						}elseif (strpos($out[2], '%')){
                            $range_to += $value*str_replace('%', '', $plus_value)/100;
                        }else{
                            $range_to += $plus_value;
                        }
                    }else{
                        if($has_procent){
                            $range = $value*str_replace('%', '', $range_string)/100;
                        }else{
                            $range = $range_string;
                        }
                        if ($plus_position !== FALSE){
                            $range = str_replace('+', '', $range);
                        }
                        if ($minus_position !== FALSE){
                            $range = str_replace('-', '', $range);
                        }
                        if ($plus_position === FALSE && $minus_position === FALSE){//нет ни плюсов ни минусов
                            $range_from -= $range;
                            $range_to += $range;
                        }elseif($plus_position !== FALSE && $minus_position === FALSE){//только плюс
							if ($range == 0){
								$range_to = NULL;
							}else{
								$range_to += $range;
							}
                        }elseif($plus_position === FALSE && $minus_position !== FALSE){//только минус
							if ($range == 0){
								$range_from = NULL;
							}else{
								$range_from -= $range;
							}
                        }
                    }
					if (isset($range_from)){
						$major_params[$prop_key] = Rule::make($prop_key)->setMin($range_from);
					}
					if (isset($range_to)){
						$major_params[$prop_key] = !empty($major_params[$prop_key])
                            ? $major_params[$prop_key]->setMax($range_to)
                            : Rule::make($prop_key)->setMax($range_to);
					}
                }
            }
			$params[$entity instanceof Item ? 'item.id' : 'id'] = Rule::make($entity instanceof Item ? 'item.id' : 'id')->setNot($entity['id'])->setExists();
			if ($entity instanceof Variant){
				$params['item.id'] = Rule::make('item.id')->setNot($entity['item_id']);
			}
            if (!empty($major_params)){
                $catalog_search = CatalogSearch::factory($this->catalog['key'], $this->segment_id)->setTypeId($type_id)->setRules(array_merge($major_params, $params))->setPublicOnly($publicOnly);
                return $search_type == self::S_ITEM ? $catalog_search->searchItems(0, $count)->getSearch() : $catalog_search->searchVariants(0, $count)->getSearch();
            }
            return array();
        }
   }
    /**
     * Обновить несколько свойств у нескольких товаров\вариантов
     * @param int $type_id
     * @param array $request_data запрос пользователя
     * @param array $values
     * @param array $errors
     */
    public function updateProperties($type_id, $request_data, $values, &$errors = array(), $segment_id = NULL){
        $properties = PropertyFactory::get(array_keys($values));
        $type = Type::getById($type_id);
        if (empty($type)){
            throw new \LogicException('Тип с #id ' . $type_id . ' не найден');
        }
        $catalog_search = CatalogSearch::factory($type->getCatalog()['key'], $this->segment_id)
            ->setTypeId($type_id)
            ->setPublicOnly(false);
        $prop_keys = array();
        $multiple = FALSE;
        foreach ($properties as $prop){
            $prop_keys[] = $prop['key'];
            if ($prop['multiple']){
                $multiple = TRUE;
            }
            if (!$type->includesProperty($prop)){
                throw new \LogicException('Свойство #id ' . $prop['id'] . ' не содержится в типе ' . $type_id);
            }
        }
        $check = !empty($request_data['check']) ? $request_data['check'] : array();
        if (empty($check)){//если используется фильтр
            //фильтрация параметров, по которым производится поиск
            $search_params = \App\CatalogMethods::getSearchableRules($request_data, $type_id, array(), $this->segment_id, $sort, $has_variant_prop);
            if ($has_variant_prop){
                $items_data = $catalog_search->setRules($search_params)->searchVariantIds(0, 100000000)->getSearch();
            }else{
                $items_data = $catalog_search->setRules($search_params)->searchItemIds(0, 100000000)->getSearch();
            }
        }else{//если используются галочки
            $search_rules = array();
            if ($multiple){
                $search_rules[] = Rule::make('variant.item_id')->setValue($check);
                $items_data = $catalog_search->setRules($search_rules)->searchVariantIds(0, 100000000)->getSearch();
            }else{
                $search_rules[] = Rule::make('id')->setValue($check);
                $items_data = $catalog_search->setRules($search_rules)->searchItemIds(0, 100000000)->getSearch();
            }
        }
        if (empty($items_data) || empty($properties)){
            return;
        }
        $use_variants = !empty($multiple) || !empty($has_variant_prop);
        $item_ids = $use_variants ? array_unique(array_values($items_data)) : array_keys($items_data);
        $variant_ids = $use_variants ? array_keys($items_data) : array();
        foreach ($properties as $property){
            if (!$type->includesProperty($property)){
                throw new \LogicException('Свойство #id ' . $property['id'] . ' не содержится в типе #id' . $type_id);
            }
            if ($property['multiple'] == 1){//разделяем свойства товаров и вариантов
                $variant_properties[$property['key']] = $values[$property['id']];
            }else{
                $item_properties[$property['key']] = $values[$property['id']];
            }
        }
        if (!empty($variant_properties)){
            $variant_properties = Variant::prepareUpdateData($type_id, $variant_properties);
        }
        if (!empty($item_properties)){
            $item_properties = Item::prepareUpdateData($type_id, $item_properties);
        }
        //чекаем общие ошибки, которые не зависят от конкретного товара
        if (!empty($item_properties)){
            Item::checkProperties($type_id, $item_properties, $errors, $segment_id, NULL, NULL, 1000000000);
        }
        if (!empty($variant_properties)){
            Variant::checkProperties($type_id, $variant_properties, $errors, $segment_id, NULL, NULL, 1000000000);
        }
        //после выявления общих ошибок
        if (empty($errors)){
            if (!empty($item_properties)){
                $items = Item::factory($item_ids, $segment_id);
                foreach ($items as $i){
                    foreach ($item_properties as $p_key => &$p_vals){
                        $prop = $i['properties'][$p_key];
                        foreach ($p_vals as $s_id => &$pv){
                            $pv['val_id'] = $prop['val_id'];
                        }
                    }
                    //сохраняем товар с выводом ошибок конкретного товара
                    $i->update(array(), $item_properties, $errors['items'][$i['id']], $segment_id);
                    if (empty($errors['items'][$i['id']])){
                        unset($errors['items'][$i['id']]);
                    }
                }
                if (empty($errors['items'])){
                    unset($errors['items']);
                }
            }
            if (!empty($variant_properties)){
                $variants = Variant::factory($variant_ids, $segment_id);
                foreach ($variants as $v){
                    foreach ($variant_properties as $p_key => &$pv_vals){
                        $prop = $v['properties'][$p_key];
                        foreach ($pv_vals as $s_id => &$pvv){
                            $pvv['val_id'] = $prop['val_id'];
                        }
                    }
                    //сохраняем вариант с выводом ошибок конкретного варианта
                    //для сохранения варианта нужны все свойства (в том числе и товара, т.к. в view может присутствовать и свойство товара)
                    $v->update(array(), $variant_properties, $errors['variants'][$v['id']], $segment_id);
                    if (empty($errors['variants'][$v['id']])){
                        unset($errors['variants'][$v['id']]);
                    }
                }
                if (empty($errors['variants'])){
                    unset($errors['variants']);
                }
            }
        }
    }
}