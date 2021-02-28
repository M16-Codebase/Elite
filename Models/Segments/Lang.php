<?php
/**
 * Description of Lang
 *
 * @author olga
 */
namespace Models\Segments;
use Models\CatalogManagement;
use Models\Logger;

class Lang implements iSegment, \ArrayAccess{
    const TABLE = 'segments';
	const LANG_KEY_RU = 'ru';
	const LANG_KEY_EN = 'en';
    const DEFAULT_KEY = self::LANG_KEY_RU;
    private static $registry = array();
    private $data = NULL;
    private $needSave = false;
    private static $loadFields = array('id', 'title', 'key');
    private static $updateFields = array('title', 'key');
    private static $additionalFields = array();
    private static $default = array();
    private static $loadIds = array();
	private static $idsByKeys = array();
	/**
	 * Список ISO-кодов стран, в которых по умолчанию выдавать русский язык
	 * @var array
	 */
	private static $rusCountries = array(
		'AB',//АБХАЗИЯ
		'AZ',//АЗЕРБАЙДЖАН
		'AM',//АРМЕНИЯ
		'BY',//БЕЛАРУСЬ
		'KG',//КЫРГЫЗСТАН
		'LV',//ЛАТВИЯ
		'LT',//ЛИТВА
		'MD',//МОЛДОВА
		'RU',//РОССИЯ
		'RO',//РУМЫНИЯ
		'RS',//СЕРБИЯ
		'SK',//СЛОВАКИЯ
		'SI',//СЛОВЕНИЯ
		'TJ',//ТАДЖИКИСТАН
		'TM',//ТУРКМЕНИЯ
		'UZ',//УЗБЕКИСТАН
		'UA',//УКРАИНА
		'EE',//ЭСТОНИЯ
		'OS'//ЮЖНАЯ ОСЕТИЯ
	);
    /**
     *
     * @param array $ids
     */
    public static function prepare(array $ids){
        if (!empty($ids)){
            $ids = array_diff($ids, array_keys(self::$registry), self::$loadIds);
            if (!empty($ids)){
                self::$loadIds = array_merge($ids, self::$loadIds);
            }
        }
    }
    /**
     *
     * @return iSegment[]
     */
    public static function getAll(){
        return self::search();
    }
    /**
     * Возвращает посты к страницам для указанного uri
     * @param string $uri - uri страницы, для которой нужны посты
     * @return \Models\ContentManagement\SegmentPost[]
     */
    public function getPagePosts($uri){
        return \Models\ContentManagement\SegmentPost::getPostsListByPageUrl($uri, $this['id']);
    }
    /**
     * @return string
     */
    public function getUrlPrefix(){
        return $this['key'] != self::DEFAULT_KEY ? '/'.$this['key'] : '';
    }

    /**
     * @param bool|false $onSite
     * @return iSegment
     */
    public static function getDefault($onSite = false){
        $on = $onSite ? 'site' : 'admin';
        if (empty(self::$default[$on])){
            self::$default['admin'] = self::getByKey(self::DEFAULT_KEY);//для админки всегда одинаковый
            self::$default['site'] = \LPS\Config::isCLI() ? self::getByKey(self::DEFAULT_KEY) : \App\Builder::getInstance()->getRouter()->getSegment();
            if (empty(self::$default['site'])){//если роутер не определил язык, ищем в куках
                self::$default['site'] = self::getCookieLang();
            }
//            if (empty(self::$default['site'])){//если нет в куках
//				//смотрим в заголовке браузера
//				$brouser_lang = self::getAcceptLanguage(array('ru_RU', 'en_US'));
//				if ($brouser_lang == 'ru_RU'){//если в заголовке русский, значит 100% наши
//					self::$default['site'] = self::getByKey(self::LANG_KEY_RU);
//				}else{//иначе будем определять по IP
//                    /** @TODO База  */
//					$geoIp = new \SxGeo\Geocoder(\LPS\Config::getRealDocumentRoot() . '/vendor/zapimir/sypexgeo/SxGeoCity.dat');//база со странами
//					$request = \App\Builder::getInstance()->getRequest();
//					$country = $geoIp->getCity($request->getClientIp());
//					if (empty($country) || in_array($country, self::$rusCountries)){
//						self::$default['site'] = self::getByKey(self::LANG_KEY_RU);
//					}else{
//						self::$default['site'] = self::getByKey(self::LANG_KEY_EN);
//					}
//					unset($geoIp);//освобождаем память
//				}
//            }
			if (empty(self::$default['site'])){//если по каким-то причинам не определился
				self::$default['site'] = self::getByKey(self::DEFAULT_KEY);
			}
        }
        if (empty(self::$default[$on])){
            self::$default[$on] = self::getByKey(self::DEFAULT_KEY);
        }
        return self::$default[$on];
    }
    public static function getCookieLang(){
        if(!empty($_COOKIE['segment_id'])){
            $def_id = !empty($_COOKIE['segment_id']) ? $_COOKIE['segment_id'] : NULL;
            return self::getById($def_id);
        }
        return NULL;
    }
    /**
     *
     * @param array $ids
     * @return Lang[]
     */
    public static function factory($ids = array()){
		$getIds = array();
		if (!empty($ids)){
			$getIds = array_unique(array_merge($ids, self::$loadIds));
		}
        if (!empty(self::$registry)){
            $getIds = array_diff($getIds, array_keys(self::$registry));
        }
        if (empty(self::$registry) || !empty($getIds)){
            $db = \App\Builder::getInstance()->getDB();
            $entities = $db->query('
                SELECT `'.implode('`, `', static::$loadFields).'`
                FROM `'. static::TABLE .'`
                WHERE 1'
            )->select('id');
            foreach ($entities as $id => $data){
				static::$registry[$id] = new static($data);
				self::$idsByKeys[$data['key']] = $id;
            }
        }
		if (empty($ids)){
			return self::$registry;//если ids не переданы, значит надо забрать всё что есть
		}
        $result = array();
        foreach ($ids as $id_result){
			if (!empty(self::$registry[$id_result])){
				$result[$id_result] = self::$registry[$id_result];
			}
        }
        return $result;
    }
    /**
     *
     * @param int $id
     * @return Lang
     */
    public static function getById($id){
        if (!empty(static::$registry[$id])){
            return static::$registry[$id];
        }
        $entity = static::factory(array($id));
        return !empty($entity) ? $entity[$id] : NULL;
    }
    /**
     *
     * @param string $key
     * @return Lang
     */
    public static function getByKey($key){
		if (empty(self::$idsByKeys) && !empty(self::$registry)){
			return NULL;
		}
		if (empty(self::$registry)){
			self::factory();
		}
		return !empty(self::$idsByKeys[$key]) ? self::$registry[self::$idsByKeys[$key]] : NULL;
    }
    public static function getByTitle($title){
        $result = self::search(array('title' => $title));
        return !empty($result) ? reset($result) : NULL;
    }
    /**
     *
     * @param array $params
     * @return Lang[]
     * @throws \LogicException
     */
    public static function search($params = array(), &$count = 0, $start = 0, $limit = 1000000){
		if (empty($params)){
			return self::factory();//если параметры не переданы, значит хотим забрать все
		}
        $db = \App\Builder::getInstance()->getDB();
        $order_part = '';
		if (empty($params['order'])){
			$params['order'] = array('position' => 0);
		}
        if (!empty($params['order'])){
            if (is_array($params['order'])){
                foreach ($params['order'] as $key => $desc){
                    $order[] = $key . (!empty($desc) ? ' DESC ' : ' ');
                }
                $order_part = implode(', ', $order);
            }else{
                throw new \LogicException('Order param must be an array("key" => "desc")');
            }
		}
        $entity_ids = $db->query('
            SELECT SQL_CALC_FOUND_ROWS `id`
            FROM `'.static::TABLE.'`
            WHERE 1
            { AND `key` = ?s}
            { AND `title` = ?s}
            ' . (!empty($order_part) ? ('ORDER BY ' . $order_part) : '') .
            'LIMIT ?d, ?d',
                !empty($params['key']) ? $params['key'] : $db->skipIt(),
                !empty($params['title']) ? $params['title'] : $db->skipIt(),
                $start,
                $limit)
        ->getCol('id', 'id');
        $count = $db->query('SELECT FOUND_ROWS()')->getCell();
        return static::factory($entity_ids);
    }

    /**
     * @param string $key
     * @param string $title
     * @param array $errors
     * @return bool|FALSE|int|\MysqlSimple\Result
     */
    public static function create($key, $title = NULL, &$errors = array()){
        if (empty($title)){
            $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
        }
        if (empty($key)){
            $errors['key'] = \Models\Validator::ERR_MSG_EMPTY;
        } elseif (self::checkKeyExists($key)) {
            $errors['key'] = \Models\Validator::ERR_MSG_EXISTS;
        }
        if (!empty($errors)){
            return FALSE;
        }
        $db = \App\Builder::getInstance()->getDB();
        $id = $db->query('INSERT INTO `'.static::TABLE.'` SET `title` = ?s, `key` = ?s', $title, $key);
//        Logger::add(array(
//            'type' => Logger::LOG_TYPE_CREATE,
//            'entity_type' => 'segment',
//            'entity_id' => $id,
//            'additional_data' => array('key' => $key, 'title' => $title)
//        ));
		return $id;
    }

    /**
     * @param string $key
     * @param int|null $not_id
     * @return bool
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    private static function checkKeyExists($key, $not_id = NULL){
        $db = \App\Builder::getInstance()->getDB();
        return $db->query('SELECT 1 FROM ?# WHERE `key` = ?s{ AND `id` != ?d}',
            self::TABLE,
            $key,
            !empty($not_id) ? $not_id : $db->skipIt())->getCell();
    }
    /**
     * Удаляем объект
     * @param int $id
     * @param string $error
     * @return bool
     */
    public static function delete($id, &$error = NULL) {
        $entity = static::getById($id);
        if (!empty($entity)) {
            $db = \App\Builder::getInstance()->getDB();
            $db->query('DELETE FROM `' . static::TABLE . '` WHERE `id`=?d', $entity['id']);
            static::clearRegistry(array($id));
			CatalogManagement\Catalog::onSegmentDelete($id);
            \App\Builder::getInstance()->getAccountController()->onSegmentDelete($id, \App\UserContainer::CONTEXT_ADMIN);
            return true;
        }else{
            $error = 'already deleted';
            return false;
        }
    }
     /**
     * вычистить информацию из реестра
     * @param array $ids
     */
    public static function clearRegistry($ids = array()) {
        if (empty($ids)) {
            $ids = !empty(static::$registry) ? array_keys(static::$registry) : array();
        }
        foreach ($ids as $id) {
            if (!empty(static::$registry[$id])) { //не используем getById, т.к. данная функция может быть использована в factory, т.е. получится бесконечная рекурсия
                $entity = static::$registry[$id];
                $entity->save();
                unset(static::$registry[$id]);
            }
        }
    }
	/**
	 * Определение языка из заголовка браузера
	 * @param array $languages
	 * @return string
	 */
	private static function getAcceptLanguage($languages){
		if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) || 
			!$_SERVER['HTTP_ACCEPT_LANGUAGE']) {
			return $languages[0];
		} 

		/*
		 * Разбираем заголовок Accept-Language
		 *
		 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
		 * 14.4 Accept-Language
		 * Accept-Language = "Accept-Language" ":"
		 *                   1#( language-range [ ";" "q" "=" qvalue ] )
		 * language-range  = ( ( 1*8ALPHA *( "-" 1*8ALPHA ) ) | "*" )
		 *
		 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.9
		 * 3.9 Quality Values
		 * qvalue         = ( "0" [ "." 0*3DIGIT ] )
		 *                | ( "1" [ "." 0*3("0") ] )
		 */
		preg_match_all("/([a-z]{1,8})(?:-([a-z]{1,8}))?(?:\s*;\s*q\s*=\s*(1|1\.0{0,3}|0|0\.[0-9]{0,3}))?\s*(?:,|$)/i",
					   $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);

		// Результат по умолчанию - первый из доступных языков
		$result = $languages[0];
		$max_q = 0;		
		for ($i = 0; $i < count($matches[0]); $i++) {
			// Выделяем очередной язык
			$lang = $matches[1][$i];
			if (!empty($matches[2][$i])) {
				// Переводим ru-RU в ru_RU (т.к. локаль ru_RU)
				$lang .= '_'.$matches[2][$i]; 
			}
			// Определяем приоритет
			if (!empty($matches[3][$i])) {
				$q = (float)$matches[3][$i];   
			} else {
				$q = 1.0;
			}  
			// Проверяем есть ли проверяемый язык в массиве доступных
			if (in_array($lang, $languages) && ($q > $max_q)) {
				$result = $lang;
				$max_q = $q;
			}
			// Если язык только из первой части (например, просто ru, а не ru-RU) и более приоритетный язык еще не найден, 
			// то пробуем найти в массиве доступых языков тот, который начинается так же   
			elseif (empty($matches[2][$i]) && ($q * 0.8 > $max_q)) {
				$n = strlen($lang);
				foreach ($languages as $l) {
					if (!strncmp($l, $lang, $n)) {
						$result = $l;
						// Поскольку не точное совпадение, то уменьшаем q на 20%
						$max_q = $q * 0.8;
						break;
					}
				}
			}
		}
		return $result;
	}
    private function __construct($data){
        $allow_fields = array_merge(static::$loadFields, static::$additionalFields);
        foreach ($allow_fields as $field){
            $this->data[$field] = !empty($data[$field]) ? $data[$field] : NULL;
        }
    }
    public function __destruct(){
        $this->save();
    }
    public function getData($key){
        if (in_array($key, static::$loadFields) || in_array($key, static::$additionalFields)){
            return $this->data[$key];
        }else{
            throw new \LogicException('No key ' . $key . ' in ' . __CLASS__);
        }
    }
    /**
     * Переписывает данные объекта
     * @param string $key
     * @param mixed $value
     * @throws \LogicException
     */
    private function setData($key, $value) {
        if (array_key_exists($key, $this->data)) {
            if (in_array($key, static::$loadFields)){
                $this->data[$key] = $value;
                $this->needSave = true;
            }else{
                throw new \LogicException('Key '.$key.' unchangable');
            }
        }else{
            throw new \LogicException('Key ' . $key . ' not allowed in ' . __CLASS__);
        }
    }

    /**
     * Редактирование
     * @param array $params
     * @param array $errors
     * @return bool
     */
    public function update(array $params, &$errors = array()) {
        if (empty ($params))
            return true;
        if (array_key_exists('key', $params) && self::checkKeyExists($params['key'], $this['id'])){
            $errors['key'] = \Models\Validator::ERR_MSG_EXISTS;
            return FALSE;
        }
        foreach (static::$updateFields as $field){
            if (array_key_exists ($field, $params)){
                $this->setData($field, $params[$field]);
            }
        }
        return true;
    }
    /**
     * Сохраняет все поля объекта в базу
     */
    public function save() {
        if ($this->needSave) {
            $update_fields = array();
            foreach (static::$loadFields as $field) {
                $update_fields[$field] = $this->data[$field];
            }
            \App\Builder::getInstance()->getDB()->query('UPDATE `' . static::TABLE . '` SET ?a WHERE `id` = ?d', $update_fields, $this['id']);
        }
        $this->needSave = false;
    }

    public function asArray(){
        return $this->data;
    }

    public function getId(){
        return $this->data['id'];
    }

        /* ****************************** ArrayAccess **************************** */

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->getData($offset);
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value) {
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }

    /**
     * @param string $offset
     * @throws \Exception
     */
    public function offsetUnset($offset) {
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }
}