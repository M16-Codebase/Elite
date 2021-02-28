<?php
/**
 * Контроллер регистрации и аутентификации
 */
namespace App\Auth;
use Symfony\Component\HttpFoundation\Cookie;
use App\Auth\Users\Factory;
class Controller{
    const PASS_LENGTH = 8;
    const REFERAL_ALLOW_SIMBOLS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    const REFERAL_COUNT_SIMBOLS = 6;
	const TABLE = Factory::TABLE;
	private $allowAllUsersUpdateFields = array('import');
    /** @var Controller */
    private static $instance = null;
    /** @var \MysqlSimple\Controller */
    protected $db = null;
    /**
     * 
     * @return self
     */
    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new Controller();
        }
        return self::$instance;
    }

    protected function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
    }

    /**
     * Регистрация пользователя
     * @param string $email
     * @param string $pass
     * @param string $role
     * @return bool
     * @throws \Exception
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     * @TODO избавится? переделать?
     */
    public function regUser($email=null, $pass=null, $role=null) {
        if (!$email || !$pass)
            return false;
        $pass_hash = self::getPassHash($pass, $email);
        $new_ref_num = self::randomPassword(self::REFERAL_COUNT_SIMBOLS, self::REFERAL_ALLOW_SIMBOLS);
        while ($this->db->query('SELECT 1 FROM `'.self::TABLE.'` WHERE `referal_number` = ?s', $new_ref_num)->getCell()){
            $new_ref_num = self::randomPassword(self::REFERAL_COUNT_SIMBOLS, self::REFERAL_ALLOW_SIMBOLS);
        }
//		if (empty($role)){
//			throw new \Exception('Не задана роль пользователя');
//		}
		$role = \Models\Roles::getInstance()->get(!empty($role) ? $role : \LPS\Config::DEFAULT_ROLE);
		if (empty($role)){
			throw new \Exception('В системе не найдена роль: "' . $role . '"');
		}
        $rec = array(
            'email' => $email,
            'pass_hash' => $pass_hash,
            'reg_ip' => \LPS\Config::isCLI() ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'],
            'role' => $role['id'],
            'status' => Users\RegistratedUser::STATUS_ACTIVE,
            'referal_number' => $new_ref_num
        );
        $id = $this->db->query('INSERT INTO `users` SET ?a, `reg_date`=NOW()', $rec);
//		$this->db->query('INSERT INTO `sites_roles` SET `site_id` = ?d, `user_id` = ?d, `role_id` = ?d', \App\Builder::getInstance()->getConfig()->getParametr('site', 'site_id'), $id, $role['id']);
        if (empty($id)) {
            throw new \Exception('Не удалось зарегистрировать пользователя');
        }
        return true;
    }
	
	public function setParamsToAllUsers($params = array()){
		if (empty($params)){
			return;
		}
		foreach ($params as $f => $v){
			if (!in_array($f, $this->allowAllUsersUpdateFields)){
				throw new \Exception('Нельзя записывать поле '.$f.' для всех пользователей');
			}
		}
		$db = \App\Builder::getInstance()->getDB();
		$db->query('UPDATE `'.self::TABLE.'` SET ?a', $params);
	}

    /**
     * Аутентификация
     * @param string $email
     * @param string $password
     * @param string $error
     * @return \App\Auth\Users\RegistratedUser|false
     */
    public function authenticate($email, $password, &$error = NULL, $context = \App\UserContainer::CONTEXT_READ_ONLY) {
        //проверки
        $user = Users\Factory::getInstance($context)->getUserByEmail($email);
        if (empty($user)) {
            $error = 'wrong_email';
            return false;
        }
        $keyHash = self::getPassHash($password, $email);
        if ($keyHash !== $user->getPassword()) {
            $error = 'wrong_password';
            return false;
        }
        if (\LPS\Config::AUTH_EMAIL_VALIDATION && !$user->getEmailValid()) {
            $error = 'no_email_valid';
            return false;
        }
        $userRoleClass = '\App\Auth\Account\\' . $user['role'];
		$userRoleFile = \App\Builder::getInstance()->getConfig()->getRealDocumentRoot() . '/App/Auth/Account/' . $user['role'] . '.php';
        if (!file_exists($userRoleFile) || !class_exists($userRoleClass)){
            $error = 'no_role_in_system';
            return false;
        }
        if ($user['status'] == Users\RegistratedUser::STATUS_DELETED){
            $error = 'deleted';
            return false;
        }
        if ($user['status'] == Users\RegistratedUser::STATUS_BANNED){
            $error = 'banned';
            return false;
        }
        return $user;
    }

    /**
     * Аутентификация через логинзу
     * @param string $network идентификатор соцсети
     * @param string $identity идентификатор юзера в соцсети
     * @param string $error
     * @return mixed
     */
    public function authenticateViaSocialNetwork($network, $identity, &$error = NULL, $context = \App\UserContainer::CONTEXT_READ_ONLY) {
        //проверки
        $user = Users\Factory::getInstance($context)->getUserBySocialIdentity($network, $identity);
        if (empty($user)) {
            $error = 'not_found';
            return false;
        }
        $userRoleClass = '\App\Auth\Account\\' . $user['role'];
        $userRoleFile = \App\Builder::getInstance()->getConfig()->getRealDocumentRoot() . '/App/Auth/Account/' . $user['role'] . '.php';
        if (!file_exists($userRoleFile) || !class_exists($userRoleClass)){
            $error = 'no_role_in_system';
            return false;
        }
        if ($user['status'] == Users\RegistratedUser::STATUS_DELETED){
            $error = 'deleted';
            return false;
        }
        if ($user['status'] == Users\RegistratedUser::STATUS_BANNED){
            $error = 'banned';
            return false;
        }
        return $user;
    }

    /**
     * Получить аккаунт на основе запроса
     * @return \App\Auth\Account\AuthorizedAccount
     */
    public function getAccount($request) {
        $userFactory = Users\Factory::getInstance();
        $user_id = $request->cookies->get('user_id');
        $auth = $request->cookies->get('auth');
        $hash = $request->cookies->get('hash');
        $pass_hash = $request->cookies->get('pass_hash');
        //если куки пустые, то у нас неавторизованный пользователь, возвращаем аккаунт гостя.
        if (empty($user_id) || empty($auth) || empty($hash) || empty($pass_hash)) {
            return $userFactory->getGuest();
        }
        /* @var $user \App\Auth\Users\RegistratedUser */
        $user = $userFactory->getUser($user_id);
        //если не нашелся пользователь с таким id, то возвращаем аккаунт гостя
        if (empty($user)) {
            return $userFactory->getGuest();
        }
        if (\LPS\Config::HARD_AUTH){//если жесткая авторизация
            //проверяем, что это тот же пользователь, с того же ip, с той же системой, и в куках правильный хэш
            $auth_data = self::getAuthFromData($user_id, $request->server->get('remote_addr'), $request->server->get('user_agent'), $hash);
            if ($auth_data != $user->getAuth() || $auth_data != $auth) {
                return $userFactory->getGuest();
            }
        }else{
            //проверяем, что хэш пароля правильный
            if (self::getCheckPassHash($user->getPassword(), $request->server->get('HTTP_USER_AGENT'), $request->server->get('REMOTE_ADDR')) != $pass_hash){
                return $userFactory->getGuest();
            }
        }
        return $user->getAccount($this);
    }
    /**
     * Разлогиниться
     */
    public function logout() {
        $account = \App\Builder::getInstance()->getAccount();
        if (!empty($account) && !is_null($account->getUser())){
            $account->getUser()->setAuth('');
        }
    }
    /**
     * При удалении сегмента (от проекта к проекту будет меняться)
     * @param int $segment_id
     */
    public function onSegmentDelete($segment_id, $context = \App\UserContainer::CONTEXT_READ_ONLY){
        $users = Users\Factory::getInstance($context)->getUsers(array('region_id' => $segment_id));
        $region = \Models\Region::search(array(), $count, 0, 1);
        $region = reset($region);
        foreach ($users as $user){
            /* @var $user Users\RegistratedUser */
            $user->update(array('region_id' => !empty($region) ? $region['id'] : NULL), false);
        }
    }
    /**
     * Строка для идентификации пользователя
     * @param int $user_id
     * @param string $ip
     * @param string $user_agent
     * @param string $hash
     * @return string
     */
    public static function getAuthFromData($user_id, $ip, $user_agent, $hash) {
        return md5(serialize(array(
            'user_id' => $user_id,
            'ip' => $ip,
            'user_agent' => $user_agent,
            'hash' => $hash
        )));
    }
    /**
     * Хэш пароля
     * @param string $pass
     * @param string $email
     * @return string
     */
    public static function getPassHash($pass, $email){
        return md5($email . ':' . \LPS\Config::HASH_SOLT_STRING . ':' . $pass);
    }
    /**
     * Алгоритм хэширования пароля, для сверки из кук
     * @param type $pass_hash
     * @param type $user_agent
     * @param type $ip
     * @return type
     */
    public static function getCheckPassHash($pass_hash, $user_agent, $ip){
        return md5($pass_hash . \LPS\Config::HASH_SOLT_STRING . $user_agent . \LPS\Config::HASH_SOLT_STRING . $ip . \LPS\Config::HASH_SOLT_STRING);
    }

    public static function randomPassword($length = self::PASS_LENGTH, $allow = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789") {
        $i = 1;
        $ret = '';
        while ($i <= $length) {
            $max  = strlen($allow)-1;
            $num  = rand(0, $max);
            $temp = substr($allow, $num, 1);
            $ret  = $ret . $temp;
            $i++;
        }
        return $ret;
    }
}
?>