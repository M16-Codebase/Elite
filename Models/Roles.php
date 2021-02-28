<?php
/**
 * Роли пользователей
 *
 * @author olga
 */
namespace Models;
use App\Configs\AccessConfig;

class Roles {
    const TABLE = 'user_roles';
    const TABLE_GROUPS = 'user_roles_groups';
    const STATUS_ENABLE = 'enable';
    const STATUS_DISABLE = 'disable';
    /** @var Roles */
    private static $instance = null;
    private static $load_fields = array('id', 'group_id', 'key', 'title', 'default_permission', 'after_login_redirect', 'position');
    private $roles = array();
    private $groups = array();
    /**
     * используемые в проекте роли (будут отображаться везде только эти роли)
     * Переехали отсюда в \App\AccessConfig
     */
//    private $usesRoles = array('SuperAdmin', 'Admin', 'Guest', 'User');
    /**
     *
     * @return Roles
     */
    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new Roles();
        }
        return self::$instance;
    }
    /**
     * Получить роль если задан ключ, или список ролей если ключ не задан
     * @param string $key ключ роли
     * @param string $returnByKey отдать массив по ключу или по id (и то и другое уникальные поля)
     * @return null
     */
    public function get($key = NULL, $returnByKey = 'key', $force_refresh = false){
        if (empty($this->roles) || $force_refresh){
            $db = \App\Builder::getInstance()->getDB();
            $this->roles = $db->query('SELECT `'.implode('`, `', self::$load_fields).'` FROM `'.self::TABLE.'`
                WHERE `key` IN (?l) ORDER BY `position` ASC', AccessConfig::getUsesRoles())->select('key');
        }
        if (!empty($key)){
            return !empty($this->roles[$key]) ? $this->roles[$key] : array();
        }
        if ($returnByKey == 'key'){
            return $this->roles;
        }else{
            foreach ($this->roles as $role){
                $rolesById[$role['id']] = $role;
            }
            return $rolesById;
        }
        return NULL;
    }

    public function getGroups(){
        if (empty($this->groups)){
            $this->groups = \App\Builder::getInstance()->getDB()->query('SELECT `id`, `key`, `title`, `access_level`, `access_area`
                FROM `' . self::TABLE_GROUPS . '` ORDER BY `access_level` DESC')->select('key');
        }
        return $this->groups;
    }

    public function getRolesByGroups(){
        $db = \App\Builder::getInstance()->getDB();
        $rolesByGroups = $db->query('SELECT `ur`.`'.implode('`, `ur`.`', self::$load_fields).'`, `ug`.`key` AS `group_key`, `ug`.`title` AS `group_title`, `ug`.`access_level`, `ug`.`access_area` FROM `'.self::TABLE.'` AS `ur`
                INNER JOIN `' . self::TABLE_GROUPS . '` AS `ug` ON `ur`.`group_id` = `ug`.`id`
                WHERE `ur`.`key` IN (?l) ORDER BY `ug`.`access_level` DESC, `ur`.`position` ASC', AccessConfig::getUsesRoles())->select('group_key', 'key');
        $groups = $this->getGroups();
        foreach($groups as $group_key => $group){
            $groups[$group_key]['members'] = !empty($rolesByGroups[$group_key]) ? $rolesByGroups[$group_key] : array();
        }
        return $groups;
    }
    
    public function clearCache(){
        $this->roles = array();
    }
    /**
     * Добавить роль
     * @param array $params
     * @param string $error
     * @return boolean
     */
    public function add($params, &$error = NULL){
        if (empty($params['key'])){
            $error = '"Ключ" - обязательное свойство';
            return FALSE;
        }
        if (!in_array($params['key'], AccessConfig::getUsesRoles())){
            $error = 'Недопустимое значение поля "Ключ", допустимые значения: ' . implode(', ', AccessConfig::getUsesRoles());
            return FALSE;
        }
        $exists = $this->get($params['key']);
        if (!empty($exists)){
            $error = 'Такая роль уже есть';
            return FALSE;
        }
        $db = \App\Builder::getInstance()->getDB();
        $params['position'] = $db->query('SELECT MAX(`position`) FROM ?#', self::TABLE)->getCell() + 1;
        unset($params['id']);
//		$params['default_permission'] = !empty($params['default_permission']) ? self::STATUS_ENABLE : self::STATUS_DISABLE;
        $db->query('INSERT INTO `'.self::TABLE.'` SET ?a', $params);
        $this->clearCache();
        UserPermission::getInstance()->setRolePermissions($this->get($params['key']));
        return TRUE;
    }
    /**
     * Редактировать роль
     * @param int $id
     * @param array $params
     * @param string $error
     * @return boolean
     */
    public function edit($id, $params, &$error = NULL){
        if (!empty($params['key'])){
            $error = '"Ключ" нельзя менять';
            return FALSE;
        }
        $db = \App\Builder::getInstance()->getDB();
        $db->query('UPDATE `'.self::TABLE.'` SET ?a WHERE `id` = ?d', $params, $id);
        $this->clearCache();
        return TRUE;
    }
    /**
     * Удалить роль
     * @param int $id
     * @return boolean
     */
    public function del($id){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('DELETE FROM `'.self::TABLE.'` WHERE `id` = ?d', $id);
        $this->clearCache();
        UserPermission::getInstance()->delRolePermissions($id);
        return TRUE;
    }

    /**
     * сменить порядок ролей
     * @param string $role_key
     * @param int $new_position новая позиция роли
     * @return bool
     */
    public function changeRolePosition($role_key, $new_position){
        $db = \App\Builder::getInstance()->getDB();
        $roles = $this->get();
        if (empty($roles[$role_key])){
            return false;
        }
        $old_position =$roles[$role_key]['position'];
        if ($new_position < $old_position) {
            $db->query('
                    UPDATE `' . self::TABLE . '`
                    SET `position`=`position`+1
                    WHERE `position`>=?d AND `position`<?d', $new_position, $old_position
            );
        } else {
            $db->query('
                    UPDATE `' . self::TABLE . '`
                    SET `position`=`position`-1
                    WHERE `position`<=?d AND `position`>?d', $new_position, $old_position
            );
        }
        $db->query('
            UPDATE `' . self::TABLE . '`
            SET `position`=?d
            WHERE `id` = ?d', $new_position, $roles[$role_key]['id']
        );
        $this->roles = array();
        return true;
    }
}

?>
