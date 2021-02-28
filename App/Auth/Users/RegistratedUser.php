<?php
namespace App\Auth\Users;

use App\Configs\CatalogConfig;
use App\Auth\Users\Helpers\iRegistratedUserHelper;

abstract class RegistratedUser implements \ArrayAccess{
	const TABLE_USERS_BONUS_LOG = 'user_bonus_log';
    const STATUS_ACTIVE = 'active';
    const STATUS_BANNED = 'banned';
    const STATUS_EXPIRED = 'expired';
    const STATUS_DELETED = 'deleted';
	const STATUS_NEW = 'new';
    
    const TABLE_USERS = 'users';
    const TABLE_ADDRESS = 'users_address';
	
    protected $data = array();
    
    protected $access_roles = array('Admin', 'SuperAdmin');

    protected $loadFields = array('id', 'role', 'role_title', 'pass_hash', 'name', 'surname', 'status', 'email', 'auth', 'email_valid', 'person_type', 'reg_date', 'referer', 'referal_number', 'subscribe', 'last_update', 'external_id', 'bonus', 'last_update_bonus');
    protected $updateFields = array('name', 'surname', 'email', 'role', 'status', 'auth', 'person_type', 'referer', 'subscribe', 'external_id', 'bonus', 'last_update_bonus');
    protected static $customFields = array(

        );
    protected $additionalFields = array('segment', 'reg_timestamp', 'addresses', 'image', 'socialAuth');
    protected $statusList = array(self::STATUS_ACTIVE, self::STATUS_BANNED, self::STATUS_EXPIRED, self::STATUS_DELETED);
    protected $db;
    protected $validator = NULL;

    protected static $edit_user_form_fields = array();

    /**
     * @TODO Куда это все воткнуть?
     * @return array
     */
    public static function getEditUserFormFields(){
        return static::$edit_user_form_fields;
    }

    /**
     * Функция создания объектов используется только в классе Factory
     * @param Factory $f
     * @param array $data
     * @param string $userClass
     * @return RegistratedUser
     */
    public static function _makeUser(Factory $f, array $data, $userClass){
        return new $userClass($data);
    }

    protected function __construct($data){
        foreach ($this->loadFields as $field) {
            if (!array_key_exists($field, $data)){
                throw new \LogicException('Переданы не все данные для создания пользователя. Не передано поле "' . $field . '"');
            }
            $this->data[$field] = !empty($data[$field]) ? $data[$field] : null;
        }
        $this->data['socialAuth'] = Factory::getUserSocialAuthData($this->data['id']);
        $this->db = \App\Builder::getInstance()->getDB();
        $this->validator = \Models\Validator::getInstance(\App\Builder::getInstance()->getRequest());
    }
    /**
     * Проверка класса пользователя через instanceof
     * @param $className
     * @return bool
     */
    public function isInstanceOf($className){
        return $this instanceof $className;
    }

    public function checkFields($params){
        $allow_params = array();
        foreach ($this->updateFields as $key){
            if (isset($params[$key])){
                $allow_params[$key] = $params[$key];
            }
        }
        return $allow_params;
    }

    public function getAccount(\App\Auth\Controller $controller){
        if (!is_null($this->data['role'])) {
            $userClass = '\App\Auth\Account\\' . $this['role'];
            $filePath = \App\Builder::getInstance()->getConfig()->getRealDocumentRoot() . '/App/Auth/Account/' . $this->data['role'] . '.php';
            if (file_exists($filePath) && class_exists($userClass)) {
                return \App\Auth\Account\AuthorizedAccount::_makeAccount($controller, $this, $userClass);
            }
        }
        return null;
    }

    public function isActive(){
        if ($this->data['status'] == 'active'){
            return true;
        }
        return false;
    }

    public function getId() {
        return $this->data['id'];
    }

    public function getRole() {
        return $this->data['role'];
    }

	public function getRoleTitle(){
		return $this->data['role_title'];
	}

    public function getEmail() {
        return $this->data['email'];
    }

    public function getName() {
        return $this->data['name'];
    }

    public function getPassword() {
        return $this->data['pass_hash'];
    }

    public function getAuth(){
        return $this->data['auth'];
    }

    public function getEmailValid(){
        return $this->data['email_valid'];
    }
    public function getSegmentId(){
        return !empty($this->data['segment_id']) ? $this->data['segment_id'] : NULL;
    }
    public function getSegment(){
        return !empty($this->data['segment_id']) ? \Models\Region::getById($this->data['segment_id']) : NULL;
    }
    public function getPersonType(){
        if (is_null($this->data['person_type'])){
            $this->person_type = 'man';
        }
        return $this->data['person_type'];
    }
    public function getAddresses(){
        return $this->db->query('SELECT `id`, `address` FROM `'.self::TABLE_ADDRESS.'` WHERE `user_id` = ?d', $this->getId())->getCol('id', 'address');
    }
    public function getSubscribe(){
        return $this->data['subscribe'];
    }

    /**
     * Редактирование учетной записи
     * @param array $data
     * @param array $errors
     * @return boolean
     */
    public function update($data, &$errors = NULL){
        if (!empty($errors)){
            return FALSE;
        } else {
            return $this->editUser($data, true, $errors);
        }
    }
    /**
     * @param array $data
     * @return bool
     */
    private function editUser($data, $need_check = true, &$errors = array()){
		$db = \App\Builder::getInstance()->getDB();
        $helper_need_update = FALSE;
        foreach(self::$dataProviders as $data_provider){
            /** @var $data_provider iRegistratedUserHelper */
            $helper_need_update = $helper_need_update || $data_provider->preUpdate($this, $data, $this->getSegmentId(), $errors);
        }
        $params = $need_check ? $this->checkFields($data) : $data;
        if ((!empty($params) || $helper_need_update) && empty($errors)){
            $old_data = $this->asArray();
			$roles = \Models\Roles::getInstance()->get(null, 'id');
            if (!empty($params['role']) && !is_numeric($params['role'])){
				$role_entity = \Models\Roles::getInstance()->get($params['role']);
				$params['role'] = $role_entity['id'];
                }
            if (!empty($params)){
                $db->query('UPDATE `users` SET ?a, `last_update` = NOW() WHERE `id`=?d', $params, $this->getId());
                foreach ($params as $field => $val){
                    if ($field == 'role'){
                        $val = $roles[$val]['key'];
                    }
                    $this->data[$field] = $val;
                }
            }
            foreach(self::$dataProviders as $data_provider){
                /** @var $data_provider iRegistratedUserHelper */
                $data_provider->onUpdate($this, $old_data, $this->getSegmentId());
            }
            /**
             * Обновление данных подписчика в subscribe.pro
             * @TODO Утащить в хелперы
             */
            if ($this['person_type'] != 'man' && $this->checkSubscribeFieldsChanged($params)){
                $subscribe_member = \Models\SubscribeManagement\SubscribeMember::getByEmail($this['email']);
                if (!empty($subscribe_member)){
                    $subscribe_member->edit(array(
                        'name' => $this['name'],
                        'surname' => $this['surname'],
                        'company_name' => $this['company_name'],
                        'inner' => 1
                    ), TRUE);
                } else {
                    $sc = \Models\SubscribeManagement\SubscribeController::getInstance();
                    $sc->setMember(array(
                        'email' => !empty($this['email']) ? $this['email'] : '',
                        'name' => !empty($this['name']) ? $this['name'] : '',
                        'surname' => !empty($this['surname']) ? $this['surname'] : '',
                        'company_name' => !empty($this['company_name']) ? $this['company_name'] : '',
                        'inner' => 1
                    ));
                    if (empty($this->subscribe)) {
                        $sc->setUserSubscribeStatus($this['email'], 0);
                    }
                }
            }
            return true;
        }
        return false;
    }
    
    public function delete(){
        foreach(self::$dataProviders as $data_provider){
            $data_provider->onDelete($this->data['id']);
        }
        $this->db->query('DELETE FROM `' . self::TABLE_USERS . '` WHERE `id`=?d', $this->data['id']);
    }
    
    public static function cleanup(){
        $users = Factory::getInstance(\App\UserContainer::CONTEXT_ADMIN)->getUsers(array('status' => self::STATUS_DELETED));
        foreach($users as $user){
            $user->delete();
        }
    }
    /**
     * Проверяет необходимость обновления данных подписчика (были ли затронуты поля, используемые в подписках)
     * @param array $fields
     * @return boolean
     * @TODO Утащить в хелперы
     */
    private function checkSubscribeFieldsChanged($fields){
        $required_fields = \Models\SubscribeManagement\SubscribeController::getSubscriberFields();
        foreach($required_fields as $key){
            if (isset($fields[$key])){
                return TRUE;
            }
        }
        return FALSE;
    }
    public function setImage($FILE){
        if (!empty($this->data['image_id'])){
            $image = \Models\ImageManagement\Image::getById($this->image_id);
            $image->reload($FILE);
        } else {
            $image = \Models\ImageManagement\Image::add($FILE);
            $this->editUser(array('image_id' => $image['id']));
        }
    }
    
    public function deleteImage(){
        if (empty($this['image_id'])){
            return FALSE;
        } else {
            $this->editUser(array('image_id' => NULL), false, $errors);
            if (empty($errors)){
                $result = \Models\ImageManagement\Image::del($this['image_id']);
                return $result == 'already_deleted' ? FALSE : TRUE;
            } else {
                return FALSE;
            }
        }
    }

    /**
     * При смене email надо менять и пароль
     * @param sting $email
     * @param string $pass
     * @return bool|FALSE|int|\MysqlSimple\Result
     */
    public function setEmail($email, $pass){
        $this->email = $email;
        $this->editUser(array('email'=>$email), false);
        return $this->changePassword($pass);
    }
    public function setAuth($auth = ''){
        return $this->editUser(array('auth'=>$auth), false);
    }

    /**
     * Привязывает аккаунт соцсети для аутентификации
     * @param string $network
     * @param string $identity
     * @param string $error
     * @return bool
     */
    public function setSocialAuth($network, $identity, &$error = NULL){
        if (!empty($this->data['socialAuth'][$network])){
            $error = 'already_set';
            return FALSE;
        }
        if (!Factory::isSocialIdentityFree($network, $identity)){
            $error = 'already_used';
            return FALSE;
        }
        $this->db->query('INSERT INTO `' . Factory::TABLE_USER_SOCIAL_DATA . '` SET `user_id` = ?d, `network` = ?s, `identity` = ?s', $this->data['id'], $network, $identity);
        $this->data['socialAuth'][$network] = $identity;
        return TRUE;
    }

    /**
     * Отвязывает аккаунт соцсети
     * @param string $network
     * @return bool
     */
    public function deleteSocialAuth($network){
        if (empty($this->data['socialAuth'][$network])){
            return FALSE;
        }
        $this->db->query('DELETE FROM `' . Factory::TABLE_USER_SOCIAL_DATA . '` WHERE `user_id` = ?d AND `network` = ?s', $this->data['id'], $network);
        unset($this->data['socialAuth'][$network]);
        return TRUE;
    }

    public function setEmailValid($valid = false){
        $this->email_valid = $valid;
        return $this->editUser(array('email_valid' => !empty($valid) ? 1 : 0), false);
    }
    
    public function setSubscribe($subscribe){
        $this->subscribe = $subscribe;
        return $this->editUser(array('subscribe' => $subscribe));
    }

    public function setStatus($status){
        if (!in_array($status, $this->statusList)){
            return false;
        }
        $this->status = $status;
        return $this->editUser(array('status' => $status, 'auth' => ''), false);
    }
    public function setSegment($id){
        if (!empty($id) && $id != $this->getSegmentId()){
            $region = \Models\Region::getById($id);
            if (!empty($region)){
                //TODO onChangeRegion
                return $this->editUser(array('region_id' => $id));
            }
        }
    }
	private function setBonus($bonus){
//        if ($bonus > $this['bonus']){
//            $this->all_bonus = (empty($this->all_bonus) ? 0 : $this->all_bonus) + ($bonus - $this['bonus']);
//			$this['bonus']_sum = (empty($this['bonus']_sum) ? 0 : $this['bonus']_sum) + ($bonus - $this['bonus']);
//        }
//        $this['bonus'] = $bonus;
//        $this->last_update_bonus = date('Y-m-d H:i:s');
        return $this->update(array('bonus' => $bonus, 'last_update_bonus' => date('Y-m-d H:i:s')));
    }
    /**
     * Увеличение количества бонусов. Все поля обязательны
     * @param int $sum
     * @param int $user_id кто добавил
     * @param string $comment
     * @param string $error
     * @return bool
     */
    public function increaseBonus($sum, $user_id, $comment, &$error){
		if ($sum < 0 || !is_numeric($sum)){
			$error = 'Неверно задано число бонусов';
			return;
		}
        $result = $this->setBonus($this['bonus'] + $sum);
        if ($result){
            $db = \App\Builder::getInstance()->getDB();
            $db->query('INSERT INTO `'.self::TABLE_USERS_BONUS_LOG.'` SET `date` = NOW(), `manager_id` = ?d, `value` = ?d, `comment` = ?s, `user_id` = ?d', $user_id, $sum, $comment, $this['id']);
        }
		return $result;
	}
    /**
     * Уменьшение количества бонусов. Все поля обязательны
     * @param int $sum
     * @param int $user_id кто отнял
     * @param string $comment
     * @param string $error
     * @return bool
     */
	public function decreaseBonus($sum, $user_id, $comment, &$error){
		if ($sum < 0 || !is_numeric($sum)){
			$error = 'Неверно задано число бонусов';
			return;
		}
		if ($sum > $this['bonus']){
			$error = 'Число бонусов пользователя меньше вычитаемого количества';
			return;
		}
        $result = $this->setBonus($this['bonus'] - $sum);
        if ($result){
            $db = \App\Builder::getInstance()->getDB();
            $db->query('INSERT INTO `'.self::TABLE_USERS_BONUS_LOG.'` SET `date` = NOW(), `manager_id` = ?d, `value` = ?d, `comment` = ?s, `user_id` = ?d', $user_id, -$sum, $comment, $this['id']);
        }
		return $result;
	}
    public function updateAddress($id, $text){
        if (!empty($text)){
            return $this->db->query('UPDATE `'.self::TABLE_ADDRESS.'` SET `address` = ?s WHERE `id` = ?d', $text, $id);
        }else{
            return $this->db->query('DELETE FROM `'.self::TABLE_ADDRESS.'` WHERE `id` = ?d', $id);
        }
    }

    public function setNewAddress($address){
        if (empty($address)){
            return false;
        }
        return $this->db->query('INSERT INTO `'.self::TABLE_ADDRESS.'` SET `user_id` = ?d, `address` = ?s', $this->getId(), $address);
    }

    public function deleteAddress($id){
        return $this->db->query('DELETE FROM `'.self::TABLE_ADDRESS.'` WHERE `id` = ?d', $id);
    }

    public function changePassword($new_pass){
        if(!empty($new_pass)){
            $hash = \App\Auth\Controller::getPassHash($new_pass, $this->getEmail());
            return $this->db->query('UPDATE `users` SET `pass_hash` = ? WHERE `id` = ?d', $hash, $this->getId());
        }
        return false;
    }

    public function asArray(){
        $data = array();
        foreach ($this->loadFields as $f){
            $data[$f] = $f == 'person_type' && is_null($this->data[$f]) ? 'man' : $this->data[$f];//null для админов
        }
        $data['addresses'] = $this->getAddresses();
        foreach (self::$dataProvidersByFields as $field => $provider){
            $data[$field] = $provider->get($this, $field);
        }
        return $data;
    }
	/**
	 * Получить цену позиции заказа у конкретного пользователя
	 * @param \Models\CatalogManagement\iOrderData $variant
	 */
	public function getOrderPositionPrice(\Models\CatalogManagement\Variant $variant){
		return !empty($variant[CatalogConfig::KEY_VARIANT_PRICE]) ? $variant[CatalogConfig::KEY_VARIANT_PRICE] : NULL;
	}
    /**
     * Узнать процент бонусов в данном сегменте у данного пользователя
     * @param int $segment_id
     * @return int
     * @throws \LogicException
     */
    public function getBonusRatio($segment_id = NULL){
        return \App\Configs\OrderConfig::getParameter(\App\Configs\Settings::KEY_ORDER_BONUS_RATIO);
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return in_array($offset, $this->loadFields) || in_array($offset, $this->additionalFields) || isset(self::$dataProvidersByFields[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        if (in_array($offset, array_merge($this->loadFields, $this->additionalFields))){
            if ($offset == 'segment'){
                return !empty($this->data['segment_id']) ? \Models\Region::getById($this->data['segment_id']) : NULL;
            }elseif($offset == 'image'){
                return \Models\ImageManagement\Image::getById($this['image_id']);
            }elseif(array_key_exists($offset, $this->data)){
                return $this->data[$offset];
            }
        } elseif (!empty(self::$dataProvidersByFields[$offset])){
            return self::$dataProvidersByFields[$offset]->get($this, $offset);
        } else {
            throw new \LogicException('Field ' . $offset . ' not exists');
        }
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
    
    /******************************* работа с iRegistratedUserHelper *****************************/
    /**
     * @var iRegistratedUserHelper[]
     */
    private static $dataProviders = array();
    /**
     * @var iRegistratedUserHelper[]
     */
    private static $dataProvidersByFields = array();

    /**
     * @static
     * @param iRegistratedUserHelper $provider
     */
    static function addDataProvider(iRegistratedUserHelper $provider){
        self::$dataProviders[get_class($provider)] = $provider;
        foreach ($provider->fieldsList() as $field){
            self::$dataProvidersByFields[$field] = $provider;
        }
    }

    /**
     * @static
     * @param iRegistratedUserHelper $provider
     */
    static function delDataProvider(iRegistratedUserHelper $provider){
        unset(self::$dataProviders[get_class($provider)]);
    }

    /**
     * Собираем правила для поиска по хелперам пользователей
     * @param $params
     */
    public static function preSearch(&$params, &$order){
        if (!empty(self::$dataProviders)){
            foreach(self::$dataProviders as $provider){
                $use_in_search = false;
                $ids = $provider->preSearch($params, $order, $use_in_search);
                if ($use_in_search){
                    $params['ids'] = array_key_exists('ids', $params) ? array_intersect($ids, (is_array($params['ids']) ? $params['ids'] : array($params['ids']))) : $ids;
                }
            }
        }
    }

    public static function prepareHelpers(array $ids){
        if (!empty(self::$dataProviders)){
            foreach(self::$dataProviders as $provider){
                $provider->prepare($ids);
            }
        }
    }

    public static function getDataProviders(){
        return self::$dataProviders;
    }
}