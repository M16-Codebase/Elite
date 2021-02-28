<?php
/**
 * фабрика объектов пользователей
 */
namespace App\Auth\Users;
use App\Builder;
use App\UserContainer;

class Factory{
	const TABLE = 'users';
	const TABLE_CUSTOM_FIELDS = 'users_custom_fields';
    const TABLE_USER_SOCIAL_DATA = 'users_social_data';
    /**
     * Максимальная емкость реестра
     */
    const MAX_REGISTRY_LEN = 1000;
    /** @var Factory[] */
    private static $instance = array();

    private static $social_auth_data = array();

    private $loadIds = array();
    /**
     * @var UserContainer[] Кэш контекстных контейнеров пользователей
     */
    private $container_registry = array();
    /**
     * @var RegistratedUser[] Кэш пользователей (в контекстные обертки мы заворачиваем одного и того же пользователя,
     *                      таким образом пользователь, отредактированный в одном контесте, становится таким же и в другом)
     */
    private static $user_registry = array();

    private $context = NULL;
    /**
     * @param string $context
     * @return Factory
     */
    public static function getInstance($context = UserContainer::CONTEXT_READ_ONLY){
        if (!UserContainer::checkAllowedContext($context)){
            throw new \LogicException("Недопустимое значение контекста UserContainer — $context");
        }
        if (empty(self::$instance[$context])){
            self::$instance[$context] = new Factory($context);
        }
        return self::$instance[$context];
    }

    public static function clearRegistry($ids = array()){
        $ids = is_array($ids) ? $ids : array($ids);
        if (!\LPS\Config::isCLI()){
            // Из реестра нельзя вычистить самого себя
            $user = \App\Builder::getInstance()->getAccount()->getUser();
            if (!empty($user)){
                $ids = !empty($ids) ? $ids : array_keys(self::$user_registry);
                if(($key = array_search($user['id'], $ids)) !== false) {
                    unset($ids[$key]);
                }
                if (empty($ids)){
                    return;
                }
            }
        }
        if (!empty(self::$instance)){
            foreach(self::$instance as $i){
                $i->clearContainerRegistry($ids);
            }
        }
        if (empty($ids)) {
            self::$user_registry = array();
        } else {
            foreach($ids as $id){
                unset(self::$user_registry[$id]);
            }
        }
    }


    protected function clearContainerRegistry($ids = array()){
        if (empty($ids)) {
            $this->container_registry = array();
        } else {
            foreach($ids as $id){
                unset($this->container_registry[$id]);
            }
        }
    }

    /**
     * @param $context
     */
    private function __construct($context){
        $this->context = $context;
    }
    /**
     *
     * @param array $ids
     */
    public function prepare(array $ids){
        if (!empty($ids)){
            $ids = array_diff($ids, array_keys($this->container_registry), $this->loadIds);
            if (!empty($ids)){
                $this->loadIds = array_merge($ids, $this->loadIds);
            }
        }
    }

    /**
     * ищет и создает пользователя по id или параметрам
     * @param int $id
     * @param array $params
     * @return \App\Auth\Users\RegistratedUser
     */
    public function getUser($id = null, $params = array()){
        if (!empty($id)){
            $params['ids'] = array($id);
        }
        $users = $this->getUsers($params);
        return reset($users);
    }
    /**
     * ищет и создает пользователя по id
     * @param int $id
     * @return \App\Auth\Users\RegistratedUser
     */
    public function getUserById($id){
        $result = $this->factory(array($id));
        return reset($result);
    }
    
    public function getUserByEmail($email){
        return $this->getUser(NULL, array('email' => $email));
    }

    public function getUserBySocialIdentity($network, $identity){
        return $this->getUser(NULL, array('social_network' => $network, 'social_auth_identity' => $identity));
    }
    /**
     * создает пользователя на основании данных
     * @param array $data
     * @return \App\Auth\Users\RegistratedUser
     */
    private function makeUser($data){
        if (!empty($data['role'])){
            $userClass = '\App\Auth\Users\\' . $data['role'];
            if ($data['role'] == 'User'){
                $userClass .= ucfirst($data['person_type']);
            }
            if (class_exists($userClass)){
                return RegistratedUser::_makeUser($this, $data, $userClass);
            }
        }
        return null;
    }
    /**
     * создает пользователей на основании данных
     * @param array $data array('num' => array(data))
     */
    private function makeUsers($data){
        foreach ($data as $num => $d){
            $user = $this->makeUser($d);
            if (!empty($user)){
                self::$user_registry[$d['id']] = $user;
            }
        }
    }

    /**
     * ищет и создаёт пользователей по ids
     * @param array $params
     * @param int $count
     * @throws \LogicException
     * @return \App\Auth\Users\RegistratedUser[]
     */
    public function getUsers($params = array(), &$count = 0){
        $db = \App\Builder::getInstance()->getDB();
        if (!empty($params['person_type'])){
            $params['type'] = $params['person_type'];
        }
        RegistratedUser::preSearch($params, $order);
        if (!empty($params['order'])){
            if (is_array($params['order'])){
                foreach ($params['order'] as $key => $desc){
                    $order[] = $key . (!empty($desc) ? ' DESC ' : ' ');
                }
            }else{
                throw new \LogicException('Order param must be an array("key" => "desc")');
            }
        }
        $order_part = !empty($order) ? implode(', ', $order) : '';
        $foundIds = $db->query('
            SELECT SQL_CALC_FOUND_ROWS `u`.`id`
            FROM `'.self::TABLE.'` AS `u`
            INNER JOIN `'.\Models\Roles::TABLE.'` AS `r` ON (`r`.`id` = `u`.`role`)
            LEFT JOIN `' . self::TABLE_USER_SOCIAL_DATA . '` AS `soc` ON (`u`.`id` = `soc`.`user_id`)
            WHERE 1
            { AND `u`.`id` IN (?i)}
            { AND `u`.`email` IN (?l)}
            { AND `u`.`company_name` = LIKE ?s}
            { AND `r`.`key` IN (?l)}
            { AND `r`.`key` NOT IN (?l)}
            { AND `u`.`segment_id` = ?d}
            { AND `u`.`status` IN (?l)}
            { AND (`u`.`person_type` = ?s ' . (!empty($params['type']) && $params['type'] == 'man' ? ' OR `u`.`person_type` IS NULL' : '') .' )}
            { AND `u`.`phone` LIKE ?s}
            { AND `u`.`name` LIKE ?s}
            { AND `u`.`surname` LIKE ?s}
            { AND `u`.`referal_number` = ?s}
            { AND `u`.`referer` = ?d}
            { AND `u`.`reg_date` > ?s}
            { AND `reg_date` < ?s}
            { AND `u`.`reg_date` > ?s}
            { AND `reg_date` < ?s}
            { AND `u`.`inn` = ?s}
            { AND `u`.`subscribe` = ?d}
            { AND `u`.`show_in_contacts` = ?d}
            { AND `u`.`last_update` > ?s}
            { AND `soc`.`identity` = ?s}
            { AND `soc`.`network` = ?s}
            ' . (!empty($order_part) ? ('ORDER BY ' . $order_part) : '') . '
            { LIMIT {?d, }?d}',
                !empty($params['ids']) ? (is_array($params['ids']) ? $params['ids'] : array($params['ids'])) : $db->skipIt(),
                !empty($params['email']) ? (is_array($params['email']) ? $params['email'] : array($params['email'])) : $db->skipIt(),
                !empty($params['company_name']) ? ('%' . $params['company_name'] . '%') : $db->skipIt(),
                !empty($params['role']) ? (is_array($params['role']) ? $params['role'] : array($params['role'])) : $db->skipIt(),
                !empty($params['no_role']) ? (is_array($params['no_role']) ? $params['no_role'] : array($params['no_role'])) : $db->skipIt(),
                !empty($params['region_id']) ? $params['region_id'] : $db->skipIt(),
                !empty($params['status']) ? (is_array($params['status']) ? $params['status'] : array($params['status'])) : $db->skipIt(),
                !empty($params['type']) ? $params['type'] : $db->skipIt(),
                !empty($params['phone']) ? ('%' . preg_replace('~[^0-9]~', '', $params['phone']) . '%') : $db->skipIt(),
                !empty($params['name']) ? $params['name'] : $db->skipIt(),
                !empty($params['surname']) ? $params['surname'] : $db->skipIt(),
                !empty($params['referal_number']) ? $params['referal_number'] : $db->skipIt(),
                !empty($params['referer']) ? $params['referal'] : $db->skipIt(),
                !empty($params['date_min']) ? date('Y-m-d', strtotime($params['date_min'])) : $db->skipIt(),
                !empty($params['date_max']) ? date('Y-m-d', strtotime('+1 day', strtotime($params['date_max']))) : $db->skipIt(),
                !empty($params['reg_date']) ? date('Y-m-d', strtotime($params['reg_date'])) : $db->skipIt(),
                !empty($params['reg_date']) ? date('Y-m-d', strtotime('+1 day', strtotime($params['reg_date']))) : $db->skipIt(),
                !empty($params['inn']) ? $params['inn'] : $db->skipIt(),
                !empty($params['subscribe']) ? $params['subscribe'] : $db->skipIt(),
                !empty($params['show_in_contacts']) ? 1 : $db->skipIt(),
                !empty($params['last_update']) ? $params['last_update'] : $db->skipIt(),
                !empty($params['social_auth_identity']) ? $params['social_auth_identity'] : $db->skipIt(),
                !empty($params['social_network']) ? $params['social_network'] : $db->skipIt(),
                !empty($params['start']) ? $params['start'] : $db->skipIt(),
                !empty($params['limit']) ? $params['limit'] : $db->skipIt()
            )->getCol('id', 'id');
        $count = $db->query('SELECT FOUND_ROWS()')->getCell();
        return $this->factory($foundIds);
    }

    /**
     * @param array $ids
     * @return UserContainer[]
     */
    public function factory(array $ids){
        $getIds = array_unique(array_merge($ids, $this->loadIds));
        if (count(self::$user_registry) + count($getIds) > self::MAX_REGISTRY_LEN){
            self::clearRegistry();
        }
        if (!empty(self::$user_registry)){
            $getIds = array_diff($getIds, array_keys(self::$user_registry));
        }
        if (!empty($getIds)){
            $userData = \App\Builder::getInstance()->getDB()->query('
                SELECT SQL_CALC_FOUND_ROWS `u`.*, UNIX_TIMESTAMP(`u`.`expired`) AS `expired_timestamp`,
                   `r`.`key` AS `role`, `r`.`title` AS `role_title`
                FROM `'.self::TABLE.'` AS `u`
                INNER JOIN `'.\Models\Roles::TABLE.'` AS `r` ON (`r`.`id` = `u`.`role`)
                LEFT JOIN `' . self::TABLE_USER_SOCIAL_DATA . '` AS `soc` ON (`u`.`id` = `soc`.`user_id`)
                WHERE `u`.`id` IN (?i)', $getIds)->select('id');
            if (!empty($userData)){
                RegistratedUser::prepareHelpers(array_keys($userData));
            }
            $this->loadSocialAuthData(array_keys($userData));
            $this->makeUsers($userData);
        }
        $result = array();
        foreach ($ids as $id_result){
            if (!empty($this->container_registry[$id_result])){
                $result[$id_result] = $this->container_registry[$id_result];
            } elseif (!empty(self::$user_registry[$id_result])){
                $user_container = new UserContainer(self::$user_registry[$id_result], $this->context);
                $this->container_registry[$id_result] = $user_container;
                $result[$id_result] = $user_container;
            }
        }
        return $result;
    }
    /**
     * @param array $ids
     */
    private function loadSocialAuthData(array $ids){
        $db = Builder::getInstance()->getDB();
        $loaded_ids = array_keys(self::$social_auth_data);
        $all_ids = array_merge($loaded_ids, $ids);
        $ids = array_diff($all_ids, $loaded_ids);
        if (!empty($ids)){
            self::$social_auth_data = self::$social_auth_data + $db->query('SELECT `user_id` AS `id`, `identity`, `network` FROM `' . self::TABLE_USER_SOCIAL_DATA . '` WHERE `user_id` IN (?i)', $ids)->getCol(array('id', 'network'), 'identity');
        }
    }

    public static function getUserSocialAuthData($user_id){
        return !empty(self::$social_auth_data[$user_id]) ? self::$social_auth_data[$user_id] : array();
    }

    /**
     * Проверяет привязан ли аккаунт соцсети к какому-либо аккаунту
     * @param string $network
     * @param string $identity
     * @return bool TRUE если аккаунт свободен
     */
    public static function isSocialIdentityFree($network, $identity){
        return Builder::getInstance()->getDB()->query('SELECT 1 FROM `' . self::TABLE_USER_SOCIAL_DATA . '` WHERE `network` = ?s AND `identity` = ?s', $network, $identity)->getCell() ? FALSE : TRUE;
    }
    /**
     * Возвращает объект гостя
     * @return \App\Auth\Account\Guest
     */
    public function getGuest(){
        return new \App\Auth\Account\Guest();
    }

    public function createUser($data, &$errors = array()){
        if ($this->context == UserContainer::CONTEXT_READ_ONLY){
            throw new \LogicException('Невозможно создать пользователя, выбран контекст #read_only');
        }
        $db = \App\Builder::getInstance()->getDB();
        $user_fields = array();
        $validator = \Models\Validator::getInstance(\App\Builder::getInstance()->getRequest());
        $user_fields['email'] = $validator->checkValue($data['email'], 'checkEmail', $errors['email'], array('uniq' => true));
        $pass = $validator->checkValue($data['pass'], 'checkString', $errors['pass'], array('count_min' => 4));
        $validator->isErrorsEmpty($errors); // Очищаем пустые ошибки
        if (!$user_fields['email'] || !$pass){
            return false;
        } elseif (empty($data['pass2']) || $pass != $data['pass2']){
            $errors['pass2'] = 'not_same';
            return false;
        }
        $creation_hash = md5(time() . $user_fields['email']); // Хэш для хелперов
        $user_fields['pass_hash'] = \App\Auth\Controller::getPassHash($pass, $user_fields['email']);
        do{
            $user_fields['referal_number'] = \App\Auth\Controller::randomPassword(\App\Auth\Controller::REFERAL_COUNT_SIMBOLS, \App\Auth\Controller::REFERAL_ALLOW_SIMBOLS);
        } while ($db->query('SELECT 1 FROM `'.RegistratedUser::TABLE_USERS.'` WHERE `referal_number` = ?s', $user_fields['referal_number'])->getCell());
        UserContainer::preCreate($this->context, $data, $user_fields, $errors);
        $dataProviders = RegistratedUser::getDataProviders();
        foreach($dataProviders as $data_provider){
            /** @var $data_provider Helpers\iRegistratedUserHelper */
            $data_provider->preCreate($user_fields, $errors, $creation_hash);
        }
        if (empty($errors)){
            $user_fields['status'] = RegistratedUser::STATUS_ACTIVE;
            $user_fields['reg_ip'] = \LPS\Config::isCLI() ? '127.0.0.1' : \App\Builder::getInstance()->getRequest()->server->get('REMOTE_ADDR');
            $id = $db->query('INSERT INTO `' . RegistratedUser::TABLE_USERS . '` SET ?a, `reg_date`=NOW()', $user_fields);
            if (empty($id)) {
                throw new \Exception('Не удалось зарегистрировать пользователя');
            }
            foreach($dataProviders as $data_provider){
                /** @var $data_provider Helpers\iRegistratedUserHelper */
                $data_provider->onCreate($id, $creation_hash);
            }
            return $id;
        }
        return false;
    }
}
?>