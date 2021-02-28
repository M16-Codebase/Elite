<?php
namespace Modules\Site;
/**
 * Description of Main
 *
 * @author Charles Manson
 */
use Models\SubscribeManagement\SubscribeController as SC;
use Models\SubscribeManagement\SubscribeMember as Member;

class Subscribe extends \LPS\WebModule{
    const SUBSCRIBERS_PAGE_SIZE = 20;
    const SEPARATOR_CELL = ';';
    const SEPARATOR_CELL_TAB = "\t";
    const SEPARATOR_ROW = "\n";

    private static $sort_fields = array('surname', 'company_name', 'email', 'lockremove', 'create_time', 'groups');
    private static $filter_params = array('surname', 'company_name', 'email', 'status', 'date_min', 'date_max');
    /**
     * @var \Models\SubscribeManagement\SubscribeController
     */
    private $sc = null;
    
    public function init(){
        $this->sc = SC::getInstance();
    }

    public function index(){
        $this->getAns()->setFormData(json_decode(\Models\TechnicalConfig::getInstance()->get(SC::AUTH_CONFIG_KEY), TRUE));
    }
    
//    public function import(){
//        $this->sc->importDataFromSendSay();
//        return '';
//    }
    
    public function saveAuthData(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $errors = array();
        \Models\Validator::getInstance($this->request)->checkFewResponseValues(array(
            'login' => array('type' => 'checkString'),
            'sublogin' => array('type' => 'checkString'),
            'password' => array('type' => 'checkString')
        ), $errors);
        if (empty($errors)){
            $auth_data = $this->request->request->all();
            if ($this->sc->checkAuthData($auth_data)) {
                \Models\TechnicalConfig::getInstance()->set(SC::AUTH_CONFIG_KEY, 'basic', json_encode($auth_data), '');
            } else {
                $ans->addErrorByKey('auth', 'wrong_credentials');
            }
        } else {
            $ans->setErrors($errors);
        }
    }
    
    public function subscribersLists(){
        $this->subscribersListsInner(true);
    }

    public function subscribersListsInner($inner = false) {
        if (!$inner) {
            $this->setJsonAns();
        }
        $this->getAns()
            ->add('groups', $this->sc->getGroupList($error));
        if (!empty($error)){
            //   return $this->redirect('/subscribe/?error=' . $error);
        }
    }
    
    public function addSubscribersList(){
        $errors = array();
        $name = $this->request->request->get('name');
        $type = $this->request->request->get('type', 'list');
        if (empty($name)) {
            $errors['name'] = 'empty';
        }
        if (!in_array($type, array('list'))){
            $errors['type'] = 'incorrect_value';
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            $id = $this->sc->createGroup($name, $type);
            return $this->run('subscribersListsInner');
        }
    }
    
    public function deleteSubscribersList(){
        $ans = $this->setJsonAns();
        if ($id = $this->request->request->get('id')){
            $this->sc->deleteGroup($id);
            $ans->add('groups', $this->sc->getGroupList());
        } else {
            $ans->addErrorByKey('id', 'empty')->setEmptyContent();
        }
    }
    
    public function clearSubscribersList(){
        $group_id = $this->request->request->get('id', $this->request->query->get('id'));
        if (empty($group_id)){
            $this->setJsonAns()->setEmptyContent()->addErrorByKey('id', 'empty');
        } else {
            $this->sc->clearGroup($group_id);
            $this->request->query->set('group_id', $group_id);
            return $this->run('subscribersList');
        }
    }
    
    public function subscribers(){
        $group_id = $this->request->query->get('group_id');
        if ($this->subscribersList(true) === FALSE){
            return $this->notFound();
        }
    }
    
    public function subscribersList($inner = false){
        if (!$inner){
            $this->setJsonAns();
        }
        $order = $this->request->query->get('order', array('email' => 'asc'));
        foreach($order as $field => $direction){
            if (in_array($field, self::$sort_fields)){
                $sort_params = array(
                    'field' => $field,
                    'order' => in_array($direction, array('asc', 'desc')) ? $direction : (empty($direction) ? 'asc' : 'desc')
                );
            }
        }
        $group_id = $this->request->query->get('group_id', $this->request->request->get('group_id'));
        if ($this->request->query->has('export')){
            $params = array('group_id' => $group_id);
            foreach(self::$filter_params as $field) {
                $params[$field] = $this->request->query->get($field);
            }
//            $params = $this->request->query->get('filter');
            $members = Member::search($params, $count, 0, 100000, !empty($sort_params) ? $sort_params : array('field' => 'email'));
            //$this->sc->getMemberList($group_id, 1, 1000000, !empty($sort_params) ? $sort_params : array('field' => 'member.email', 'order' => 'asc'));
            $path = \LPS\Config::getRealDocumentRoot() . '/data/temp/';
            if (!file_exists($path)){
                \LPS\Components\FS::makeDirs($path);
            }
            $filename = $path . 'subscribers.csv';
            $fp = fopen($filename, 'w');
            $in_cp = \LPS\Config::getParametr('site', 'codepage');
            $out_cp = 'cp1251';
            foreach($members as $member){
                fwrite($fp, 
                    iconv(
                        $in_cp, 
                        $out_cp, 
                        $member['email'] . ';' . $member['name'] . ';' . $member['surname'] . ';' . $member['company_name']
                    ) . PHP_EOL
                );
            }
            fclose($fp);
            \Models\FilesManagement\Download::existsFile($filename, NULL, TRUE);
            exit();
        } else {
            $group = $this->sc->getGroupById($group_id);
            if (empty($group)){
                return FALSE;
            }
            $page = $this->request->query->get('page', 1);
            $params = array('group_id' => $group_id);
            foreach(self::$filter_params as $field) {
                $params[$field] = $this->request->query->get($field);
            }
//            $params = $this->request->query->get('filter');
//            $params['group_id'] = $group_id;
            if ($this->request->query->has('sort_params')){
                $sort_params = $this->request->query->get('sort_params');
            }
            $this->getAns()
                ->add('members', Member::search($params, $count, ($page - 1) * self::SUBSCRIBERS_PAGE_SIZE, self::SUBSCRIBERS_PAGE_SIZE, !empty($sort_params) ? $sort_params : array('field' => 'email')))
                ->add('count', $count)
                ->add('pageNum', $page)
                ->add('pageSize', self::SUBSCRIBERS_PAGE_SIZE)
                ->add('group', $group)
                ->add('groups', $this->sc->getGroupList())
                ->add('filter_params', serialize($params))
                ->add('sort_params', !empty($sort_params) ? serialize($sort_params) : '')
                ->add('inner_members', $this->db->query('SELECT `u`.`email`, `u`.`person_type` FROM `users` as `u` WHERE `u`.`person_type` = "org" OR `u`.`person_type` = "fiz"')->getCol('email', 'person_type'));
            if ($inner) {
                return TRUE;
            }
        }
    }
    
    public function importSubscribers(){
        $ans = $this->setJsonAns('Modules/Site/Subscribe/subscribersList.tpl');
        $errors = array();
        $group_id = $this->request->request->get('group_id');
		$file = $this->request->files->get('file');
        if (empty($file)){
            $errors['file'] = 'empty';
        } elseif (empty($group_id)){
            $errors['group_id'] = 'empty';
        } else {
            $group = $this->sc->getGroupById($group_id);
            if (empty($group)){
                $errors['group'] = 'not_found';
            }
        }
        if (empty($errors)){
            $path = \LPS\Config::getRealDocumentRoot() . '/data/temp/';
            if (!file_exists($path)){
                \LPS\Components\FS::makeDirs($path);
            }
            $filename = $path . 'importsubscribers.csv';
            move_uploaded_file($file->getRealPath(), $filename);
            //$filename = $file->getRealPath();
            
            $d = fopen($filename, 'r');
			$file_row = TRUE;
            $users_list = array();
            $in_cp = 'cp1251';
            $out_cp = \LPS\Config::getParametr('site', 'codepage');
			while ($file_row !== FALSE){
				$file_row = fgetcsv($d, 0, static::SEPARATOR_CELL, static::SEPARATOR_ROW);
				if ($file_row === FALSE){
					break;
				}
                if (empty($file_row[0])){
                    continue;
                }
                $users_list[] = array(
                    'email' => iconv($in_cp, $out_cp, $file_row[0]),
                    'name' => !empty($file_row[1]) ? iconv($in_cp, $out_cp, $file_row[1]) : '',
                    'surname' => !empty($file_row[2]) ? iconv($in_cp, $out_cp, $file_row[2]) : '',
                    'company_name' => !empty($file_row[3]) ? iconv($in_cp, $out_cp, $file_row[3]) : '',
                    'group' => $group_id
                );
			}
			fclose($d);
            unlink($filename);
            $this->sc->importUsers($users_list);
            return $this->run('subscribersList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }
    
    public function member(){
        $this->getAns()
            ->add('member', $this->sc->getMember($this->request->query->get('email')));
    }
    
    public function importListPopup(){
        $ans = $this->setJsonAns();
        $target_id = $this->request->request->get('id');
        if (empty($target_id)) {
            $ans->setEmptyContent()->addErrorByKey('id', 'empty');
        } else {
            $ans->add('target_group', $target_id)
                ->add('groups', $this->sc->getGroupList());
        }
    }
    
    public function importSubscribersList(){
        $errors = array();
        $target_group = $this->request->request->get('target_group');
        $target_group_name = $this->request->request->get('target_group_name');
        $source_group = $this->request->request->get('id');
        $subscribers = $this->request->request->get('subscribers', array());
        $filter_params = $this->request->request->get('filter_params');
        if (!empty($filter_params)){
            $filter_params = unserialize($filter_params);
            $this->request->query->set('filter', $filter_params);
            if (empty($subscribers)){
                $members = Member::search($filter_params);
                $subscribers = array_keys($members);
            }
        }
        $sort_params = $this->request->request->get('sort_params');
        if (!empty($sort_params)){
            $this->request->query->set('sort_params', unserialize($sort_params));
        }
        if (empty($target_group) || $target_group == 'add-new-list'){
            if (empty($target_group_name)){
                $errors['target_group'] = 'empty';
            } else {
                $target_group = $this->sc->createGroup($target_group_name, 'list');
            }
        }
        if (empty($errors)){
            $this->sc->importList(array(
                'source_group' => $source_group,
                'export_mode' => !empty($subscribers) ? 'subscribers' : 'list',
                'target_group' => $target_group,
                'source_email' => $subscribers
            ));
            $this->request->query->set('group_id', $source_group);
            return $this->run('subscribersList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }
    
    public function addSubscriber(){
        $ans = $this->setJsonAns();
        $errors = array();
        \Models\Validator::getInstance($this->request)->checkFewResponseValues(array(
            'email' => array('type' => 'checkEmail'),
            'group_id' => array('type' => 'checkString')
        ), $errors);
        if (empty($errors)){
           // $this->request->query->set('group_id', $this->request->request->get('group'));
            $member = $this->sc->addMemberToList(
                    $this->request->request->get('email'),
                    $this->request->request->get('group_id'));
            if (empty($member)) {
                $errors['error'] = 'error';
            }
            if (empty($errors)){
                $ans->addData('member', $member);
                $this->subscribersList(true);
            }
        }
        if (!empty($errors)) {
            $ans->setErrors($errors)->setEmptyContent();
        }
//        \Models\Validator::getInstance($this->request)->checkFewResponseValues(array(
//            'name' => array('type' => 'checkString'),
//            'surname' => array('type' => 'checkString'),
//            'company_name' => array('type' => 'checkString', 'options' => array('empty' => true)),
//            'email' => array('type' => 'checkEmail'),
//            'group' => array('type' => 'checkString')
//        ), $errors);
//        if (empty($errors)){
//            $this->request->query->set('group_id', $this->request->request->get('group'));
//            $error = $this->sc->setMember($this->request->request->all(), FALSE, 'error');
//            if ($error == 'member_exists'){
//                $errors['email'] = 'already_exists';
//            } elseif ($error === false){
//                $errors['error'] = 'error';
//            }
//            if (empty($errors)){
//                $this->subscribersList(true);
//            }
//        }
//        if (!empty($errors)) {
//            $ans->setErrors($errors)->setEmptyContent();
//        }
    }
    
    public function subscriberFields(){
        $ans = $this->setJsonAns();
        $errors = array();
        $email = $this->request->request->get('email');
        if (empty($email)){
            $errors['email'] = 'empty';
        } else {
            $member = $this->sc->getMember($email);
            if (empty($member)){
                $errors['member'] = 'not_found';
            }
        }
        if (!empty($errors)){
            $ans->setErrors($errors)->setEmptyContent();
        } else {
            $ans
                ->setFormData($member->asArray());
        }
    }
    
    public function editSubscriber(){
        $errors = array();
        \Models\Validator::getInstance($this->request)->checkFewResponseValues(array(
            'name' => array('type' => 'checkString'),
            'surname' => array('type' => 'checkString'),
            'company_name' => array('type' => 'checkString', 'options' => array('empty' => true)),
            'email' => array('type' => 'checkEmail'),
            'scope' => array('type' => 'checkInt', 'options' => array('empty' => true))
        ), $errors);
        if (empty($errors)){
            $referrer = $this->request->server->get('HTTP_REFERER');
            if (!empty($referrer)) {
                $parts = parse_url($referrer);
                if (!empty($parts['query'])) {
                    $parts = explode('&', $parts['query']);
                    foreach($parts as $p){
                        $p = explode('=', $p);
                        if (!empty($p[0]) && isset($p[1])) {
                            $this->request->query->set($p[0], $p[1]);
                        }
                    }
                }
            }
            $this->sc->setMember($this->request->request->all());
            return $this->run('subscribersList');
        } else {
            $this->setJsonAns()->setErrors($errors)->setEmptyContent();
        }
    }
    
    public function toggleLockStatus(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $email = $this->request->request->get('email');
        $status = $this->request->request->get('status');
        if (empty($email)){
            $ans->addErrorByKey('email', 'empty');
        } else {
            $this->sc->setUserSubscribeStatus($email, $status);
            $ans->addData('status', 'ok');
        }
    }
    
    public function deleteSubscribers(){
        $emails = $this->request->request->get('email', $this->request->query->get('email'));
        $group_id = $this->request->request->get('group', $this->request->query->get('group'));
        if (empty($emails)){
            $this->setJsonAns()->setEmptyContent()->addErrorByKey('email', 'empty');
        } elseif(empty($group_id)) {
            $this->setJsonAns()->setEmptyContent()->addErrorByKey('group', 'empty');
        } else {
            $this->sc->deleteMembersFromList($emails, $group_id);
            $this->request->request->set('email', NULL);
            $this->request->query->set('email', NULL);
            return $this->run('subscribersList');
        }
    }
    
    public function synchronize(){
        $this->sc->cronSynchronize();
        return '';
    }
}
