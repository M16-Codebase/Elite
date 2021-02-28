<?php
/**
 * Description of CityRoute
 *
 * @author mac-proger
 */
namespace Models;

class CityRoute implements \ArrayAccess{
    //put your code here
    const TABLE_CITY = 'segment_city';
    const TABLE_CITY_PRICE = 'segment_city_price';
    const TABLE_PRICE_RANGE = 'segment_price_range';
    const TABLE_HOLIDAYS = 'segment_delivery_holidays';
    private static $city_fields = array('id', 'name', 'segment_id', 'weekday', 'rect');
    private static $city_update_fields = array('name', 'segment_id', 'weekday', 'rect');
    private static $custom_fields = array('next_cars', 'price', 'price_available');
    
    private static $registry = array();
    
    private static $range = array();
    
    private $data = array();
    private $need_save_city = FALSE;
    private $need_save_price = FALSE;
    /**
	 * 
	 * @param array $ids
	 * @return CityRoute[]
	 */
    public static function factory($ids){
        $getIds = array_diff($ids, array_keys(self::$registry));
        if (!empty($getIds)){
            $db = \App\Builder::getInstance()->getDB();
            $entities = $db->query(
                    'SELECT `' . implode('`, `', self::$city_fields) . '` FROM `' . self::TABLE_CITY . '`'
                  . 'WHERE `id` IN (?i)', $getIds
                )->select('id');
            foreach ($getIds as $id) {
                self::$registry[$id] = isset($entities[$id]) ? new self($entities[$id]) : NULL;        
            }
        }
        $result = array();
        foreach ($ids as $id_result) {
            $result[$id_result] = self::$registry[$id_result];
        }
        return $result;
    }
    /**
     * 
     * @param int $id
     * @return CityRoute
     */
    public static function getById($id){
        if (!empty(self::$registry[$id])){
            return self::$registry[$id];
        }
        $entity = self::factory(array($id));
        return !empty($entity) ? $entity[$id] : NULL;
    }
    /**
	 * 
	 * @param array $params
	 * @param int $start
	 * @param int $limit
	 * @return CityRoute[]
	 */
    public static function search($params = array(), $start = 0, $limit = 100000){
        $db = \App\Builder::getInstance()->getDB();
        
        $entity_ids = $db->query(
                'SELECT `id` FROM `'.self::TABLE_CITY.'` WHERE 1'
               .'{ AND `name` = ?s}'
               .'{ AND `segment_id` = ?d}'
               .' ORDER BY `name`'
               .' LIMIT ?d, ?d',
                !empty($params['city']) ? $params['city'] : $db->skipIt(),
                !empty($params['segment_id']) ? $params['segment_id'] : $db->skipIt(),
                $start,
                $limit
            )->getCol('id', 'id');
        return self::factory($entity_ids);
    }
    
    private function __construct(array $data) {
        $this->data = $data;
        $days = trim($this->data['weekday'], ".");
        $this->data['weekday'] = (strlen($days) != 0) ? explode('.', $days) : array();
    }
    
    public function __destruct() {
        $this->save();
    }
    
    public static function create(array $fields){
        $db = \App\Builder::getInstance()->getDB();
        foreach($fields as $key=>$field){
            if (!in_array($key, self::$city_fields) || $key == 'id'){
                unset($fields[$key]);
            }
        }
        $id = $db->query('INSERT INTO `'.self::TABLE_CITY.'` SET ?a', $fields);
        return self::getById($id);
    }
    
    public function edit(array $data){
        $allow_days = array(0,1,2,3,4,5,6);
        if (isset($data['id'])){
            unset($data['id']);
        }
        foreach($data as $field=>$value){
            if (in_array($field, self::$city_update_fields)){
                if ($field == 'weekday'){
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    foreach($value as $key=>$day){
                        if (!in_array($day,$allow_days)){
                            unset($value[$key]);
                        }
                    }
                }
                $this->data[$field] = $value;
                $this->need_save_city = TRUE;
            } elseif ($field == 'price'){
                $this->setData('price', $value);
                $this->need_save_price = TRUE;
            }
        }
    }
    
    public function save(){
        $db = \App\Builder::getInstance()->getDB();
        if ($this->need_save_city){
            $db->query('UPDATE `' . self::TABLE_CITY . '` SET'
                   . ' `name` = ?s, `segment_id` = ?d, `weekday` = ?s, `rect` = ?s'
                   . ' WHERE `id` = ?d',
                    $this->data['name'],
                    $this->data['segment_id'],
                    '.'.implode('.', $this->data['weekday']).'.',
                    $this->data['rect'],
                    $this->data['id']);
            $this->need_save_city = FALSE;
        }
        if ($this->need_save_price){
            $price = $this->getData('price');
            $price_lines = array_merge($price['weight'], $price['volume']);
            foreach($price_lines as $line){
                $db->query('INSERT INTO `' . self::TABLE_CITY_PRICE . '` (`range_id`, `city_id`, `value`) VALUES (?d, ?d, ?d) ON DUPLICATE KEY UPDATE `value` = ?d',
                           $line['id'], $this['id'], $line['value'], $line['value']);
            }
        }
    }
    
    private function loadPrice(){
        $db = \App\Builder::getInstance()->getDB();
        $price = $db->query(
            'SELECT `rng`.`id`, `rng`.`min`, `rng`.`max`, `rng`.`type`, `city`.`value` FROM `' 
                . self::TABLE_PRICE_RANGE . '` AS `rng` LEFT JOIN `' . self::TABLE_CITY_PRICE . '` as `city`'
                . ' ON `rng`.`id` = `city`.`range_id` AND `city`.`city_id` = ?d ORDER BY `rng`.`type`, `rng`.`min`',
                $this->data['id']
        )->select('type', 'id');
        if (empty($price['volume'])){
            $price['volume'] = array();
        }
        if (empty($price['weight'])){
            $price['weight'] = array();
        }
        $this->data['price'] = $price;
        return $price;
    }
    
    private function checkAvailPrice(){
        $price = $this['price'];
        $avail = array('weight' => FALSE, 'volume' => FALSE);
        foreach($avail as $type=>$val){
            foreach($price[$type] as $price_item){
                if (isset($price_item['value'])){
                    $avail[$type] = TRUE;
                    break;
                }
            }
        }
        return $this->data['price_available'] = $avail['weight'] && $avail['volume'];
    }
    /**
     * Рассчитывает стоимость доставки в данный город
     * @param float $weight масса груза
     * @param float $volume объем груза
     * @return float стоимость доставки
     */
    public function calculatePrice($weight, $volume = NULL){
        $weight = ceil(str_replace(',', '.', $weight));
        $volume = ceil(str_replace(',', '.', $volume));
        $db = \App\Builder::getInstance()->getDB();
        $weight_koef = $db->query(
            'SELECT `city`.`value` FROM `' . self::TABLE_PRICE_RANGE . '` as `rng`'
          . ' LEFT JOIN `' . self::TABLE_CITY_PRICE . '` as `city` ON `rng`.`id` = `city`.`range_id`'
          . ' WHERE `rng`.`type` = \'weight\'AND `city`.`city_id` = ?d AND `rng`.`min` < ?f AND (`rng`.`max` >= ?f OR `rng`.`max` IS NULL)',
            $this['id'], $weight, $weight
        )->getCell();
        $volume_koef = $db->query(
            'SELECT `city`.`value` FROM `' . self::TABLE_PRICE_RANGE . '` as `rng`'
          . ' LEFT JOIN `' . self::TABLE_CITY_PRICE . '` as `city` ON `rng`.`id` = `city`.`range_id`'
          . ' WHERE `rng`.`type` = \'volume\'AND `city`.`city_id` = ?d AND `rng`.`min` <= ?d AND `rng`.`max` > ?d OR `rng`.`max` IS NULL',
            $this['id'], $volume, $volume
        )->getCell();
        return ($weight_koef && $volume_koef) ? max($weight*$weight_koef, $volume*$volume_koef) : ($weight_koef ? $weight*$weight_koef : ($volume_koef ? $volume*$volume_koef : FALSE));
    }
    
    private function getNearestCars(){
        $holidays = self::getHolidays(date('Y-m-d'), date('Y-m-d', strtotime('+1 month')));
        $cars_list = array();
        $date = time();
        for($i = 0; $i < 2; $i++){
            $date = $this->getNearestSheduleDay($date);
            if (!$date) {  // в расписании ничего нет
                break;
            }
            while(true){
                if (empty($holidays[date('Y-m-d', $date)])){
                    break;
                }
                $date = strtotime('+1 day', $date);
            }
            $cars_list[$i] = array(
                'start' => $date,
                'endRequest' => $this->getRequestDay($date, $holidays)
            );
        }
        $this->data['next_cars'] = $cars_list;
        return $cars_list;
    }
    /**
     * Поиск ближайшего дня отправки машины относительно заданного дня без учета выходных
     * @param int $from_date timestamp даты начала поиска, если не указан используем текущую дату
     * @return int timestamp даты отправки или FALSE если расписание пустое
     */
    private function getNearestSheduleDay($from_date = FALSE){
        $date_conv_str = array(
            0 => 'next Monday',
            1 => 'next Tuesday',
            2 => 'next Wednesday',
            3 => 'next Thursday',
            4 => 'next Friday',
            5 => 'next Saturday',
            6 => 'next Sunday'
        );
        if (!$from_date) {
            $form_date = time();
        }
        // MySQL weekday result 0..6, PHP - 1..7 
        $weekday = date('N', $from_date) - 1;
        $first_before = false; $first_after = false; $same_day = in_array($weekday, $this['weekday']);
        foreach($this['weekday'] as $day){
            if ($first_before === false && $day < $weekday){
                $first_before = $day;
            }
            if ($first_after === false && $day > $weekday){
                $first_after = $day;
                break;
            }
        } 
        $nearest_day = ($first_after !== FALSE) ? $first_after : $first_before;
        if ($nearest_day === FALSE && $same_day){
            $nearest_day = $weekday;
        }
        return ($nearest_day !== FALSE) ? strtotime($date_conv_str[$nearest_day], $from_date) : FALSE;
    }
    
    private function getRequestDay($timestamp, $holidays){
        $date = strtotime(date('Y-m-d 12:00:00', $timestamp));
        for ($i = 0; $i < 2; $i++){
            $date = strtotime('-1 day', $date);
            while(true){
                if (empty($holidays[date('Y-m-d', $date)])){
                    break;
                }
                $date = strtotime('-1 day', $date);
            }
            if ($date < time()) {
                $date = NULL;
                break;
            }
        }
        return $date;
    }
    
    private function getData($key){
        if (isset($this->data[$key])){
            return $this->data[$key];
        }else{
            switch ($key) {
                case 'price':
                    return $this->loadPrice();
                    break;
                
                case 'next_cars':
                    return $this->getNearestCars();
                    break;
                
                case 'price_available':
                    return $this->checkAvailPrice();
                    break;

                default:
                    throw new \LogicException('No key ' . $key . ' in ' . __CLASS__);
                    break;
            }
        }
        /*elseif ($key == 'price'){
            return $this->loadPrice();
        } elseif ($key == 'next_cars') {
            return $this->getNearestCars();
        } elseif ($key)
        } else {
            throw new \LogicException('No key ' . $key . ' in ' . __CLASS__);
        }*/
    }
    /**
     * Переписывает данные объекта
     * @param string $key
     * @param mixed $value
     * @throws \LogicException
     */
    private function setData($key, $value) {
        if ($key == 'price') {
            $price = $this->getData('price');
            if (!is_array($value)){
                throw new \LogicException('Value for key ' . $key . ' must be array in ' . __CLASS__);
            }
            foreach($value as $range_id => $price_value){
                if (!empty($price['weight']) && array_key_exists($range_id, $price['weight'])){
                    $this->data['price']['weight'][$range_id]['value'] = $price_value;
                } elseif (!empty($price['volume']) && array_key_exists($range_id, $price['volume'])){
                    $this->data['price']['volume'][$range_id]['value'] = $price_value;
                }
            }
        } elseif (in_array($key, self::$city_update_fields)){
            $this->data[$key] = $value;
        } else {
            throw new \LogicException('Key ' . $key . ' not allowed in ' . __CLASS__);
        }
    }
    /**
     * Устанавливает список выходных дней
     * @param array $days массив дат формата yyyy-mm-dd
     * @return boolean
     */
    public static function setHolidays($days){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('TRUNCATE TABLE `' . self::TABLE_HOLIDAYS . '`');
        $date_string = '';
        foreach($days as $day){
            if (!empty($date_string)){
                $date_string .= ', ';
            }
            $date_string .= '(\'' . $day . '\')';
        }
        if (!empty($date_string)) {
            $db->query('REPLACE INTO `' . self::TABLE_HOLIDAYS . '` (`date`) VALUES ' . $date_string);
        }
        return TRUE;
    }
    /**
     * 
     * @param string $start
     * @param string $end
     * @return array
     */
    public static function getHolidays($start = NULL, $end = NULL){
        $db = \App\Builder::getInstance()->getDB();
        $day_list = $db->query('SELECT `date` FROM `' . self::TABLE_HOLIDAYS . '`'
                . ' WHERE 1'
                . '{ AND `date` >= ?s}'
                . '{ AND `date` <= ?s}',
                !empty($start) ? $start : $db->skipIt(),
                !empty($end) ? $end : $db->skipIt()
            )->getCol('date', 'date');
        return $day_list;
    }
    
    // Работа с ценовыми диапазонами (масса, объем)
    
    public static function getRangeList(){
        if (empty(self::$range)){
            $db = \App\Builder::getInstance()->getDB();
            self::$range = $db->query('SELECT * FROM `' . self::TABLE_PRICE_RANGE . '` ORDER BY `min`')->select('type', 'min');
        }
        return self::$range;
    }
    
    public static function addRange($data, &$error = NULL){
        if (empty($data['max'])){
            $error = 'empty data max';
            return;
        }
        if (empty($data['city_id'])){
            $error = 'empty data city_id';
            return;
        }
        $city = self::getById($data['city_id']);
        if (empty($city)){
            $error = 'city not found';
            return;
        }
        $range_list = self::getRangeList();
        if (empty($data['type'])){
            $error = 'empty data type';
            return;
        }
        $db = \App\Builder::getInstance()->getDB();
        if (empty($range_list[$data['type']])){
            $r_id_first = $db->query('INSERT INTO `'.self::TABLE_PRICE_RANGE.'` SET `min` = ?d, `max` = ?d, `type` = ?s ', 0, $data['max'], $data['type']);
            $r_id_last = $db->query('INSERT INTO `'.self::TABLE_PRICE_RANGE.'` SET `min` = ?d, `max` = ?d, `type` = ?s ', $data['max'], NULL, $data['type']);
            $city->edit(array('price' => array($r_id_first => $data['value'], $r_id_last => $data['value'])));
            $city->save();
            $city->loadPrice();
            self::$range = array();//почистим
            return TRUE;
        }
        $max_value = NULL;//вычисляем максимальный max
        $range = NULL;//вычисляем id диапазона, в который вклинивается новый
        foreach ($range_list[$data['type']] as $min => $value_data){
            if ($data['max'] == $min || $data['max'] == $value_data['max']){
                $error = 'same value';
                break;
            }
            if ($data['max'] >= $min && ($data['max'] <= $value_data['max'] || is_null($value_data['max']))){
                $range = $value_data;
            }
            if ($value_data['max'] > $max_value){
                $max_value = $value_data['max'];
            }
        }
        if (!empty($error)){
            return;
        }
        if (!empty($range)){//если нашли тот, в который вклинивается
            $r_id = $db->query('INSERT INTO `'.self::TABLE_PRICE_RANGE.'` SET `min` = ?d, `max` = ?d, `type` = ?s', $range['min'], $data['max'], $data['type']);
            $city->edit(array('price' => array($r_id => $data['value'])));
            $city->save();
            $db->query('UPDATE `'.self::TABLE_PRICE_RANGE.'` SET `min` = ?d WHERE `id` = ?d', $data['max'], $range['id']);
            $city->loadPrice();
            self::$range = array();//почистим
            return TRUE;
        }
        if (!empty($max_value)){
            $r_id = $db->query('INSERT INTO `' . self::TABLE_PRICE_RANGE . '` SET `min` = ?d, `max` = ?d, `type` = ?s', $max_value, $data['max'], $data['type']);
            $city->edit(array('price' => array($r_id => $data['value'])));
            $city->save();
            $city->loadPrice();
            self::$range = array();//почистим
            return TRUE;
        }
        return;
    }
    
    public static function editRange($id, $data, &$error = NULL){
        $city = self::getById($data['city_id']);
        if (empty($city)){
            $error = 'city not found';
            return;
        }
        $range_list = self::getRangeList();
        if (empty($data['type']) || empty($range_list[$data['type']])){
            $error = 'empty data type';
            return;
        }
        if ($data['max'] == 0){
            $data['max'] = NULL;
        }
        $range = NULL;
        $max_value = NULL;
        $range_inner = NULL;
        foreach ($range_list[$data['type']] as $r){
            if ($r['id'] == $id){
                $range = $r;
            }
            if ($data['max'] == $r['min'] || $data['max'] == $r['max']){
                $error = 'same value';
                break;
            }
            if ($data['max'] >= $r['min'] && ($data['max'] <= $r['max'] || is_null($r['max']))){
                $range_inner = $r;
            }
            if ($r['max'] > $max_value){
                $max_value = $r['max'];
            }
        }
        if (empty($range)){
            $error = 'range not found';
            return;
        }
        if (isset($data['max']) && $data['max'] <= $range['min']){
            $error = 'max must be > min';
            return;
        }
        $db = \App\Builder::getInstance()->getDB();
        if ($range_inner['id'] == $range['id']){
            $range_inner = NULL;//у самого себя минимум не трогаем. надо найти следующий
            if (isset($range_list[$data['type']][$range['max']])){
                $db->query('UPDATE `'.self::TABLE_PRICE_RANGE.'` SET `min` = ?d WHERE `id` = ?d', $data['max'], $range_list[$data['type']][$range['max']]['id']);
            }
        }
        if (!empty($range_inner)){
            foreach ($range_list[$data['type']] as $r){
                if ($r['min'] > $range['min'] && (!is_null($r['max']) && $r['max'] <= $range_inner['min'])){
                    $db->query('DELETE FROM `'.self::TABLE_PRICE_RANGE.'` WHERE `id` = ?d', $r['id']);
                }
            }
        }
        $db->query('UPDATE `'.self::TABLE_PRICE_RANGE.'` SET `max` = ?d WHERE `id` = ?d', $data['max'], $range['id']);
        if (!empty($range_inner)){
            $db->query('UPDATE `'.self::TABLE_PRICE_RANGE.'` SET `min` = ?d WHERE `id` = ?d', $data['max'], $range_inner['id']);
        }
        self::$range = array();
        $city->edit(array('price' => array($range['id'] => $data['value'])));
        $city->save();
        $city->loadPrice();
        return TRUE;
    }
    
    public static function delRange($id, $type = 'weight', &$error = NULL){
        $db = \App\Builder::getInstance()->getDB();
        $range_list = self::getRangeList();
        $range = NULL;
        foreach ($range_list[$type] as $r){
            if ($r['id'] == $id){
                $range = $r;
                break;
            }
        }
        if (empty($range)){
            $error = 'range not found';
            return;
        }
        $range_prev = NULL;
        $range_next = NULL;
        foreach ($range_list[$type] as $r){
            if ($r['max'] == $range['min']){
                $range_prev = $r;
            }
            if ($r['min'] == $range['max']){
                $range_next = $r;
            }
        }
        $db->query('DELETE FROM `'.self::TABLE_PRICE_RANGE.'` WHERE `id` = ?d', $range['id']);
        $db->query('DELETE FROM `'.self::TABLE_CITY_PRICE.'` WHERE `range_id` = ?d', $range['id']);
        if (empty($range_next)){
            $db->query('UPDATE `'.self::TABLE_PRICE_RANGE.'` SET `max` = NULL WHERE `id` = ?d', $range_prev['id']);
        }else{
            $db->query('UPDATE `'.self::TABLE_PRICE_RANGE.'` SET `min` = ?d WHERE `id` = ?d', !empty($range_prev) ? $range_prev['max'] : 0, $range_next['id']);
        }
        self::$range = array();
    }
    
    //-----------------------------------------------------
    
    public function offsetExists($offset) {
        return isset($this->data[$offset]) || in_array($offset, self::$custom_fields);
    }
    
    public function offsetGet($offset) {
        return $this->getData($offset);
    }
    
    public function offsetSet($offset, $value) {
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }
    
    public function offsetUnset($offset) {
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }
}
