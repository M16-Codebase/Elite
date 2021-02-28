<?php
namespace Models\CatalogManagement\Exchange\Import;

use Models\CatalogManagement\Type;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Properties;
use App\Configs\CatalogConfig;
use Models\CronTasks\Task;

/**
 * Требования к импорту:
 *  по любым ключам каталога
 *  в пределах одного каталога
 *  массовое чтение\запись
 *  возможность создания товаров\вариантов
 *  возможность чтения из разных форматов файлов (csv, excel, xml)
 * 
 * 
 * Данный класс только для обработки данных
 *
 * @author pochepochka
 */
abstract class ImportCatalogEntities implements iImport{
    /**
     * название поля для id варианта
     */
    const FIELD_NAME_VARIANT_ID = 'id';
    /**
     * название поля для id айтема
     */
    const FIELD_NAME_ITEM_ID = 'item_id';
    /**
     * название поля для id категории
     */
	const FIELD_NAME_TYPE_ID = 'type_id';
    /**
     * уникальный идентификатор айтема из внешней системы
     */
    const ITEM_UNIQUE_KEY = CatalogConfig::KEY_ITEM_CODE;
    /**
     * уникальный идентификтор варианта из внешней системы
     */
    const VARIANT_UNIQUE_KEY = CatalogConfig::KEY_VARIANT_CODE;
    /**
     * игнорировать ли пустые значения (значение по умолчанию, можно переопределить в задаче $this->task['data']['empty_value'] == 'ignore')
     */
    const IGNORE_EMPTY_FIELD = TRUE;
    /**
     * добавлять ли новые значения перечислений (значение по умолчанию, можно переопределить в задаче $this->task['data']['enum'] == 'add')
     */
    const ADD_ENUM_VALUE = TRUE;
    /**
     *
     * @var static[]
     */
    protected static $instances = array();
    /**
     * 
     * @param Task $task
     * @return static
     */
    public static final function getInstance(Task $task){
        if (!isset(self::$instances[$task['id']])){
            self::$instances[$task['id']] = new static($task);
        }
        return self::$instances[$task['id']];
    }
    /**
     *
     * @var Type
     */
    protected $catalog = NULL;
    /**
     *
     * @var int
     */
    protected $segment_id = NULL;
    /**
     *
     * @var Task
     */
    protected $task = NULL;
    /**
     * если создали айтем, то не надо заново создавать, поэтому запишем маркер
     * @var array 
     */
    private $createdItems = array();
    /**
     * Если идентифиикатор - код из внешней системы, то запишем соответствие
     * @var array 
     */
    private $itemIdByCode = array();
    /**
     * Если идентификатор - код из внешней системы, то запишем соответствие
     * @var array
     */
    private $variantIdByCode = array();
    /**
     * счетчик строк данных
     * @var int 
     */
    private $i = 0;
    /**
     * Счетчик айтемов
     * @var int
     */
    protected $items_count = 0;
    /**
     * Счетчик вариантов
     * @var int
     */
    protected $variants_count = 0;
    protected final function __construct(Task $task) {
        if (empty($task['data']['catalog_key'])){
            throw new \Exception('В крон задаче для импорта каталога должен быть известен каталог');
        }
        $this->catalog = Type::getByKey($task['data']['catalog_key']);
        $this->segment_id = $task['segment_id'];
        $this->task = $task;
        //Скорее всего не понадобится, т.к. основной определитель - это процент выполнения задачи
//        if (!empty($task['data']['last_num'])){
//            $this->i = $task['data']['last_num'];
//        }
    }
    /**
     * Обработка данных. Сюда передаем уже разбитые на блоки данные
     * @param array $data каждый элемент массива - это либо даннные варианта, либо данные айтема, возможно и то и другое
     */
    protected final function dataProcessing($data){
        list($items, $variants, $types) = $this->getEntities($data);
        foreach ($data as $d){
            if (!empty($d['num'])){//если хотим, можем передавать данные о номере строки
                $this->i = $d['num'];
                unset($d['num']);
            }else{
                $this->i++;//счетчик сразу, т.к. при добавлении ошибок до конца не проходим
            }
            //надо записать процент выполнения
            $errors = NULL;
            $type_id = isset($d[self::FIELD_NAME_TYPE_ID]) ? $d[self::FIELD_NAME_TYPE_ID] : NULL;
            $item_id = isset($d[self::FIELD_NAME_ITEM_ID]) ? $d[self::FIELD_NAME_ITEM_ID] : NULL;
            $variant_id = isset($d[self::FIELD_NAME_VARIANT_ID]) ? $d[self::FIELD_NAME_VARIANT_ID] : NULL;
            $actions = array();//действия, которые надо совершить над айтемами\вариантами
            if (!empty($type_id) && empty($types[$type_id])){
                $this->task->addError($this->i, 'Не найдена категория с ID: «' . $type_id . '»');
                continue;
            }
            if (!empty($type_id) && $types[$type_id]['allow_children']){
                $this->task->addError($this->i, 'В данном типе нельзя создавать айтемы: «' . $type_id . '»');
                continue;
            }
            if (isset($d[self::ITEM_UNIQUE_KEY]) && empty($d[self::ITEM_UNIQUE_KEY])){
                $this->task->addError($this->i, 'При использовании кода айтема, его заполнение обязательно');
                continue;
            }
            if (isset($d[self::VARIANT_UNIQUE_KEY]) && empty($d[self::VARIANT_UNIQUE_KEY])){
                $this->task->addError($this->i, 'При использовании кода варианта, его заполнение обязательно');
                continue;
            }
            if (!empty($item_id) && !is_numeric($item_id) && !empty($this->createdItems[$item_id])){
                $item_id = $this->createdItems[$item_id];
            }
            if (!empty($d[self::ITEM_UNIQUE_KEY]) && !empty($this->itemIdByCode[$d[self::ITEM_UNIQUE_KEY]])){
                $item_id = $this->itemIdByCode[$d[self::ITEM_UNIQUE_KEY]];
            }
            if (!empty($d[self::VARIANT_UNIQUE_KEY]) && !empty($this->variantIdByCode[$d[self::VARIANT_UNIQUE_KEY]])){
                $variant_id = $this->variantIdByCode[$d[self::VARIANT_UNIQUE_KEY]];
            }
            if (!empty($item_id) && is_numeric($item_id) && empty($items[$item_id])){
                $this->task->addError($this->i, 'Не найден айтем с ID: «' . $item_id . '»' . (isset($d[self::ITEM_UNIQUE_KEY]) ? (' ('.self::ITEM_UNIQUE_KEY.': «'.$d[self::ITEM_UNIQUE_KEY].'»)') : ''));
                continue;
            }
            if (!empty($variant_id) && is_numeric($variant_id) && empty($variants[$variant_id])){
                $this->task->addError($this->i, 'Не найден вариант с ID: «' . $variant_id . '»' . (isset($d[self::VARIANT_UNIQUE_KEY]) ? (' ('.self::VARIANT_UNIQUE_KEY.': «'.$d[self::VARIANT_UNIQUE_KEY].'»)') : ''));
                continue;
            }
            if (!isset($variant_id)){
                if (!isset($item_id)){
                    $this->task->addError($this->i, 'Не заданы обязательные поля ('.self::FIELD_NAME_ITEM_ID.' или '.self::FIELD_NAME_VARIANT_ID.')');
                    continue;
                }elseif(empty($item_id) || !is_numeric($item_id)){
                    if (empty($type_id)){
                        $this->task->addError($this->i, 'Отсутствует указатель на категорию');
                        continue;
                    }
                    //создание айтема
                    $actions['createItem'] = TRUE;
				}else{
                    if (!empty($type_id) && $type_id != $items[$item_id]['type_id']){
                        //перенос айтема
                        $actions['transferItem'] = TRUE;
                    }
                    //редактирование айтема
                    $actions['editItem'] = TRUE;
                }
            }elseif(empty($variant_id)){
                if (!empty($item_id) && is_numeric($item_id)){
                    if (!empty($type_id) && $type_id != $items[$item_id]['type_id']){
                        //перенос айтема
                        $actions['transferItem'] = TRUE;
                    }else{
                        //редактирование айтема
                        $actions['editItem'] = TRUE;
                    }
                }else{
                    if (empty($type_id)){
                        $this->task->addError($this->i, 'Отсутствует указатель на категорию');
                        continue;
                    }
                    //создание айтема
                    $actions['createItem'] = TRUE;
                }
                //создание варианта
                $actions['createVariant'] = TRUE;
            }else{
                if (empty($item_id) || ($item_id == $variants[$variant_id]['item_id'] && (empty($type_id) || $items[$item_id]['type_id'] == $type_id))){
                    //редактирование айтема и варианта
                    $actions['editItem'] = TRUE;
                    $actions['editVariant'] = TRUE;
                }else{
                    if (is_numeric($item_id)){
                        //перенос айтема
                        if (!empty($type_id) && $items[$item_id]['type_id'] != $type_id){
                            $actions['transferItem'] = TRUE;
                        }
                        //перенос варианта
                        if ($item_id != $variants[$variant_id]['item_id']){
                            $actions['transferVariant'] = TRUE;
                        }
                        // + редактирование айтема и варианта
                        $actions['editVariant'] = TRUE;
                        $actions['editItem'] = TRUE;
                    }else{
                        //создание айтема из айтема варианта, перенос варианта в новый айтем + редактирование айтема и варианта
                        $old_item_properties = $variants[$variant_id]->getItem()['properties'];
                        foreach ($old_item_properties as $p_k => $p_d){
                            if (!array_key_exists($p_k, $d)){//если значения нет
                                $d[$p_k] = $p_d['real_value'];
                            }
                        }
                        $actions['createItem'] = TRUE;
                        $actions['transferVariant'] = TRUE;
                        $actions['editVariant'] = TRUE;
                    }
                }
            }
            $type = !empty($types[$type_id]) ? $types[$type_id] : (!empty($this->task['data']['type_id']) ? Type::getById($this->task['data']['type_id']) : NULL);
            //все экшены собрали, теперь выполняем
            if (!empty($actions['createItem'])){
                if (empty($type)){
                    $this->task->addError($this->i, 'Невозможно создать айтем - не указана категория');
                    continue;
                }
                $new_item = $this->createItem($type, $d, $errors);
                $this->createdItems[$item_id] = $new_item['id'];
                if (!empty($errors)){
                    $this->task->addError($this->i, $errors);
                    $errors = array();
                    continue;
                }
                $item_id = $new_item['id'];
                $items[$item_id] = $new_item;
            }elseif (!empty($actions['transferItem'])){
                $this->transferItem($items[$item_id], $type['id'], $errors);
                if (!empty($errors)){
                    $this->task->addError($this->i, $errors);
                    $errors = array();
                    continue;
                }
            }
            if (!empty($actions['editItem'])){
                $this->editItem($items[$item_id], $d, $errors);
                if (!empty($errors)){
                    $this->task->addError($this->i, $errors);
                    $errors = array();
                    continue;
                }
            }
            if (!empty($actions['createVariant'])){
                $this->createVariant($items[$item_id], $d, $errors);
                if (!empty($errors)){
                    $this->task->addError($this->i, $errors);
                    $errors = array();
                    continue;
                }
            }elseif (!empty($actions['transferVariant'])){
                $this->transferVariant($variants[$variant_id], $items[$item_id], $errors);
                if (!empty($errors)){
                    $this->task->addError($this->i, $errors);
                    $errors = array();
                    continue;
                }
            }
            if (!empty($actions['editVariant'])){
                $this->editVariant($variants[$variant_id], $d, $errors);
                if (!empty($errors)){
                    $this->task->addError($this->i, $errors);
                    $errors = array();
                    continue;
                }
            }
            if (!empty($actions['createItem']) || !empty($actions['editItem']) || !empty($actions['transferItem'])){
                $this->items_count++;
            }
            if (!empty($actions['createVariant']) || !empty($actions['editVariant']) || !empty($actions['transferVariant'])){
                $this->variants_count++;
            }
        }
        Item::saveCacheData();
        \Models\CatalogManagement\Catalog::clearAllRegistry();
    }
    private function prepareData($data, Type $type, $entity_type, $old_data = NULL){
        $propList = $type->getProperties();
        $update_data = array();
        $entityClass = 'Models\CatalogManagement\\' . ucfirst($entity_type);
        foreach ($propList as $p){
            if (!array_key_exists($p['key'], $data) 
                || ($entity_type == Item::CATALOG_IDENTITY_KEY && $p['multiple'] == 1)
                || ($entity_type == Variant::CATALOG_IDENTITY_KEY && $p['multiple'] != 1)
            ){
                continue;
            }
            if ($data[$p['key']] == '' 
                && ((!array_key_exists('empty_value', $this->task['data']) && self::IGNORE_EMPTY_FIELD)
                    || (!empty($this->task['data']['empty_value']) && $this->task['data']['empty_value'] == 'ignore')
                )
            ){
                continue;
            }
            if ($p instanceof Properties\Entity){
                continue;
            }
            $update_data[$p['key']] = $this->getValue($data[$p['key']], $p, $type);
        }
        return $entityClass::prepareUpdateData(
            $type['id'], 
            $update_data, 
            $this->segment_id,
            $old_data
        );
    }
    /**
     * Получить нормальное значение из возможно кривых
     * @param mixed $p_value
     * @param Property $prop
     * @param Type $type
     * @return mixed
     */
    private function getValue($p_value, Property $prop, Type $type){
        if ($prop instanceof Properties\Entity){
            return;
        }
        if ($prop['set'] == 1){
            $p_value = explode(\LPS\Config::CSV_SEPARATOR_SET_VALUES, $p_value);
        }else{
            $p_value = array($p_value);
        }
        foreach ($p_value as &$pv){
            $pv = trim($pv);
            if($prop['data_type'] == Properties\Flag::TYPE_NAME){
                if (array_search($val, $prop['values']) == 'yes'){
                    $pv = 1;
                }else{
                    $pv = 0;
                }
            }elseif($prop['data_type'] == Properties\Float::TYPE_NAME){
                $pv = floatval(str_replace(',', '.', str_replace(' ', '', $pv)));
            }elseif($prop['data_type'] == Properties\Int::TYPE_NAME){
                $pv = str_replace(' ', '', $pv);
            }elseif($prop['data_type'] == Properties\Date::TYPE_NAME){
                $pv = date('Y-m-d', strtotime($pv));
            }elseif($prop['data_type'] == Properties\DateTime::TYPE_NAME){
                $pv = date('Y-m-d H:i:s', strtotime($pv));
            }
        }
        //разбираемся с enum значениями отдельно от остальных
        if ($prop['data_type'] == Properties\Enum::TYPE_NAME){
            $p_value = $this->enumValues($p_value, $prop, $type);
        }
        if (is_array($p_value) && $prop['set'] != 1){
            $p_value = reset($p_value);
        }
        return $p_value;
    }
    private function enumValues($values, Properties\Enum $prop, Type $type){
		if (empty($type) && !empty($this->task['data']['type_id'])){
			$type_id = $this->task['data']['type_id'];
            $type = Type::getById($type_id, $this->segment_id);
		}
        foreach ($values as $num => &$val){
            $ids = $prop->getEnumIds($val);
            $enum_id = reset($ids);
            //если значение не найдено и стоит задача добавления новых значений, то добавляем
            if (empty($enum_id)){
                if ((!array_key_exists('enum', $this->task['data']) && self::ADD_ENUM_VALUE) || (!empty($this->task['data']['enum']) && $this->task['data']['enum'] == 'add')){
                    $enum_id = $prop->addEnumValue($val);
                    if (empty($val)){
                        unset($values[$num]);
                        continue;
					}
                }else{
                    unset($values[$num]); 
                    continue;
                }
            }elseif (!$type->checkPropertyAccessibility($prop, $enum_id)){
                $type->setSingleEnumUse($prop['id'], TRUE, $enum_id);
            }
            $val = $enum_id;
        }
        return $values;
    }
    private function createItem(Type $type, $data, &$errors){
        $update_data = $this->prepareData($data, $type, Item::CATALOG_IDENTITY_KEY);
        if (empty($update_data)){
			$errors['main'] = 'Нет данных для сохранения';
            return;
        }
        $item_id = Item::create(
            $type['id'], 
            Item::S_HIDE, 
            $update_data, 
            $errors, 
            $this->segment_id
        );
        if (!empty($errors)){
            return;
        }
        $item = Item::getById($item_id, $this->segment_id);
		//заново перелопачиваем, чтобы подсосались val_id
        $item->updateValues($this->prepareData($data, $type, Item::CATALOG_IDENTITY_KEY, $item['properties']), $errors, $this->segment_id);
        return $item;
    }
    private function createVariant(Item $item, $data, &$errors){
        $type = $item->getType();
        $update_data = $this->prepareData($data, $type, Variant::CATALOG_IDENTITY_KEY);
        if (empty($update_data)){
            return;
        }
        $variant_id = $item->createVariant(
            Variant::S_HIDE, 
            $update_data, 
            $errors, 
            $this->segment_id
        );
        if (!empty($errors)){
            return;
        }
        $variant = Variant::getById($variant_id, $this->segment_id);
        $variant->updateValues($update_data, $errors, $this->segment_id);
        return $variant;
    }
    private function editItem(Item $item, $data, &$errors){
        $type = $item->getType();
        $update_data = $this->prepareData($data, $type, Item::CATALOG_IDENTITY_KEY, $item['properties']);
        if (empty($update_data)){
            return;
        }
        $item->updateValues($update_data, $errors, $this->segment_id);
    }
    private function editVariant(Variant $variant, $data, &$errors){
        $type = $variant->getType();
        $update_data = $this->prepareData($data, $type, Variant::CATALOG_IDENTITY_KEY, $variant['properties']);
        if (empty($update_data)){
            return;
        }
        $variant->updateValues($update_data, $errors, $this->segment_id);
    }
    private function transferItem(Item $item, $type_id, &$errors){
        return $item->changeType($type_id, $errors);
    }
    private function transferVariant(Variant $variant, Item $item, &$errors){
        return $variant->changeItem($item, $errors);
    }
    /**
     * Собирает все сущности, которые понадобятся
     * @param array $data
     * @return array('items', 'variants', 'types')
     */
    private function getEntities($data){
        $this->itemIdByCode = array();
        $this->variantIdByCode = array();
        $variant_ids = array();
        $item_ids = array();
        $type_ids = array();
        $item_codes = array();
        $variant_codes = array();
        $items = array();
        $variants = array();
        //надо собрать все id
        foreach ($data as $str){
            if (isset($str[self::VARIANT_UNIQUE_KEY]) && !empty($str[self::VARIANT_UNIQUE_KEY])){
                $variant_codes[$str[self::VARIANT_UNIQUE_KEY]] = $str[self::VARIANT_UNIQUE_KEY];
            }elseif (isset($str[self::FIELD_NAME_VARIANT_ID]) && !empty($str[self::FIELD_NAME_VARIANT_ID]) && is_numeric($str[self::FIELD_NAME_TYPE_ID])){
                $variant_ids[$str[self::FIELD_NAME_VARIANT_ID]] = $str[self::FIELD_NAME_VARIANT_ID];
            }
            if (isset($str[self::ITEM_UNIQUE_KEY]) && !empty($str[self::ITEM_UNIQUE_KEY])){
                $item_codes[$str[self::ITEM_UNIQUE_KEY]] = $str[self::ITEM_UNIQUE_KEY];
            }elseif (isset($str[self::FIELD_NAME_ITEM_ID]) && !empty($str[self::FIELD_NAME_ITEM_ID])){
                if (is_numeric($str[self::FIELD_NAME_ITEM_ID])){
                    $item_ids[$str[self::FIELD_NAME_ITEM_ID]] = $str[self::FIELD_NAME_ITEM_ID];
                }elseif(!empty($this->createdItems[$str[self::FIELD_NAME_ITEM_ID]])){
                    $item_ids[$this->createdItems[$str[self::FIELD_NAME_ITEM_ID]]] = $this->createdItems[$str[self::FIELD_NAME_ITEM_ID]];
                }
            }
            if (isset($str[self::FIELD_NAME_TYPE_ID]) && !empty($str[self::FIELD_NAME_TYPE_ID]) && is_numeric($str[self::FIELD_NAME_TYPE_ID])){
                $type_ids[$str[self::FIELD_NAME_TYPE_ID]] = $str[self::FIELD_NAME_TYPE_ID];
            }
        }
        if (!empty($variant_codes)){
            $variants = \Models\CatalogManagement\Search\CatalogSearch::factory($this->catalog['key'], $this->segment_id)->setRules(
                array(self::VARIANT_UNIQUE_KEY => \Models\CatalogManagement\Rules\Rule::make(self::VARIANT_UNIQUE_KEY)->setValue($variant_codes))
            )->searchVariants()->getSearch();
        }
        if (!empty($item_codes)){
            $items = \Models\CatalogManagement\Search\CatalogSearch::factory($this->catalog['key'], $this->segment_id)->setRules(
                array(self::ITEM_UNIQUE_KEY => \Models\CatalogManagement\Rules\Rule::make(self::ITEM_UNIQUE_KEY)->setValue($item_codes))
            )->searchItems()->getSearch();
        }
        $items += Item::factory($item_ids, $this->segment_id);
        $variants += Variant::factory($variant_ids, $this->segment_id);
        $types = Type::factory($type_ids, $this->segment_id);
        foreach ($items as $i){
            if (!empty($item_codes) && isset($i[self::ITEM_UNIQUE_KEY]) && !empty($item_codes[$i[self::ITEM_UNIQUE_KEY]])){
                $this->itemIdByCode[$i[self::ITEM_UNIQUE_KEY]] = $i['id'];
            }
        }
        foreach ($variants as $v){
            if (!empty($variant_codes) && isset($v[self::VARIANT_UNIQUE_KEY]) && !empty($variant_codes[$v[self::VARIANT_UNIQUE_KEY]])){
                $this->variantIdByCode[$v[self::VARIANT_UNIQUE_KEY]] = $v['id'];
            }
        }
        return array($items, $variants, $types);
    }
}
