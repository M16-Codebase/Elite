<?php
namespace Models\CatalogManagement\Search;
/**
 * Класс поиска объектов каталога
 * Для улучшения читаемости кода класс построен по принципу "змеиный хвост"
 * Результаты поиска отдаются в виде экземпляра объекта CatalogSearchResult, содержащего в себе
 * все данные о результатах поиска - найденные объекты, общее количество объектов, подходящих под условия поиска,
 * количество по типам
 *
 * @author charles manson
 */
use Models\CatalogManagement\Type;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\Rules\iRule;
use Models\CatalogManagement\Rules\RuleAggregator;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
class CatalogSearch {
    const SORT_RANDOM = "random";
    const SORT_IN_ORDER = "order";
    
    // Режимы поиска
    const S_ITEM = 1;
    const S_VARIANT  = 2;
    
    private $allow_sort_mode = array(self::SORT_IN_ORDER, self::SORT_RANDOM);
    
    private $segment_id = 0;
    private $type_id = NULL;
    private $rules = array();
    private $sort_mode = self::SORT_IN_ORDER;
    private $public_only = TRUE;
    private $count_by_types = FALSE;
    private $not_items_ids = NULL;
    private $sql_injections = array();
    private $catalog = NULL;
    private $get_count = TRUE;
	

    /**
     *
     * @param string $catalog_key Ключ каталога верхнего уровня (catalog, orders)
     * @param int $segment_id
     * @return CatalogSearch
     */
    public static function factory($catalog_key = NULL, $segment_id = NULL, $isarenda=0){
        return new self($catalog_key, $segment_id);
    }

    /**
     * @param $catalog_key
     * @param null $segment_id
     * @throws \Exception
     */
    private function __construct($catalog_key, $segment_id = NULL) {
        $this->catalog = Type::getByKey($catalog_key, Type::DEFAULT_TYPE_ID, $segment_id);
        if (empty($this->catalog) || !$this->catalog->isCatalog() && $this->catalog['id'] != Type::DEFAULT_TYPE_ID){
            throw new \Exception('В объект поиска по каталогу должен быть передан ключ категории первого уровня. «' . $catalog_key . '» не является категорией первого уровня');
        }
        $this->type_id = $this->catalog['id'];
        $this->segment_id = $segment_id;
    }
    /**
     * Устанавливает сегмент для поиска, по умолчанию 0
     * @param int $segment_id
     * @return CatalogSearch
     */
    public function setSegment($segment_id){
        $this->segment_id = !empty($segment_id) ? $segment_id : 0;
        return $this;
    }

    /**
     * Устанавливает корневой тип для поиска, id каталога по умолчанию
     * @param int $type_id
     * @throws \Exception
     * @return CatalogSearch
     */
    public function setTypeId($type_id){
        $type = Type::getById($type_id);
        /*
        echo '<pre>';
        print_r($type);
        echo '</pre>';
        exit();
        */
        if ((!$type->isParent($this->catalog['id']) && $type['id'] != $this->catalog['id'])&& '|'.strpos($_SERVER['REQUEST_URI'],'arenda')==false){
            throw new \Exception('Категория товара должна быть вложена в каталог «' . $this->catalog['title'] . '» или быть им');
        }
        $this->type_id = $type_id;
        return $this;
    }
    /**
     * Устанавливает правила поиска
     * @param iRule[] $rules
     * @return CatalogSearch
     */
    public function setRules($rules){
        $this->rules = $rules;
        return $this;
    }
    /**
     * Режим сортировки, SORT_IN_ORDER, SORT_RANDOM, по умолчанию SORT_IN_ORDER
     * @param string $sort
     * @return \Models\CatalogManagement\Search\CatalogSearch
     */
    public function setSortMode($sort){
        if (in_array($sort, $this->allow_sort_mode)){
            $this->sort_mode = $sort;
        }
        return $this;
    }
    /**
     * Вкл/выкл подсчета найденных объектов по типу, выкл по умолчанию
     * @param boolean $enable
     * @return \Models\CatalogManagement\Search\CatalogSearch
     */
    public function setEnableCountByTypes($enable){
        $this->count_by_types = !empty($enable);
        return $this;
    }

    /**
     * Вставка SQL-инъекций в запрос
     * @param $type - тип (set, select, where, from)
     * @param $sql_token
     * @return $this
     */
    public function setSqlInjection($type, $sql_token){
        if (in_array($type, array('set', 'select', 'where', 'from'))){
            if (!empty($sql_token)){
                $this->sql_injections[$type] = $sql_token;
            } else {
                unset($this->sql_injections[$type]);
            }
        }
        return $this;
    }
    /**
     * Вкл/выкл поиска скрытых объектов (паблик/админка)
     * @param bool $public_only
     * @return CatalogSearch
     */
    public function setPublicOnly($public_only){
        $this->public_only = !empty($public_only);
        return $this;
    }

    /**
     * Вкл/выкл подсчета общего количества сущностей, попадающих под фильтр
     * @param bool $set
     * @return $this
     */
    public function enableTotalCount($set){
        $this->get_count = !empty($set);
        return $this;
    }
    /**
     * Поиск айтемов, возвращает экземпляр класса CatalogSearchItemsResult, содержащий объекты айтемов и дополнительную информацию о поиске
     * @param int $start
     * @param int $page_size
     * @return CatalogSearchItemsResult
     */
    public function searchItems($start = 0, $page_size = 100000,$is_ard=false){
        $result = $this->getSearches(self::S_ITEM, $start, $page_size, FALSE,$is_ard);
        return CatalogSearchItemsResult::factory($result, $this->segment_id);
    }
    /**
     * Поиск вариантов, возвращает экземпляр класса CatalogSearchVariantsResult, содержащий объекты вариантов и дополнительную информацию о поиске
     * @param int $start
     * @param int $page_size
     * @return CatalogSearchVariantsResult
     */
    public function searchVariants($start = 0, $page_size = 100000){
        $result = $this->getSearches(self::S_VARIANT, $start, $page_size, FALSE);
        return CatalogSearchVariantsResult::factory($result, $this->segment_id);
    }
    /**
     * Поиск айтемов без создания объектов, возвращает экземпляр класса CatalogSearchResult, содержащий массив айдишников айтемов и дополнительную информацию о поиске
     * @param int $start
     * @param int $page_size
     * @return CatalogSearchResult
     */
    public function searchItemIds($start = 0, $page_size = 100000){
        $result = $this->getSearches(self::S_ITEM, $start, $page_size, FALSE);
        $result['mode'] = 'items';
        return CatalogSearchResult::factory($result, $this->segment_id);
    }
    /**
     * Поиск вариантов без создания объектов, возвращает экземпляр класса CatalogSearchResult, содержащий массив айдишников вариантов и дополнительную информацию о поиске
     * @param int $start
     * @param int $page_size
     * @return CatalogSearchResult
     */
    public function searchVariantIds($start = 0, $page_size = 100000){
        $result = $this->getSearches(self::S_VARIANT, $start, $page_size, FALSE);
        $result['mode'] = 'variants';
        return CatalogSearchResult::factory($result, $this->segment_id);
    }
    /**
     * Подсчет количества айтемов, соответствующих
     * @return CatalogSearchResult
     */
    public function getItemsCountByTypes(){
        $result = $this->getSearches(self::S_ITEM, 0, 100000, TRUE);
        $result['mode'] = 'items';
        return CatalogSearchResult::factory($result, $this->segment_id);
    }
    /**
     * 
     * @return CatalogSearchResult
     */
    public function getVariantsCountByTypes(){
        $result = $this->getSearches(self::S_VARIANT, 0, 100000, TRUE);
        $result['mode'] = 'variants';
        return CatalogSearchResult::factory($result, $this->segment_id);
    }
    /**
     * Обертка для megaSearch(), аттрибуты, принимаемые методом, зависят от вызывающих публичных методов, все остальные аттрибуты megaSearch берутся из полей объекта CatalogSearch
     * @param int $search_type тип разыскиваемых объектов (Item, Variant)
     * @param int $start
     * @param int $page_size
     * @param boolean $count_by_types_only
     * @return array keys (searches - список id найденных объектов, total_count - общее количество объектов, подходящих под условия поиска, count_by_types - общее количество объектов подходящих под условия поиска в каждом типе)
     */
    private function getSearches($search_type, $start, $page_size, $count_by_types_only = FALSE,$is_ard=false){
        $count_by_types = $this->count_by_types || $count_by_types_only;
        $result['searches'] = $this->megaSearch($this->type_id, $search_type, $count, $start, $page_size, $this->sort_mode, $count_by_types, $this->not_items_ids, $count_by_types_only,$is_ard);
        $result['total_count'] = $count;
        $result['count_by_types'] = $count_by_types;
        return $result;
    }
    public function getItemSqlQuery($start = 0, $page_size = 10000000){
        $countByTypes = FALSE;
        return $this->getSql($this->type_id, self::S_ITEM, $start, $page_size, $this->sort_mode, $countByTypes, $this->not_items_ids, FALSE);
    }
    public function getVariantSqlQuery($start = 0, $page_size = 10000000){
        $countByTypes = FALSE;
        return $this->getSql($this->type_id, self::S_VARIANT, $start, $page_size, $this->sort_mode, $countByTypes, $this->not_items_ids, FALSE);
    }
    /**
     * Достаем следующей и предыдущий item
     * @param \Models\CatalogManagement\CatalogPosition|CatalogPosition $entity
     * @throws \Exception
     * @return array
     */
    public function getAround(CatalogPosition $entity){
		$sql_set = 'SET @prev:= NULL, @current:= NULL, @next:= NULL, @find_marker:= NULL; ';
		$this->db->query($sql_set);
		$sql_select['pre_query'] = 'SELECT IF(`tbl`.`obj_id` = "'.intval($entity['id']).'", @find_marker:= 1, NULL)'
			. ', IF (@find_marker IS NULL, @prev:=`tbl`.`obj_id`, @prev) AS `prev`'
			. ', IF (@find_marker IS NULL OR @find_marker = 1, @current:=`tbl`.`obj_id`, @current) AS `current`'
			. ', IF (@find_marker = 2 AND @next IS NULL, @next:=`tbl`.`obj_id`, @next) AS `next`'
			. ', IF (@find_marker = 1, @find_marker:= 2, NULL) FROM (';
		$sql_select['post_query'] = ') AS `tbl`';
		$count = NULL;
        $count_by_types = false;
		list($selectQuery, $sql_vars) = $this->getSql($entity['type_id'], self::S_ITEM, 0, 9999999, self::SORT_IN_ORDER, $count_by_types, null, false);
		$query = $sql_select['pre_query'] . $selectQuery . $sql_select['post_query'];
		array_unshift($sql_vars, $query);
		$db = \App\Builder::getInstance()->getDB();
		call_user_func_array(array($db, 'query'), $sql_vars);
		$final_result = $db->query('SELECT @prev as `prev`, @next as `next`')->getRow();
		$items = Item::factory(array($final_result['prev'], $final_result['next']));
		return array('prev' => !empty($final_result['prev']) && !empty($items[$final_result['prev']]) ? $items[$final_result['prev']] : NULL,
			 'next' => !empty($final_result['next']) && !empty($items[$final_result['next']]) ? $items[$final_result['next']] : NULL
        );
    }

    /**
     * 
     * @param int $type_id тип позиций, поиск по всем дочерним типам
     * @param int $search_type тип поиска, смотри константы self::S_*
     * @param int $count общее количество записей, удовлетворяющих запросу
     * @param int $start с какой позиции показывать
     * @param int $page_size сколько показывать
     * @param string $sort Значение "random" будет сортировать рандомно. Значение "order" - в порядке, указанном в правилах
     * @param array $countByTypes массив, который хранит количество подходящих товаров по разным типам type_id=>count
     * @param array $not_items_ids не учитывать айтемы с этими id
     * @param bool $countByTypesOnly если нужны только типы с количеством товаров
     * @return mixed
     * @throws \Exception
     */
    private function megaSearch($type_id, $search_type = self::S_ITEM,
           &$count = 0, $start = 0, $page_size = 100000, $sort = self::SORT_IN_ORDER,
           &$countByTypes = null, $not_items_ids = null, $countByTypesOnly = false, $is_ard=false)
    {
        $sql_result = $this->getSql($type_id, $search_type, $start, $page_size, $sort, $countByTypes, $not_items_ids, $countByTypesOnly,$is_ard);
        if (empty($sql_result)){
            return array();
        }
        $db = \App\Builder::getInstance()->getDB();
        $db->query('SET SESSION group_concat_max_len = 1000000');//чтобы group_concat не обрезался
        $result = $db->nakedQuery($sql_result);
        if (empty($result)){
            return array();
        }
        if ($search_type == self::S_ITEM){
            $result = $result->select('obj_id');
        }else{
            $result = $result->getCol('obj_id', 'id');
        }
        /*
        echo '<pre>';
        debug_print_backtrace();
        echo '</pre>';
        exit();
        */
        $count = $this->get_count ? $db->query('SELECT FOUND_ROWS()')->getCell() : NULL;
        return $result;
    }

    /**
     * Преобразует массив значений в строку экранированных значений через запятую
     * @param array $list
     * @return string
     */
    private function escapeArray(Array $list, $int = FALSE){
        return implode(', ', array_map(array(\App\Builder::getInstance()->getDB(), $int ? 'escape_int' : 'escape_value'), $list));
    }

    /**
     * забираем sql
     * @param int $type_id тип позиций, поиск по всем дочерним типам
     * @param int $search_type тип поиска, смотри константы self::S_*
     * @param int $start с какой позиции показывать
     * @param int $page_size сколько показывать
     * @param string $sort Значение "random" будет сортировать рандомно. Значение "order" - в порядке, указанном в правилах
     * @param Array|bool $countByTypes массив, который хранит количество подходящих товаров по разным типам type_id=>count
     * @param array $not_items_ids не учитывать айтемы с этими id
     * @param bool $countByTypesOnly если нужны только типы с количеством товаров
     * @return string
     * @throws \Exception
     */
    private function getSql($type_id, $search_type = self::S_ITEM,
            $start = 0, $page_size = 100000, $sort = self::SORT_IN_ORDER,
            &$countByTypes = false, $not_items_ids = null, $countByTypesOnly = false, $is_ard=null) {
	    if ( empty( $sort ) ) {
		    $sort = self::SORT_IN_ORDER;
	    }
	    if ( $sort != self::SORT_RANDOM && $sort != self::SORT_IN_ORDER ) {
		    throw new \LogicException( 'Sort must be random or in rules order' );
	    }
	    $db = \App\Builder::getInstance()->getDB();
	    if ( is_null( $this->segment_id ) ) {
		    $segment          = \App\Segment::getInstance()->getDefault();
		    $this->segment_id = ! empty( $segment ) ? $segment['id'] : null;
	    }
	    if ( $this->rules instanceof RuleAggregator ) {
		    $megaRule = $this->rules;
	    } else {
		    $megaRule = RuleAggregator::make( RuleAggregator::LOGIC_AND, RuleAggregator::makeRules( $this->rules ) );
	    }
	    if ( $search_type !== self::S_ITEM && $search_type !== self::S_VARIANT ) {
		    throw new \Exception( 'incorrect parametr $search_type' );
	    }
	    //Поиск проходит во всех дочерних конечных типах переданных типов
	    $type_ids = array();//в каких типах будем искать
	    if ( ! is_array( $type_id ) ) {
		    $type_id = array( $type_id );
	    }
	    $types             = Type::factory( $type_id, $this->segment_id );
	    $allow_type_status = $this->public_only ? array( Type::STATUS_VISIBLE ) : array(
		    Type::STATUS_VISIBLE,
		    Type::STATUS_HIDDEN
	    );
	    foreach ( $types as $type ) {
		    if ( in_array( $type['status'], $allow_type_status ) ) {
			    if ( ! $type['allow_children'] ) {
				    $type_ids[] = $type['id'];
			    } else {
				    $children = $type->getAllChildren( $allow_type_status );
				    if ( ! empty( $children ) ) {
					    foreach ( $children as $child ) {
						    if ( empty( $child['allow_children'] ) ) {
							    $type_ids = array_merge( $type_ids, ! empty( $child ) ? array_keys( $child ) : array() );
						    }
					    }
				    }
			    }
		    }
	    }
	    if ( empty( $type_ids ) ) {
		    return null;//если в переданном типе нет дочерних, то искать нечего.
	    }
	    //свойств может быть несколько с одним ключем, поэтому берем двумерный массив key => id => property
	    $props        = PropertyFactory::search( $type_ids, PropertyFactory::P_ALL, 'idByKey', 'position', 'parents', array(), $this->segment_id );//свойства ищем для всех дочерних типов(внутри функции будут искаться свойства их родителей)
	    $joinVariants = false; // Нужно ли подключать дополнительно инфраструктуру хранения  объектов ("вариантов") или можно обойтись без этого
	    //Дополнительно механизм фильтрации по объектам производится только в случае если есть соответсвующие параметры
	    $search_prop_keys = $megaRule->getRulePropKeys();
	    $use_type_in_rule = false;//если определение типа используется в правилах, то потом не надо опять это делать
	    foreach ( $search_prop_keys as $param_key => $endingLogic ) {
		    if ( ! empty( $props[ $param_key ] ) ) {
			    foreach ( $props[ $param_key ] as $p ) {
				    if ( ! empty( $p['multiple'] ) ) {
					    $joinVariants = true;
					    break;
				    }
			    }
		    } else {
			    if ( strpos( $param_key, 'variant' ) !== false ) {
				    $joinVariants = true;
				    break;
			    }
		    }
		    if ( $param_key === 'type_id' ) {
			    $use_type_in_rule = true;
		    }
	    }
	    if ( $search_type == self::S_VARIANT ) { // Если ведется поиск по вариациям, то нам нужно как минимум получить их Id
		    $joinVariants = true;
	    }
	    $selectQuery = 'SELECT ' . ( $this->get_count ? 'SQL_CALC_FOUND_ROWS ' : '' );
	    $sql         = array();// основной запрос
	    if ( $search_type == self::S_VARIANT ) {//разные данные возвращаются
		    $sql['select'] = '`variant`.`id` AS `obj_id`, `item`.`id`';
	    } else {
		    $sql['select'] = '`item`.`id` AS `obj_id`' . ( $joinVariants ? ' , GROUP_CONCAT(`variant`.`id` SEPARATOR ",") AS `find_variants`' : '' ) . ', `item`.`parent_id` AS `obj_parent_id`, `item`.`parents` AS `obj_parents`';
	    }
	    if ( ! empty( $this->sql_injections['select'] ) ) {
		    $sql['select'] .= "\n/*sql injection [select]*/\n\t" . $this->sql_injections['select'];
	    }
	    $sql['from']  = "\n FROM `" . Item::TABLE_ITEMS . '` AS `item`'
	                    . ( $joinVariants
			    ? ( "\n\tINNER JOIN `" . Variant::TABLE_VARIANTS . '` AS `variant` '
			        . 'ON (`variant`.`item_id` = `item`.`id`' .
			        ( ! isset( $search_prop_keys['variant.status'] )
				        ? ( ' AND `variant`.`status` ' . ( $this->public_only ? ' = ' . Variant::S_PUBLIC : ' NOT IN (' . Variant::S_DELETE . ', ' . Variant::S_TMP . ')' ) )
				        : '' )
			        . ')' )
			    : '' );
	    $sql['order'] = array(); // Сортировка обращаю внимание что это блок следует после выравнивания параметров, тоесть все параметры уже прошли выравнивание формата данных
	    //вытаскиваем товары только из запрашиваемых типов
	    if ( ! $use_type_in_rule ) {
		    if ( count( $type_ids ) === 1 ) {
			    $sql['where'][] = '`item`.`type_id` = ' . intval( reset( $type_ids ) );
		    } else {
			    $sql['where'][] = '`item`.`type_id` IN (' . $this->escapeArray( $type_ids, true ) . ')';
		    }
	    }
	    //не обходим стороной статусы
	    $sql['where'][] = '`item`.`status` ' . ( $this->public_only ? ' = ' . Item::S_PUBLIC : ' NOT IN (' . Item::S_DELETE . ', ' . Item::S_TMP . ')' );
	    if (!empty($_SERVER['REQUEST_URI'])&& strpos( '|' . $_SERVER['REQUEST_URI'], 'catalog' ) == false ) {
		    if ( strpos( $_SERVER['REQUEST_URI'], 'arenda' ) or $is_ard == true ) {
			    $sql['where'][] = "`item`.`is_arenda` ='1' OR `item`.`type_id` =33";
		    } else {
			    $sql['where'][] = "`item`.`is_arenda` ='0'";
		    }
        }
        if (!empty($not_items_ids)){
            $sql['where'][] = '`item`.`id` NOT IN (' . $this->escapeArray($not_items_ids, TRUE) . ')';
        }
        $megaRule->compileSql($props, $sql, $this->segment_id, $search_type);
        if (!empty($this->sql_injections['from'])){
            $sql['from'] .= "\n/*sql injection [from]*/\n\t".$this->sql_injections['from'];
        }
        $selectQuery .= $sql['select'] . ' ' . $sql['from'];
        if (!empty($this->sql_injections['where'])){
            $sql['where'][]= "\n/*sql injection [where]*/\n".$this->sql_injections['where'];
        }
        $selectQueryWhere = '';
        if (!empty($sql['where'])) {
            $selectQueryWhere = " \n WHERE " . implode(" AND \n\t", $sql['where']);
        }
        $selectQuery .= $selectQueryWhere;
        if ($countByTypes !== FALSE){//если требуется посчитать количество
            $selectQueryCount = 'SELECT `item`.`type_id`, COUNT(
                '. ($joinVariants ? 'DISTINCT(`item`.`id`)' : '*') .'
            ) AS `count` ' . $sql['from'] . $selectQueryWhere . " \n GROUP BY `item`.`type_id`";
            $countByTypes = $db->nakedQuery($selectQueryCount)->getCol('type_id', 'count');
            $types = Type::factory(array_keys($countByTypes));
            foreach ($types as $t){
                if (!isset($countByTypes[$t['parent_id']])){
                    $countByTypes[$t['parent_id']] = 0;
                }
                $countByTypes[$t['parent_id']] += $countByTypes[$t['id']];
            }
        }
        //далее только для результата. Для количества не нужно.
        if ($countByTypesOnly){
            return NULL;
        }
        if ($search_type == self::S_ITEM){// то итог надо склеить только если было разделение на "вариации" и если ищутся "item", а не "variant"
            $selectQuery.=" \n GROUP BY `item`.`id`";
        }
		if (!empty($this->sql_injections['having'])){
			$selectQuery.= " \n HAVING " . $this->sql_injections['having'];
		}
        if (!empty($sql['order']) || (!empty($sort) && $sort == 'random')){
            $selectQuery.= " \n ORDER BY " . (!empty($sql['order']) ? implode(", ", $sql['order']) : (!empty($sort) && $sort == 'random' ? "RAND()" : '')) . ', `item`.`id`';
        }
        $selectQuery .= PHP_EOL . ' LIMIT ' . $db->escape_int($start) . ', ' . $db->escape_int($page_size);
		if(!empty($_SERVER['REQUEST_URI'])&&strpos($_SERVER['REQUEST_URI'],'arenda') or $is_ard==true){
			$selectQuery=str_replace('`item`.`type_id` = ','`item`.`type_id` != 333333333333333',$selectQuery);
		}
		//echo '<pre>'.$selectQuery.'</pre>';
		return $selectQuery;
		//exit;
    }
}
