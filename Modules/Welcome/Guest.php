<?php
namespace Modules\Welcome;
use App\Auth\Controller as AuthController;
use App\Auth\Users\Factory as UserFactory;
use App\Auth\Users\RegistratedUser;
use App\Auth\Users\UserFiz;
use App\UserContainer;
use Models\Action;
use Models\AuthenticationManagement\SocialAuth;
use Models\Validator;
use Symfony\Component\HttpFoundation\Cookie;
class Guest extends \LPS\WebModule{
    private static $validation_params = array(
        'fiz' => array(
            'name' => array('type' => 'checkString'), 
            'surname' => array('type' => 'checkString'), 
            'patronymic' => array('type' => 'checkString', 'options' => array('empty' => TRUE)), 
            'phone' => array('type' => 'checkPhone', 'options' => array('empty' => TRUE))
        ),
        'org' => array(
            'name' => array('type' => 'checkString'), 
            'patronymic' => array('type' => 'checkString', 'options' => array('empty' => TRUE)), 
            'surname' => array('type' => 'checkString'), 
            'phone' => array('type' => 'checkPhone'), 
            'organisation_phone' => array('type' => 'checkPhone', 'options' => array('empty' => TRUE)), 
            'organisation_fax' => array('type' => 'checkPhone', 'options' => array('empty' => TRUE)), 
            'requisites' => array('type' => 'checkString', 'options' => array('empty' => TRUE)), 
            'jure_address' => array('type' => 'checkString', 'options' => array('empty' => TRUE)), 
            'document_address' => array('type' => 'checkString', 'options' => array('empty' => TRUE)), 
            'company_name' => array('type' => 'checkString'), 
            'inn' => array('type' => 'checkString', 'options' => array('count' => 10)), 
            'ogrn' => array('type' => 'checkString', 'options' => array('empty' => TRUE)), 
            'okpo' => array('type' => 'checkString')
        )
    );
    private static $person_types = array('fiz', 'org');

    public function route($route){
        $uri = $this->request->server->get('REQUEST_URI');
        if (strpos($uri, '/welcome/login/') !== FALSE || strpos($uri, '/welcome/logout/') !== FALSE){
            return 'notFound';
        }
        return parent::route($route);
    }

    public function index(){
        $rm = $this->router->getRequestModule();
        if (is_callable(array($this, $rm))){
            return $this->run($rm);
        }else{
            return $this->notFound();
        }
    }
    
    public function registration(){
        $ajax = $this->request->request->get('ajax');
        if (!($this->account instanceof \App\Auth\Account\Guest) && !$ajax){
            $role = \Models\Roles::getInstance()->get($this->account->getUser()->getRole());
            return $this->redirect(!empty($role['after_login_redirect']) ? $role['after_login_redirect'] : '/site/');
        }
        if ($ajax && !($this->getAns() instanceof \LPS\Container\JsonContentContainer)){
            $this->setJsonAns()->setEmptyContent();
        }
        if(\LPS\Config::getParametr('site', 'disable_registration')){
            return $this->notFound();
    	}
        $form_submit = $this->request->request->all();
        if (!empty($form_submit)){
            $params = array();
            $checker = \Models\Validator::getInstance($this->request);
            $errors = array();
            //стандартная проверка мыла и пароля
            $email = $checker->checkResponseValue('email', 'checkEmail', $errors['email'], array('uniq' => true));
            $pass = $checker->checkResponseValue('pass', 'checkString', $errors['pass'], array('count_min' => 4));
            $pass2 = $this->request->request->get('pass2');
            if ($pass2 != $pass){
                $errors['pass2'] = 'not_same';
            }
            //тип  пользователя
            $person_type = $this->request->request->get('person_type', 'fiz');
            if (!in_array($person_type, self::$person_types)){
                $errors['person_type'] = \Models\Validator::ERR_MSG_EMPTY;
            } else {
                $checker->checkFewResponseValues(self::$validation_params[$person_type], $errors);
            }
            //собираем параметры, которые надо записать пользователю при регистрации
            $params['person_type'] = $person_type;
            foreach(array_keys(self::$validation_params[$person_type]) as $f){
                $params[$f] = $this->request->request->get($f);
            }
            if (empty($errors)){
                $controller = \App\Builder::getInstance()->getAccountController();
                if ($controller->regUser($email, $pass, \App\Configs\AccessConfig::ROLE_SUPER_ADMIN)){
                    $user = $controller->authenticate($email, $pass, $error, \App\UserContainer::CONTEXT_USER);
                    if (!$ajax){
                        $this->response = $this->redirect('/');
                    }
                    if (!empty($user)){
                        $this->request->request->set('new_user_id', $user->getId());
                        //Запись успешной авторизации в ответ.
                        $this->setSuccessAuth($user, $pass, $email);
                        if (!empty($params)){
                            $user->update($params);
                        }
                        //отправляем письмо пользователю о том что он зарегался
                        $mail_ans = new \LPS\Container\WebContentContainer(
                                'mails/registration.tpl');
                        $site_config = \App\Builder::getInstance()->getSiteConfig();
                        $mail_ans->add('user', $user)
                            ->add('new_pass', $pass)
                            ->add('user_email', $user->getEmail())
                            ->add('site_config', $site_config);
                      //  \Models\Email::send($mail_ans, array($user->getEmail() => $user->getName()));
                    }
                    if (!$ajax){
                        return $this->response;
                    }
                }
            }else{
                if ($ajax) {
                    $this->getAns()->setErrors($errors);
                } else {
                    $this->getAns()->add('errors', $errors);
                }
            }
        }
    }
    
    public function login(){
        $ajax = $this->request->request->get('ajax');
        if (!($this->account instanceof \App\Auth\Account\Guest) && !$ajax){
            $role = \Models\Roles::getInstance()->get($this->account->getUser()->getRole());
            return $this->redirect(!empty($role['after_login_redirect']) ? $role['after_login_redirect'] : '/site/');
        }
        // аутентификация через соцсети
        if ($auth_type = $this->request->query->get('auth_type')){
            return $this->socialNetworkLogin($auth_type);
        }
        $email = $this->request->request->get('email');
        $pass = $this->request->request->get('pass');
        // введен логин и пароль либо успешная аутентификация через соцсети
        if ((!empty($email) && !empty($pass))){
            $controller = \App\Builder::getInstance()->getAccountController();
            $user = $controller->authenticate($email, $pass, $error);
            if (empty($user)){
                if ($ajax){
                    return json_encode(array('errors' => $error));
                }
                $this->getAns()->add('error', $error);
            }else{
                if ($ajax){
                    return json_encode(array('status' => 'ok'));
                }
                if ($this->request->request->has('cms_login')){
                    //мы не хотим давать авторизоваться обычным пользователям через страницу авторизации (только через попап)
                    if ($user->isInstanceOf('\App\Auth\Users\Admin')){
                        return $this->getSuccessAuthResponse($user, $email, $pass, FALSE, TRUE);
                    }else{
                        $this->getAns()->add('error', 'not_admin');
                    }
                } else {
                    return $this->getSuccessAuthResponse($user, $email, $pass);
                }
            }
        }
        if ($ajax){
            return json_encode(array('errors' => array('main' => 'no data')));
        }
    }

    /**
     * Выполняет аутентификацию пользователя и возвращает редирект
     * @param \App\Auth\Users\RegistratedUser $user
     * @param string $email
     * @param string $pass
     * @param bool $social_auth
     * @param bool $redirect_to_admin_page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function getSuccessAuthResponse($user, $email, $pass, $social_auth = FALSE, $redirect_to_admin_page = FALSE){
        $role = \Models\Roles::getInstance()->get($user->getRole());
        $s = \App\Builder::getInstance()->getCurrentSession();
        if ($user->isInstanceOf('\App\Auth\Users\Admin') && $redirect_to_admin_page){
            $redirect_url = !empty($role['after_login_redirect']) ? $role['after_login_redirect'] : '/site/';
        } else{
            $redirect_url = $s->get('after_login_redirect');
            $s->set('after_login_redirect', NULL);
            if (empty($redirect_url)){
                $prev_url = $this->request->request->get('prev_url');
                $redirect_url = !empty($prev_url) ? $prev_url : $this->getPrevUrl();
            }
        }
        $this->response = $this->redirect(!empty($redirect_url) ? $redirect_url : (!empty($role['after_login_redirect']) ? $role['after_login_redirect'] : '/site/'));
        $this->clearPrevUrl();
        //Запись успешной авторизации в ответ.
        $this->setSuccessAuth($user, $pass, $email, $social_auth);
        $s->set('just_auth', 1);
        $s->set('user_social_data', NULL);
        return $this->response;
    }

    /**
     * @param string $auth_type
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    private function socialNetworkLogin($auth_type){
        $social_auth = SocialAuth::getAuthModule($auth_type);
        if (!empty($social_auth)){
            $social_network_user_data = $social_auth->getUserData($this->request->query->all(), $error);
            if (!empty($social_network_user_data)){
                // ищем пользователя привязанного к данному аккаунту соцсети
                $user = \App\Builder::getInstance()->getAccountController()->authenticateViaSocialNetwork($social_network_user_data['network'], $social_network_user_data['identity'], $error);
                if (!empty($user)){
                    // нашли - входим им
                    return $this->getSuccessAuthResponse($user, $user['email'], NULL, TRUE);
                } else{
                    $s = \App\Builder::getInstance()->getCurrentSession();
                    $s->set('user_social_data', $social_network_user_data);
                    if (!empty($social_network_user_data['email'])) {
                        $user = \App\Auth\Users\Factory::getInstance()->getUserByEmail($social_network_user_data['email']);
                        if (!empty($user)){
                            $user->setSocialAuth($social_network_user_data['network'], $social_network_user_data['identity'], $error);
                            return $this->getSuccessAuthResponse($user, $social_network_user_data['email'], NULL, TRUE);
                        } else {
                            return $this->redirect($this->getModuleUrl() . 'finishSocialRegistration/');
                        }
                    } else {
                        return $this->redirect($this->getModuleUrl() . 'getEmail/');
                    }
                }
            }
        } else {
            return $this->notFound();
        }
    }

    /**
     * Окончание регистрации через соцсети в случае если соцсеть не отдает email
     */
    public function getEmail(){
        $ajax = $this->request->request->get('ajax');
        if (!($this->account instanceof \App\Auth\Account\Guest) && !$ajax){
            $role = \Models\Roles::getInstance()->get($this->account->getUser()->getRole());
            return $this->redirect(!empty($role['after_login_redirect']) ? $role['after_login_redirect'] : '/site/');
        }
        $s = \App\Builder::getInstance()->getCurrentSession();
        $user_social_data = $s->get('user_social_data');
        if (empty($user_social_data)){
            return $this->notFound();
        }
        $errors = array();
        $email = Validator::getInstance($this->request)->checkResponseValue('email', 'checkEmail', $errors['email']);
        $pass = $this->request->request->get('pass');
        $user = !empty($email) ? \App\Auth\Users\Factory::getInstance()->getUserByEmail($email) : NULL;
        if (empty($user) && !empty($email)){
            // пользователь не найден, email валидный - отправляем на завершение регистрации
            $user_social_data['email'] = $email;
            $s->set('user_social_data', $user_social_data);
            return $this->redirect($this->getModuleUrl() . 'finishSocialRegistration/');
        }
        if (!empty($user) && !empty($pass)){
            // Пользователь найден, введен пароль
            $controller = \App\Builder::getInstance()->getAccountController();
            $user = $controller->authenticate($email, $pass, $errors['pass']);
            if (!empty($user)){
                // Пароль верный - прикрепляем соцсеть и логинимся пользователем
                $user->setSocialAuth($user_social_data['network'], $user_social_data['identity'], $error);
                if ($error == 'already_set'){
                    $errors['network'] = $error;
                } else {
                    return $this->getSuccessAuthResponse($user, $email, NULL, TRUE);
                }
            }
        }
        $email = $this->request->request->get('email');
        $this->getAns()
            ->add('email', $email)
            ->add('pass', $pass)
            ->add('errors', $errors)
            ->setFormData(array(
                'email' => $email,
                'pass' => $pass
            ));
    }

    public function finishSocialRegistration(){
        $s = \App\Builder::getInstance()->getCurrentSession();
        $user_data = $s->get('user_social_data');
        $user_data['role'] = \LPS\Config::DEFAULT_SOCIAL_ROLE;
        $user_data['person_type'] = \LPS\Config::DEFAULT_SOCIAL_PERSON_TYPE;
        if (empty($user_data)){
            return $this->notFound();
        }
        $validation_params = UserContainer::getValidationParams(NULL, $user_data['role'], $user_data['person_type']);
        $post_data = $this->request->request->all();
        if (!empty($post_data)){
            $checker = Validator::getInstance($this->request);
            $errors = array();
            foreach($validation_params as $field => $checker_params){
                $user_data[$field] = !empty($post_data[$field]) ? $post_data[$field] : (!empty($user_data[$field]) ? $user_data[$field] : NULL);
                $checker->checkValue($user_data[$field], $checker_params['type'], $errors[$field], !empty($checker_params['options']) ? $checker_params['options'] : array());
            }
            if ($checker->isErrorsEmpty($errors) && $this->registrateUserViaSocialNetwork($user_data)){
                $this->setJsonAns()->setEmptyContent()->addData('url', $this->getModuleUrl() . 'socialAuthSuccessRedirect/');
                return $this->redirect($this->getModuleUrl() . 'socialAuthSuccessRedirect/'); /** @TODO СДЕЛАТЬ РЕДИРЕКТ В СКРИПТАХ */
//                if ($this->registrateUserViaSocialNetwork($user_data)){
//                    $user = \App\Auth\Users\Factory::getInstance()->getUserByEmail($user_data['email']);
//                    return $this->getSuccessAuthResponse($user, $user_data['email'], NULL, TRUE);
//                }
            } else {
                $this->setJsonAns()->setEmptyContent()->setErrors($errors);
            }
        }
        $this->getAns()
            ->add('user_form_fields', UserFiz::getEditUserFormFields())
            ->setFormData($user_data);
    }

    public function socialAuthSuccessRedirect(){
        $user_data = \App\Builder::getInstance()->getCurrentSession()->get('user_social_data');
        if (!empty($user_data) && !empty($user_data['email']) && $user = \App\Auth\Users\Factory::getInstance()->getUserByEmail($user_data['email'])){
            return $this->getSuccessAuthResponse($user, $user_data['email'], NULL, TRUE);
        } else {
            return $this->notFound();
        }
    }


    /**
     * Регистрирует пользователя по данным соцсети, если пользователь с такой электронной почтой уже существует просто добавляет данные для аутентификации через соцсеть
     * @param array $user_data
     * @param array $errors
     * @return bool
     */
    private function registrateUserViaSocialNetwork($user_data, &$errors = array()){
        $user = UserFactory::getInstance()->getUserByEmail($user_data['email']);
        if (empty($user)){
            $controller = \App\Builder::getInstance()->getAccountController();
            $result = $controller->regUser($user_data['email'], \App\Auth\Controller::randomPassword(\App\Auth\Controller::REFERAL_COUNT_SIMBOLS, \App\Auth\Controller::REFERAL_ALLOW_SIMBOLS));
            if ($result){
                $user = UserFactory::getInstance(\App\UserContainer::CONTEXT_USER)->getUserByEmail($user_data['email']);
                $user->update($user_data, $errors);
            }
        }
        if (!empty($user)){
            $user->setSocialAuth($user_data['network'], $user_data['identity']);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function setSuccessAuth(\App\UserContainer $user, $pass, $email, $social_auth = FALSE){
        $expires = time() + 30 * 24 * 3600;
        $salt = \LPS\Config::HASH_SOLT_STRING;
        $this->setCookie('user_id', $user->getId(), $expires);
        $this->setCookie('pass_hash', AuthController::getCheckPassHash(!$social_auth ? \App\Auth\Controller::getPassHash($pass, $email) : $user->getPassword(), $this->request->server->get('HTTP_USER_AGENT'), $this->request->server->get('REMOTE_ADDR')), $expires);
        $hash = $this->request->cookies->get('hash');
        if (empty($hash)) {
            $hash = md5($salt . time());
            $this->setCookie('hash', $hash, $expires);
        }
        $auth = \App\Auth\Controller::getAuthFromData($user->getId(), $this->request->server->get('remote_addr'), $this->request->server->get('user_agent'), $hash);
        $user->setAuth($auth);
        $this->setCookie('auth', $auth, $expires);
    }

    public function getSocialAuthLink(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $network_key = $this->request->request->get('network_key');
        $auth_module = SocialAuth::getAuthModule($network_key);
        if (empty($network_key) || empty($auth_module)){
            $ans->addErrorByKey('network_key', 'empty');
        } elseif (!$auth_module->isEnable()) {
            $ans->addErrorByKey('network_key', 'disabled');
        } else {
            $ans->addData('auth_link', $auth_module->getAuthLink());
            $s = \App\Builder::getInstance()->getCurrentSession();
            $s->set('after_login_redirect', $this->request->request->get('referrer_url'));
        }
    }
    /**
     * 
     * @return type
     */
    public function logout(){
        $referer_uri = $this->request->server->get('HTTP_REFERER');
        preg_match('~https?://(www\.)?([^/]*)/([a-zA-Z\-_]*)/(([a-zA-Z\-_]*)/)?~', $referer_uri, $out);
        $module = !empty($out[3]) ? $out[3] : NULL;
        $action = !empty($out[5]) ? $out[5] : NULL;
        $actionData = (!empty($module)) ? Action::getInstance()->search(array('moduleUrl' => $module, 'action' => !empty($action) ? $action : 'index')) : array();
        $actionData = reset($actionData);
        $user = $this->account->getUser();
        $guest_account = UserFactory::getInstance()->getGuest();
        if ($guest_account->isPermission($module, $action)){//публичная страница
            $response = $this->redirect($referer_uri);
        }else{
            // С админской странички попадаем на страницу входа в админку
            $redirect_url = (!empty($user) && $user->isInstanceOf('\App\Auth\Users\Admin') && !empty($actionData) && $actionData['admin']) ? '/login/' : '/';
            $response = $this->redirect($redirect_url);
        }
        \App\Builder::getInstance()->getAccountController()->logout();
        $fields = array('user_id', 'pass_hash', 'hash', 'auth');
        foreach ($fields as $f){
            $this->clearCookie($f, $response);
        }
        return $response;
    }
    
	public function passwordRecovery(){
		$form_submit = $this->request->request->all();
		$ajax = $this->request->request->get('ajax');
		if (!empty($form_submit)){
			$validator = \Models\Validator::getInstance($this->request);
			$errors = array();
	//		$email = $this->request->query->get('email');//TODO временная версия
			$email = $validator->checkResponseValue('email', 'checkEmail', $errors['email']);
			$user = \App\Auth\Users\Factory::getInstance()->getUser(null, array('email' => $email));
			if (empty($user)){
				$errors['user'] = 'not_exists';
			}
			foreach ($errors as $f => $err){
				if (empty($err)){
					unset($errors[$f]);
				}
			}
			if (empty($errors)){
				$info = $user->asArray();
				$hash = md5(md5(mt_rand(time()/2, time())).\LPS\Config::HASH_SOLT_STRING.serialize($info));
				$this->getAns()->add('user_info', $info)
						->add('hash', $hash);
				$mail_ans = new \LPS\Container\WebContentContainer();
				$mail_ans->setTemplate('mails/passremind.tpl');
				$mail_ans->add('user', $user)->add('hash', $hash)
						->add('site_config', \App\Builder::getInstance()->getSiteConfig())
						->add('region', !empty($user['region_id']) ? \App\Segment::getInstance()->getById($user['region_id']) : \App\Segment::getInstance()->getDefault(true));
				\Models\Email::send($mail_ans, array($email => ''));
				$db = \App\Builder::getInstance()->getDB();
				$db->query('REPLACE INTO `restore_pass` SET `user_id`=?d, `check`=?, `date`=NOW()', $info['id'], $hash);
			}
			if (!empty($ajax)){
				return !empty($errors) ? json_encode(array('errors' => $errors)) : json_encode(array('status' => 'ok'));
			}elseif (empty($errors)){
				$this->getAns()->add('status', 'ok');
			}else{
				$this->getAns()->add('errors', $errors);
			}
		}
    }
    
    public function newpass(){
        $check = $this->request->query->get('check', $this->request->request->get('check'));
        if(!empty($check)){
            $db = \App\Builder::getInstance()->getDB();
            $id = $db->query('SELECT `user_id` FROM `restore_pass` WHERE `check` = ?', $check)->getCell();
            $db->query('DELETE FROM `restore_pass` WHERE `check` = ?', $check);
            if (empty($id)){
                $error = 'Неверный код';
            }else{
                $user = \App\Auth\Users\Factory::getInstance(\App\UserContainer::CONTEXT_USER)->getUser($id);
                if (empty($user)){
                    $error = 'Пользователь не найден';
                }else{
                    $newPass = \App\Auth\Controller::randomPassword();
                    $user->changePassword($newPass);
                    $controller = \App\Builder::getInstance()->getAccountController();
                    $email = $user['email'];
                    $user = $controller->authenticate($email, $newPass, $error);
                    if (!empty($user)){
                        $role = \Models\Roles::getInstance()->get($user->getRole());
                        $this->response = $this->redirect($this->getModuleUrl() . 'newpass/');
                        //для письма
                        $mail_ans = new \LPS\Container\WebContentContainer();
                        $mail_ans->setTemplate('mails/changePass.tpl');
                        $mail_ans->add('user', $user)
                                ->add('user_email', $user['email'])
                                ->add('new_pass', $newPass)
                                ->add('site_config', \App\Builder::getInstance()->getSiteConfig())
                                ->add('region', !empty($user['region_id']) ? \App\Segment::getInstance()->getById($user['region_id']) : \App\Segment::getInstance()->getDefault(true));
                        \Models\Email::send($mail_ans, array($user['email']=>empty($user['name']) ? '' : $user['name']));
                        $this->clearPrevUrl();
                        //Запись успешной авторизации в ответ.
                        $this->setSuccessAuth($user, $newPass, $email);
                        return $this->response;
                    }
                }
            }
            $this->getAns()->add('error', $error);
        }else{
            
        }
	}

    protected function getPrevUrl(){
        $prev_url = $this->request->cookies->get('prev_url');
        $referrer = $this->request->server->get('HTTP_REFERER');
        $request_url = $this->request->server->get('request_uri');
        if (!empty($referrer) && (empty($prev_url) || ($prev_url != $referrer && $referrer != $request_url))){
            $prev_url = $referrer;
            $this->setCookie('prev_url', $prev_url);
        }
        return $prev_url;
    }

    protected function clearPrevUrl(){
        $this->clearCookie('prev_url');
    }
}