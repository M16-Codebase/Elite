<?php
//
//    Rule::make('special', array('max'=>100, 'min'=>1000));
//    Rule::make('cost')->setMin(100)->setMax(1000);
//    Rule::make('new')->setExist();
//

/**
 * Description of Rule
 *
 * @author olga
 */
namespace Models\CatalogManagement\Rules;
use Models\CatalogManagement\Properties\Property;
class Rule implements \ArrayAccess, iRule{
    const SEARCH_LIKE = 'like';
    const SEARCH_LIKE_LEFT = 'like_left';
    const SEARCH_LIKE_RIGHT = 'like_right';
    const SEARCH_REGEXP = 'regexp';
    const SEARCH_EQUAL = 'equal';
    const PROP_USED = 'used';//маркер о том, что правило использовано
    /**
     * ключ поля к которому относится правило
     * @var string
     */
    private $key = NULL;
    /**
     * Минимально допустимое значение
     * @var mixed (int|float)
     */
    private $min = NULL;
    /**
     * Максимально допустимое значение
     * @var mixed (int|float)
     */
    private $max = NULL;
    /**
     * Значение или список значений, при задании должен быть задан $search_type
     * @var mixed
     */
    private $value = NULL;
    /**
     * Значение или список значений, с которыми не должно быть совпадения
     * @var mixed 
     */
    private $not = NULL;
    /**
     * Если задан, то требуются непустные значения
     * @var bool
     */
    private $notempty = NULL;
    /**
     * Порядок сортировки: прямой от меньшего к большему (TRUE) или обратный (FALSE)
     * @var bool
     */
    private $order = NULL;
    /**
     * Тип проверки на соотвествия поля $value
     * @var string one of $allow_search_types 
     */
    private $search_type = NULL;
    private $search_by_enum_value = NULL;
    private $search_by_enum_key = NULL;
    /**
     * Список полей, доступных через интерфейс массива
     * @var array
     */
    private static $fields = array('min', 'max', 'value', 'not', 'notempty', 'order', 'search_type', 'search_by_enum_value');
    private static $allow_search_types = array(self::SEARCH_LIKE, self::SEARCH_REGEXP, self::SEARCH_EQUAL, self::SEARCH_LIKE_LEFT, self::SEARCH_LIKE_RIGHT);
    
    private static $propsStack = array();
    
    /**
     * Создаем правила из упрощенного формата фильтра
     * @param array $rules_data array('key2'=>10, 'key3'=>array('value'=>array(1,2,3)), 'key4'=>array('max'=>10, 'min'=>5]), 'key3'=>array('notempty'=>1))
     * @return self[]
     */
    public static function makeRules($rules_data){
        $result = array();
        foreach ($rules_data as $key => $rule_data){
            $rule = (is_object($rule_data) && $rule_data instanceof Rule) ? $rule_data: self::make($key, $rule_data);
            $result[$rule['key']] = $rule;
        }
        return $result;
    }
    
    /**
     * CФормировать объект правила из упрощенного формата фильтра
     * @param mixed(string|Rule) $key
     * @param \Models\CatalogManagement\Rule $data
     * @return self
     */
    public static function make($key, $data = array()){
        $rule = new Rule($key, $data);
        return $rule;
    }
    
    /**
     * @param string $key
     * @param array $data
     */
    private function __construct($key, $data = array()){
        $this->key = $key;
        if (!empty($data)){
            $data = self::normalizeParams($data, $key);
            foreach (self::$fields as $f){
                if (isset($data[$f])){
                    $this->$f = $data[$f];
                }
            }
        }
    } 
    private static function normalizeParams($data, &$key = NULL){
        if (is_int($key) && !is_null($key)){
            $key = $data;
            //$key = $data.'';
            $data = array('notempty' => TRUE);
        }elseif (!is_array($data)) {
            $data = array('value' => $data);
        }
        return $data;
    }
    public static function setPropsStack($props){
        self::$propsStack = $props;
    }
    /**
     * установка множества значений за один вызов
     * @param array $params
     * @return self
     */
    public function setParams($params){
        $params = self::normalizeParams($params);
        foreach (self::$fields as $f){
            if (isset($params[$f])){
                $this->$f = $params[$f];
                unset($params[$f]);
            }
        }
        if (!empty($params)){
            throw new \LogicException('Unwanted fields: ' . implode(',', array_keys($params)));
        }
        return $this;
    }
    /**
     * Установить ограничение на минимальное значение
     * @param int|float $value
     * @return self
     * @throws \LogicException
     */
    public function setMin($value){
        if (!is_null($value) && !is_numeric($value) && !checkdate(date('m', strtotime($value)), date('d', strtotime($value)), date('Y', strtotime($value)))){
            throw new \LogicException('Min value must be numeric or mysql format datetime');
        }
        $this->min = $value;
        return $this;
    }
    
    /**
     * Установить ограничение на максимальное значение
     * @param int|float $value
     * @return self
     * @throws \LogicException
     */
    public function setMax($value){
        if (!is_null($value) && !is_numeric($value) && !checkdate(date('m', strtotime($value)), date('d', strtotime($value)), date('Y', strtotime($value)))){
            throw new \LogicException('Max value must be numeric or mysql format datetime');
        }
        $this->max = $value;
        return $this;
    }
    
    /**
     * Установить значения
     * @param mixed $value значение, маска или массив значений
     * @param string $searchType self::SEARCH_*
     * @return self
     * @throws \LogicException
     */
    public function setValue($value, $searchType = null){
        if (is_null($searchType)){
            $searchType = self::SEARCH_EQUAL;
        }
        if (is_array($value) && $searchType != self::SEARCH_EQUAL){
            throw new \LogicException('incorrect $searchType for values set');
        } elseif (empty($value) && is_array($value)){
            throw new \LogicException('Search values array cannot be empty');
        }
        $this->setSearchType($searchType);
        $this->value = $value;
        return $this;
    }
    
    /**
     * Установка правил на несоответсвие одному или нескольким значениям
     * @param mixed $value значение или массив значений
     * @return self
     */
    public function setNot($value){
        if (empty($value) && is_array($value)){
            throw new \LogicException('Search values array cannot be empty');
        }
        $this->not = $value;
        return $this;
    }
    
    /**
     * Установка правила на непустоту значения (должно быть задано)
     * @return self
     */
    public function setExists(){
        $this->notempty = TRUE;
        return $this;
    }
    
    /**
     * Установить правило сортировки
     * @param bool|array $desc Порядок сортировки: прямой от меньшего к большему (TRUE) или обратный (FALSE)
     *                          или массив значений, в соответствии с которым сортировать (например последовательность id)
     * @return self
     */
    public function setOrder($desc){
        $this->order = $desc;
        return $this;
    }
    
    /**
     * установка типа поиска
     * @param string $type self::SEARCH_*
     * @return self
     * @throws \LogicException
     */
    public function setSearchType($type){
        if (!in_array($type, self::$allow_search_types)){
            throw new \LogicException('Search type "' . $type . '" not allow');
        }
        $this->search_type = $type;
        return $this;
    }
    
    public function setSearchByEnumValue(){
        $this->search_by_enum_value = TRUE;
        $this->search_by_enum_key = FALSE;
        return $this;
    }

    public function setSearchByEnumKey(){
        $this->search_by_enum_value = FALSE;
        $this->search_by_enum_key = TRUE;
        return $this;
    }

    /**
     * Собрать sql. Вызывается только из других методов интерфейса iRule.
     * @param array $props
     * @param array $sql
     * @param int $segment_id
     * @param int $search_type
     * @return array
     * @throws \Exception
     */
    public function _getSql($props, &$sql, $segment_id = NULL, $search_type = NULL){
        if (!empty($props)) {//Если поле в массиве дополнительных полей
            $property = reset($props);//берем первое свойство для проверки основных полей
            $fieldTableName = $property->getTable();
            //enum или не enum, таблица
            $check = ($property['data_type'] == \Models\CatalogManagement\Properties\Enum::TYPE_NAME ? '1' : '0'). ':' . $fieldTableName;
            $search_values = array();
            foreach ($props as $p){
                if ($check != ($property['data_type'] == \Models\CatalogManagement\Properties\Enum::TYPE_NAME ? '1' : '0'). ':' . $p->getTable()
                    or (
                        $property instanceof \Models\CatalogManagement\Properties\Diapason
                        xor $p instanceof \Models\CatalogManagement\Properties\Diapason
                    )
                ){
                    // Если найденные свойства не из одной таблицы, или диапазоны соседствуют с пропертями других типов
                    // то считаем, что програмист или администратор жестко ошибся
                    throw new \Exception('Incorrect $prop_key for search, it have multi tables nature');
                }
                if ($this->search_by_enum_value){//если ищем по значению enum, надо найти id этого значения в разных типах
                    if (!empty($p['values'])){
                        foreach($p['values'] as $e_id => $pv){
                            if (in_array($pv['value'], is_array($this->value) ? $this->value : array($this->value))){
                                $search_values[] = $e_id;
                            }
                        }
                    }
                } elseif ($this->search_by_enum_key){
                    if (!empty($p['values'])) {
                        foreach($p['values'] as $e_id => $pv){
                            if (in_array($pv['key'], is_array($this->value) ? $this->value : array($this->value))){
                                $search_values[] = $e_id;
                            }
                        }
                    }
                }
            }
            if (!empty($search_values)){
                $this->value = $search_values;
            }
            if ($property instanceof \Models\CatalogManagement\Properties\Diapason) {
                $this->getDiapasonSql($props, $sql, $segment_id, $search_type);
            } else {
                $tableAliasName = 't_' . $this->key;
                $valueToken = '`' . $tableAliasName . '`.`value`'; // так зовется конкретное поле
                if (empty(self::$propsStack[$this->key])){
                    throw new \LogicException('In $propsStack key ' . $this->key . ' not found');
                }elseif(self::$propsStack[$this->key] != self::PROP_USED){//если такое свойство ещё не использовалось, то добавляем join на таблицу
                    //формируем условия не зависящие от правила в операторе ON за счет этого имеем максисмум читаемости запроса
                    $on_token = ($property['multiple'] ? '`' . $tableAliasName . '`.`variant_id` = `variant`.`id`' : '`' . $tableAliasName . '`.`item_id` = `item`.`id`' )
                            . ' AND `' . $tableAliasName . '`.`property_id` ' . (count($props) == 1 ? ('= ' . intval($property['id'])) : ('IN (' . implode(',', array_keys($props)) . ') '))
                            . ' AND (`' . $tableAliasName . '`.`segment_id` IS NULL' . ($property['segment'] == 1 && !is_null($segment_id) ? ' OR `' . ($tableAliasName . '`.`segment_id` = ' . $segment_id) : '') . ')';
                    //Проверить, есть ли правила кроме сорт, если нет, то LEFT JOIN
                    $only_sort = empty($this->value) && empty($this->min) && empty($this->max) && empty($this->not) && empty($this->notempty);
                    if ($only_sort){
                        $orderToken = '`' . $this->key . '_order' . '`';
                        $sql['select'] = ', IF('.$valueToken.' IS NULL, "0", "1") AS ' . $orderToken;
                    }
                    //если в принципе есть сортировка по расщепляемому свойству
                    //а ищется товар(@TODO не понятно, как тут знать о том, что мы ищем //Внимательнее надо быть: $search_type),
                    //то надо знать минимум или максимум
                    //в зависимости от возрастания или убывания порядка
                    if (isset($this->order) && $property['multiple'] && $search_type == \Models\CatalogManagement\Catalog::S_ITEM){
                        $extremumOrderToken = '`__extremum_' . $this->key . '`';
                        $sql['select'] = (!empty($sql['select']) ? $sql['select'] : '') . ', ' . ($this->order ? 'MAX' : 'MIN') . '(`' . $tableAliasName . '`.`value`) AS ' . $extremumOrderToken;
                    }
                    $sql['from'] = (!empty($sql['from']) ? $sql['from'] : '') . "\n\t" .
                            ($only_sort || self::$propsStack[$this->key] == RuleAggregator::LOGIC_OR ? 'LEFT' : 'INNER') .
                            ' JOIN `'.$fieldTableName.'` AS `'.$tableAliasName.'` ON ('.$on_token.')';
                    self::$propsStack[$this->key] = self::PROP_USED;//ставим метку о том, что уже проджойнили таблицу этого свойства
                }
                //для OR надо условия из правил писать в WHERE части, а не в ON
                $where_rule = $this->compile($valueToken);
                if ($where_rule){
                    $sql['where'][] = $where_rule;
                }
            }
        } else { //Для полей, которые в основной таблице
            if ($this->key{0}==='`' or strpos($this->key, '(')){ //если в названии есть апостроф, или открывающая скобка (используется функция), то без экранирования
                $valueToken = $this->key;
            }
            $dotPos = strpos($this->key, '.');
            if ($dotPos){
                $target = substr($this->key, 0, $dotPos);
                $param_key = substr($this->key, ++$dotPos);
                if ($target != 'variant' AND $target != 'item'){
                    trigger_error ('incorrect key');
                }
                $valueToken = '`' . $target . '`.`' . $param_key . '`';
            }else{
                $param_key = $this->key;
                $valueToken = '`item`.`' . $this->key . '`';
            }
            if ($param_key == 'timestamp'){
                $valueToken = 'UNIX_TIMESTAMP(`item`.`time`)';
            }
            $where_rule = $this->compile($valueToken);
            if ($where_rule)
                $sql['where'][] = $where_rule;
        }
        // Сортировка, порядок не будет испорчен, т.к. он задан ранее.
        // Для диапазонов сортировка задается в getDiapasonSql
        if (isset($this->order) && (empty($property) || !($property instanceof \Models\CatalogManagement\Properties\Diapason))){
            $sql['order'][$this->key] = '';
            if (!empty($orderToken)){
                $sql['order'][$this->key] .= $orderToken . ' DESC';
            }
            if (!empty($sql['order'][$this->key])){
                $sql['order'][$this->key] .= ', ';
            }
			if (!empty($extremumOrderToken)){//если расщепляемое свойство, то надо сортировать по экстремальным значениям
				$sql['order'][$this->key] .= $extremumOrderToken . ($this->order ? ' DESC ':'');
			}else{
                // если order - массив, собираем правило FIELD(`field`, <values_list>) - сортировка в порядке значений из массива
                $sql['order'][$this->key] .= (is_array($this->order)
                    ? 'FIELD(' . $valueToken . ', ' . implode(', ', array_map(array(\App\Builder::getInstance()->getDB(), 'escape_value'), $this->order)) . ')'
                    : $valueToken . ($this->order ? ' DESC ':''));
			}
        }
        return $sql;
    }

    protected function getDiapasonSql($props, &$sql, $segment_id = NULL, $search_type = NULL) {
        /** @var \Models\CatalogManagement\Properties\Diapason $property */
        $property = reset($props);
        $add_props = $property->getAddProperties();
        $minFieldTableName = $add_props['min']->getTable();
        $maxFieldTableName = $add_props['max']->getTable();
        $tableMinAliasName = 't_' . $this->key . '_min';
        $tableMaxAliasName = 't_' . $this->key . '_max';
        $valueMinToken = '`' . $tableMinAliasName . '`.`value`'; // так зовется конкретное поле
        $valueMaxToken = '`' . $tableMaxAliasName . '`.`value`'; // так зовется конкретное поле
        if (empty(self::$propsStack[$this->key])){
            throw new \LogicException('In $propsStack key ' . $this->key . ' not found');
        }elseif(self::$propsStack[$this->key] != self::PROP_USED){//если такое свойство ещё не использовалось, то добавляем join на таблицу
            //формируем условия не зависящие от правила в операторе ON за счет этого имеем максисмум читаемости запроса
            $min_on_token = ($property['multiple'] ? '`' . $tableMinAliasName . '`.`variant_id` = `variant`.`id`' : '`' . $tableMinAliasName . '`.`item_id` = `item`.`id`' )
                . ' AND `' . $tableMinAliasName . '`.`property_id` ' . (count($props) == 1 ? ('= ' . intval($add_props['min']['id'])) : ('IN (' . implode(',', array_keys($props)) . ') '))
                . ' AND (`' . $tableMinAliasName . '`.`segment_id` IS NULL' . ($property['segment'] == 1 && !is_null($segment_id) ? ' OR `' . ($tableMinAliasName . '`.`segment_id` = ' . $segment_id) : '') . ')';
            $max_on_token = ($property['multiple'] ? '`' . $tableMaxAliasName . '`.`variant_id` = `variant`.`id`' : '`' . $tableMaxAliasName . '`.`item_id` = `item`.`id`' )
                . ' AND `' . $tableMaxAliasName . '`.`property_id` ' . (count($props) == 1 ? ('= ' . intval($add_props['max']['id'])) : ('IN (' . implode(',', array_keys($props)) . ') '))
                . ' AND (`' . $tableMaxAliasName . '`.`segment_id` IS NULL' . ($property['segment'] == 1 && !is_null($segment_id) ? ' OR `' . ($tableMaxAliasName . '`.`segment_id` = ' . $segment_id) : '') . ')';
            //Проверить, есть ли правила кроме сорт, если нет, то LEFT JOIN
            $only_sort = empty($this->value) && empty($this->min) && empty($this->max) && empty($this->not) && empty($this->notempty);
            if ($only_sort){
                $orderTokenMin = '`' . $this->key . '_min_order' . '`';
                $orderTokenMax = '`' . $this->key . '_max_order' . '`';
                $sql['select'] = ', IF(`'.$tableMinAliasName.'`.`value` IS NULL, "0", "1") AS ' . $orderTokenMin
                    . ', IF(`'.$tableMaxAliasName.'`.`value` IS NULL, "0", "1") AS ' . $orderTokenMax;
            }
            //если в принципе есть сортировка по расщепляемому свойству
            //а ищется товар(@TODO не понятно, как тут знать о том, что мы ищем //Внимательнее надо быть: $search_type),
            //то надо знать минимум или максимум
            //в зависимости от возрастания или убывания порядка
            if (isset($this->order) && $property['multiple'] && $search_type == \Models\CatalogManagement\Catalog::S_ITEM){
                $extremumOrderTokenMin = '`__extremum_' . $this->key . '_min`';
                $extremumOrderTokenMax = '`__extremum_' . $this->key . '_max`';
                $sql['select'] = (!empty($sql['select']) ? $sql['select'] : '') . ', ' . ($this->order ? 'MAX' : 'MIN') . '(`' . $tableMinAliasName . '`.`value`) AS ' . $extremumOrderTokenMin;
                $sql['select'] = (!empty($sql['select']) ? $sql['select'] : '') . ', ' . ($this->order ? 'MAX' : 'MIN') . '(`' . $tableMaxAliasName . '`.`value`) AS ' . $extremumOrderTokenMax;
            }
            $sql['from'] = (!empty($sql['from']) ? $sql['from'] : '') . "\n\t" .
                'LEFT JOIN `'.$minFieldTableName.'` AS `'.$tableMinAliasName.'` ON ('.$min_on_token.')' .
                'LEFT JOIN `'.$maxFieldTableName.'` AS `'.$tableMaxAliasName.'` ON ('.$max_on_token.')';
            self::$propsStack[$this->key] = self::PROP_USED;//ставим метку о том, что уже проджойнили таблицу этого свойства
        }
        //для OR надо условия из правил писать в WHERE части, а не в ON
        $where_rule = $this->compileDiapason($valueMinToken, $valueMaxToken);
        if ($where_rule){
            $sql['where'][] = $where_rule;
        }
        if (isset($this->order)){ //Сортировка, порядок не будет испорчен, т.к. он задан ранее.
            $sql['order'][$valueMinToken] = '';
            if (!empty($orderTokenMin)){
                $sql['order'][$valueMinToken] .= $orderTokenMin . ' DESC';
            }
            if (!empty($sql['order'][$valueMinToken])){
                $sql['order'][$valueMinToken] .= ', ';
            }
            if (!empty($extremumOrderTokenMin)){//если расщепляемое свойство, то надо сортировать по экстремальным значениям
                $sql['order'][$valueMinToken] .= $extremumOrderTokenMin . ($this->order ? ' DESC ':'');
            }else{
                // если order - массив, собираем правило FIELD(`field`, <values_list>) - сортировка в порядке значений из массива
                $sql['order'][$valueMinToken] .= $valueMinToken . ($this->order ? ' DESC ':'');
            }
            $sql['order'][$valueMaxToken] = '';
            if (!empty($orderTokenMin)){
                $sql['order'][$valueMaxToken] .= $orderTokenMin . ' DESC';
            }
            if (!empty($sql['order'][$valueMaxToken])){
                $sql['order'][$valueMaxToken] .= ', ';
            }
            if (!empty($extremumOrderTokenMin)){//если расщепляемое свойство, то надо сортировать по экстремальным значениям
                $sql['order'][$valueMaxToken] .= $extremumOrderTokenMin . ($this->order ? ' DESC ':'');
            }else{
                // если order - массив, собираем правило FIELD(`field`, <values_list>) - сортировка в порядке значений из массива
                $sql['order'][$valueMaxToken] .= $valueMaxToken . ($this->order ? ' DESC ':'');
            }
        }
    }

    protected function compileDiapason($min_field, $max_field, $add_token = "\n\t\t") {
        $db = \App\Builder::getInstance()->getDB();
        $ruleToken = array();
        if (!empty($this->notempty)) {
            $ruleToken[] = '(' . $min_field.' IS NOT NULL OR ' . $max_field . ' IS NOT NULL)';
        }
        if (isset($this->value) && $this->value != ''){
            $ruleToken[] = '(' . $min_field.' IS NULL OR ' . $min_field . ' <= ' . $db->escape_value($this->value) . ')';
            $ruleToken[] = '(' . $max_field.' IS NULL OR ' . $max_field . ' >= ' . $db->escape_value($this->value) . ')';
        }
        // поиск по min/max ищем по пересечению диапазонов
        if (isset($this->max)){
            $ruleToken[] = $min_field.' <= ' . $db->escape_value(str_replace(',', '.', $this->max));
        }
        if (isset($this->min)){
            $ruleToken[] = $max_field.' >= ' . $db->escape_value(str_replace(',', '.', $this->min));
        }
//        if (isset($this->not)){
//            if (!is_array($this->not)) {
//                $ruleToken[] = $field . ' != ' . $db->escape_value($this->not);
//            } elseif (count($this->not) == 1) {
//                $ruleToken[] = $field . ' != ' . $db->escape_value(reset($this->not));
//            } else {
//                $ruleToken[] = $field . ' NOT IN (' . (implode(', ', array_map(array($db, 'escape_value'), $this->not))) . ')';
//            }
//        }
        $ruleToken = implode($add_token . ' AND ', $ruleToken);
        return $ruleToken ? $add_token . $ruleToken : false;
    }
    
    /**
    * Формирование условия поиска на основании правила
    * @param string $field конечное название поля в нашем большом запросе (созданный алиас)
    * @param string $add_token токен для оформления запроса (нужный сдвиг на вправо перед каждой)
    * @return string
    */
   protected function compile($field, $add_token = "\n\t\t"){
       $db = \App\Builder::getInstance()->getDB();
        $ruleToken = array();
        //данное условие требуется, если используется сортировка (LEFT JOIN) и в выборке могут быть NULL
        if (!empty($this->notempty)) {
            $ruleToken[] = $field.' IS NOT NULL';
        }
        if (isset($this->value) && $this->value != ''){
            if (in_array($this->search_type, array(self::SEARCH_LIKE, self::SEARCH_LIKE_LEFT, self::SEARCH_LIKE_RIGHT))){
                if (is_array($this->value)) {
                    $tokens = array();
                    foreach($this->value as $v) {
                        $search_val = ($this->search_type == self::SEARCH_LIKE)
                            ? '%' . $v . '%'
                            : ($this->search_type == self::SEARCH_LIKE_LEFT ? $v . '%' : '%' . $v);
                        $tokens[] = $field.' LIKE ' . $db->escape_value($search_val);
                    }
                    $ruleToken[] = '(' . implode(' OR ', $tokens) . ')';

                } else {
                    $search_val = ($this->search_type == self::SEARCH_LIKE)
                        ? '%' . $this->value . '%'
                        : ($this->search_type == self::SEARCH_LIKE_LEFT ? $this->value . '%' : '%' . $this->value);
                    $ruleToken[] = $field.' LIKE ' . $db->escape_value($search_val);
                }
            }elseif($this->search_type == self::SEARCH_REGEXP){
                $ruleToken[] = $field.' REGEXP ' . $db->escape_value($this->value);
            }elseif (is_array($this->value)){
                if (count($this->value) == 1) {
                    $ruleToken[] = $field . ' = ' . $db->escape_value(reset($this->value));
                } else {
                    $ruleToken[] = $field.' IN (' . (implode(', ', array_map(array($db, 'escape_value'), $this->value))) . ')';
                }
            }else{
                $ruleToken[] = $field.' = ' . $db->escape_value($this->value);
            }
        }
        if (isset($this->max)){
            $ruleToken[] = $field.' <= ' . $db->escape_value(str_replace(',', '.', $this->max));
        }
        if (isset($this->min)){
            $ruleToken[] = $field.' >= ' . $db->escape_value(str_replace(',', '.', $this->min));
        }
        if (isset($this->not)){
            if (!is_array($this->not)) {
                $ruleToken[] = $field . ' != ' . $db->escape_value($this->not);
            } elseif (count($this->not) == 1) {
                $ruleToken[] = $field . ' != ' . $db->escape_value(reset($this->not));
            } else {
                $ruleToken[] = $field . ' NOT IN (' . (implode(', ', array_map(array($db, 'escape_value'), $this->not))) . ')';
            }
        }
        $ruleToken = implode($add_token . ' AND ', $ruleToken);
        return $ruleToken ? $add_token . $ruleToken : false;
   }
    
/* ****************************** ArrayAccess **************************** */

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        if (!in_array($offset, self::$fields) && $offset != 'key'){
            throw new \LogicException('No key "' . $offset . '" in ' . __CLASS__);
        }
        return isset($this->$offset);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        if (in_array($offset, self::$fields)){
            return $this->$offset;
        }elseif($offset == 'key'){
            return $this->key;
        }else{
            throw new \LogicException('No key "' . $offset . '" in ' . __CLASS__);
        }
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value) {
        if (in_array($offset, self::$fields)){
            $this->$offset = $value;
        }else{
            throw new \LogicException('No key "' . $offset . '" in ' . __CLASS__);
        }
    }

    /**
     * @param string $offset
     * @throws \Exception
     */
    public function offsetUnset($offset) {
        return $this->setData($offset, NULL);
    }
}