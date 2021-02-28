<?php
/**
 * Отвечает за распределение доступа ролей к каждому методу каждого модуля
 *
 * @author olga
 */
namespace Models;

class UserPermission {
    const TABLE = 'user_permissions';
    const STATUS_ENABLE = 'enable';
    const STATUS_DISABLE = 'disable';
    const S_ADMIN_ACTIONS = 1;
    const S_PUBLIC_ACTIONS = 0;
    const S_ALL_ACTIONS = NULL;
    /** @var UserPermission */
    private static $instance = null;
    private $permissions = array();
    /**
     *
     * @return UserPermission
     */
    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new UserPermission();
        }
        return self::$instance;
    }

    /**
     * Поиск ограничений
     * @param string $key Роль пользователя
     * @param int|null $actions_type null - все экшены, 1 - админские, 0 - публичные
     * @return array
     */
    public function search($key = NULL, $actions_type = self::S_ALL_ACTIONS){
        $db = \App\Builder::getInstance()->getDB();
        //храним все ограничения для всех ролей и методов
        if (!is_null($actions_type) && in_array($actions_type, array(self::S_ADMIN_ACTIONS, self::S_PUBLIC_ACTIONS))){
            return $db->query('
                SELECT `r`.`key`, `a`.`module_url`, `a`.`action`, `p`.`permission`, `a`.`admin`, IF(`a`.`action` = "index", 0 , 1 ) AS `index_first`
                FROM `'.self::TABLE.'` AS `p`
                INNER JOIN `'.\Models\Roles::TABLE.'` AS `r` ON (`p`.`role_id` = `r`.`id`)
                INNER JOIN `'.\Models\Action::TABLE.'` AS `a` ON (`a`.`id` = `p`.`action_id`)
                WHERE `a`.`admin` = ?d
                ORDER BY `module_url`, `index_first`, `action`
            ', $actions_type)->getCol(array('key', 'module_url', 'action'), 'permission');
        }elseif (empty($this->permissions)){
            $this->permissions = $db->query('
                SELECT `r`.`key`, `a`.`module_url`, `a`.`action`, `p`.`permission`, IF(`a`.`action` = "index", 0 , 1 ) AS `index_first`
                FROM `'.self::TABLE.'` AS `p` 
                INNER JOIN `'.\Models\Roles::TABLE.'` AS `r` ON (`p`.`role_id` = `r`.`id`)
                INNER JOIN `'.\Models\Action::TABLE.'` AS `a` ON (`a`.`id` = `p`.`action_id`)
                WHERE 1
                ORDER BY `module_url`, `index_first`, `action`
            ')->getCol(array('key', 'module_url', 'action'), 'permission');
        }
        //если был поиск для одной роли отдаем только для одной роли либо всё
        return !empty($key) ? (!empty($this->permissions[$key]) ? $this->permissions[$key] : NULL) : $this->permissions;
    }

    public function getDefaultAccessRights($action_type = self::S_ALL_ACTIONS){
        $db = \App\Builder::getInstance()->getDB();
        $modules_list = $db->query('SELECT DISTINCT `module_url`, `module_class` FROM `' . \Models\Action::TABLE . '` WHERE `module_url` != ""{ AND `admin` = ?d}',
            in_array($action_type, array(self::S_ADMIN_ACTIONS, self::S_PUBLIC_ACTIONS)) ? $action_type : $db->skipIt())->getCol('module_url', 'module_class');
        $result = array();
        foreach($modules_list as $module_url => $module_class){
            $result[$module_url] = call_user_func($module_class . '::getDefaultRolePermission');
        }
        return $result;
    }
    /**
     * Установка ограниченией
     * @param int $role_id
     * @param int $action_id
     * @param  $permission
     */
    public function set($role_id, $action_id, $permission){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('REPLACE INTO `'.self::TABLE.'` SET `role_id` = ?d, `action_id` = ?d, `permission` = ?s', $role_id, $action_id, !empty($permission) ? self::STATUS_ENABLE : self::STATUS_DISABLE);
    }
    
    public function setActionPermissions($action){
        $roles = Roles::getInstance()->get();
        $permissions = array();
        foreach($roles as $role){
            // для админских модулей выставляем по галоч
//            $permissions[$role['key']] = !$action['admin'] || $role['default_permission'] == self::STATUS_ENABLE;
            // теперь модуль сам сообщает права доступа по умолчанию
            $permissions[$role['key']] = call_user_func($action['module_class'] . '::getDefaultRolePermission', $role['key']);
            $this->set($role['id'], $action['id'], $permissions[$role['key']]);
        }
        return $permissions;
    }
    
    public function setRolePermissions($role){
        $actions = Action::getInstance()->search();
        foreach($actions as $action){
            $this->set($role['id'], $action['id'], !$action['admin'] || $role['default_permission'] == self::STATUS_ENABLE);
        }
    }
    
    public function delRolePermissions($role_id){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('DELETE FROM `' . self::TABLE . '` WHERE `role_id` = ?d', $role_id);
    }

    /**
     * Импорт строки из csv-файла
     * @param array $permission_line - array(0 => module_class, 1 => module_url, 2 => action, 3 => title, 4 => admin, 5 => role_key, 6 => permission)
     */
    public function importPermission($permission_line){
        $user_roles = \Models\Roles::getInstance()->get();
        $routes_map = \LPS\Config::getInstance()->getModulesRouteMap();
        if (count($permission_line) != 7
            || !class_exists($permission_line[0])                                                                   // Требуемый модуль не существует
            || empty($routes_map[$permission_line[1]]) || ('Modules\\' . $routes_map[$permission_line[1]] != $permission_line[0])   // url модуля не совпадает с указанным в файле урлом
            || empty($permission_line[2])                                                                           // action не указан
            || !in_array($permission_line[4], array(0, 1))                                                          // не указан тип экшена (админ/паблик)
            || empty($user_roles[$permission_line[5]])                                                              // не найдена роль
            || !in_array($permission_line[6], array(self::STATUS_ENABLE, self::STATUS_DISABLE))                     // не задано правило (enable/disable)
        ){
            return false;
        }
        static $actions = array();
        if (empty($actions[$permission_line[1]][$permission_line[2]])){
            $actions = Action::getInstance()->search(array('moduleUrl' => $permission_line[1], 'action' => $permission_line[2]));
            if (empty($actions)){

            }
            $actions[$permission_line[1]][$permission_line[2]] = Action::getInstance()->registrate($permission_line[0], $permission_line[1], $permission_line[2], $permission_line[4], $permission_line[3]);
        }
        return \App\Builder::getInstance()->getDB()->query('REPLACE INTO `' . self::TABLE . '` SET `action_id` = ?d, `role_id` =?d, `permission` =?s',
            $actions[$permission_line[1]][$permission_line[2]], $user_roles[$permission_line[5]]['id'], $permission_line[6]);
    }
}
?>