<?php
/**
 * Подарочные сертификаты
 *
 * @author Charles Manson
 */
namespace Models\GiftCardManagement;
use Models\ImageManagement\Image;

class GiftCard implements \ArrayAccess{
    
    const TABLE_GIFTCARD_LIST = 'giftcard_list';
    const TABLE_GIFTCARD_NOMINAL = 'giftcard_nominal';
    const TABLE_GIFTCARD_PROVIDER = 'giftcard_provider';
    
    private static $registry = array();
    
    private static $load_fields = array('id', 'code', 'valid_date', 'add_date', 'user_id', 'inn', 'name', 'assign_date', 'nominal_value', 'image_id', 'provider_name', 'provider_site', 'giftcard_info_link');
    private static $update_fields = array('user_id', 'inn', 'name', 'assign_date');
    private static $custom_fields = array('image');
    
    private $data = array();
    
    public static function factory($ids = array()){
        $getIds = array_diff($ids, array_keys(self::$registry));
        if (!empty($getIds)){
            $db = \App\Builder::getInstance()->getDB();
            $entities = $db->query('SELECT `lst`.`id`, `lst`.`code`, `lst`.`valid_date`, `lst`.`add_date`, `lst`.`user_id`, `lst`.`inn`, `lst`.`name`, `lst`.`assign_date`, `nom`.`nominal_value`, `nom`.`image_id`, `p`.`provider_name`, `p`.`provider_site`, `p`.`giftcard_info_link` FROM `' . self::TABLE_GIFTCARD_LIST . '` as `lst`'
                    . ' INNER JOIN `' . self::TABLE_GIFTCARD_NOMINAL . '` as `nom` ON `lst`.`nominal_id` = `nom`.`id`'
                    . ' INNER JOIN `' . self::TABLE_GIFTCARD_PROVIDER . '` as `p` ON `nom`.`provider_id` = `p`.`id`'
                    . ' WHERE `lst`.`id` in (?i)',
                    $getIds)->select('id');
            foreach($getIds as $id){
                self::$registry[$id] = isset($entities[$id]) ? new self($entities[$id]) : NULL;
            }
        }
        $result = array();
        foreach($ids as $id){
            $result[$id] = self::$registry[$id];
        }
        return $result;
    }
    
    public static function search($params = array(), $start=0, $limit=10000000){
        if (!empty($params['nominal_id']) && !is_array($params['nominal_id'])) {
            $params['nominal_id'] = array($params['nominal_id']);
        }
        $db = \App\Builder::getInstance()->getDB();
        $ids = $db->query('SELECT `lst`.`id` FROM `' . self::TABLE_GIFTCARD_LIST . '` as `lst`'
                . ' INNER JOIN `' . self::TABLE_GIFTCARD_NOMINAL . '` as `nom` ON `lst`.`nominal_id` = `nom`.`id`'
                . ' INNER JOIN `' . self::TABLE_GIFTCARD_PROVIDER . '` as `p` ON `nom`.`provider_id` = `p`.`id`'
                . ' WHERE 1'
                . '{ AND `lst`.`id` IN (?i)}'
                . '{ AND `lst`.`nominal_id` IN (?i)}'
                . '{ AND ?d AND `lst`.`user_id` IS NULL AND `lst`.`valid_date` > NOW()}'    // для выдачи пользователю - неистекшие и неприсвоенные
                . '{ AND `lst`.`user_id` = ?d}'
                . '{ AND `lst`.`add_date` >= ?s}'
                . '{ AND `lst`.`add_date` <= ?s}'
                . '{ AND `lst`.`valid_date` >= ?s}'
                . '{ AND `lst`.`valid_date` <= ?s}'
                . '{ AND `lst`.`assign_date` >= ?s}'
                . '{ AND `lst`.`assign_date` <= ?s}'
                . '{ AND `lst`.`inn` = ?d}'
                . '{ AND `lst`.`name` LIKE ?s}'
                . ' ORDER BY ' . (empty($params['not_assigned']) ? '`add_date` DESC' : '`valid_date` ASC')  // при поиске карт для выдачи пользователю в первую очередь скоро истекающие
                . ' LIMIT ?d, ?d',
                !empty($params['ids']) ? $params['ids'] : $db->skipIt(),
                !empty($params['nominal_id']) ? $params['nominal_id'] : $db->skipIt(),
                !empty($params['not_assigned']) ? 1 : $db->skipIt(),
                !empty($params['user_id']) ? $params['user_id'] : $db->skipIt(),
                !empty($params['add_from']) ? date("Y-m-d H:i:s",$params['add_from']) : $db->skipIt(),
                !empty($params['add_to']) ? date("Y-m-d 23:59:59",$params['add_to']) : $db->skipIt(),
                !empty($params['valid_from']) ? date("Y-m-d H:i:s",$params['valid_from']) : $db->skipIt(),
                !empty($params['valid_to']) ? date("Y-m-d 23:59:59",$params['valid_to']) : $db->skipIt(),
                !empty($params['assign_from']) ? date("Y-m-d H:i:s",$params['assign_from']) : $db->skipIt(),
                !empty($params['assign_to']) ? date("Y-m-d 23:59:59",$params['assign_to']) : $db->skipIt(),
                !empty($params['inn']) ? $params['inn'] : $db->skipIt(),
                !empty($params['name']) ? '%'.$params['name'].'%' : $db->skipIt(),
                $start, $limit
        )->getCol('id', 'id');
        return self::factory($ids);
    }
    
    public static function getById($id){
        $result = self::factory(array($id));
        return reset($result);
    }
    
    private function __construct($data) {
        foreach(array('valid_date', 'add_date', 'assign_date') as $date_key){
            $data[$date_key] = !empty($data[$date_key]) ? strtotime($data[$date_key]) : NULL;
        }
        $this->data = $data;
    }
    
    public function isValid(){
        if (empty($this['user_id']) && $this['valid_date'] > time()){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * Выдача подарочного сертификата пользователю
     * @param \App\Auth\Users\RegistratedUser $user
     */
    public function assignCard($request){
		/* @var $user \App\Auth\Users\RegistratedUser */
        $user = $request['user'];
        $name = !empty($request['name']) ? $request['name'] : $user['name'];
        if (!$this->isValid() || empty($user)){
            return FALSE;
        }
		//сначала списываем бонусы, потом прикрепляем карточку
        // @TODO Вернуть нормальное списание бонусов
        $user->setBonus($user['bonus'] - $this['nominal_value']);
		//$user->decreaseBonus($this['nominal_value'], 'Заказ на подарочный сертификат ' . $this['id'], $error);
        $this->setData('user_id', $user['id']);
        $this->setData('inn', $user['inn']);
        $this->setData('name', $name);
        $this->setData('assign_date', date('Y-m-d H:i:s'));
        \App\Builder::getInstance()->getDB()->query('UPDATE `' . self::TABLE_GIFTCARD_LIST . '` SET `user_id` = ?d, `inn` = ?d, `name` = ?s, `assign_date` = ?s WHERE `id` = ?d', 
                $this['user_id'],
                $this['inn'],
                $name,
                $this['assign_date'],
                $this['id']
        );
    }
    /**
     * WTF?? Это не будет работать
     * @param array $card_info
     * @param type $nominal
     */
    public static function createCard($card_info){
        $db = \App\Builder::getInstance()->getDB();
        try {
            $id = $db->query('INSERT INTO `' . self::TABLE_GIFTCARD_LIST .  '` SET `code` = ?s, `nominal_id` = ?d, `valid_date` = ?s, `add_date` = NOW()',
                    $card_info['code'], $card_info['nominal_id'], date("Y-m-d H:i:s", $card_info['valid_date']));
        } catch (\MysqlSimple\Exceptions\MySQLQueryException $ex) {
            return FALSE;
        }
        return $id;
    }
    
    private function getData($key){
        if (in_array($key, self::$load_fields)) {
            return isset($this->data[$key]) ? $this->data[$key] : NULL;
        }else{
            switch ($key) {
                case 'image':
                    $this->data['image'] = !empty($this->data['image_id']) ? Image::getById($this->data['image_id']) : NULL;
                    return $this->data['image'];
                    break;

                default:
                    throw new \LogicException('No key ' . $key . ' in ' . __CLASS__);
                    break;
            }
        }
    }
    
    private function setData($key, $value){
        if (in_array($key, self::$update_fields)){
            $this->data[$key] = $value;
        } else {
            throw new \LogicException('Key ' . $key . ' not allowed in ' . __CLASS__);
        }
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
