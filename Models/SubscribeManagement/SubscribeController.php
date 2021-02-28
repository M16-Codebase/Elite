<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Models\SubscribeManagement;
use Models\CronTasks\Task as CronTask;

/**
 * Description of SubscribeController
 *
 * @author mac-proger
 */
class SubscribeController {
    
    const MEMBERS_TABLE = 'subscribe_members';
    const LISTS_TABLE = 'subscribe_groups';
    const MEMBERS_TO_LIST_TABLE = 'subscribe_list_members';
    
    const MAIN_GROUP_ID = 'main';
    const MAIN_GROUP_TITLE = 'Общий список';
    // Стандартные списки, которые должны быть всегда, и которые нельзя удалить
    private static $standart_lists = array();

    private static $subscriber_fields = array('name', 'surname', 'company_name');
    
    const AUTH_CONFIG_KEY = 'sendsay_auth_data';
    const AUTH_SESSION_KEY = 'subscribe_auth_session';
    
    const LOGIN = 'santech';
    const SUBLOGIN = 'santech';
    const PASSWORD = 'cha0Dih';

    const RESULT_RESPONSE = 'response';
    const RESULT_SAVE = 'save';
    /**
     * Статусы для отложенных задач на sendsay
     */
    const TASK_STATUS_MODERATION_REJECTED = -6; // - действие не прошло модерацию
    const TASK_STATUS_PREMODERATION = -5; // - действие на премодерации
    const TASK_STATUS_ = -4; // - отложенное действие (например отложенный выпуск рассылки)
    const TASK_STATUS_CANCEL = -3; // - отменён
    const TASK_STATUS_ERROR = -2; // - закончился ошибкой
    const TASK_STATUS_SUCCESS = -1; // - закончился успешно
    const TASK_STATUS_NEW =  0; // - принят
    const TASK_STATUS_RUN =  1; // - запущен
    const TASK_STATUS_PROCESSING =  2; // - начата обработка
    const TASK_STATUS_SORT =  3; // - сортировка
    const TASK_STATUS_FORMAT =  4; // - форматирование
    const TASK_STATUS_REPORT_GENERATION =  5; // - генерация отчёта

    const PERSONAL_DATA_ANKETA_ID = 'userdata'; //'a582';
    const ANKETA_FIO_KEY = 'name'; //'q709';
    const ANKETA_NAME_KEY = 'name';
    const ANKETA_SURNAME_KEY = 'surname';
    const ANKETA_COMPANY_NAME_KEY = 'company_name';
    
    const USER_FORMAT_ID = 'memberinfo';

    const API_URL = 'https://api.sendsay.ru';

    const FILE_DOWNLOAD_URL = 'https://sendsay.ru/manage/stat/report/?download=';
    const REPORT_FILES_TMP_PATH = '/data/temp/subscribe/';
    const REPORT_FILES_UNPACK_PATH = '/data/temp/subscribe/result/';
    
    const CONFIRM_MODE = 0;
    
    private static $instance = NULL;
    
    private $auth_data = array();
    private $session = NULL;
    private $site_config = NULL;
    
    private $db = NULL;
    
    public static function getStdLists(){
        if (empty(self::$standart_lists)){
            self::$standart_lists = array(
                self::MAIN_GROUP_ID => self::MAIN_GROUP_TITLE
            );
        }
        return self::$standart_lists;
    }
    
    public static function getUsedSubscribeGroups(){
        return self::$used_subscibe_groups;
    }
    /**
     * возвращает список полей, необходимых для обновления
     * @return array
     */
    public static function getSubscriberFields(){
        return self::$subscriber_fields;
    }
    /**
     * 
     * @return SubscribeController
     */
    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->site_config = \Models\TechnicalConfig::getInstance();
        $this->auth_data = json_decode($this->site_config->get(self::AUTH_CONFIG_KEY), TRUE);
        $this->db = \App\Builder::getInstance()->getDB();
    }
    
    public static function cronSynchronize(){
        $task = CronTask::getNext(
            \App\Configs\CronTaskConfig::TASK_SENDSAY_SYNCHRONIZE,
            array(CronTask::STATUS_NEW, CronTask::STATUS_PROCESS)
        );
        if (!empty($task)){
            return self::resumeCronSynchronize();
        }
        $task_id = CronTask::add(array(
            'type' => \App\Configs\CronTaskConfig::TASK_SENDSAY_SYNCHRONIZE,
            'segment_id' => NULL,
            'status' => CronTask::STATUS_NEW,
            'time_create' => date('Y-m-d H:i:s'),
            'user_id' => NULL
        ));
        $task = CronTask::getById($task_id);
        $task->update(array(
            'status' => CronTask::STATUS_PROCESS,
            'time_start' => date('Y-m-d H:i:s')
        ));
        $controller = self::getInstance();
        // Проверка наличия стандартных списков
        $controller->recreateStdGroups();
        $task->update(array(
            'percent' => 5
        ));
        // Отложенное обновление данных подписчиков на сабскрайбе
        $request_list = array();
        $subscribers_for_update = SubscribeMember::search(array('need_update' => true), $count, 0, 100000);
        if (!empty($subscribers_for_update)){
            foreach($subscribers_for_update as $member){
                $request_list[] = $controller->setMember($member->asArray(), TRUE);
            }

            $result = $controller->makeRequest(array(
                'action' => 'batch',
                'stop_on_error' => 0,
                'do' => $request_list
            ));
            if (!empty($result['result'])){
                foreach($result['result'] as $res){
                    if (empty($res['errors'])){
                        $obj = isset($res['obj']['obj']) ? $res['obj']['obj'] : $res['obj'];
                        $email = $obj['member']['email'];
                        if (!empty($subscribers_for_update[$email])) {
                            $subscribers_for_update[$email]->edit(array('need_update' => 0));
                        }
                    }
                }
            }
        }
        $task->update(array(
            'percent' => 10
        ));
        // Постановка задач на сабскрайбе
        $tasks = array();
        foreach(array('active', 'unconfirmed', 'removed') as $group_id){
            $tasks[$group_id] = array(
                'group_id' => $group_id,
                'track_id' => $controller->getMemberList($group_id, self::RESULT_SAVE),
                'status' => self::TASK_STATUS_NEW
            );
        }
        $data = $task['data'];
        if (empty($data)){
            $data = array();
        }
        $data['tasks'] = $tasks;
        $task->update(array(
            'data' => $data
        ));
    }

    public function recreateStdGroups() {
        $group_list = $this->getGroupList($error, TRUE);
        $group_recreate = array();
        foreach(self::getStdLists() as $group_id => $group_name){
            if (empty($group_list[$group_id])){
                $group_recreate[$group_id] = $group_name;
            }
        }
        if (!empty($group_recreate)){
            foreach ($group_recreate as $group_id => $group_name){
                $this->createGroup($group_name, 'list', $group_id);
            }
        }
    }

    /**
     * Продолжение синхронизации
     * (получение статусов пользователей может занять длительное время, поэтому отчеты о пользователях забираются асинхронно)
     * @return null
     * @throws \Exception
     */
    public static function resumeCronSynchronize(){
        $cron_task = CronTask::getNext(
            \App\Configs\CronTaskConfig::TASK_SENDSAY_SYNCHRONIZE,
            array(CronTask::STATUS_NEW, CronTask::STATUS_PROCESS));
        if (empty($cron_task)){
            return NULL;
        }
        $last_task = CronTask::search(array(
            'type' => \App\Configs\CronTaskConfig::TASK_SENDSAY_SYNCHRONIZE,
            'status' => CronTask::STATUS_COMPLETE
        ), $count, 0, 1);
        $last_task = reset($last_task);
        $last_sync_date = !empty($last_task) ? $last_task['time_start'] : NULL;
        if (empty($cron_task['data']['tasks'])){
            $cron_task->addError(0, 'empty_tasks');
            $cron_task->update(array('status' => CronTask::STATUS_CANCEL));
            return NULL;
        }
        $controller = self::getInstance();
        $tasks = $cron_task['data']['tasks'];
        $complete_count = 0;
        foreach($tasks as $id => $task){
            if ($task['status'] != self::TASK_STATUS_SUCCESS){
                $track_info = $controller->getTrackInfo($task['track_id'], $status);
                if ($status == self::TASK_STATUS_SUCCESS){
                    $filename = $track_info['param']['report_file'];
                    $controller->importSendsayUsersCsv($task['group_id'], $filename, $last_sync_date);
                    $tasks[$id]['status'] = self::TASK_STATUS_SUCCESS;
                    $tasks[$id]['filename'] = $filename;
                    $complete_count++;
                }
            } else {
                $complete_count++;
            }
        }
        $percent = 10 + 30 * $complete_count;
        $cron_task->update(array(
            'percent' => $percent,
            'status' => $complete_count == 3 ? CronTask::STATUS_COMPLETE : CronTask::STATUS_PROCESS,
            'data' => array(
                'tasks' => $tasks
            )
        ));
    }

    public function getTrackInfo($track_id, &$status = NULL){
        $result = $this->makeRequest(array(
            'action' => 'track.get',
            'id' => $track_id
        ));
        if (!empty($result['obj']['status'])){
            $status = $result['obj']['status'];
        }
        return !empty($result['obj']) ? $result['obj'] : NULL;
    }

    public function importSendsayUsersCsv($group_id, $filename, $last_update = NULL){
        $fp = $this->getReportFileFromSendsay($filename, $error);
        if (!empty($error) || empty($fp)){
            return NULL;
        }
        $db = \App\Builder::getInstance()->getDB();
        $inner_users_mails =  $db->query('SELECT `u`.`email` FROM ?# as `u` WHERE `u`.`person_type` = "org" OR `u`.`person_type` = "fiz"', \App\Auth\Users\Factory::TABLE)
            ->select('email');
        $field_names = array(
            'email',
            'surname',
            'name',
            'company_name',
            'create.time',
            'update.time',
            'lockconfirm',
            'lockremove'
        );
        while($line = fgets($fp)){
            $member = \LPS\Components\FormatString::getCsv($line);
            $member = array_map(function($val){return iconv('cp1251', 'UTF-8', $val);}, $member);
            $member = array_combine($field_names, $member);
            if (empty($member['lockremove'])) {
                $member['lockremove'] = NULL;
            }
            if (!empty($last_update) && $member['update.time'] < $last_update && $member['email'] != 'cmy-7@mail.ru' && $member['email'] != '000009bb@mail.ru'){
                continue;
            }
            $member['inner'] = !empty($inner_users_mails[$member['email']]) ? 1 : 0;
            $subscriber = SubscribeMember::getByEmail($member['email']);
            if (!empty($subscriber)){
                $subscriber->edit(array('lockremove' => $member['lockremove'], 'create_time' => $member['create.time']));
            } else {
                SubscribeMember::create($member['email'], $member);
            }
        }
    }

    public function getSession(){
        $session = $this->makeRequest(array(
            'action' => 'login',
            'login' => $this->auth_data['login'],
            'sublogin' => $this->auth_data['sublogin'],
            'passwd' => $this->auth_data['password']
        ), FALSE);
        return !empty($session['session']) ? $session['session'] : NULL;
    }

    /**
     * @param $filename
     * @param null $error
     * @return null|resource
     */
    protected function getReportFileFromSendsay($filename, &$error = NULL){
        $result_path = \LPS\Config::getRealDocumentRoot() . self::REPORT_FILES_UNPACK_PATH . $filename;
        $zip_tail = '.zip';
        if (substr($result_path, -strlen($zip_tail)) == $zip_tail){
            $result_path = substr($result_path, 0, -strlen($zip_tail));
        }
        if (!file_exists($result_path)){
            $session = $this->getSession();
            if (!empty($session)){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, self::FILE_DOWNLOAD_URL.$filename);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_COOKIE, 'Subscribe::ASPAuth_login='.$session);
                $output = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($httpCode != 200){
                    $error = 'http_connection_error';
                    return NULL;
                }
                $tmp_dir = \LPS\Config::getRealDocumentRoot() . '/data/temp/subscribe/';
                if (!file_exists($tmp_dir)){
                    mkdir($tmp_dir, 0770, true);
                }
                $filename = str_replace('/', '', $filename);
                $tmp_file = $tmp_dir.$filename;
                file_put_contents($tmp_file, $output);
                $zip = new \ZipArchive();
                if ($zip->open($tmp_file) === TRUE){
                    $zip->extractTo(\LPS\Config::getRealDocumentRoot() . '/data/temp/subscribe/result/');
                    $zip->close();
                    unlink($tmp_file);
                } else {
                    unlink($tmp_file);
                    $error = 'not_zip_archive';
                    return NULL;
                }
            }
        }
        return file_exists($result_path) ? fopen($result_path, 'r') : NULL;
    }
    
    public function importDataFromSendSay(){
        $session = $this->makeRequest(array(
            'action' => 'login',
            'login' => $this->auth_data['login'],
            'sublogin' => $this->auth_data['sublogin'],
            'passwd' => $this->auth_data['password']
        ), FALSE);
        $this->session = $session['session'];
        echo $this->session;
        echo '<h4>login OK</h4>';
        $groups = $this->getGroupList($error, TRUE);
        echo '<p> import ' . count($groups) . ' groups</h4>';
        $std_list = self::getStdLists();
        foreach($groups as $group_id => $group){
            if ($group['type'] == 'list'){
                $this->db->query('REPLACE INTO `' . self::LISTS_TABLE . '` SET `id` = ?s, `name` = ?s, `main_list` = ?d', $group['id'], $group['name'], isset($std_list[$group['id']]) ? 1 : 0);
            }
        }
        
        $users = $this->getMemberList(self::MAIN_GROUP_ID);
        foreach($users as $user){
            if (!empty($user['create.time'])){
                $user['create_time'] = $user['create.time'];
            }
            if (empty($user['lockremove'])){
                $user['lockremove'] = NULL;
            }
            if (empty($user['lockconfirm'])){
                $user['lockconfirm'] = 0;
            }
            SubscribeMember::create($user['email'], $user);
        }
        
        $groups = $this->getGroupList();
        foreach($groups as $group){
            $members = $this->getMemberList($group['id']);
            if (!empty($members)){
                foreach($members as $member){
                    $this->db->query('REPLACE INTO `' . self::MEMBERS_TO_LIST_TABLE . '` SET `group_id` = ?s, `email` = ?s', $group['id'], $member['email']);
                }
            }
        }
        $this->makeRequest(array('action' => 'logout'));
    }
    
    public function test(){
        /*var_dump($this->makeRequest(array(
            'action' => 'anketa.get',
            'id' => 'member'
        )));*/
        var_dump($this->makeRequest(array(
            'action' => 'issue.send',
            'letter' => array(
                'subject' => 'test',
                'from.name' => 'Сантехкомплект',
                'from.email' => 'dk@webactives.ru',
                'reply.email' => 'dk@webactives.ru',
                'reply.name' => 'Roger Waters',
                'to.name' => '[% anketa.userdata.name %]',
                'message' => array(
                    'html' => '<h1>Здравствуйте [% anketa.userdata.name %]</h1><p>Простите что вас тревожим, купите наши краники и никогда больше вы нас не увидите, спасибо'
                )
            ),
            'group' => 'p24',
            'sendwhen' => 'now'
        )));
    }
    
    public function checkAnketa(){
        $result = $this->makeRequest(array(
            'action' => 'anketa.get',
            'id' => self::PERSONAL_DATA_ANKETA_ID
        ));
        if (!empty($result['errors'])){
            $this->makeRequest(array(
                'action' => 'anketa.create',
                'name' => 'Персональные данные',
                'id' => self::PERSONAL_DATA_ANKETA_ID
            ));
            $this->makeRequest(array(
                'action' => 'anketa.quest.add',
                'anketa.id' => self::PERSONAL_DATA_ANKETA_ID,
                'obj' => array(
                    array(
                        'name' => 'Фамилия',
                        'type' => 'free',
                        'id' => self::ANKETA_SURNAME_KEY
                    ),
                    array(
                        'name' => 'Название компании',
                        'type' => 'free',
                        'id' => self::ANKETA_COMPANY_NAME_KEY
                    ),
                    array(
                        'name' => 'Имя',
                        'type' => 'free',
                        'id' => self::ANKETA_NAME_KEY
                    )
                )
            ));
        }
    }
    
    public function formatSetup(){
       /* $result = $this->makeRequest(array(
            'action' => 'format.get',
            'id' => self::USER_FORMAT_ID
        ));*/
       /* if (empty($result) || !empty($result['errors'])){
            $result = $this->makeRequest(array(
                'action' => 'format.create',
                'name' => 'Список подписчиков',
                'id' => self::USER_FORMAT_ID
            ));        
        var_dump($result);    
        }*/
        $result = $this->makeRequest(array(
            'action' => 'format.set',
            'id' => self::USER_FORMAT_ID,
            'obj' => array(
                'name' => 'Список подписчиков',
                'type' => 'view',
                'fields' => array(
                    array(
                        'aid' => 'member',
                        'qid' => 'email'
                    ),
                    array(
                        'aid' => self::PERSONAL_DATA_ANKETA_ID,
                        'qid' => self::ANKETA_SURNAME_KEY
                    ),
                    array(
                        'aid' => self::PERSONAL_DATA_ANKETA_ID,
                        'qid' => self::ANKETA_NAME_KEY
                    ),
                    array(
                        'aid' => self::PERSONAL_DATA_ANKETA_ID,
                        'qid' => self::ANKETA_COMPANY_NAME_KEY
                    ),
                    array(
                        'aid' => 'member',
                        'qid' => 'create.time'
                    ),
                    array(
                        'aid' => 'member',
                        'qid' => 'update.time'
                    ),
                    array(
                        'aid' => 'member',
                        'qid' => 'lockconfirm'
                    ),
                    array(
                        'aid' => 'member',
                        'qid' => 'lockremove'
                    )
                )
            )
        ));
    }
    
    public function initMainList(){
        $result = $this->makeRequest(array(
            'action' => 'group.get',
            'id' => self::MAIN_GROUP_ID
        ));
        if (!empty($result['errors'])){
            $result = $this->makeRequest(array(
                'action' => 'group.create',
                'id' => self::MAIN_GROUP_ID,
                'name' => self::MAIN_GROUP_TITLE,
                'type' => 'list',
                'addr_type' => 'email'
            ));
        }
    }

    /**
     * Выполнение запроса к сервису рассылки
     * @param array $request - параметры запроса
     * @param null|string $redirect - урл редиректа к api sendsay, сюда попадает урл из ответа, присланного от api
     * @param bool $init_auth
     * @param bool $allow_retry
     * @return mixed
     * @throws \ErrorException
     * @internal
     */
    public function makeRequest(array $request, $redirect = NULL, $init_auth = TRUE, $allow_retry = TRUE){
        if ($init_auth){
            if (empty($this->session)) {
                $request['one_time_auth'] = array(
                    'login' => $this->auth_data['login'],
                    'sublogin' => $this->auth_data['sublogin'],
                    'passwd' => $this->auth_data['password']
                );
            } else {
                $request['session'] = $this->session;
            }
//            $session = $this->site_config->get(self::AUTH_SESSION_KEY, 'auth');
//            $request['session'] = !empty($session) ? $session : 'empty';
        }

        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, self::API_URL . $redirect);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s,CURLOPT_POST,true);
        curl_setopt($s, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($s, CURLOPT_SSL_CIPHER_LIST, 'TLSv1.2');
        curl_setopt($s, CURLOPT_POSTFIELDS, array(
            'apiversion' => 100,
            'json' => 1,
            'request.id' => 'santech',
            'request' => json_encode($request)
        ));
        $result = json_decode(curl_exec($s), true);
        if (empty($result) && $errno = curl_errno($s)) {
            throw new \ErrorException("cUrl error #${errno} — " . curl_error($s));
        }
        curl_close($s);
//        if ($request['action'] != 'login' && !empty($result['errors']) && $result['errors'][0]['id'] == 'error/auth/failed'){
//            $auth_result = $this->makeRequest(array(
//                'action' => 'login',
//                'login' => $this->auth_data['login'],
//                'sublogin' => $this->auth_data['sublogin'],
//                'passwd' => $this->auth_data['password'] 
//            ), NULL, FALSE);
//            if (isset($auth_result['REDIRECT'])){
//                $auth_result = $this->makeRequest(array(
//                    'action' => 'login',
//                    'login' => $this->auth_data['login'],
//                    'sublogin' => $this->auth_data['sublogin'],
//                    'passwd' => $this->auth_data['password'] 
//                ), $auth_result['REDIRECT']);
//            }
//            if (empty($auth_result['errors'])){
//                $this->site_config->set(self::AUTH_SESSION_KEY, 'auth', $auth_result['session'], '');
//                if ($allow_retry){
//                    return $this->makeRequest($request, $redirect, TRUE, FALSE);
//                }
//            }
//        }
        // Повтор того же запроса по урлу редиректа (если требуется)
        if (isset($result['REDIRECT'])){
            $result = $this->makeRequest($request, $result['REDIRECT'], $init_auth);
        }
        return $result;
    }
    
    public function checkAuthData($auth_data){
        $result = $this->makeRequest(array(
            'action' => 'pong',
            'one_time_auth' => array(
                'login' => $auth_data['login'],
                'sublogin' => $auth_data['sublogin'],
                'passwd' => $auth_data['password']
            )
        ), NULL, FALSE);
        if (!empty($result['errors'])){
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    public function getGroupList(&$error = NULL, $from_sendsay = FALSE){
        if ($from_sendsay === FALSE) {
            return $this->db->query('SELECT * FROM `' . self::LISTS_TABLE . '`')->select('id');
        }
        $result = $this->makeRequest(array(
         //   'action' => 'group.list'
            'action' => 'group.get',
            'with_filter' => 1,
            'id' => array('*')
        ));
        if (!empty($result['errors'])){
            foreach($result['errors'] as $err_data){
                if ($err_data['id'] == 'error/auth/failed'){
                    $error = $err_data['explain'];
                    return array();
                }
            }
        }
        $group_list = array();
        foreach($result['list'] as $group){
            $group_list[$group['id']] = $group;
        }
        return $group_list;
    }
    
    public function getGroupById($id){
        return $this->db->query('SELECT * FROM `' . self::LISTS_TABLE . '` WHERE `id` = ?s', $id)->getRow();
//        $result = $this->makeRequest(array(
//            'action' => 'group.get',
//            'id' => $id
//        ));
//        if (!empty($result['errors']) || !empty($result['warnings'])) {
//            return NULL;
//        } else {
//            return $result['obj'];
//        }
    }
    /**
     * Создает список подписчиков
     * @param string $name Название списка
     * @param string $type Тип списка (list - список, filter - набор фильтров)
     * @return string id списка подписчиков
     */
    public function createGroup($name, $type, $id = NULL){
        $request = array(
            'action' => 'group.create',
            'name' => $name,
            'type' => $type,
            'addr_type' => 'email'
        );
        if (!empty($id)){
            $request['id'] = $id;
        }
        $result = $this->makeRequest($request);
        if (!empty($result['errors']) || !empty($result['warnings'])) {
            return NULL;
        } else {
            $std_lists = self::getStdLists();
            $this->db->query('INSERT IGNORE INTO `' . self::LISTS_TABLE . '` SET `id`=?s, `name`=?s, `type`=?s, `main_list`=?d', $result['id'], $name, $type, isset($std_lists[$id]) ? 1 : 0);
            return $result['id'];
        }
    }
    
    public function updateGroup($id, $params){
    }
    
    public function deleteGroup($id){
        $result = $this->makeRequest(array(
            'action' => 'group.delete',
            'id' => $id
        ));
        if (!empty($result['errors']) && $result['error'] != 'group_id_not_exists'){
            return FALSE;
        } else {
            $this->db->query('DELETE FROM `' . self::LISTS_TABLE . '` WHERE `id`=?s', $id);
            $this->db->query('DELETE FROM `' . self::MEMBERS_TO_LIST_TABLE . '` WHERE `group_id`=?s', $id);
            
            return TRUE;
        }
    }
    
    public function clearGroup($id){
        $result = $this->makeRequest(array(
            'action' => 'group.clean',
            'id' => $id,
            'all' => 1
        ));
        if (empty($result['errors'])) {
            $this->db->query('DELETE FROM `' . self::MEMBERS_TO_LIST_TABLE . '` WHERE `group_id`=?s', $id);
        }
    }
    
    private function isGroupExists($group_id){
        $result = $this->makeRequest(array(
            'action' => 'group.get',
            'id' => $group_id
        ));
        return empty($result['errors']);
    }
    /**
     * 
     * @param string $group_id - id фильтруемого списка
     * @param array $params - параметры фильтрации
     * @return string
     */
    public function makeFilterGroup($group_id, $params){
        $filter_id = $group_id . 'filter';
        if (!$this->isGroupExists($filter_id)){
            $this->makeRequest(array(
                'action' => 'group.create',
                'id' => $filter_id,
                'name' => $filter_id,
                'type' => 'filter',
                'addr_type' => 'email'
            ));
        }
        $filter_rules = array(
            array(
                'which' => 'PRF',
                'resp' => 1,
                'pid' => $group_id
            )
        );
        if (!empty($params['date_min'])){
            $this->pushFilterRule(array(
                'aid' => 'member', 
                'qid' => 'create.time', 
                'resp' => date('Y-m-d 00:00:00', strtotime($params['date_min'])),
                'which' => 'ge', 
                'qid.name' => 'Дата создания', 
                'aid.name' => 'Системная', 
                'qid.type' => 'dt'
            ), $filter_rules);
        }
        if (!empty($params['date_max'])){
            $this->pushFilterRule(array(
                'aid' => 'member', 
                'qid' => 'create.time', 
                'resp' => date('Y-m-d 23:59:59', strtotime($params['date_max'])),
                'which' => 'le', 
                'qid.name' => 'Дата создания', 
                'aid.name' => 'Системная', 
                'qid.type' => 'dt'
            ), $filter_rules);
        }
        if (!empty($params['status'])){
            $this->pushFilterRule($this->makeStatusRules($params['status']), $filter_rules);
        }
        $this->makeRequest(array(
            'action' => 'group.filter.set',
            'id' => $filter_id,
            'filter' => $filter_rules
        ));
        return $filter_id;
    }
    /**
     * добавляет правило в список рулов через заданное условие (OR, AND)
     * @param array $rule правило фильтрации
     * @param array $filter_rules выходной список рулов
     * @param string $logic условие (OR, AND)
     */
    private function pushFilterRule($rule, &$filter_rules, $logic = 'AND'){
        if (!empty($filter_rules)){
            array_push($filter_rules, array('which' => $logic));
        }
        array_push($filter_rules, $rule);
    }
    
    private function makeStatusRules($params){
        $group = array();
        if (!empty($params['new'])){
            $this->pushFilterRule(array(
                'aid' => "member", 
                'qid' => "lockconfirm", 
                'resp' => null, 
                'which' => "dirtyS", 
                'qid.name' => "Подписка не подтверждена", 
                'aid.name' => "Системная", 
                'qid.type' => "free"
            ), $group, 'OR');
            $this->pushFilterRule(array(
                'aid' => "member", 
                'qid' => "lockremove", 
                'resp' => null, 
                'which' => "emptyS", 
                'qid.name' => "Подписка удалена", 
                'aid.name' => "Системная", 
                'qid.type' => "dt"
            ), $group, 'AND');
        }
        if (!empty($params['lock'])){
            $this->pushFilterRule(array(
                'aid' => "member", 
                'qid' => "lockremove", 
                'resp' => null, 
                'which' => "dirtyS", 
                'qid.name' => "Подписка удалена", 
                'aid.name' => "Системная", 
                'qid.type' => "dt"
            ), $group, 'OR');
        }
        if (!empty($params['active'])){
            $this->pushFilterRule(array(
                'aid' => "member", 
                'qid' => "lockconfirm", 
                'resp' => null, 
                'which' => "emptyS", 
                'qid.name' => "Подписка не подтверждена", 
                'aid.name' => "Системная", 
                'qid.type' => "free"
            ), $group, 'OR');
            $this->pushFilterRule(array(
                'aid' => "member", 
                'qid' => "lockremove", 
                'resp' => null, 
                'which' => "emptyS", 
                'qid.name' => "Подписка удалена", 
                'aid.name' => "Системная", 
                'qid.type' => "dt"
            ), $group, 'AND');
        }
        return array( 
            'which' => "group",
            'group' => $group
        );
    }
    
    public function getMemberList($group_id, $result_mode = self::RESULT_RESPONSE, $page = 1, $pageSize = 100000, $sort = array('field' => 'member.email', 'order' => 'asc')){
        $params = array(
            'action' => 'member.list',
            'group' => $group_id,
            'format' => self::USER_FORMAT_ID,
            'sort' => !empty($sort['field']) ? $sort['field'] : 'member.email',
            'sort.order' => !empty($sort['order']) ? $sort['order'] : 'asc',
            'result' => $result_mode
        );
        if ($result_mode == self::RESULT_RESPONSE) {
            $params['page'] = $page;
            $params['pagesize'] = $pageSize;
        }
        $result = $this->makeRequest($params);
        if (empty($result['errors'])){
            if ($result_mode == self::RESULT_SAVE){
                return $result['track.id'];
            }
            $db = \App\Builder::getInstance()->getDB();
//            $inner_users_list = \App\Auth\Users\Factory::getInstance()->getUsers(array('type' => 'org', 'status' => array('new', 'active')));
            $inner_users_mails =  $db->query('SELECT `email`, `person_type` FROM `users` WHERE `person_type` IN ("fiz", "org")', \App\Builder::getInstance()->getConfig()->getParametr('site', 'site_id'))->select('email');
//            foreach($inner_users_list as $site_user){
//                $inner_users_mails[$site_user['email']] = true;
//            }
            $members = array();
            foreach($result['list'] as $result_line){
                if (is_array($result_line)){
                    $member = array();
                    foreach($result_line as $key => $item){
                        $member[$result['order'][$key]['quest']] = $item;
                    }
                } else {
                    $member = array('email' => $result_line);
                }
                // true для подписчиков из базы сайта
                $member['inner'] = !empty($inner_users_mails[$member['email']]) ? 1 : 0;
                $members[] = $member;
            }
            return $members;
        } else {
            return NULL;
        }
    }
    
    public function getMemberCount($group_id, $field = NULL){
        $result = $this->makeRequest(array(
            'action' => 'member.list.count',
            'group' => $group_id
        ));
        if (empty($result['errors'])){
            if (empty($field)){
                return $result['obj'];
            } else {
                return isset($result['obj'][$field]) ? $result['obj'][$field] : NULL;
            }
        } else {
            return NULL;
        }
    }
    
    public function getMember($email){
        return SubscribeMember::getByEmail($email);
//        $result = $this->makeRequest(array(
//            'action' => 'member.get',
//            'email' => $email
//        ));
//        if (!empty($result['errors'])){
//            return NULL;
//        }
//        $member = array(
//            'email' => $result['obj']['member']['email'],
//            'name' => (isset($result['obj'][self::PERSONAL_DATA_ANKETA_ID]) && isset($result['obj'][self::PERSONAL_DATA_ANKETA_ID][self::ANKETA_NAME_KEY])) ? $result['obj'][self::PERSONAL_DATA_ANKETA_ID][self::ANKETA_NAME_KEY] : '',
//            'surname' => (isset($result['obj'][self::PERSONAL_DATA_ANKETA_ID]) && isset($result['obj'][self::PERSONAL_DATA_ANKETA_ID][self::ANKETA_SURNAME_KEY])) ? $result['obj'][self::PERSONAL_DATA_ANKETA_ID][self::ANKETA_SURNAME_KEY] : '',
//            'company_name' => (isset($result['obj'][self::PERSONAL_DATA_ANKETA_ID]) && isset($result['obj'][self::PERSONAL_DATA_ANKETA_ID][self::ANKETA_COMPANY_NAME_KEY])) ? $result['obj'][self::PERSONAL_DATA_ANKETA_ID][self::ANKETA_COMPANY_NAME_KEY] : '',
//            'create.time' => $result['obj']['member']['create.time'],
//            'lockremove' => !empty($result['obj']['member']['lockremove']) ? $result['obj']['member']['lockremove'] : NULL
//        );
//        return $member;
    }
    
    public function setMember($data = array(), $return_query = FALSE, $if_exists = 'overwrite'){
        if (empty($data['group'])){
            $groups = array(self::MAIN_GROUP_ID => 1);
        } else {
            if (!is_array('group')){
                $groups = array($data['group'] => 1);
            } else{
                $groups = $data['group'];
            }
            $groups[self::MAIN_GROUP_ID] = 1;
        }
        $params = array(
            'action' => 'member.set',
            'addr_type' => 'email',
            'email' => $data['email'],
            'if_exists' => $if_exists,
            'newbie.confirm' => self::CONFIRM_MODE,
            'obj' => array(
                '-group' => $groups
            ),
            'return_fresh_obj' => 1
        );
        if (!empty($data['group_id'])){
            $params['obj']['-group'] = array(
                $data['group_id'] => 1
            );
        }
        if (!empty($data['name']) || !empty($data['surname']) || !empty($data['company_name'])){
            $params['obj'][self::PERSONAL_DATA_ANKETA_ID] = array(
                self::ANKETA_NAME_KEY => !empty($data['name']) ? $data['name'] : '',
                self::ANKETA_SURNAME_KEY => !empty($data['surname']) ? $data['surname'] : '',
                self::ANKETA_COMPANY_NAME_KEY => !empty($data['company_name']) ? $data['company_name'] : ''
            );
        }
        // возврат параметров запроса без отправки его на сервер (нужно для групповых запросов)
        if ($return_query) {
            return $params;
        }
        $result = $this->makeRequest($params);
        if (!empty($result['errors'])){
            $error = reset($result['errors']);
            $error = reset($error);
            return $error == 'member_exists' ? $error : false;
        } else {
            // Сохраняем подписчика в локальную базу
            $group = array();
            foreach($groups as $id=>$val){
                if ($val){
                    $group[] = $id;
                }
            }
            $local_base_member = SubscribeMember::getByEmail($data['email']);
            if (empty($local_base_member)){
                $obj = isset($result['obj']['obj']) ? $result['obj']['obj'] : $result['obj'];
                $ss_result = !empty($obj['member']) ? $obj['member'] : array();
                $local_member_data = array_merge($ss_result, array(
                    'email' => $data['email'],
                    'name' => !empty($data['name']) ? $data['name'] : '',
                    'surname' => !empty($data['surname']) ? $data['surname'] : '',
                    'company_name' => !empty($data['company_name']) ? $data['company_name'] : '',
                    'inner' => !empty($data['inner']) ? $data['inner'] : 0
                ));
                $local_base_member = SubscribeMember::create($local_member_data['email'], $local_member_data);
            } elseif($if_exists == 'overwrite'){
                $local_base_member->edit($data);
            }
            $local_base_member->edit(array('group' => $group));
            return $local_base_member;
        }
        
    }
    
    public function addMemberToList($email, $group_id){
        $result = $this->makeRequest(array(
            'action' => 'member.set',
            'addr_type' => 'email',
            'email' => $email,
            'if_exists' => 'overwrite',
            'newbie.confirm' => self::CONFIRM_MODE,
            'obj' => array(
                '-group' => array(
                    $group_id => 1,
                    self::MAIN_GROUP_ID => 1
                )
            ),
            'return_fresh_obj' => 1
        ));
        if (!empty($result['errors'])){
            return NULL;
        }
        $member = array(
            'email' => $result['obj']['member']['email'],
            'name' => (isset($result['obj'][self::PERSONAL_DATA_ANKETA_ID]) && isset($result['obj'][self::PERSONAL_DATA_ANKETA_ID][self::ANKETA_NAME_KEY])) ? $result['obj'][self::PERSONAL_DATA_ANKETA_ID][self::ANKETA_NAME_KEY] : '',
            'surname' => (isset($result['obj'][self::PERSONAL_DATA_ANKETA_ID]) && isset($result['obj'][self::PERSONAL_DATA_ANKETA_ID][self::ANKETA_SURNAME_KEY])) ? $result['obj'][self::PERSONAL_DATA_ANKETA_ID][self::ANKETA_SURNAME_KEY] : '',
            'company_name' => (isset($result['obj'][self::PERSONAL_DATA_ANKETA_ID]) && isset($result['obj'][self::PERSONAL_DATA_ANKETA_ID][self::ANKETA_COMPANY_NAME_KEY])) ? $result['obj'][self::PERSONAL_DATA_ANKETA_ID][self::ANKETA_COMPANY_NAME_KEY] : '',
            'create_time' => $result['obj']['member']['create.time'],
            'lockremove' => !empty($result['obj']['member']['lockremove']) ? $result['obj']['member']['lockremove'] : NULL
        );
        SubscribeMember::create($email, $member);
        $this->db->query('REPLACE INTO `' . self::MEMBERS_TO_LIST_TABLE . '` SET `group_id`=?s, `email`=?s', $group_id, $email);
        return $member;
    }
    
    public function deleteMembers($email){
        $params = array('action' => 'member.delete');
        if (is_array($email)) {
            $params['list'] = $email;
        } else {
            $params['email'] = $email;
        }
        $this->makeRequest($params);
    }
    
    public function deleteMembersFromList($emails, $group_id){
        $params = array(
            'action' => 'member.set',
            'addr_type' => 'email',
            'if_exists' => 'overwrite',
            'newbie.confirm' => self::CONFIRM_MODE,
            'obj' => array(
                '-group' => array(
                    $group_id => 0
                )
            )
        );
        $emails = is_array($emails) ? $emails : array($emails);
        foreach($emails as $email){
            $params['email'] = $email;
            $this->makeRequest($params);
        }
        $this->db->query('DELETE FROM `' . self::MEMBERS_TO_LIST_TABLE . '` WHERE `group_id`=?s AND `email` IN (?l)', $group_id, $emails);
    }
    
    public function getAnketaList(){
        return $this->makeRequest(array(
            'action' => 'anketa.list'
        ));
    }
    
    public function getAnketa($id){
        return $this->makeRequest(array(
            'action' => 'anketa.get',
            'id' => $id
        ));
    }
    /**
     * Устанавливает/снимает запрет рассылок указанным подписчикам
     * @param string|array $email string or array - список мейлов подписчиков
     * @param bool $status true - разрешить рассылки, false - запретить
     * @param bool $return_query true - вернуть данные запроса без его выполнения (для сборки групповых запросов)
     * @return array
     */
    public function setUserSubscribeStatus($email, $status, $return_query = FALSE){
        $params = array(
            'action' => $status ? 'stoplist.delete' : 'stoplist.add',
            'list' => is_array($email) ? $email : array($email)
        );
        $member = SubscribeMember::getByEmail($email);
        if (!empty($member)){
            $member->edit(array(
                'lockremove' => $status ? NULL : date('Y-m-d H:i:s')
            ));
        }
        // возврат параметров запроса без отправки его на сервер (нужно для групповых запросов)
        if ($return_query) {
            return $params;
        }
        return $this->makeRequest($params);
    }
    
    public function importUsers($users_list, $if_exists = 'overwrite') {
        $query_list = array();
        $stop = array(0 => array(), 1 => array());
        foreach($users_list as $user){
            $user_params = array(
                'email' => $user['email'],
                'name' => !empty($user['name']) ? $user['name'] : '',
                'surname' => !empty($user['surname']) ? $user['surname'] : '',
                'company_name' => !empty($user['company_name']) ? $user['company_name'] : ''
            );
            if (!empty($user['group'])){
                $user_params['group'] = $user['group'];
            }
            $query_list[] = $this->setMember($user_params, TRUE);
            if (!empty($user['subscribe'])) {
                $stop[$user['subscribe']][] = $user['email'];
            }
        }
        foreach($stop as $status => $emails){
            if (!empty($emails)){
                $query_list[] = $this->setUserSubscribeStatus($emails, $status, TRUE);
            }
        }
        $result = $this->makeRequest(array(
            'action' => 'batch',
            'stop_on_error' => 0,
            'do' => $query_list
        ));
        if (!empty($result['result'])) {
            foreach($result['result'] as $meber_data) {
                $member_data = $meber_data['obj'];
                $local_base_member = SubscribeMember::getByEmail($member_data['member']['email']);
                if (empty($local_base_member)){
                    $obj = isset($result['obj']['obj']) ? $result['obj']['obj'] : $result['obj'];
                    $ss_result = !empty($obj['member']) ? $obj['member'] : array();
                    $local_member_data = array_merge($ss_result, array(
                        'email' => $member_data['member']['email'],
                        'name' => !empty($member_data['userdata']['name']) ? $member_data['userdata']['name'] : '',
                        'surname' => !empty($member_data['userdata']['surname']) ? $member_data['userdata']['surname'] : '',
                        'company_name' => !empty($member_data['userdata']['company_name']) ? $member_data['userdata']['company_name'] : '',
                        'inner' => 0
                    ));
                    $local_base_member = SubscribeMember::create($local_member_data['email'], $local_member_data);
                } elseif($if_exists == 'overwrite'){
                    $local_base_member->edit(array(
                        'email' => $member_data['member']['email'],
                        'name' => !empty($member_data['userdata']['name']) ? $member_data['userdata']['name'] : '',
                        'surname' => !empty($member_data['userdata']['surname']) ? $member_data['userdata']['surname'] : '',
                        'company_name' => !empty($member_data['userdata']['company_name']) ? $member_data['userdata']['company_name'] : ''
                    ));
                }
                $local_base_member->edit(array('group' => array_keys($member_data['-group'])));
            }
        }
    }
    /**
     * Экспорт подписчиков из одного списка в другой
     * @param array $data поля <ul><li>export_mode - режим экспорта, значения list, subscribers</li>
     * <li>source_group - id исходной группы (list)</li>
     * <li>source_email - список мейлов подписчиков (subscribers)</li>
     * <li>target_group - id группы приемника</li></ul>
     */
    public function importList($data){
        $from = array();
        if (empty($data['export_mode']) || $data['export_mode'] == 'list'){
            $from['group'] = $data['source_group'];
        } else {
            if (!is_array($data['source_email'])) {
                $from['email'] = $data['source_email'];
            } else {
                $from['list'] = $data['source_email'];
            }
        }
        $this->makeRequest(array(
            'action' => 'group.snapshot',
            'to' => array(
                'id' => $data['target_group'],
                'clean' => 0
            ),
            'from' => $from
        ));
        $emails = $data['export_mode'] == 'list' ?
                $this->db->query('SELECT `email` FROM `' . self::MEMBERS_TO_LIST_TABLE . '` WHERE `group_id`=?s', $data['source_group'])->getCol('email', 'email') :
            (is_array($data['source_email']) ? $data['source_email'] : array($data['source_email']));
        foreach($emails as $email){
            $this->db->query('REPLACE INTO `' . self::MEMBERS_TO_LIST_TABLE . '` SET `group_id` = ?s, `email` = ?s', $data['target_group'], $email);
        }
    }
}
