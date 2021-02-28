<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 05.06.15
 * Time: 12:49
 */

namespace App;


use App\Auth\Users\RegistratedUser;
use App\Configs\AccessConfig;

class UserContainer implements \ArrayAccess{
    const CONTEXT_READ_ONLY = 'read_only';
    const CONTEXT_USER = 'user';
    const CONTEXT_ADMIN = 'admin';

    private static $allow_context = array(
        self::CONTEXT_READ_ONLY,
        self::CONTEXT_USER,
        self::CONTEXT_ADMIN
    );

    private static $allow_user_role = array(
        self::CONTEXT_USER => array(AccessConfig::ROLE_USER),
        self::CONTEXT_ADMIN => array(
            AccessConfig::ROLE_USER,
            AccessConfig::ROLE_ADMIN,
            AccessConfig::ROLE_BROKER,
            AccessConfig::ROLE_SEO_ADMIN,
            AccessConfig::ROLE_SUPER_ADMIN
        )
    );

    private static $default_user_role = array(
        self::CONTEXT_USER => AccessConfig::ROLE_USER,
        self::CONTEXT_ADMIN => AccessConfig::ROLE_ADMIN
    );
    /**
     * @var array Общие параметры валидации (одинаковые для всех ролей)
     */
    private static $common_validation_params = array(
        'segment_id' => array('type' => 'checkInt', 'options' => array('empty' => TRUE)),
        'bonus' => array('type' => 'checkInt', 'options' => array('empty' => TRUE)),
        'last_update_bonus' => array('type' => 'checkString', 'options' => array('empty' => TRUE))
    );
    /**
     * @var array Индивидуальные параметры валидации
     */
    private static $validation_params = array(
        AccessConfig::ROLE_USER_FIZ => array(
            'name' => array('type' => 'checkString'),
            'surname' => array('type' => 'checkString'),
            'phone' => array('type' => 'checkPhone', 'options' => array('empty' => true))
        ),
        AccessConfig::ROLE_USER_ORG => array(
            'inn' => array('type' => 'checkNumber', 'options' => array('empty' => true)),
            'kpp' => array('type' => 'checkString', 'options' => array('empty' => true)),
//            'inn' => array('type' => 'checkString', 'options' => array('count' => 10)),
//            'okpo' => array('type' => 'checkString', 'options' => array('count_min' => 8, 'count_max' => 10)),
            'ogrn' => array('type' => 'checkNumber', 'empty' => true),
//            'ogrn' => array('type' => 'checkString', 'options' => array('count' => 13), 'empty' => true),
//            'jure_address' => array('type' => 'checkString'),
//            'document_address' => array('type' => 'checkString'),
//            'requisites' => array('type' => 'checkString'),
//            'organisation_phone' => array('type' => 'checkPhone'),
//            'organisation_fax' => array('type' => 'checkPhone'),
            'company_name' => array('type' => 'checkString'),
            'city' => array('type' => 'checkString', 'options' => array('empty' => true)),
            'name' => array('type' => 'checkString'),
            'surname' => array('type' => 'checkString'),
            'phone' => array('type' => 'checkPhone')
        ),
        AccessConfig::ROLE_ADMIN => array(
            'name' => array('type' => 'checkString'),
            'surname' => array('type' => 'checkString', 'options' => array('empty' => true)),
            'phone' => array('type' => 'checkPhone', 'options' => array('empty' => true))
        )
    );

    private static function userClassParents(RegistratedUser $userObj){
        $class = get_class($userObj);
        return self::getUserClassesStack($class);
    }

    private static function getUserClassesStack($className){
        $classes = array($className => $className) + class_parents($className);
        $result = array();
        foreach ($classes as $class) {
            $class = explode('\\', $class);
            $class = array_pop($class);
            $result[$class] = $class;
        }
        return $result;
    }

    private static function getClassByRole($role, $person_type){
        $class = AccessConfig::ROLE_ADMIN;
        if ($role == AccessConfig::ROLE_USER) {
            switch ($person_type){
                case 'fiz':
                    $class = AccessConfig::ROLE_USER_FIZ;
                    break;
                case 'org':
                    $class = AccessConfig::ROLE_USER_ORG;
                    break;
                default:
                    throw new \LogicException('Incorrect person_type for User role');
            }
        }
        return 'App\Auth\Users\\' . $class;
    }
    /**
     * возвращает параметры валидации для указанной роли пользователя, если роль не указана, то параметры берем из текущего класса (из которого метод вызван)
     * @param RegistratedUser|NULL $object
     * @param string $role
     * @param string $person_type
     * @return array
     */
    public static function getValidationParams($object = NULL, $role = NULL, $person_type = NULL){
        $classes_stack = !empty($role) ? self::getUserClassesStack(self::getClassByRole($role, $person_type)) : self::userClassParents($object);
        $validation_params = array();
        foreach($classes_stack as $class){
            if (isset(self::$validation_params[$class])){
                $validation_params = self::$validation_params[$class];
                break;
            }
        }
        return array_merge($validation_params, self::$common_validation_params);
    }
    /**
     * Валидация данных при создании / редактировании юзера
     * @param array $data входные данные
     * @param array $validation_params
     * @param array $errors
     * @param array $old_data старые значения, требуются для того чтобы не обновлять неизменные значения
     * @return array ассоциативный массив данных для обновления
     */
    private static function validateFields($data, $validation_params, &$errors = array(), $old_data = array()){
        $validator = \Models\Validator::getInstance(\App\Builder::getInstance()->getRequest());
        $update_fields = array();
        foreach($validation_params as $field=>$params){
            if (!array_key_exists($field, $data) && !empty($old_data[$field])){
                // Если значение уже задано, его можно не редактировать
                continue;
            }
            $value = !empty($data[$field]) ? $data[$field] : NULL;
            $value = $validator->checkValue(
                $value,
                $params['type'],
                $errors[$field],
                !empty($params['options']) ? $params['options'] : array());
            if (empty($errors[$field]) && (empty($old_data[$field]) || $value != $old_data[$field])){
                $update_fields[$field] = !empty($value) ? $value : NULL;
            }
        }
        if (!empty($errors)){
            foreach($errors as $field=>$value){
                if (empty($value) || !empty($old_data[$field]) && $value == \Models\Validator::ERR_MSG_EMPTY){
                    unset($errors[$field]);
                }
            }
        }
        return $update_fields;
    }

    /**
     * @param string $context
     * @return bool
     */
    public static function checkAllowedContext($context){
        return in_array($context, self::$allow_context);
    }

    /**
     * @param string $context
     * @param string $role
     * @param array $errors
     * @return string
     */
    public static function getAllowedUserRole($context, $role, &$errors = array()){
        if (empty($role)){
            return self::$default_user_role[$context];
        } elseif (!in_array($role, self::$allow_user_role[$context])) {
            $errors['role'] = 'incorrect';
        }
        return $role;
    }
    /**
     * @var RegistratedUser
     */
    private $userObj = NULL;
    /**
     * @var string
     */
    private $context = NULL;

    public function __construct(RegistratedUser $user, $context){
        if (!self::checkAllowedContext($context)){
            throw new \LogicException("Недопустимое значение контекста UserContainer — $context");
        }
        $this->userObj = $user;
        $this->context = $context;
    }

    /**
     * @param string $context
     * @param array $data
     * @param array $user_fields
     * @param array $errors
     * @return bool
     * @throws \Exception
     */
    public static function preCreate($context, $data, &$user_fields, &$errors){
        $role_key = UserContainer::getAllowedUserRole($context, !empty($data['role']) ? $data['role'] : NULL, $errors);
        if (!empty($errors['role'])){
            return FALSE;
        }
        $role = \Models\Roles::getInstance()->get($role_key);
        if (empty($role)){
            throw new \Exception('В системе не найдена роль: "' . $role . '"');
        }
        $user_fields['role'] = $role['id'];
        if ($role['key'] != \App\Configs\AccessConfig::ROLE_USER){
            $user_fields['person_type'] = 'man';
        } else {
            if (empty($data['person_type'])){
                $errors['person_type'] = \Models\Validator::ERR_MSG_EMPTY;
            } elseif (!in_array($data['person_type'], array('org', 'fiz'))){
                $errors['person_type'] = \Models\Validator::ERR_MSG_INCORRECT_FORMAT;
            } else {
                $user_fields['person_type'] = $data['person_type'];
            }
        }
        if (empty($errors)) {
            $validation_params = self::getValidationParams(NULL, $role_key, $user_fields['person_type']);
            $user_fields = array_merge($user_fields, self::validateFields($data, $validation_params, $errors));
        }
    }

    public function update($data, &$errors = NULL){
        if ($this->context == self::CONTEXT_READ_ONLY){
            throw new \LogicException('Невозможно отредактировать пользователя, выбран контекст #read_only');
        }
        $update_fields = $data;
        // Проверка смены роли пользователя
        if (isset($data['role']) && ($data['role'] != $this->userObj['role'] || isset($data['person_type']) && $data['person_type'] != $this->userObj['person_type'])){
            $roles = \Models\Roles::getInstance()->get();
            if (!isset($roles[$data['role']])){
                $errors['role'] = \Models\Validator::ERR_MSG_INCORRECT_FORMAT;
            } else {
                if ($data['role'] == 'User'){
                    // у User person_type может принимать только значения org и fiz
                    if (empty($data['person_type']) || !in_array($data['person_type'], array('org', 'fiz'))){
                        $errors['person_type'] = \Models\Validator::ERR_MSG_INCORRECT_FORMAT;
                    } else {
                        $update_fields['role'] = $data['role'];
                        $update_fields['person_type'] = $data['person_type'];
                    }
                } else {
                    // у всех остальных - только man
                    $update_fields['role'] = $data['role'];
                    $update_fields['person_type'] = 'man';
                }
            }
        }
        // Параметры валидации. Если удаляем пользователя ($data['status'] == 'deleted') — валидация не нужна, отправляем пустой массив
        if (count($data) == 1 && isset($data['status'])) {
            // При смене статуса пользователя валидация не нужна, просто передаем статус модели пользователя
            $update_fields = $data;
        } else {
            $validation_params = self::getValidationParams(
                    $this->userObj,
                    !empty($update_fields['role']) ? $update_fields['role'] : NULL,
                    !empty($update_fields['person_type']) ? $update_fields['person_type'] : NULL
                );
            $update_fields = array_merge($update_fields, self::validateFields($data, $validation_params, $errors, $this->userObj->asArray()));
        }
        return $this->userObj->update($update_fields, $errors);
    }

    public function delete(){
        if ($this->context == self::CONTEXT_READ_ONLY){
            throw new \LogicException('Невозможно отредактировать пользователя, выбран контекст #read_only');
        }
        $this->userObj->delete();
    }

    public function isInstanceOf($className) {
        return $this->userObj instanceof $className;
    }

//    public static function createUser($data, &$errors = array(), $check_pass2 = false){
//        return RegistratedUser::createUser($data, $errors, $check_pass2);
//    }

    public function __call($name, $arguments){
        return call_user_func_array(array($this->userObj, $name), $arguments);
    }

    public static function __callStatic($name, $arguments) {
        return call_user_func_array(RegistratedUser::$name, $arguments);
    }

    public function __toString(){
        $name = $this->userObj['name'] . (!empty($this->userObj['surname']) ? ' ' . $this->userObj['surname'] : '');
        return !empty($name) ? $name : 'User#' . $this->userObj['id'];
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return $this->userObj->offsetExists($offset);
    }
    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->userObj->offsetGet($offset);
    }
    /**
     * @param string $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value) {
        $this->userObj->offsetSet($offset, $value);
    }
    /**
     * @param string $offset
     * @throws \Exception
     */
    public function offsetUnset($offset) {
        $this->userObj->offsetUnset($offset);
    }
}