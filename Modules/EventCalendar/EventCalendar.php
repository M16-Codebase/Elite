<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Modules\EventCalendar;
use Models\Staff;

class EventCalendar extends \LPS\AdminModule{
    
    private $validation_params = array(
        'title' => array('type' => 'checkString'),
        'startDate' => array('type' => 'checkDate'),
        'startTime' => array('type' => 'checkTime'),
        'endDate' => array('type' => 'checkDate'),
        'endTime' => array('type' => 'checkTime'),
        'location' => array('type' => 'checkString'),
        'price' => array('type' => 'checkNumber', 'options' => array('empty' => true)),
        'htmlLink' => array('type' => 'checkUrl', 'options' => array('empty' => true)),
        'type' => array('type' => 'checkNumber')
    );
    
    private $event_colors = array(
        1 => '#ffc4dd',
        2 => '#c7f99f',
        3 => '#f9e59f',
        4 => '#9fd6f9',
        5 => '#f9bf9f',
        6 => '#bdcdff',
        7 => '#a5f1ff'
    );
    
    private $event_types = array(
        0 => array('title' => '', 'color_id' => 1),
        1 => array('title' => 'Совещание', 'color_id' => 2),
        2 => array('title' => 'Пресс-конференция Maris', 'color_id' => 3),
        3 => array('title' => 'Деловые мероприятия', 'color_id' => 4),
        4 => array('title' => 'Корпоративные мероприятия', 'color_id' => 5),
        5 => array('title' => 'Деловые мероприятия для партнеров Maris', 'color_id' => 6),
        6 => array('title' => 'Мероприятия сo-branding', 'color_id' => 7),
        7 => array('title' => 'Сonference call', 'color_id' => 8)
    );
    
    public function index(){
        $start = (date('N') == 1) ? strtotime('-2 week') : strtotime('last monday -2 week');
        $end = strtotime('+2 week', $start);
        $eventcal = \Models\GoogleCalendar\GoogleCalendar::getInstance();
        $events = $eventcal->getEvents();//array('start' => $start, 'end' => $end));
        $eventsByDay = array();
        foreach($events as $event){
            $date = date('d.m.Y', $event['start']);
            if (!isset($eventsByDay[$date])){
                $eventsByDay[$date] = array();
            }
            $eventsByDay[$date][] = $event;
        }
        $this->getAns()
            ->add('calendar_id', urlencode(\LPS\Config::GOOGLE_CALENDAR_ID))
            ->add('events', $eventsByDay)
            ->add('event_colors', $this->event_colors);
    }
    
    public function edit(){
        $errors = array();
        try{
            $eventcal = \Models\GoogleCalendar\GoogleCalendar::getInstance();
        } catch (\Google_AuthException $ex) {
            $errors[] = $ex->getMessage();
        }
        
        $this->setJsonAns();
        $id = $this->request->query->get('id');
        if ($this->request->request->has('save')){
            $validator = \Models\Validator::getInstance($this->request);
            $validator->checkFewResponseValues($this->validation_params, $errors);
            $post = $this->request->request->all();
            if (!isset($post['company']) && !isset($post['dept']) && !isset($post['pers'])){
                $errors['attenders'] = "Не выбраны приглашенные лица";
            }
            
            if (empty($errors)){
                $company = $this->request->request->has('company');
                $depts = isset($post['dept']) ? $post['dept'] : array();
                $persons = isset($post['pers']) ? $post['pers'] : array();
                $params = array(
                    'title' => $post['title'],
                    'description' => $post['description'],
                    'location' => $post['location'],
                    'start' => strtotime($post['startDate'] . ' ' . $post['startTime']),
                    'end' => strtotime($post['endDate'] . ' ' . $post['endTime']),
                    'color' => $this->event_types[$post['type']]['color_id']
                );
                $props = array(
                    'organizer' => $post['organizer'],
                    'price' => $post['price'],
                    'inv_company' => $company,
                    'inv_dept' => implode('.', array_keys($depts)),
                    'inv_pers' => implode('.', array_keys($persons)),
                    'type_id' => $post['type'],
                    'type_title' => $this->event_types[$post['type']]['title']
                );
                if (isset($post['htmlLink'])){
                    $props['htmlLink'] = $post['htmlLink'];
                }
                $guests = array();
                if ($company) {
                    $staff = Staff::search();
                    foreach($staff as $person){
                        $guests[] = array('email' => $person['email'], 'displayName' => $person['name'].' '.$person['surname']);
                    }
                } else {
                    foreach($depts as $key=>$val){
                        $staff = Staff::search(array('parent_id' => $key));
                        foreach($staff as $person){
                            $guests[$person['id']] = array('email' => $person['email'], 'displayName' => $person['name'].' '.$person['surname']);
                        }
                    }
                    if (!empty($persons)){
                        $staff = Staff::search(array('ids' => array_keys($persons)));
                        foreach($staff as $person){
                            $guests[$person['id']] = array('email' => $person['email'], 'displayName' => $person['name'].' '.$person['surname']);
                        }
                    }
                }
                if ($id){
                    $eventcal->updateEvent($id, $params, $props, $guests);
                    $this->getAns()->addData('action', 'update');
                } else {
                    $id = $eventcal->createEvent($params, $props, $guests);
                    $this->getAns()->addData('action', 'create');
                }
            } else {
                $this->getAns()->setErrors($errors);
            }
        }
        if ($id){
            $evt = $eventcal->getEvent($id);
            $this->getAns()->addData('event', $evt)->addData('event_colors', $this->event_colors);
            if (is_null($evt)){
                $ans = $this->getAns();
                $ans->setEmptyContent();
                $ans->addErrorByKey('id', 'event not found');
                return $ans;
            }
            
            $form_data = array(
                'id' => $evt['id'],
                'title' => $evt['title'],
                'description' => $evt['description'],
                'location' => $evt['location'],
                'organizer' => isset($evt['properties']['organizer']) ? $evt['properties']['organizer'] : NULL,
                'price' => isset($evt['properties']['price']) ? $evt['properties']['price'] : NULL,
                'startDate' => date('d.m.Y', $evt['start']),
                'startTime' => date('H:i:s', $evt['start']),
                'endDate' => date('d.m.Y', $evt['end']),
                'endTime' => date('H:i:s', $evt['end']),
                'type' => isset($evt['properties']['type_id']) ? $evt['properties']['type_id'] : 0,
                'company' => isset($evt['properties']['inv_company']) ? $evt['properties']['inv_company'] : FALSE,
                'dept' => isset($evt['properties']['inv_dept']) ? $this->makeCheckbox($evt['properties']['inv_dept']) : array(),
                'pers' => isset($evt['properties']['inv_pers']) ? $this->makeCheckbox($evt['properties']['inv_pers']) : array(),
                'htmlLink' => isset($evt['properties']['htmlLink']) ? $evt['properties']['htmlLink'] : ''
            );
            $this->getAns()
				->add('evt_id', $id)
                ->setFormData($form_data);
        }
        $deptList = \Models\Staff::search(array('empty_parent' => true));
        $departments = array();
        foreach($deptList as $dept){
            $departments[$dept['id']] = array(
                'name' => $dept['name'],
                'staff' => \Models\Staff::search(array('parent_id' => $dept['id']))
            );
        }
        $this->getAns()
            ->add('depts', $departments)
            ->add('evt_types', $this->event_types);
    }
    
    public function deleteEvent(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $eventcal = \Models\GoogleCalendar\GoogleCalendar::getInstance();
        if ($eventcal->deleteEvent($this->request->query->get('id'))){
            $ans->addData('status', 'ok')
                ->addData('id', $this->request->query->get('id'));
        } else {
            $ans->addErrorByKey('error', 'event delete failed');
        }
    }
    
    public function viewEvent(){
        $ans = $this->setJsonAns();
        $errors = array();
        try{
            $eventcal = \Models\GoogleCalendar\GoogleCalendar::getInstance();
        } catch (\Google_AuthException $ex) {
            $errors[] = $ex->getMessage();
        }
        $evt = $eventcal->getEvent($this->request->query->get('id'));
        if (is_null($evt)){
            $ans->addErrorByKey('id', 'Не удалось загрузить событие');
        } else{
            $evt['properties']['inv_company'] = ($evt['properties']['inv_company'] == 'true') ? true : false;
            if ($evt['properties']['inv_company']) {
                $inv_dept = \Models\Staff::search(array('empty_parent' => true));
                $inv_pers = array();
            } else {
                $dept_ids = (!empty($evt['properties']['inv_dept'])) ? explode('.', $evt['properties']['inv_dept']) : array();
                $inv_dept = (!empty($dept_ids)) ? \Models\Staff::search(array('ids' => $dept_ids)) : array();
                $inv_pers = (!empty($evt['properties']['inv_pers'])) ? \Models\Staff::search(array('ids' => explode('.', $evt['properties']['inv_pers']))) : array();
            }
            $pers_by_dept = array();
            foreach($inv_dept as $dept){
                $pers_by_dept[$dept['id']] = \Models\Staff::search(array('parent_id' => $dept['id']));
            }
            if (!empty($inv_pers)) {
                foreach($inv_pers as $key => $pers){
                    if (!in_array($pers['parent_id'], $dept_ids)){
                        $inv_dept[] = \Models\Staff::get($pers['parent_id']);
                        $pers_by_dept[$pers['parent_id']] = array();
                    }
                    if (!isset($pers_by_dept[$pers['parent_id']][$pers['id']])) {
                        $pers_by_dept[$pers['parent_id']][$pers['id']] = $pers;
                    }
                    unset($inv_pers[$key]);
                }
            }
            $this->getAns()
                ->add('event', $evt)
                ->add('inv_dept', $inv_dept)
                ->add('pers_by_dept', $pers_by_dept)
                ->add('start_date', date('d.m.Y H:i', $evt['start']))
                ->add('end_date', date('d.m.Y H:i', $evt['end']))
                ->add('event_colors', $this->event_colors);
        }
    }
    
    public function updateDate(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $errors = array();
        $id = $this->request->request->get('id');
        if ($id){
            $start = $this->request->request->get('start');
            $end = $this->request->request->get('end');
            if (!$start || !$end) {
                $errors[] = 'Не задан диапазон дат';
            }
        } else {
            $errors[] = 'Не задан id';
        }
        
        if (empty($errors)){
            $eventcal = \Models\GoogleCalendar\GoogleCalendar::getInstance();
            if (!$eventcal->updateEvent($id, array('start' => $start, 'end' => $end))){
                $errors[] = 'Не удалось обновить событие';
            } else {
                $ans->addData('status', 'ok');
            }
        } else {
            $ans->setErrors($errors);
        }
    }
    
    private function makeCheckbox($param){
        if (empty($param)) {
            return array();
        }
        $result = array();
        $keys = explode('.', $param);
        foreach($keys as $key){
            $result[$key] = 'on';
        }
        return $result;
    }
}