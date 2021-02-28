<?php
/**
 * Публичные методы, определяющие видимость сущностей каталога в паблике
 * Все методы ориентированы на публичную часть для текущего пользователя (в некоторых системах от пользователя не зависит)
 * 1. генераторы ссылок
 * 2. определение видимости
 */
namespace App;
use App\Configs\RealEstateConfig;
use Models\CatalogManagement\Properties\Diapason;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Rules\RuleAggregator;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use App\Configs\CatalogConfig;

class CatalogMethods {
    /**
     * //по каким полям можно делать сортировку
     * @var type
     */
    static protected $sort_allows = array(
        'id',
        'position',
//        CatalogConfig::KEY_ITEM_TITLE,
//		CatalogConfig::KEY_VARIANT_TITLE,
//        \App\Configs\ReviewConfig::CREATION_DATE
    );
    static protected $sort_by_catalog_allows = array(
        CatalogConfig::CATALOG_KEY_REAL_ESTATE => array(
            RealEstateConfig::KEY_APPART_PRICE,
            RealEstateConfig::KEY_APPART_BED_NUMBER,
            RealEstateConfig::KEY_APPART_CLOSE_PRICE,
            RealEstateConfig::KEY_APPART_AREA_ALL,
            'floor_floor_number'
        ),
        CatalogConfig::CATALOG_KEY_RESALE => array(
            RealEstateConfig::KEY_APPART_TITLE,
            RealEstateConfig::KEY_APPART_PRICE,
            RealEstateConfig::KEY_APPART_CLOSE_PRICE,
            RealEstateConfig::KEY_APPART_PRIORITY
        ),
        CatalogConfig::CATALOG_KEY_RESIDENTIAL => array(
            RealEstateConfig::KEY_APPART_TITLE,
            RealEstateConfig::KEY_APPART_PRICE,
            RealEstateConfig::KEY_APPART_CLOSE_PRICE,
            RealEstateConfig::KEY_APPART_PRIORITY
        )
    );
    public static function getVariantUrl(Variant $variant){
        return $variant->getItem()->getUrl() . 'v' . $variant['id'] . '/';
    }
    /**
     * getUrl может меняться в зависимости от каталога
     */
    public static function getItemUrl(Item $item){
        $type = $item->getType();
        $catalog = $type->getCatalog();
        switch ($catalog['key']){
            case CatalogConfig::CATALOG_KEY:
            default:
                return $item->getType()->getUrl() . 'i' . $item['id'] . '/';
        }
    }
    
    public static function getTypeUrl(Type $type, $segment_id = NULL){
        if (is_null($segment_id)){
            $default_segment = \App\Segment::getInstance()->getDefault(true);
        }else{
            $default_segment = \App\Segment::getInstance()->getById($segment_id);
        }
        $parents = $type['parents'];
        array_shift($parents);//первый нам не нужен, т.к. он всегда и везде
        return '/catalog/' . (!empty($parents) ? (implode('/', $parents) . '/') : '') . $type['id'] . '/';
    }
    /**
     * фильтруем свойства поиска, создаем правила для поиска
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $type_id
     * @param array $prop_params
     * @param int $segment_id
     * @param array $sort_params
     * @param boolean $has_variant_prop переменная для записи значения, используются ли свойства вариантов
     * @param bool $empty_filter_rules — возвращает true в случае пустого фильтра
     * @return RuleAggregator[]
     */
    public static function getSearchableRules($request, $type_id, $prop_params = array(), $segment_id = NULL, &$sort_params = array(), &$has_variant_prop = FALSE, &$empty_filter_rules = false){
        $type = Type::getById($type_id, $segment_id);
        $catalog = $type->getCatalog();
        $properties = PropertyFactory::search($type_id, PropertyFactory::P_SEARCH, 'key', 'group', 'parents', $prop_params, $segment_id);
        if ($request instanceof \Symfony\Component\HttpFoundation\Request){
            $user_data = $request->query->all();
            if (empty($user_data)){
                $user_data = $request->request->all();
            }
        }else{
            $user_data = $request;
        }
        $search_params=array();
        //отдельный хак для id
        if (!empty($user_data['item_id'])){
            $search_params['id'] = Rule::make('id')->setValue($user_data['item_id']);
            unset($user_data['item_id']);
        }
        //отдельный хак для варианта
        if (!empty($user_data['variant_id'])){
            $search_params['variant.id'] = Rule::make('id')->setValue($user_data['variant_id']);
            unset($user_data['variant_id']);
        }
        //отдельный хак для родительского айтема
        if (!empty($user_data['parent_id'])){
            $search_params['parent_id'] = Rule::make('parent_id')->setValue($user_data['parent_id']);
            unset($user_data['parent_id']);
        }
        $search_by_rooms_number = array();
		/* исключительные ситуации */
        //костыль для выбора 3,4,5+
        if (!empty($user_data[Configs\RealEstateConfig::KEY_APPART_BED_NUMBER])){
            $search_by_rooms_number = $user_data[Configs\RealEstateConfig::KEY_APPART_BED_NUMBER];
            $first_part_numbers = array();
            $second_part_rule = NULL;
            $first_part_rule = NULL;
            foreach ($search_by_rooms_number as $num){
                if ($num < 5){
                    $first_part_numbers[] = $num;
                }else{
                    $second_part_rule = Rule::make(Configs\RealEstateConfig::KEY_APPART_BED_NUMBER)->setMin(5);
                }
            }
            if (!empty($first_part_numbers)){
                $first_part_rule = Rule::make(Configs\RealEstateConfig::KEY_APPART_BED_NUMBER)->setValue($first_part_numbers);
            }
            unset($user_data[Configs\RealEstateConfig::KEY_APPART_BED_NUMBER]);
        }
//		if (!empty($user_data['currency'])){
//			$our_currency = \Models\Currency::get();
//			if (!empty($our_currency[$user_data['currency']]) && $user_data['currency'] != 'RUR'){
//				$oc = $our_currency[$user_data['currency']];
//				if (!empty($user_data[CatalogConfig::KEY_VARIANT_NEW_PRICE])){
//					if (isset($user_data[CatalogConfig::KEY_VARIANT_NEW_PRICE]['min'])){
//						$user_data[CatalogConfig::KEY_VARIANT_NEW_PRICE]['min'] = $user_data[CatalogConfig::KEY_VARIANT_NEW_PRICE]['min'] * $oc['value'] / $oc['nominal'];
//					}
//					if (isset($user_data[CatalogConfig::KEY_VARIANT_NEW_PRICE]['max'])){
//						$user_data[CatalogConfig::KEY_VARIANT_NEW_PRICE]['max'] = $user_data[CatalogConfig::KEY_VARIANT_NEW_PRICE]['max'] * $oc['value'] / $oc['nominal'];
//					}
//				}
//				if (isset($user_data[CatalogConfig::KEY_VARIANT_PRICE_MIN]['min'])){
//					$user_data[CatalogConfig::KEY_VARIANT_PRICE_MIN]['min'] = $user_data[CatalogConfig::KEY_VARIANT_PRICE_MIN]['min'] * $oc['value'] / $oc['nominal'];
//				}
//				if (isset($user_data[CatalogConfig::KEY_VARIANT_PRICE_MAX]['max'])){
//					$user_data[CatalogConfig::KEY_VARIANT_PRICE_MAX]['max'] = $user_data[CatalogConfig::KEY_VARIANT_PRICE_MAX]['max'] * $oc['value'] / $oc['nominal'];
//				}
//			}
//			unset($user_data['currency']);
//		}
		//стандартный подход к свойствам:
        if (!empty($user_data['foreign_price'])){
            $currencies = \Models\Currency::get();
            $usd = $currencies[\Models\Currency::C_USD]['value'];
            if (!empty($user_data['foreign_price']['min'])){
                $user_data[RealEstateConfig::KEY_APPART_CLOSE_PRICE]['min'] = $user_data['foreign_price']['min'] * $usd / 1000;
            }
            if (!empty($user_data['foreign_price']['max'])){
                $user_data[RealEstateConfig::KEY_APPART_CLOSE_PRICE]['max'] = $user_data['foreign_price']['max'] * $usd / 1000;
            }
            unset($user_data['foreign_price']);
        }
        foreach ($properties as $p){
            $k = $p['key'];
			if ($p['data_type'] == \Models\CatalogManagement\Properties\Date::TYPE_NAME && $p['search_type'] == PropertyFactory::SEARCH_BETWEEN){
				if (isset($user_data[$k]['min']) && $user_data[$k]['min'] != ''){
					$user_data[$k]['min'] = strtotime($user_data[$k]['min']);
				}
				if (isset($user_data[$k]['max']) && $user_data[$k]['max'] != ''){
					$user_data[$k]['max'] = strtotime($user_data[$k]['max']);
				}
			}
            if (isset ($user_data[$k]) && $user_data[$k] != ''){
                $rule = Rule::make($k);
                ((is_array($user_data[$k]) && $p['search_type']!==PropertyFactory::SEARCH_BETWEEN) || $p['search_type'] == PropertyFactory::SEARCH_AUTOCOMPLETE)
                ? $rule->setValue($user_data[$k],
                    $p['search_type'] == PropertyFactory::SEARCH_AUTOCOMPLETE
                        ? Rule::SEARCH_LIKE
                        : NULL
                    )
                : $rule->setParams($user_data[$k]);
                $search_params[$k] = $rule;
                if ($p['multiple'] == 1){
                    $has_variant_prop = TRUE;
                }
            }
        }
        // Нужно чтобы определять пустой фильтр в кустике
        $empty_filter_rules = empty($search_params);
        $get_sort = !empty($user_data['order']) ? $user_data['order'] : (!empty($user_data['sort']) ? $user_data['sort'] : array());
        if (!empty($get_sort) && is_array($get_sort)){
            foreach ($get_sort as $sort_pole => $desc){
                if (in_array($sort_pole, self::$sort_allows) || !empty(self::$sort_by_catalog_allows[$catalog['key']]) && in_array($sort_pole, self::$sort_by_catalog_allows[$catalog['key']])){
                    if (empty($search_params[$sort_pole])){
                        $sort_params[$sort_pole] = $search_params[$sort_pole] = Rule::make($sort_pole)->setOrder(empty($desc));
                    }else{
                        $sort_params[$sort_pole] = $search_params[$sort_pole]->setOrder(empty($desc));
                    }
                }else{
//                    throw new \LogicException('Сортировка по свойству ' . $sort_pole . ' не разрешена.');
                }
            }
        }
        $result = array();
        //продолжение костыля для 3,4,5+
        if (!empty($first_part_rule) && !empty($second_part_rule)){
            $result[] = RuleAggregator::make(RuleAggregator::LOGIC_OR, array($first_part_rule, $second_part_rule));
        }elseif(!empty($first_part_rule)){
            $search_params[Configs\RealEstateConfig::KEY_APPART_BED_NUMBER] = $first_part_rule;
        }elseif(!empty($second_part_rule)){
            $search_params[Configs\RealEstateConfig::KEY_APPART_BED_NUMBER] = $second_part_rule;
        }
        //сбор рулов из стандартного свойства
        $result[] = RuleAggregator::make(RuleAggregator::LOGIC_AND, $search_params);
        
        return $result;
    }

    /**
     * @param $complex_ids
     * @param null $segment_id
     * @return array
     */
    public static function getRealEstateFilters($complex_ids, $segment_id = null, $type_id = null) {
        if (empty($complex_ids)) {
            return array();
        }
        $catalog = Type::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE, Type::DEFAULT_TYPE_ID, $segment_id);
        $flat_category = Type::getByKey(RealEstateConfig::CATEGORY_KEY_FLAT, $catalog['id'], $segment_id);
        $bed_number_prop = PropertyFactory::getSingleByKey(RealEstateConfig::KEY_APPART_BED_NUMBER, $flat_category['id'], 'self', $segment_id);
        $area_all_prop = PropertyFactory::getSingleByKey(RealEstateConfig::KEY_APPART_AREA_ALL, $flat_category['id'], 'self', $segment_id);
        $flat_state_prop = PropertyFactory::getSingleByKey(RealEstateConfig::KEY_APPART_STATE, $flat_category['id'], 'self', $segment_id);
        $flat_state_val = null;
        foreach($flat_state_prop['values'] as $val) {
            if ($val['key'] == RealEstateConfig::KEY_APPART_STATE_FOR_SALE) {
                $flat_state_val = $val['id'];
                break;
            }
        }
        $complex_ids = is_array($complex_ids) ? $complex_ids : array($complex_ids);
        $db = \App\Builder::getInstance()->getDB();
        $result = $db->query('
                SELECT `top_parent_id` AS `parent_id`, `bedroom_count`, MAX(`area_all`) AS `area_max`, MIN(`area_all`) AS `area_min`
                FROM (
                    SELECT `i`.`id` AS `item_id`,
                        ' . (empty($type_id)
                                ? 'TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(`parents`, ".", 2), ":", -1))'
                                : 'TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(`parents`, ".' . $type_id . ':", -1), ".", 1))')
                        . ' AS `top_parent_id`,
                        IF (`bedroom`.`value` > 5, 5, `bedroom`.`value`) AS `bedroom_count`,
                        `area`.`value` AS `area_all`
                    FROM `items` `i` INNER JOIN `items_properties_int` AS `state` ON (`i`.`id` = `state`.`item_id` AND `state`.`property_id` = ?d)
                        INNER JOIN `items_properties_int` AS `bedroom` ON (`i`.`id` = `bedroom`.`item_id` AND `bedroom`.`property_id` = ?d)
                        INNER JOIN `items_properties_float` as `area` ON (`i`.`id` = `area`.`item_id` AND `area`.`property_id` = ?d)
                    WHERE `i`.`status` = ?d AND `i`.`type_id` = ?d AND `state`.`value` = ?d
                ) AS `tbl`
                WHERE `top_parent_id` IN (?i)
                GROUP BY `parent_id`, `bedroom_count`
                ORDER BY `parent_id`, `bedroom_count`', $flat_state_prop['id'], $bed_number_prop['id'], $area_all_prop['id'], Item::S_PUBLIC, $flat_category['id'], $flat_state_val, $complex_ids)
            ->select('parent_id', 'bedroom_count');
        return $result;
    }
	/**
	 * правила, по которым определяется, что товар\вариант виден на сайте в паблике
	 * @return Rule[]
	 */
	public static function getVisibleRules(){
		$rules['status'] = Rule::make('status')->setValue(self::getItemStatus());
		$rules['variant.status'] = Rule::make('variant.status')->setValue(self::getVariantStatus());
		return $rules;
	}
	public static function getTypeStatus(){
		$allow_statuses = array(Type::STATUS_VISIBLE);
		$account = \App\Builder::getInstance()->getAccount();
		if ($account->isPermission('catalog-type', 'updateHidden')){
			$allow_statuses[] = Type::STATUS_HIDDEN;
		}
		return $allow_statuses;
	}
	public static function getItemStatus(){
		$account = \App\Builder::getInstance()->getAccount();
		$allow_statuses = array(Item::S_PUBLIC);
		if ($account->isPermission('catalog-type', 'updateHidden')){
			$allow_statuses[] = Item::S_HIDE;
		}
		return $allow_statuses;
	}
	public static function getVariantStatus(){
		$account = \App\Builder::getInstance()->getAccount();
		$allow_statuses = array(Variant::S_PUBLIC);
		if ($account->isPermission('catalog-item', 'changeHidden')){
			$allow_statuses[] = Variant::S_HIDE;
		}
		return $allow_statuses;
	}
	/**
	 * Проверить, виден ли тип в паблике
	 * @param \Models\CatalogManagement\Type $type
	 * @return type
	 */
	public static function checkTypeVisibility(Type $type){
        return in_array($type['status'], self::getTypeStatus());
	}
	/**
	 * Проверить, виден ли товар в паблике
	 * @param Item $item
	 * @return bool
	 */
    public static function checkItemVisibility($item){
		if (!self::checkTypeVisibility($item->getType())){
			return FALSE;
		}
		return in_array($item['status'], self::getItemStatus());
    }
	/**
	 * Проверить, виден ли вариант в паблике
	 * @param Variant $variant
	 * @return bool
	 */
    public static function checkVariantVisibility($variant){
		if (!self::checkItemVisibility($variant->getItem())){
			return FALSE;
		}
		return in_array($variant['status'], self::getVariantStatus());
    }
	/**
	 * забрать только видимые варианты у товара
	 * @param \Models\CatalogManagement\Item $item
	 */
	public static function getVisibleVariants(Item $item){
		
	}
	/**
	 * фильтрует типы, которые не должны быть видны пользователям, т.к. у них нет видимых товаров
	 * !!!только для публичной части и только в выбранном сегменте
	 * @param Type[] $types
	 */
	public static function filterNonVisibleTypes($types = array()){
		if (empty($types)){
			return array();
		}
		$result = array();
		foreach ($types as $t_id => $t){
			if ($t['counters']['visible_items'] > 0){
				$result[$t_id] = $t;
			}
		}
		return $result;
	}
	/**
	 * Взять всех детей одного типа с учетом видимости на сайте
	 * @param int $main_type_id
	 * @return type
	 */
	public static function getTypeChildren($main_type_id = Type::DEFAULT_TYPE_ID){
        $main_type = Type::getById($main_type_id);
        $children = self::filterNonVisibleTypes($main_type->getChildren());
        return $children;
    }
    
    public static function getForeignPrice($search_properties){
        if (!empty($search_properties[\App\Configs\RealEstateConfig::KEY_APPART_CLOSE_PRICE]) && $search_properties[\App\Configs\RealEstateConfig::KEY_APPART_CLOSE_PRICE]['search_type'] == PropertyFactory::SEARCH_BETWEEN){
            //если поиск по цене, то надо отдать верстальщикам цену в валюте
            $price_prop = $search_properties[\App\Configs\RealEstateConfig::KEY_APPART_CLOSE_PRICE];
            $currencies = \Models\Currency::get();
            if (!empty($currencies[\Models\Currency::C_USD])){
                $usd = $currencies[\Models\Currency::C_USD]['value'];
                $foreign_price = array(
                    'key' => 'foreign_price',
                    'count' => $price_prop['search_values']['count'],
                    'min' => floor(($price_prop['search_values']['min'] * 1000)/$usd),
                    'max' => ceil(($price_prop['search_values']['max'] * 1000)/$usd),
                    'step' => round(($price_prop['search_values']['step'] * 1000)/$usd)
                );
                return $foreign_price;
            }
        }
        return NULL;
    }
}
?>