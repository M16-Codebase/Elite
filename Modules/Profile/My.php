<?php
namespace Modules\Profile;
use App\Configs\CatalogConfig;
use App\Configs\OrderConfig;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Positions\Order;
use Models\ContentManagement\Post;
use Models\CatalogManagement\Type AS TypeEntity;
use Models\Validator;

class My extends \LPS\WebModule{
    /**
     * Закрываем гостю допуск к личному кабинету
     */
    protected static $module_custom_permissions = array(
        \App\Configs\AccessConfig::ROLE_GUEST => false
    );

    const POST_PROFI_ID = 436;
    const POST_MASTER_ID = 437;
    const ORDER_PAGE_SIZE = 100000000;
    const REVIEW_PAGE_SIZE = 100000000;
    private $editableFields = array('name', 'person_type', 'phone', 'requisites', 'inn', 'company_name', 'surname');
    public function index(){
        $user = $this->account->getUser();
        $sv = \Models\CatalogManagement\CatalogHelpers\Type\SegmentVisible::factory();
        $this->getAns()->add('user', $user)
            ->setFormData($user->asArray());
        $this->setCountsForLeftMenu();
    }
    
    public function userData($inner = FALSE){
        if (!$inner){
            $this->setJsonAns();
        }
        $user = $this->account->getUser();
        $checker = \Models\Validator::getInstance($this->request);
        $form_submit = $this->request->request->all();
        if (!is_null($form_submit)){
            $errors = array();
            $params = array();
            $params['name'] = $this->request->request->get('name');
            $email = $this->request->request->get('email');
            $email = trim($email);
            if (isset($email) && $user->getEmail() != $email){
                $new_email = $checker->checkResponseValue('email', 'checkEmail', $errors['email'], array('uniq' => true));
            }
            foreach ($this->editableFields as $f){
                if (isset($form_submit[$f])){
                    if ($f == 'inn'){
                        $params[$f] = $checker->checkResponseValue('inn', 'checkInt', $errors['inn'], array('count' => 10));
                    }else{
                        $params[$f] = $form_submit[$f];
                    }
                }
            }
            foreach ($errors as $field => $error){
                if (empty($error)){
                    unset($errors[$field]);
                }
            }
            if (empty($errors)){
                $user->update($params);
                $pass = $this->request->request->get('pass');
                if (!empty($new_email)){
                    $user->setEmail($new_email, !empty($pass) ? $pass : \App\Auth\Controller::randomPassword());
                }elseif(!empty($pass)){
                    $user->changePassword($pass);
                }
                $region = $this->request->request->get('region_id');
                if (isset($region)){
                    $user->setSegment($region);
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
                if (!empty($new_email) || !empty($pass)){
                    $mail_ans = new \LPS\Container\WebContentContainer('mails/changePass.tpl');
                    $mail_ans->add('new_pass', $pass)
                        ->add('site_config', \App\Builder::getInstance()->getSIteConfig())
                        ->add('region', !empty($region) ? \App\Segment::getInstance()->getById($region) : \App\Segment::getInstance()->getDefault(true))
                        ->add('user_email', $user->getEmail());
                    \Models\Email::send($mail_ans, array($user->getEmail() => $user->getName()));
                }
                if ($inner){
                    return $this->redirect('/profile/');
                }
            }else{
                if (!$inner){
                    $this->getAns()->setErrors($errors);
                }else{
                    $this->getAns()->add('errors', $errors);
                }
            }
        }
    }
    
    public function companyData(){
        $this->setJsonAns();
        $user = $this->account->getUser();
        $checker = \Models\Validator::getInstance($this->request);
        $form_submit = $this->request->request->all();
        if (!is_null($form_submit)){
            $errors = array();
            foreach ($this->editableFields as $f){
                if (isset($form_submit[$f])){
                    if ($f == 'inn'){
                        $params[$f] = $checker->checkResponseValue('inn', 'checkInt', $errors['inn'], array('count' => 12));
                    }else{
                        $params[$f] = $form_submit[$f];
                    }
                }
            }
            foreach ($errors as $field => $error){
                if (empty($error)){
                    unset($errors[$field]);
                }
            }
            if (empty($errors)){
                $user->update($params);
            }else{
                $this->getAns()->setErrors($errors);
            }
        }
    }
    
    public function addAddress(){
        $address = $this->request->request->get('address');
        $user = $this->account->getUser();
        $user->setNewAddress($address);
        return $this->run('addressList');
    }
    
    public function delAddress(){
        $id = $this->request->request->get('id');
        $user = $this->account->getUser();
        $user->deleteAddress($id);
        return $this->run('addressList');
    }
    
    public function addressList(){
        $this->setJsonAns();
    }
    
    public function changePass(){
        $this->setJsonAns();
        $user = $this->account->getUser();
        $checker = \Models\Validator::getInstance($this->request);
        $pass = $checker->checkResponseValue('pass', 'checkString', $errors['pass'], array('count_min' => 4));
        $pass2 = $this->request->request->get('pass2');
        if ($pass2 != $pass){
            $errors['pass2'] = 'not_same';
        }
        if(empty($errors['pass']) && empty($errors['pass2'])){
            $user->changePassword($pass);
            $mail_ans = new \LPS\Container\WebContentContainer('mails/changePass.tpl');
            $mail_ans->add('new_pass', $pass)
                ->add('user_email', $user->getEmail())
                ->add('site_config', \App\Builder::getInstance()->getSiteConfig())
                ->add('region', !empty($user['region_id']) ? \App\Segment::getInstance()->getById($user['region_id']) : \App\Segment::getInstance()->getDefault(true));
            \Models\Email::send($mail_ans, array($user->getEmail() => $user->getName()));
            return json_encode(array('status' => 'ok'));
        }else{
            if (!empty($pass)){
                unset($errors['pass']);
            }
            return json_encode(array('errors' => $errors));
        }
    }
    
    public function regionData(){
        $this->setJsonAns();
        $region_id = $this->request->request->get('region_id');
        $user = $this->account->getUser();
        $region = \Models\Region::getById($region_id);
        if (!empty($region)){
            $user->setSegment($region_id);
            $this->getAns()->add('region', $region);
        }else{
            $this->getAns()->addErrorByKey('main', 'Регион не найден');
        }
    }
    
    public function setSubscribe(){
        $check = $this->request->request->get('subscribe');
        $user = $this->account->getUser();
        $user->setSubscribe(!empty($check) ? 1 : 0);
        return '';
    }

    /**
     * Проверка прав
     * @param string $action
     * @return boolean
     */
    public function isPermission($action){
        if (!empty($this->account) && !($this->account instanceof \App\Auth\Account\Guest)){
            return true;
        }
        return false;
    }

    public function changeRegion(){
		$reg_id = $this->request->request->get('reg_id', $this->request->query->get('reg_id'));
		$region = \Models\Region::getById($reg_id);
		if (!empty($region)){
			$this->account->getUser()->update(array('region_id' => $reg_id), false);
		}
        return $this->redirect($this->request->server->get('HTTP_REFERER'));
    }
    /**
     * @ajax
     */
    public function inviteFriends(){
        $this->setAjaxResponse();
        $this->setJsonAns()->setEmptyContent();
        $emails = $this->request->request->get('email');
        $name = $this->request->request->get('name');
        if (!is_array($emails)){
            $emails = array($emails);
        }
        if (!empty($emails)){
            $user = $this->account->getUser();
            foreach ($emails as $email){    
                $mailAns = new \LPS\Container\WebContentContainer('mails/invite.tpl');
                $mailAns->add('user', $user)
                    ->add('email', $email)
                    ->add('name', $name)
                    ->add('site_config', \App\Builder::getInstance()->getSiteConfig())
                    ->add('region', !empty($user['region_id']) ? \App\Segment::getInstance()->getById($user['region_id']) : \App\Segment::getInstance()->getDefault(true))
                    ->setTemplate('mails/invite.tpl');
                \Models\Email::send($mailAns, array($email => !empty($name) ? $name : ''), $user['email'], $user['name'] . (!empty($user['surname']) ? (' ' . $user['surname']) : ''));
            }
        }else{
            $this->getAns()->addErrorByKey('email', 'empty');
        }
    }
    
    public function requestBonus(){
        $this->setJsonAns();
        $bonus = $this->request->request->get('bonus');
        $user = $this->account->getUser();
        if ($user['bonus'] < $bonus){
            $this->getAns()->addErrorByKey('main', 'Количество баллов больше чем есть у пользователя');
        }else{
            $site_config = \App\Builder::getInstance()->getSiteConfig();
            $emails = $site_config['feedback_request_bonus'];
            $this->getAns()->add('user', $user)
                ->add('site_config', $site_config)
                ->add('region', !empty($user['region_id']) ? \App\Segment::getInstance()->getById($user['region_id']) : \App\Segment::getInstance()->getDefault(true))
                ->setTemplate('mails/requestBonus.tpl');
            \Models\Email::send($this->getAns(), !empty($emails) ? explode(',', str_replace(' ', '', $emails)) : Config::getParametr('email', 'to'));
        }
        return '';
    }
    
    public function bonus(){
        $user = $this->account->getUser();
//        if ($user['program'] == 'master'){
//            $post_id = self::POST_MASTER_ID;
//        }elseif($user['program'] == 'profi'){
//            $post_id = self::POST_PROFI_ID;
//        }else{
//            $post_id = NULL;
//        }
//        $post = Post::getById($post_id);
        $this->setCountsForLeftMenu();
    }
    
    public function orders(){
        $user = $this->account->getUser();
        $order_rules = array(
            OrderConfig::KEY_ORDER_USER => Rule::make(OrderConfig::KEY_ORDER_USER)->setValue($user['id'])
        );
//        $date = $this->request->query->get('date');
//        $date_from = $this->request->query->get('date_from');
//        $date_to = $this->request->query->get('date_to');
        $status = $this->request->query->get('status');
        if (!empty($status)) {
            $order_rules[OrderConfig::KEY_ORDER_STATUS] = Rule::make(OrderConfig::KEY_ORDER_STATUS)->setValue($status);
        }
//        $params['position_title'] = $this->request->query->get('position_title');
        $order_id = $this->request->query->get('id');
        if (!empty($order_id)) {
            $order_rules['id'] = Rule::make('id')->setValue($status);
        }
        $page = $this->request->query->get('page', 1);
//        $code = $this->request->query->get('code');
//        if (!empty($code)){
//            $search_rules = array();
//            $var_ids = \Models\CatalogManagement\Search\CatalogSearch::factory(CatalogConfig::CATALOG_KEY, $this->segment['id'])->setRules($search_rules)->searchVariantIds();
//            $params['position_ids'] = array_keys($var_ids->getSearch());
//        }
//        $params['no_status'] = Order::S_TMP;
        $orders = CatalogSearch::factory(CatalogConfig::ORDERS_KEY, $this->segment['id'])
            ->setRules(array(

            ))
            ->searchItems(($page-1)*self::ORDER_PAGE_SIZE, self::ORDER_PAGE_SIZE);
        $this->getAns()->add('orders', $orders->getSearch())
            ->add('pageNum', $page)
            ->add('pageSize', self::ORDER_PAGE_SIZE)
            ->add('count', $orders->getTotalCount());
        
        $this->setCountsForLeftMenu();
    }
    
    public function reviews(){
        $user = $this->account->getUser();
        $page = $this->request->query->get('page', 1);
//        $reviews = \Models\CatalogManagement\Review::search(array('user_id' => $user['id']), $count, ($page-1)*self::REVIEW_PAGE_SIZE, self::REVIEW_PAGE_SIZE);
//        $this->getAns()->add('reviews', $reviews)
//            ->add('pageNum', $page)
//            ->add('pageSize', self::REVIEW_PAGE_SIZE)
//            ->add('count', $count);
        $this->setCountsForLeftMenu();
    }
    
    private function setCountsForLeftMenu(){
        $user = $this->account->getUser();
        $orders_count = CatalogSearch::factory(CatalogConfig::ORDERS_KEY, $this->segment['id'])
            ->setRules(array(Rule::make(OrderConfig::KEY_ORDER_USER)->setValue($user['id'])))
            ->searchItemIds(0, 1)
            ->getTotalCount();
        $this->getAns()->add('count_orders', $orders_count);
//        $reviews = \Models\CatalogManagement\Review::search(array('user_id' => $user['id']));
//        $this->getAns()->add('count_reviews', count($reviews));
        $this->getAns()->add('count_reviews', 0);
    }

    public function attachAuth(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $user = $this->account->getUser();
        $errors = array();
        $token = $this->request->request->get('token');
        if (empty($token)){
            $errors['token'] = Validator::ERR_MSG_EMPTY;
        } else {
            $uLogin_result = json_decode(file_get_contents('http://ulogin.ru/token.php?token=' .$token . '&host=' . $this->request->server->get('HTTP_HOST')), true);
            if (!empty($uLogin_result['network']) && !empty($uLogin_result['identity'])){
                $result = $user->setSocialAuth($uLogin_result['network'], $uLogin_result['identity'], $errors['social_account']);
                if ($result){
                    unset($errors['social_account']);
                }
            } elseif (!empty($uLogin_result['error'])){
                $errors['uLogin'] = $uLogin_result['error'];
            }
        }
        if(!empty($errors)){
            $ans->setErrors($errors);
        }
    }

    public function detachAuth(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $user = $this->account->getUser();
        $network = $this->request->request->get('network');
        if (empty($network)){
            $ans->addErrorByKey('network', 'empty');
        } else {
            $result = $user->deleteSocialAuth($network);
        }
    }
}
?>
