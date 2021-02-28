<?php
/**
 * Description of SuperAdmin
 *
 * @author olga
 */
namespace Modules\Profile;
use App\Auth\Users\Factory;
class EditUsers extends \LPS\AdminModule{
    const DEFAULT_ACCESS = \App\Configs\AccessConfig::ACCESS_NO_BROKERS;
    const PAGE_SIZE = 20;
    private $allow_order_fields = array('email', 'reg_date', 'company_name', 'status', 'full_name');
    private $editableFields = array('name', 'person_type', 'phone', 'requisites', 'company_name', 'inn', 'surname', 'subscribe', 'master', 'skype', 'icq', 'mobile_phone', 'occupation', 'show_in_contacts', 'discount_tool', 'discount_equip');
    public function index(){
		$params['type'] = $this->request->query->get('type');
		if (empty($params['type'])){
			return $this->notFound();
		}
        $this->usersList(TRUE);
    }
	/**
	 * @ajax
	 */
	public function usersList($inner = FALSE){
		if (!$inner){
			$ans = $this->setJsonAns();
            if ($this->request->query->has('email_send') || $this->request->request->has('email_send')){
                $ans->addData('email_send', $this->request->query->get('email_send', $this->request->request->get('email_send')));
            }
		}
		$params = array();
        $params['type'] = $this->request->query->get('type', $this->request->request->get('type'));
		$page = $this->request->query->get('page', $this->request->request->get('page', 1));
        if ($page < 1 || intval($page) != $page){
            $page = 1;
        }
		$params['start'] = ($page-1)*self::PAGE_SIZE;
		$params['limit'] = self::PAGE_SIZE;
		$params['status'] = $this->request->query->get('status', array('active', 'banned', 'expired'));
        $params['email'] = $this->request->query->get('email');
        $params['reg_date'] = $this->request->query->get('reg_date');
        $params['date_min'] = $this->request->query->get('date_min');
        $params['date_max'] = $this->request->query->get('date_max');
        $params['inn'] = $this->request->query->get('inn');
        $params['company_name'] = $this->request->query->get('company_name');
        $params['surname'] = $this->request->query->get('surname');
        $params['role'] = $this->request->query->get('role');
		if (!($this->account instanceof \App\Auth\Account\SuperAdmin)){
			$params['no_role'] = 'SuperAdmin';
		}
		$order = $this->request->query->get('order');
        if (!empty($order) && is_array($order)){
            foreach ($order as $p => $d){
                if (!in_array($p, $this->allow_order_fields)){
                    unset($order[$p]);
                }else{
                    if ($p == 'full_name'){
                        $params['order']['surname'] = $d;
                        $params['order']['name'] = $d;
                    } else {
                        $params['order'][$p] = $d;
                    }
                }
            }
        }
		$count = 0;
		$users = Factory::getInstance()->getUsers($params, $count);
		$this->getAns()->add('users', $users)
			->add('pageNum', $page)
			->add('pageSize', $params['limit'])
			->add('count', $count)
			->add('current_person_type', $params['type'])
		;
	}
    /**
     * @ajax
     * @return string
     */
    public function createUser(){
        if ($this->request->request->get('role') == 'SuperAdmin' && ! $this->account instanceof \App\Auth\Account\SuperAdmin){
            throw new \LogicException('Нельзя создавать пользователя с ролью "разработчик"');
        }
//        \App\Auth\Users\RegistratedUser::createUser($this->request->request->all(), $errors, true);
        \App\Auth\Users\Factory::getInstance(\App\UserContainer::CONTEXT_ADMIN)->createUser($this->request->request->all(), $errors, true);
        if (\Models\Validator::isErrorsEmpty($errors)){
            $user = \App\Auth\Users\Factory::getInstance(\App\UserContainer::CONTEXT_ADMIN)->getUserByEmail($this->request->request->get('email'));
            $new_addresses = $this->request->request->get('new_addresses');
            if (!empty($new_addresses)){
                foreach ($new_addresses as $text){
                    $user->setNewAddress($text);
                }
            }
            $this->getAns()->add('new_pass', $this->request->request->get('pass'))
                ->add('user_email', $user->getEmail())
                ->add('site_config', \App\Builder::getInstance()->getSiteConfig())
                ->add('user', $user)
                ->setTemplate('mails/registration.tpl');
            \Models\Email::send($this->getAns(), array($user->getEmail() => $user['name']));
            $this->request->query->set('type', $this->request->request->get('person_type'));
            return $this->run('usersList');
        }else{
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }
    /**
     * @ajax
     * @throws \LogicException
     */
    public function editUserFields(){
        $this->setAjaxResponse();
        $user_id = $this->request->request->get('user_id');
        $user = \App\Auth\Users\Factory::getInstance(\App\UserContainer::CONTEXT_ADMIN)->getUser($user_id);
        if (empty($user)){
            throw new \LogicException('User not found ID:' . $user_id);
        }
        $this->getAns()
			->add('site_id', \App\Builder::getInstance()->getConfig()->getParametr('site', 'site_id'))
			->add('roles', \Models\Roles::getInstance()->get())
			->add('user', $user)
			->add('current_person_type', $user['person_type'])
			->setFormData($user->asArray())
			->add('addresses', $user->getAddresses());
    }
    /*ajax*/
    public function changePass(){
        $user_id = $this->request->request->get('user_id');
        $user = \App\Auth\Users\Factory::getInstance(\App\UserContainer::CONTEXT_ADMIN)->getUser($user_id);
        $pass = $this->request->request->get('pass');
        if (empty($pass)){
            $pass = \App\Auth\Controller::randomPassword();
        }
        $user->changePassword($pass);
        $this->getAns()->add('new_pass', $pass)
                ->add('user_email', $user->getEmail())
                ->add('site_config', \App\Builder::getInstance()->getSiteConfig())
                ->setTemplate('mails/changePass.tpl');
        \Models\Email::send($this->getAns(), array($user->getEmail() => $user->getName()));
        return '';
    }
    /**
     * Удаление фотографии юзера
     */
    public function deletePhoto(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $errors = array();
        $id = $this->request->request->get('id');
        if (empty($id)){
            $errors['id'] = 'empty';
        } else {
            $user = \App\Auth\Users\Factory::getInstance(\App\UserContainer::CONTEXT_ADMIN)->getUser($this->request->request->get('id'));
            if (empty($user)){
                $errors['id'] = 'not_found';
            } elseif (empty($user['image_id'])){
                $error['image'] = 'empty';
            } else {
                $user->deleteImage();
                $ans->addData('status', 'ok');
            }
        }
        if (!empty($errors)){
            $ans->setErrors($errors);
        }
    }
    /**
     * @ajax
     */
    public function editUser(){
        $user_id = $this->request->request->get('user_id', $this->request->request->get('id'));
        $user = \App\Auth\Users\Factory::getInstance(\App\UserContainer::CONTEXT_ADMIN)->getUser($user_id);
        if ($user['role'] == 'SuperAdmin' && !$this->account instanceof \App\Auth\Account\SuperAdmin){
            throw new \LogicException('Нельзя менять данные разработчика');
        }
        $role = $this->request->request->get('role');
        if (!empty($role) && $role == 'SuperAdmin' && ! $this->account instanceof \App\Auth\Account\SuperAdmin){
            $errors['role'] = 'Нельзя создавать пользователя с ролью "разработчик"';
        }
        $current_user = $this->account->getUser();
        if ($current_user->getId() == $user->getId() && !$this->account instanceof \App\Auth\Account\SuperAdmin){
            if (!empty($role) && $role != $current_user['role']){
                $errors['role'] = 'Нельзя менять роль самому себе';
            }
            $status = $this->request->request->get('status');
            if (!empty($status) && $status != $current_user['status']){
                $errors['status'] = 'Нельзя менять статус самому себе';
            }
        }
		$form_submit = $this->request->request->all();
		$checker = \Models\Validator::getInstance($this->request);
		if (isset($form_submit['email'])){
			$email = trim($form_submit['email']);
			if ($email != $user->getEmail()){
				$new_email = $checker->checkResponseValue('email', 'checkEmail', $errors['email'], array('uniq' => true));
                if (empty($errors['email'])){
                    unset($errors['email']);
                }
			}
		}
        $pass = $this->request->request->get('pass');
        if (empty($errors)){
            $user->update($form_submit, $errors);
        }
        if (empty($errors)){
            $email_send = false;
            if (!empty($new_email)){
                $pass = !empty($pass) ? $pass : \App\Auth\Controller::randomPassword();
                $user->setEmail($new_email, $pass);
            }elseif(!empty($pass)){
                $user->changePassword($pass);
            }
            if (!empty($new_email) || !empty($pass)){
                $this->getAns()->add('new_pass', $pass)
                ->add('user_email', $user->getEmail())
                ->add('site_config', \App\Builder::getInstance()->getSiteConfig())
                ->setTemplate('mails/changePass.tpl');
                \Models\Email::send($this->getAns(), array($user->getEmail() => $user->getName()));
                $email_send = true;
            }
            if (!empty($status) && $status != $user['status']){
                $user->setStatus($status);
                if ($status == \App\Auth\Users\RegistratedUser::STATUS_DELETED){
                    $orders = \Models\OrderManagement\Order::search(array('user_id' => $user['id']));
                    foreach ($orders as $o){
                        $o->setStatus(\Models\OrderManagement\Order::STATUS_DELETE);
                    }
                }
            }
            $addresses = $this->request->request->get('address');
            if (!empty($addresses)){
                foreach ($addresses as $id => $text){
                    $user->updateAddress($id, $text);
                }
            }
            $new_addresses = $this->request->request->get('new_addresses');
            if (!empty($new_addresses)){
                foreach ($new_addresses as $text){
                    $user->setNewAddress($text);
                }
            }
            $bonus = $this->request->request->get('bonus');
            if (!empty($bonus) && $bonus != $user['bonus']){
                if ($bonus < 0){
                    $bonus = 0;
                }
                /** @TODO разобраться с бонусом */
//                $user->setBonus($bonus);
            }
//            $params = array();
//            foreach ($this->editableFields as $f){
//                if (isset($form_submit[$f])){
//                    $params[$f] = $form_submit[$f];
//                }
//            }
//            $params['show_in_contacts'] = !empty($params['show_in_contacts']) ? 1 : 0;
//            $user->update($params);
            $photo = $this->request->files->get('photo');
            if (!empty($photo)){
                $user->setImage($photo);
            }
            $this->request->query->set('type', $user['person_type']);
            $this->request->query->set('email_send', $email_send);
            return $this->run('usersList');
        }else{
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }

    public function deleteUser(){
        $id = $this->request->request->get('id');
        $errors = array();
        if (empty($id)){
            $errors['id'] = 'empty';
        } else {
            $user = Factory::getInstance(\App\UserContainer::CONTEXT_ADMIN)->getUser($id);
            if (empty($user)){
                $errors['id'] = 'not_found';
            }
        }
        if (empty($errors)){
            $user->update(array('status' => \App\Auth\Users\RegistratedUser::STATUS_DELETED), $errors);
        }
        if (empty($errors)){
            $this->request->query->set('type', $user['person_type']);
            return $this->run('usersList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }
    /**
     * Установить бонус
     * @ajax
     * @throws \LogicException
     */
    public function setBonus(){
        $user = \App\Auth\Users\Factory::getInstance(\App\UserContainer::CONTEXT_ADMIN)->getUser($this->request->request->get('user_id'));
        if (empty($user)){
            throw new \LogicException('Пользователь не найден');
        }
        $bonus_sum = $this->request->request->get('bonus_sum');
        if (!empty($bonus_sum)){
            if ($bonus_sum{0} == '+'){
                $bonus_plus = substr($bonus_sum, 1);
                $bonus_sum = NULL;
            }elseif($bonus_sum{0} == '-'){
                $bonus_minus = substr($bonus_sum, 1);
                $bonus_sum = NULL;
            }
        }
        if (empty($bonus_plus)){
            $bonus_plus = $this->request->request->get('bonus_plus');
        }
        if (empty($bonus_minus)){
            $bonus_minus = $this->request->request->get('bonus_minus');
        }
        $bonus = 0;
        if (!is_null($bonus_sum) && $bonus_sum != ''){
            $bonus = $bonus_sum;
        }elseif(!is_null($bonus_plus) && $bonus_plus != ''){
            $bonus = $user['bonus'] + $bonus_plus;
        }elseif(!is_null($bonus_minus) && $bonus_minus != ''){
            $bonus = $user['bonus'] - $bonus_minus;
        }else{
            $error = 'Бонус не задан';
        }
        /** @TODO разобраться с бонусом */
//        $user->setBonus($bonus);
        if (!empty($error)){
            return json_encode(array('error' => 'error'));
        }else{
            return json_encode(array('status' => 'ok'));
        }
    }
    /**
     * @ajax автокомплит компаний
     * @return string
     */
    public function getCompanyNames(){
        $q = $this->request->request->get('term', $this->request->query->get('term'));
        $result = array();
        if (!empty($q)){
            $result = \App\Builder::getInstance()->getDB()->query('SELECT DISTINCT(`company_name`) AS `cn` FROM `users` WHERE `company_name` LIKE ?s AND `person_type` = "org"', '%' . $q . '%')->getCol(`cn`, `cn`);
        }
        return json_encode($result);
    }
    /**
     * @ajax автокомплит фамилий
     * @return string
     */
    public function getNames(){
        $q = $this->request->request->get('term', $this->request->query->get('term'));
        $result = array();
        if (!empty($q)){
            $result = \App\Builder::getInstance()->getDB()->query('SELECT DISTINCT(`name`) AS `cn` FROM `users` WHERE `name` LIKE ?s', '%' . $q . '%')->getCol(`cn`, `cn`);
        }
        return json_encode($result);
    }
}
?>