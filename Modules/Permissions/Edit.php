<?php
/**
 * Description of Edit
 *
 * @author olga
 */
namespace Modules\Permissions;
use App\Configs\AccessConfig;
use Models\Action;
use Models\Roles;
use Models\UserPermission;

class Edit extends \LPS\AdminModule{
    protected static $module_custom_permissions = array(
        AccessConfig::ROLE_SUPER_ADMIN => true
    );

    const DEFAULT_ACCESS = AccessConfig::ACCESS_WEB_MODULE;
    public function index(){
        $this->permissionsList(TRUE);
    }

    public function permissionsList($inner = FALSE){
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        $userPermission = UserPermission::getInstance();
        $ans
            ->add('roles', \Models\Roles::getInstance()->get())
            ->add('roles_by_groups', \Models\Roles::getInstance()->getRolesByGroups())
            ->add('actions_public', \Models\Action::getInstance()->search(array('order' => array('module_url' => 0), 'admin' => UserPermission::S_PUBLIC_ACTIONS)))
            ->add('actions_admin', \Models\Action::getInstance()->search(array('order' => array('module_url' => 0), 'admin' => UserPermission::S_ADMIN_ACTIONS)))
            ->add('default_access_rights', $userPermission->getDefaultAccessRights())
            ->add('permissions', $userPermission->search())
            ->add('module_titles', \LPS\Config::getParametr('site', 'module_titles'));
    }
    
    public function setPermission(){
        $role_id = $this->request->request->get('role_id');
        $action_id = $this->request->request->get('action_id');
        $permission = $this->request->request->get('permission');
        UserPermission::getInstance()->set($role_id, $action_id, $permission);
        return '';
    }
    
    public function roles(){
        $this->rolesList(1);
    }

    public function renameAction(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $id = $this->request->request->get('id');
        $title = $this->request->request->get('title');
        $db = \App\Builder::getInstance()->getDB();
        if (empty($id) || !$db->query('SELECT 1 FROM `' . Action::TABLE . '` WHERE `id`=?d', $id)->getCell()){
            $ans->addErrorByKey('id', 'empty');
        } elseif (empty($title)){
            $ans->addErrorByKey('title', 'empty');
        } else {
            $db->query('UPDATE `' . Action::TABLE . '` SET `title` = ?s WHERE `id` = ?d', $title, $id);
            $ans->addData('id', $id)->addData('title', $title);
        }
    }

    public function changeRolePosition(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $role_key = $this->request->request->get('role_key');
        $new_position = $this->request->request->get('position');
        $roles = Roles::getInstance()->get();
        if (empty($roles[$role_key])){
            $ans->addErrorByKey('role_key', 'not_found');
        } elseif (empty($new_position)){
            $ans->addErrorByKey('new_position', 'empty');
        } else {
            Roles::getInstance()->changeRolePosition($role_key, $new_position);
            return $this->run('rolesList');
        }
    }
    
    public function rolesList($inner = false){
        if (!$inner){
            $this->setJsonAns();
        }
        $roles = \Models\Roles::getInstance()->get(NULL, 'key', TRUE); // Требуется перезагрузка ролей, на случай редактирования
        $this->getAns()->add('roles', $roles);
    }

    public function roleFields() {
        $ans = $this->setJsonAns();
        $roles = \Models\Roles::getInstance()->get(null, 'id');
        $role_id = $this->request->request->get('id');
        if (!empty($role_id) && empty($roles[$role_id])) {
            $ans->setEmptyContent()
                ->addErrorByKey('id', \Models\Validator::ERR_MSG_EMPTY);
        } else {
            $ans->add('allow_roles', AccessConfig::getUsesRoles())
                ->add('used_roles', array_keys(\Models\Roles::getInstance()->get()));
            if (!empty($role_id)) {
                $role = $roles[$role_id];
                $ans->add('role', $role)
                    ->setFormData($role);
            }
        }
    }
    
    public function addRole(){
        \Models\Roles::getInstance()->add($this->request->request->all(), $error);
        if (empty($error)){
            return $this->run('rolesList');
        }else{
            return json_encode(array('errors' => array('key' => $error)));
        }
    }
    
    public function editRole(){
        if (!$this->request->request->get('id')) {
            return $this->run('addRole');
        }
        \Models\Roles::getInstance()->edit($this->request->request->get('id'), $this->request->request->all(), $error);
        if (empty($error)){
            return $this->run('rolesList');
        }else{
            return json_encode(array('error' => $error));
        }
    }
    
    public function deleteRole(){
        \Models\Roles::getInstance()->del($this->request->request->get('id'));
        if (empty($error)){
            return $this->run('rolesList');
        }else{
            return json_encode(array('error' => $error));
        }
    }

    public function exportPermissions(){
        $actions_group = $this->request->query->get('actions_group');
        $actions = \Models\Action::getInstance()->search(array('admin' => $actions_group));
        $permissions = UserPermission::getInstance()->search(null, $actions_group);
        $path = \LPS\Config::getRealDocumentRoot() . '/data/temp/';
        if (!file_exists($path)){
            \LPS\Components\FS::makeDirs($path);
        }
        $filename = $path . 'access_rights.csv';
        $fp = fopen($filename, 'w');
        foreach($actions as $act){
            foreach($permissions as $user_role => $role_permissions){
                if (isset($role_permissions[$act['module_url']][$act['action']])) {
                    fwrite($fp, $act['module_class'] . ';' . $act['module_url'] . ';' . $act['action'] . ';' . $act['title'] . ';' . $act['admin'] . ';' . $user_role . ';' . $role_permissions[$act['module_url']][$act['action']] . PHP_EOL);
                }
            }
        }
        fclose($fp);
        \Models\FilesManagement\Download::existsFile($filename, NULL, TRUE);
        unlink($filename);
        return '';
    }

    public function importPermissions(){
        $errors = array();
        $file = $this->request->files->get('file');
        if (empty($file)){
            $errors['file'] = 'empty';
        }
        if (empty($errors)){
            $path = \LPS\Config::getRealDocumentRoot() . '/data/temp/';
            if (!file_exists($path)){
                \LPS\Components\FS::makeDirs($path);
            }
            $filename = $path . 'import_rights.csv';
            move_uploaded_file($file->getRealPath(), $filename);

            $userPermissions = UserPermission::getInstance();
            $fp = fopen($filename, 'r');
            while($line = fgetcsv($fp, 0, ';')){
                $userPermissions->importPermission($line);
            }
            fclose($fp);
            unlink($filename);
            return $this->run('permissionsList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }
}

?>
