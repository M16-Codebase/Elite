<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 08.09.14
 * Time: 15:57
 */

namespace Models\MenuManagement;


use Models\ImageManagement\Image;
use Models\Validator;

class MenuItem implements \ArrayAccess{
    const TABLE_MENU = 'menu';
    const TABLE_MENU_ITEM = 'menu_item';

    private static $registry = array();
    private static $menu_items = array();

    private static $load_fields = array('id', 'menu_id', 'parent_id', 'name', 'title', 'url', 'image_id', 'permissions', 'position');
    private static $update_fields = array('menu_id', 'parent_id', 'name', 'title', 'url', 'permissions');
    private static $required_fields = array('menu_id', 'name');

    private $data = array();
    private $child_items = array();

    private static $checker_params = array(
        'menu_id' => array('type' => 'checkInt'),
        'parent_id' => array('type' => 'checkInt', 'options' => array('empty' => true)),
        'name' => array('type' => 'checkString'),
        'title' => array('type' => 'checkString', 'options' => array('empty' => true)),
        'url' => array('type' => 'checkString', 'options' => array('empty' => true))
    );

    // Методы для работы с меню

    public static function getMenuList(){
        return \App\Builder::getInstance()->getDB()->query('SELECT `id`, `key` FROM `' . self::TABLE_MENU . '` ORDER BY `key`')->select();
    }

    public static function getMenuById($id){
        return \App\Builder::getInstance()->getDB()->query('SELECT `id`, `key` FROM `' . self::TABLE_MENU . '` WHERE `id` = ?d', $id)->getRow();
    }

    public static function changeMenuKey($menu_id, $key){
        return \App\Builder::getInstance()->getDB()->query('UPDATE IGNORE `' . self::TABLE_MENU . '` SET `key` = ?s WHERE `id` = ?d', $key, $menu_id) ? TRUE : FALSE;
    }

    public static function createMenu($key){
        return \App\Builder::getInstance()->getDB()->query('INSERT IGNORE `' . self::TABLE_MENU . '` SET `key` = ?s', $key);
    }

    public static function deleteMenu($id){
        $db = \App\Builder::getInstance()->getDB();
        $item_ids = $db->query('SELECT `id` FROM `' . self::TABLE_MENU_ITEM . '` WHERE `menu_id` = ?d', $id)->getCol('id', 'id');
        foreach($item_ids as $item_id){
            self::delete($item_id);
        }
        $db->query('DELETE FROM `' . self::TABLE_MENU . '` WHERE `id` = ?d', $id);
        return TRUE;
    }

    // Методы для работы с элементами меню
    /**
     * @param string $menu_key
     * @param bool $force_refresh
     * @return MenuItem[]
     */
    public static function getMenuItemsByKey($menu_key, $force_refresh = FALSE){
        if (empty($menu_key)){
            return array();
        }
        if (empty(self::$menu_items[$menu_key]) || $force_refresh){
            $result = array();
            $items = self::search(array('menu_key' => $menu_key));
            foreach($items as $item){
                if (empty($item['parent_id'])){
                    $result[$item['position']] = $item;
                } else {
                    $parent = self::getById($item['parent_id']);
                    if (!empty($parent)){
                        $parent->addChild($item);
                    }
                }
            }
            self::$menu_items[$menu_key] = $result;
        }
        return self::$menu_items[$menu_key];
    }

    /**
     * @param array $params
     * @return MenuItem[]
     */
    public static function search(array $params){
        $db = \App\Builder::getInstance()->getDB();
        $ids = $db->query('SELECT `i`.`id` FROM `' . self::TABLE_MENU . '` AS `m` INNER JOIN `' . self::TABLE_MENU_ITEM . '` AS `i` ON `m`.`id` = `i`.`menu_id`
            WHERE 1
            { AND `m`.`key` = ?s}
            ORDER BY `i`.`menu_id`, `i`.`position`
            ',
            !empty($params['menu_key']) ? $params['menu_key'] : $db->skipIt()
        )->getCol('id', 'id');
        return self::factory($ids);
    }

    /**
     * @param array $ids
     * @return MenuItem[]
     */
    public static function factory(array $ids){
        $loaded_ids = array_keys(self::$registry);
        $all_ids = array_merge($loaded_ids, $ids);
        $getIds = array_diff($all_ids, $loaded_ids);
        if (!empty($getIds)){
            $entities = \App\Builder::getInstance()->getDB()->query('SELECT `' . implode('`, `', self::$load_fields) . '` FROM `' . self::TABLE_MENU_ITEM . '` WHERE `id` IN (?i)', $getIds)->select('id');
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

    /**
     * @param $id
     * @return MenuItem|NULL
     */
    public static function getById($id){
        $result = self::factory(array($id));
        return reset($result);
    }

    private function __construct($data){
        foreach(self::$load_fields as $field){
            $this->data[$field] = isset($data[$field]) ? $data[$field] : NULL;
        }
    }

    protected function addChild(MenuItem $child_item){
        if ($child_item['parent_id'] == $this->data['id']){
            $this->child_items[$child_item['position']] = $child_item;
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private static function normalizeParams($data){
        foreach($data as $key => $value){
            if (!in_array($key, self::$update_fields)){
                unset($data[$key]);
            }
        }
        return $data;
    }

    private static function validateData($data, &$errors){
        $checker = Validator::getInstance();
        foreach(self::$checker_params as $field => $p){
            $data[$field] = $checker->checkValue($data[$field], $p['type'], $errors[$field], isset($p['options']) ? $p['options'] : array());
        }
        return $checker->isErrorsEmpty($errors);
    }

    public static function create($data, &$errors = array()){
        $data = self::normalizeParams($data);
        if (!self::validateData($data, $errors)){
            return FALSE;
        }
        if (empty($data['parent_id'])) {
            $data['parent_id'] = NULL;
        }
        $db = \App\Builder::getInstance()->getDB();
        $position = $db->query('SELECT MAX(`position`) FROM `' . self::TABLE_MENU_ITEM . '` WHERE `menu_id` = ?d AND ' . (!empty($data['parent_id']) ? '`parent_id` = ?d' : '`parent_id` IS NULL'),
            $data['menu_id'],
            $data['parent_id'])->getCell() + 1;
        $item_id = $db->query('INSERT INTO `' . self::TABLE_MENU_ITEM . '` SET ?a, `position` = ?d', $data, $position);
        return $item_id;
    }

    public static function delete($id){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('DELETE FROM `' . self::TABLE_MENU_ITEM . '` WHERE `id` = ?d', $id);
        $child_ids = $db->query('SELECT `id` FROM `' . self::TABLE_MENU_ITEM . '` WHERE `parent_id` = ?d', $id)->getCol('id', 'id');
        if (!empty($child_ids)){
            $db->query('DELETE FROM `' . self::TABLE_MENU_ITEM . '` WHERE `parent_id` = ?d', $id);
            foreach($child_ids as $child_id){
                unset(self::$registry[$child_id]);
            }
        }
        unset(self::$registry[$id]);
        return TRUE;
    }

    public function update($data, &$errors = array()){
        $data = self::normalizeParams($data);
        if (!self::validateData($data, $errors)){
            return FALSE;
        }
        if (empty($data['parent_id'])) {
            $data['parent_id'] = NULL;
        }
        foreach($data as $k=>$v){
            $this->data[$k] = $v;
        }
        $db = \App\Builder::getInstance()->getDB();
        $db->query('UPDATE `' . self::TABLE_MENU_ITEM . '` SET ?a WHERE `id` = ?d', $data, $this->data['id']);
        return TRUE;
    }

    public function changePosition($new_pos){
        if ($new_pos == $this->data['position']){
            return TRUE;
        }
        $db = \App\Builder::getInstance()->getDB();
        if ($new_pos > $this->data['position']){
            $db->query('UPDATE `' . self::TABLE_MENU_ITEM . '` SET `position` = `position`-1
                WHERE `position` > ?d AND `position` <= ?d AND `menu_id` = ?d AND ' . (!empty($this->data['parent_id']) ? '`parent_id` = ?d' : '`parent_id` IS NULL'),
                $this->data['position'], $new_pos, $this->data['menu_id'], $this->data['parent_id']);
        } else {
            $db->query('UPDATE `' . self::TABLE_MENU_ITEM . '` SET `position` = `position`+1
                WHERE `position` < ?d AND `position` >= ?d AND `menu_id` = ?d AND ' . (!empty($this->data['parent_id']) ? '`parent_id` = ?d' : '`parent_id` IS NULL'),
                $this->data['position'], $new_pos, $this->data['menu_id'], $this->data['parent_id']);
        }
        $db->query('UPDATE `' . self::TABLE_MENU_ITEM . '` SET `position` = ?d WHERE `id` = ?d', $new_pos, $this->data['id']);
        $this->data['position'] = $new_pos;
        return TRUE;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return bool
     */
    public function setImage($file){
        if (empty($file)){
            return FALSE;
        }
        if (empty($this->data['image_id'])){
            $image = Image::add($file);
            \App\Builder::getInstance()->getDB()->query('UPDATE `' . self::TABLE_MENU_ITEM . '` SET `image_id` = ?d WHERE `id` = ?d', $image['id'], $this->data['id']);
            $this->data['image_id'] = $image['id'];
        } else {
            $image = Image::getById($this->data['image_id']);
            if (empty($image)){
                return FALSE;
            } else {
                $image->reload($file);
            }
        }
        return TRUE;
    }

    public function asArray(){
        return $this->data;
    }

    // ArrayAccess
    public function offsetExists($offset){
        return isset($this->data[$offset]) || $offset == 'has_children' || $offset = 'child_items' || ($offset == 'image' && isset($this->data['image_id']));
    }

    public function offsetGet($offset){
        if (in_array($offset, self::$load_fields)) {
            return $this->data[$offset];
        } elseif ($offset == 'has_children'){
            return !empty($this->child_items);
        } elseif ($offset == 'child_items'){
            return $this->child_items;
        } elseif ($offset == 'image'){
            return !empty($this->data['image_id']) ? Image::getById($this->data['image_id']) : NULL;
        } else {
            throw new \LogicException('No key ' . $offset . ' in ' . __CLASS__);
        }
    }

    public function offsetSet($offset, $value){
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }

    public function offsetUnset($offset){
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }

} 