<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Models\GoogleCalendar;
use \Google_Event;
use \Google_Client;
use \Google_Calendar;
use \Google_EventDateTime;

class GoogleCalendar{
    const SCOPE_URL = 'https://www.googleapis.com/auth/calendar';

    /**
     * @var GoogleCalendar 
     */
    static private $i = Null;
    
    /**
     * @param array $config keys: "client_id", "calendar_id", "service_email", "app_name", "key_file" 
     */
    public static function getInstance($config = NULL) {
        if (is_null(self::$i)){
            self::$i = new GoogleCalendar($config);
        }
        return self::$i;
    }
    
    private $config = array();
    /**
     *
     * @var \Google_Client 
     */
    private $client = null;
    /**
     *
     * @var \Google_Service_Calendar
     */
    private $calendar = null;    
    /**
     * 
     * @param array $config keys: "client_id", "calendar_id", "service_email", "app_name", "key_file"
     */
    public function __construct($config = NULL) {
        if (empty($config)){
            $config = \LPS\Config::$googleapi_default_config;
        }
        $token = \Models\TechnicalConfig::getInstance()->get('google_api_key');
        $json = json_decode($token);
        $this->config = $config;
        $client = new \Google_Client();
        $key = file_get_contents(\LPS\Config::getRealDocumentRoot() . $this->config['key_file']);
        $client->setApplicationName($this->config['app_name']);
        $auth = new \Google_Auth_AssertionCredentials(
            $this->config['service_email'],
            array(self::SCOPE_URL),
            $key
        );
        $auth->sub = $this->config['user_email'];
        $client->setAssertionCredentials($auth);
        $client->setClientId($this->config['client_id']);
//        if (empty($json) || ($json->created + $json->expires_in - 15 < time())){
//            $client::$auth->refreshTokenWithAssertion();
//        } else {
//            $client->setAccessToken($token);
//        }
//
//        $token = $client->getAccessToken();
//        $client->setAccessToken($token);
        \Models\TechnicalConfig::getInstance()->set('google_api_key', 'system', $token, 'google_api_key');
        $this->client = $client;
        $this->calendar = new \Google_Service_Calendar($this->client);
    }
    
    /**
     * @param array $params keys: "title", "description", "location", "start", "end"
     * @param array $properties any key=>value
     * @param array $attendees emails array
     * @param array $notify intervals in minutes
     */
    public function createEvent(Array $params, $properties = array(), $attendees = array(), $notify = array(60)){
        $event = new \Google_Event();
        $params['properties'] = $properties;
        $params['attendees'] = $attendees;
        $params['notify'] = $notify;
        
        $this->updateFields($event, $params);
        $event = $this->calendar->events->insert($this->config['calendar_id'], $event, array('sendNotifications' => true));
        
        return $event['id'];
    }
    
    /**
     * @param int $id event identificator 
     * @param array $params keys: "title", "description", "location", "start", "end"
     * @param array $properties any key=>value
     * @param array $attendees emails array
     * @param array $notify intervals in minutes
     */
    public function updateEvent($id, Array $params = array(), $properties = Null, $attendees = Null, $notify = Null){
        try{
            $event = $this->calendar->events->get($this->config['calendar_id'], $id);
        } catch(\Google_ServiceException $ex) {
            return NULL;
        }
        //\Google_Event()
        if (!empty($attendees)){
            foreach($attendees as $key=>$guest){
                if (gettype($guest) == 'string') {
                    $attendees[$key] = array('email' => $guest);
                }
            }
            $params['attendees'] = $attendees;
        }
        $params['properties'] = $properties;
        $params['notify'] = $notify;
        
        $this->updateFields($event, $params);
        $event = $this->calendar->events->update($this->config['calendar_id'], $id, $event, array('sendNotifications' => true));
        return $event->getId();
    }
    
    /**
     * 
     * @param array $array keys: start, end
     */
    public function getEvents($params = array()){
        $search_opts = array(
//            'calendarId' => $this->config['calendar_id'],
//            'orderBy' => 'startTime',
//            'singleEvents' => true
        );
        if (isset($params['start'])) {
            $search_opts['timeMin'] = date('Y-m-d\T00:00:00.000P', $params['start']);
        }
        if (isset($params['end'])) {
            $search_opts['timeMax'] = date('Y-m-d\T23:59:59.000P', $params['end']);
        }
//        $this->calendar->events->
        $google_events = $this->calendar->events->listEvents($this->config['calendar_id'], $search_opts);
        $events = array();
//        var_dump($google_events->getItems());
        foreach($google_events->getItems() as $item){
            $events[$item['id']] = $this->convertEvent($item);
        }
        return $events;
    }
    /**
     * Get single event
     * @param type $id Google Calendar event id
     */
    public function getEvent($id){
        if (!empty($id)){
            try{
                $evtResource = $this->calendar->events->get($this->config['calendar_id'], $id);
            } catch (\Google_ServiceException $ex) {
                return NULL;
            }
            return $this->convertEvent($evtResource);
        }
        return NULL;
    }
    
    /**
     * 
     * @param int $id event identificator 
     */
    public function deleteEvent($id) {
        try{
            $this->calendar->events->delete($this->config['calendar_id'], $id, array('sendNotifications' => true));
        } catch (\Google_ServiceException $ex) {
            return NULL;
        }
        return TRUE;
    }
    
    private function updateFields(\Google_Event $event, $params){
        if (isset($params['title'])){
            $event->setSummary($params['title']);
        }
        if (isset($params['description'])){
            $description = '';
            /*if (isset($params['properties']['type_title'])){
                $description .= '<p><span style="font-weight:800">Тип мероприятия: </span>'.$params['properties']['type_title'].'</p>';
            }
            if (isset($params['properties']['organizer'])){
                $description .= '<p><span style="font-weight:800">Организатор: </span>'.$params['properties']['organizer'].'</p>';
            }
            if (isset($params['location'])){
                $description .= '<p><span style="font-weight:800">Место проведения: </span>'.$params['location'].'</p>';
            }
            if (isset($params['properties']['price'])){
                $description .= '<p><span style="font-weight:800">Стоимость участия: </span>'.$params['properties']['price'].'</p>';
            }
            if (isset($params['properties']['htmlLink'])){
                $description .= '<p><span style="font-weight:800">Подробности: </span><a href="'.$params['properties']['htmlLink'].'">'.$params['properties']['htmlLink'].'</a></p>';
            }*/
            if (isset($params['properties']['type_title'])){
                $description .= 'Тип мероприятия: '.$params['properties']['type_title']."\n";
            }
            if (isset($params['properties']['organizer'])){
                $description .= 'Организатор: '.$params['properties']['organizer']."\n";
            }
            if (isset($params['location'])){
                $description .= 'Место проведения: '.$params['location']."\n";
            }
            if (isset($params['properties']['price'])){
                $description .= 'Стоимость участия: '.$params['properties']['price']."\n";
            }
            if (isset($params['properties']['htmlLink'])){
                $description .= 'Подробности: '.$params['properties']['htmlLink']."\n\n";
            }
            $description .= $params['description'];
            $event->setDescription($description);
        }
        if (isset($params['start'])){
            $time = new \Google_EventDateTime();
            $time->setDateTime(date('Y-m-d\TH:i:s.000P', $params['start']));
            $event->setStart($time);
        }
        if (isset($params['end'])){
            $time = new \Google_EventDateTime();
            $time->setDateTime(date('Y-m-d\TH:i:s.000P', $params['end']));
            $event->setEnd($time);
        }
        if (isset($params['color'])){
            $event->setColorId($params['color']);
        }
        if (isset($params['location'])){
            $event->setLocation($params['location']);
        }
        if (isset($params['properties'])){
            if (isset($params['description'])) {
                $params['properties']['description'] = $params['description'];
            }
            $extendedProperties = new \Google_EventExtendedProperties();
            $extendedProperties->setShared($params['properties']);
            $event->setExtendedProperties($extendedProperties);
        }
        if (isset($params['attendees'])){
            $attendees = array();
            foreach($params['attendees'] as $guest){
                $attendee = new \Google_EventAttendee();
                $attendee->setEmail($guest['email']);
                if (isset($guest['displayName'])){
                    $attendee->setDisplayName($guest['displayName']);
                }
                $attendees[] = $attendee;
            }
            $event->attendees = $attendees;   
        }
        if (isset($params['notify'])){
            $reminders = new \Google_EventReminders();
            $remList = array();
            foreach($params['notify'] as $notifyTime){
                $reminder = new \Google_EventReminder();
                $reminder->setMethod('email');
                $reminder->setMinutes($notifyTime);
                $remList[] = $reminder;
            }
            $reminders->setUseDefault(false);
            $reminders->setOverrides(array($reminder));
            $event->setReminders($reminders);    
        }
    }
    private function convertEvent($srcEvent){
        $attendee_status = array();
        if (isset($srcEvent['attendees'])){
            foreach($srcEvent['attendees'] as $guest){
                $attendee_status[$guest['email']] = $guest['responseStatus'];
            }
        }
        $event = array(
            'id' => $srcEvent['id'],
            'title' => $srcEvent['summary'],
            'description' => (isset($srcEvent['extendedProperties']) && isset($srcEvent['extendedProperties']['shared']) && isset($srcEvent['extendedProperties']['shared']['description'])) ? $srcEvent['extendedProperties']['shared']['description'] : NULL,
            'location' => isset($srcEvent['location']) ? $srcEvent['location'] : NULL,
            'start' => $this->makeTimeStamp($srcEvent['start']),
            'end' => $this->makeTimeStamp($srcEvent['end']),
            'attendees' => isset($srcEvent['attendees']) ? $srcEvent['attendees'] : array(),
            'notify' => isset($srcEvent['reminders']['overrides']) ? $this->getAttrArray($srcEvent['reminders']['overrides'], 'minutes') : array(),
            'properties' => (isset($srcEvent['extendedProperties']) && isset($srcEvent['extendedProperties']['shared'])) ? $this->getProperties($srcEvent['extendedProperties']['shared']) : array(),
            'htmlLink' => (isset($srcEvent['extendedProperties']['shared']) && isset($srcEvent['extendedProperties']['shared']['htmlLink'])) ? $srcEvent['extendedProperties']['shared']['htmlLink'] : '',
            'guestStatus' => $attendee_status
        );
        return $event;
    }
    
    private function makeTimeStamp($g_date){
        $date = isset($g_date['dateTime']) ? $g_date['dateTime'] : $g_date['date'].' 12:00:00';
        return strtotime($date);
    }
    
    private function getAttrArray($attrs, $key){
        $result = array();
        foreach ($attrs as $item){
            $result[] = $item[$key];
        }
        return $result;
    }
    
    private function getProperties($props){
        $result = array();
        foreach ($props as $key => $prop){
            $result[$key] = $prop;
        }
        return $result;
    }
}

