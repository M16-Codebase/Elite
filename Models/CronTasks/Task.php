<?php
namespace Models\CronTasks;

use LPS\Config;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * Абстрактный класс задачи крона
 * Для появления нового типа задач достаточно создать класс задач (наследованный от текущего), и активировать их в "расписании задач"
 *
 * @author olya
 */
abstract class Task implements \ArrayAccess, iCronTask{
    /**
     * общая таблица задач крона
     */
    const TABLE = 'cron_tasks';
    /**
     * таблица с расписанием и описанием типов задач
     */
    const TABLE_SHEDULE = 'cron_task_shedule';
    /**
     * запись обрабатываемых ошибок задач крона
     */
    const TABLE_ERRORS = 'cron_tasks_errors';
    /**
     * где лежат классы задач
     */
    const TASK_TYPE_PATH = 'Models/CronTasks';
    /* статусы */
    const STATUS_NEW = 'new';
    const STATUS_PROCESS = 'processed';
    const STATUS_COMPLETE = 'complete';
    const STATUS_CANCEL = 'cancel';
    const STATUS_SENT = 'sent';
    /**
     * путь к файлам крон задач
     */
    const FILE_PATH = '/data/cron_task_files/';
    /**
     * можно ли останавливать задачу
     */
    const STOPPABLE = FALSE;
    /**
     * можно ли отменять задачу
     */
    const CANCELABLE = FALSE;
    /**
     * событие на остановку
     */
    const EVENT_STOP = 'stop';
    /**
     * событие на отмену
     */
    const EVENT_CANCEL = 'cancel';
    /**
     * является задача периодической или одноразовой. TRUE == одноразовая
     */
    const MANUAL = FALSE;
    /**
     * название задачи на русском языке по умолчанию (можно переопределить в БД)
     */
    const TITLE = '';
    /**
     *
     * @var \MysqlSimple\Controller
     */
    private static $db = NULL;
    /**
     * Расписание в виде массива с данными о каждом типе задачи
     * @var array 
     */
    private static $shedule = array();
    /**
     * Список загружаемых полей
     * @var array
     */
    private static $load_fields = array('id', 'time_create', 'time_start', 'time_end', 'status', 'data', 'segment_id', 'percent', 'errors', 'type', 'user_id', 'file_name', 'count_errors', 'event');
    /**
     * Список существующих типов задач
     * @var array
     */
    private static $existsTypes = array();
    
    private $task_id = NULL;
    private $data = NULL;
    /**
     *
     * @return Task
     */
    final public static function getById($task_id){
        if (empty($task_id)){
            return;
        }
        $result = self::search(array('ids' => array($task_id)), $count, 0, 1);
        return reset($result);
    }
    private static function init(){
        if (!isset(self::$db)){
            self::$db = \App\Builder::getInstance()->getDB();
        }
    }
    /**
     * название типа задачи, по-умолчанию — имя класса без неймспейса;
     */
    final public static function getType(){
        $ref_class = new \ReflectionClass(get_called_class());
        return !$ref_class->isAbstract() ? substr(get_called_class(), strrpos(get_called_class(), '\\')+1) : false;
    }
    /**
     * Добавляем новую задачу, можно не передавать статус (автоматом проставится NEW)
     * время создания запишется текущее
     * @param array $data
     * <ul>
     *  <li>user_id</li>
     *  <li>time_create</li>
     *  <li>time_start</li>
     *  <li>time_end</li>
     *  <li>segment_id</li>
     *  <li>file_name</li>
     *  <li>status</li>
     *  <li>data</li>
     *  <li>type</li>
     *  <li>percent</li>
     *  <li>errors</li>
     * </ul>
     * @param UploadedFile|string $FILE
     * @return boolean
     */
    final public static function add($data, $FILE = NULL){
        if (empty($data['type'])){
            throw new \Exception('Должен передаваться тип задачи');
        }
        self::init();
        if (!empty($FILE)){
            $data['file_name'] = $FILE instanceof UploadedFile ? $FILE->getClientOriginalName() : basename($FILE);
        }
        $update_data = array_intersect_key($data, array_flip(self::$load_fields));
        if (empty($update_data['data'])){
            $update_data['data'] = array_diff_key($data, array_flip(self::$load_fields));
        }
        if (isset($update_data['time_create'])){
            unset($update_data['time_create']);//все равно текущее требуется
        }
        if (empty($update_data['status'])){
            $update_data['status'] = self::STATUS_NEW;
        }
        if (!empty($FILE)){
            $file_extension = $FILE instanceof UploadedFile ? $FILE->getClientOriginalExtension() : pathinfo($FILE)['extension'];
            $update_data['data']['ext'] = $file_extension;
            $update_data['file_name'] = $FILE instanceof UploadedFile ? $FILE->getClientOriginalName() : basename($FILE);
            $file_code = \LPS\Components\Encoding::detect($FILE instanceof UploadedFile ? $FILE->getRealPath() : $FILE);
            $update_data['data']['code'] = $file_code;
        }
        if (!empty($update_data['data'])){
            //ВНИМАНИЕ, дальше использовать $update_data['data'] как массив нельзя
            $update_data['data'] = json_encode($update_data['data'], JSON_UNESCAPED_UNICODE);
        } else {
            unset($update_data['data']);
        }
        $task_id = self::$db->query('INSERT INTO `'.self::TABLE.'` SET ?a, `time_create` = NOW()', $update_data);
        if (!empty($FILE)){
            $abs_path = self::getFilePath($task_id, 'absolute');
            if (!file_exists($abs_path)){
                \LPS\Components\FS::makeDirs($abs_path, 0770);
            }
            $new_file_name = $abs_path . $task_id . '.' . $file_extension;
            if ($file_code == 'utf-8'){
                if ($FILE instanceof \Symfony\Component\HttpFoundation\File\UploadedFile){
                    $result = move_uploaded_file($FILE->getRealPath(), $new_file_name);
                }else{
                    $result = rename($FILE, $new_file_name);
                }
            }else{
                $result = file_put_contents($new_file_name, \LPS\Components\Encoding::getBom('utf-8') . mb_convert_encoding(file_get_contents($FILE instanceof UploadedFile ? $FILE->getRealPath() : $FILE), 'utf-8', $file_code));
                if ($result){
                    unlink($FILE instanceof UploadedFile ? $FILE->getRealPath() : $FILE);
                }
            }
            if (!$result){
                $task = Task::getById($task_id);
                $task->setCancel(array('errors' => 'Не удалось скопировать файл'));
                return FALSE;
            }
            chmod($new_file_name, 0660);
        }
        return $task_id;
    }
    /**
     * Поиск задач
     * @param array $params
     * @param 0|NULL $count
     * @param int $start
     * @param int $limit
     * @return self[]
     */
    final public static function search($params, &$count = NULL, $start = 0, $limit = 1000000000){
        self::init();
        $order_part = !empty($params['order']) ? self::getOrderString($params['order']) : NULL;
        $loadFields = '`task`.`' . implode('`, `task`.`', self::$load_fields) . '`';
        $tasks = self::$db->query('
			SELECT SQL_CALC_FOUND_ROWS '. $loadFields .', 
                    UNIX_TIMESTAMP(`task`.`time_create`) AS `timestamp_create`, 
                    UNIX_TIMESTAMP(`task`.`time_start`) AS `timestamp_start`, 
                    UNIX_TIMESTAMP(`task`.`time_end`) AS `timestamp_end`
                FROM `'.self::TABLE.'` AS `task`
				WHERE 1
                { AND `task`.`segment_id` = ?d}
                { AND `task`.`id` IN (?i)}
                { AND `task`.`type` IN (?l)}
                { AND `task`.`status` IN (?l)}
                { AND `task`.`time_create` >= ?s}
                { AND `task`.`time_create` <= ?s}
                { AND `task`.`user_id` IN (?i)}
                { AND `task`.`file_name` LIKE ?s}
                { AND `task`.`event` = ?s}
                { AND `task`.`event` IS NULL AND ?d}
                ORDER BY ' . (!empty($order_part) ? $order_part : ('`task`.`time_create` DESC')) . '
                LIMIT ?d, ?d
            ', 
            !empty($params['segment_id']) ? $params['segment_id'] : self::$db->skipIt(),
            !empty($params['ids']) ? $params['ids'] : self::$db->skipIt(),
            !empty($params['type']) ? (is_array($params['type']) ? $params['type'] : array($params['type'])) : self::$db->skipIt(),
            !empty($params['status']) ? (is_array($params['status']) ? $params['status'] : array($params['status'])) : self::$db->skipIt(),
            !empty($params['from']) ? date('Y-m-d H:i:s', strtotime($params['from'])) : self::$db->skipIt(),
            !empty($params['to']) ? date('Y-m-d H:i:s', strtotime($params['to'])) : self::$db->skipIt(),
            !empty($params['user_id']) ? $params['user_id'] : self::$db->skipIt(),
            !empty($params['file_name']) ? ('%' . $params['file_name'] . '%') : self::$db->skipIt(),
            !empty($params['event']) ? $params['event'] : self::$db->skipIt(),
            array_key_exists('event', $params) && is_null($params['event']) ? 1 : self::$db->skipIt(),
            $start,
            $limit
        )->select('id');
        $count = self::$db->query('SELECT FOUND_ROWS()')->getCell();
        $result = array();
        foreach ($tasks as $t){
            $class_name = self::checkTypeExists($t['type']);
            if (!$class_name){
                throw new \Exception('Не найден метод для типа крон-задачи: ' . $t['type']);
            }
            $result[$t['id']] = new $class_name($t);
        }
		return $result;
    }
    private static function checkTypeExists($type){
        if (!isset(self::$existsTypes[$type])){
            $class_name = '\\' . str_replace('/', '\\', self::TASK_TYPE_PATH) . '\\' . ucfirst($type);
            if (class_exists($class_name)){
                $ref_class = new \ReflectionClass($class_name);
                if ($ref_class->isAbstract() || !$ref_class->isSubclassOf(__CLASS__) || $ref_class->isInterface()){
                    self::$existsTypes[$type] = FALSE;
                }else{
                    self::$existsTypes[$type] = $class_name;
                }
            }else{
                self::$existsTypes[$type] = FALSE;
            }
        }
        return self::$existsTypes[$type];
    }
    private static function getOrderString($order_params){
        $order_part = '';
        if (empty($order_params)){
            return;
        }
        if (is_array($order_params)){
            foreach ($order_params as $key => $desc){
                $order[] = '`task`.`' . $key . '`' . (!empty($desc) ? ' DESC ' : ' ');
            }
            $order_part = implode(',', $order);
        }else{
            throw new \LogicException('Order param must be an array("key" => "desc")');
        }
        return $order_part;
    }
    /**
     * Тут мы хотим получить следующую необработанную задачу
     * @param string $type @see self::$types
     * @param array $statuses не только новые, но возможно и "в работе"
     * @return Task
     * @throws \Exception
     */
    final public static function getNext($type, $statuses = array(self::STATUS_NEW)){
        $params = array(
            'status' => $statuses,
            'type' => $type,
            'order' => array(
                'time_create' => 0//нам нужна самая ранняя задача
            ),
            'event' => NULL//задача не должна быть вручную остановлена или отменена
        );
        $result = self::search($params, $count, 0, 1);
        return !empty($result) ? reset($result) : NULL;
    }
    /**
     * Отдает текущую задачу
     */
    final public static function getCurrent(){
        //по умолчанию сортировка обратная по дате создания, поэтому нужен только статус
        $params = array(
            'status' => self::STATUS_PROCESS
        );
        $result = self::search($params, $count, 0, 1);
        return reset($result);
    }
    /**
     * Удаляем стаааарые файлы
     * @return type
     */
    final public static function removeOldFiles(){
        $expire_time = 60*60*24*30;//месяц назад
        $dir = Config::getRealDocumentRoot() . self::FILE_PATH;
        if (!file_exists($dir)){
            return;
        }
        $iterator = new \RecursiveDirectoryIterator($dir);
        $files = new \RecursiveIteratorIterator($iterator);
        foreach($files as $file) {
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if (!$file->isDir()){
                // текущее время
                $time_sec = time();
                // время изменения файла
                $time_file = $file->getMTime();
                // тепрь узнаем сколько прошло времени (в секундах)
                $time = $time_sec - $time_file;
                if ($time > $expire_time){
                    unlink($file->getRealPath());
                }
            }
        }
    }
    final public static function cancelBrokenTasks($type){
        //все задачи этого класса идут по очереди, поэтому, если есть незавершенные задачи этого типа, то их можно все отменить, т.к. произошел какой-то сбой (смотреть логи)
        $prev_tasks = self::search(array('type' => $type, 'status' => self::STATUS_PROCESS, 'event' => NULL));
        foreach ($prev_tasks as $pt){
            $update_task_params = array('status' => self::STATUS_CANCEL, 'time_end' => date('Y-m-d H:i:s'));
            $task_errors = $pt['errors'];
            if (empty($task_errors)){
                $update_task_params['errors'] = 'Произошел сбой в программе';
            }
            $pt->update($update_task_params);
        }
    }
    /**
     * Путь к файлам
     * @param int $task_id
     * @param string $type relative | absolute
     * @return string
     */
    public static function getFilePath($task_id, $type = 'relative'){
        if (empty($task_id)){
            return;
        }
        $path = self::FILE_PATH . sprintf('%02d/', $task_id % 100);
        if ($type == 'absolute'){
            $path = Config::getRealDocumentRoot().$path;
        }
        return $path;
    }
    /**
     * установить расписание
     */
    final public static function setShedule($data){
        if (empty($data)){
            return;
        }
        self::init();
        $values = array();
        $fields = array('type', 'title', 'plan', 'position', 'status', 'fixed', 'last_time');
        $shedule = self::getShedule();
        foreach ($data as $k => $d){
            $vals = array();
            if (isset($d['plan'])){
                $d['plan'] = json_encode($d['plan'], JSON_UNESCAPED_UNICODE);
            }
            //нам нужен правильный порядок, и только существующие поля
            foreach ($fields as $f){
                $vals[$f] = self::$db->escape_value(array_key_exists($f, $d) ? $d[$f] : (isset($shedule[$d['type']]) ? $shedule[$d['type']][$f] : 0));
            }
            $values[$k] = implode(', ', $vals);
        }
        if (!empty($values)){
            self::$db->query('REPLACE INTO `'.self::TABLE_SHEDULE.'` (`'.implode('`, `', $fields).'`) VALUES ('.implode('), (', $values).')');
        }
        self::$shedule = array();
    }
    final public static function delSheduleType($type){
        self::init();
        self::$db->query('DELETE FROM `'.self::TABLE_SHEDULE.'` WHERE `type` = ?s', $type);
    }
    /**
     * Взять расписание
     * @return type
     */
    final public static function getShedule(){
        self::init();
        if (empty(self::$shedule)){
            self::$shedule = self::$db->query('SELECT * FROM `'.self::TABLE_SHEDULE.'` ORDER BY `position`')->select('type');
            foreach (self::$shedule as &$sh){
                $sh['plan'] = json_decode($sh['plan'], TRUE);
                $type_class = self::checkTypeExists($sh['type']);
                if (!empty($type_class)){
                    $sh['is_manual'] = $type_class::MANUAL;
                    if (empty($sh['title'])){
                        $sh['title'] = $type_class::TITLE;
                    }
                }
            }
        }
        return self::$shedule;
    }
    /**
     * разрешенные и существующие типы задач
     */
    final public static function getTypes(){
        $shedule = self::getShedule();
        $saved_types = array_keys($shedule);
        //надо проверить их на существование
        foreach ($saved_types as $t){
            if (!self::checkTypeExists($t)){
                self::delSheduleType($t);
            }
        }
        //теперь надо проверить новые
        $files = glob(Config::getRealDocumentRoot() . self::TASK_TYPE_PATH . '/*.php');
        foreach ($files as $f){
            self::checkTypeExists(basename($f, '.php'));
        }
        $types = array();
        foreach (self::$existsTypes as $t => $c){
            if (!empty($c)){
                $types[$t] = $c;
            }
        }
        return $types;
    }
    private function __construct($task){
        self::init();
        $this->task_id = $task['id'];
        $this->data = $task;
        if (!empty($task['user_id'])){
            \App\Auth\Users\Factory::getInstance()->prepare(array($task['user_id']));
        }
        $this->data['data'] = json_decode($this->data['data'], 1);
    }
    /**
     * Обновить данные о задаче
     * @param array $data
     * <ul>
     *  <li>user_id</li>
     *  <li>time_create</li>
     *  <li>time_start</li>
     *  <li>time_end</li>
     *  <li>segment_id</li>
     *  <li>file_name</li>
     *  <li>status</li>
     *  <li>data</li>
     *  <li>type</li>
     *  <li>percent</li>
     *  <li>errors</li>
     * </ul>
     * @return boolean
     */
    final public function update($data){
        $update_data = array_intersect_key($data, array_flip(self::$load_fields));
        if (empty($update_data)){
            return;
        }
        if (!empty($update_data['data'])){
            $update_data['data'] = json_encode($update_data['data'], JSON_UNESCAPED_UNICODE);
        }
        if (!empty($update_data['status']) && (in_array($update_data['status'], array(self::STATUS_COMPLETE, self::STATUS_CANCEL, self::STATUS_SENT)))){
            $update_data['time_end'] = date('Y-m-d H:i:s');
        }
        self::$db->query('UPDATE `'.self::TABLE.'` SET ?a WHERE `id` = ?d', $update_data, $this->task_id);
        if (!empty($update_data['data'])){
            $update_data['data'] = json_decode($update_data['data'], 1);
        }
        $this->data = $update_data + $this->data;
        return TRUE;
    }
    /**
     * Стартуем задачу
     * @param type $data
     */
    final public function setStart($data = array()){
        $data['status'] = self::STATUS_PROCESS;
        $data['time_start'] = date('Y-m-d H:i:s');
        return $this->update($data);
    }
    /**
     * Завершаем задачу
     * @param array $data
     * @return boolean
     */
    final public function setComplete($data = array()){
        $data['status'] = self::STATUS_COMPLETE;
        $data['time_end'] = date('Y-m-d H:i:s');
        $data['percent'] = 100;
        $data['count_errors'] = $this->getErrorsCount();
        return $this->update($data);
    }
    /**
     * Отменяем задачу
     * @param array $data
     * @return boolean
     */
    final public function setCancel($data = array()){
        $data['status'] = self::STATUS_CANCEL;
        $data['time_end'] = date('Y-m-d H:i:s');
        $data['count_errors'] = $this->getErrorsCount();
        return $this->update($data);
    }
    /**
     * Получить количество ошибок
     * @return int
     */
    private function getErrorsCount(){
        return self::$db->query('SELECT COUNT(*) FROM `'.self::TABLE_ERRORS.'` WHERE `task_id` = ?d', $this->task_id)->getCell();
    }
    /**
     * Записать ошибку
     * @param int $num идентификационный номер ошибки (в задачах с файлами обычно номер строки файла)
     * @param mixed $error строка ошибки или массив (массив сворачивается в строку json_encode)
     * @return int
     */
    final public function addError($num, $error){
        if (is_array($error)){
            $error = json_encode($error, JSON_UNESCAPED_UNICODE);
        }
        return self::$db->query('INSERT INTO `'.self::TABLE_ERRORS.'` SET `task_id` = ?d, `number` = ?d, `error` = ?s', $this->task_id, $num, $error);
    }
    /**
     * Выставить процент
     * @param int $percent
     * @return boolean
     */
    final public function setPercent($percent, $data = array()){
        $data['percent'] = $percent;
        return $this->update($data);
    }
    /**
     * Получить список ошибок
     * @return array()
     */
    final public function getErrors(){
        return self::$db->query('SELECT * FROM `'.self::TABLE_ERRORS.'` WHERE `task_id` = ?d', $this->task_id)->getCol('number', 'error');
    }
    /**
     * Поставить событие на отмену задачи
     * @return boolean
     */
    final public function setCancelEvent(){
        if (!$this->isCancelable()){
            return NULL;
        }
        return $this->update(array('event' => 'cancel'));
    }
    /**
     * Поставить событие на остановку задачи
     * @return boolean
     */
    final public function setStopEvent(){
        if (!$this->isStoppable()){
            return NULL;
        }
        return $this->update(array('event' => 'stop'));
    }
    /**
     * Отменить событие на остановку задачи
     * @return boolean
     */
    final public function setRestartEvent(){
        if (!$this->isStoppable()){
            return NULL;
        }
        return $this->update(array('event' => NULL));
    }
    /**
     * Проверить, не выставили ли событие
     * @return boolean
     * @throws \Exception
     */
    final public function checkEvent(){
        //Проверяем что там в базе
        $event = self::$db->query('SELECT `event` FROM `'.self::TABLE.'` WHERE `id` = ?d', $this['id'])->getCell();
        if (empty($event)){
            return FALSE;
        }
        if ($event == 'cancel'){
            $this->setCancel(array('event' => NULL));
        }elseif ($event == 'stop'){
            //ничего не делаем, т.к. отдается название события, и дальше задача сама разбирается
        }else{
            throw new \Exception('Предусмотрены только следующие события: cancel и stop. Получено событие: ' . $event);
        }
        return $event;
    }
    /**
     * Можно ли останавливать задачу
     * @return boolean
     */
    final public function isStoppable(){
        return static::STOPPABLE && $this['event'] != self::EVENT_STOP && in_array($this['status'], array(self::STATUS_NEW, self::STATUS_PROCESS));
    }
    /**
     * Можно ли отменять задачу
     * @return boolean
     */
    final public function isCancelable(){
        return static::CANCELABLE && $this['event'] != self::EVENT_CANCEL && in_array($this['status'], array(self::STATUS_NEW, self::STATUS_PROCESS));
    }
    final public function isRestartable(){
        return static::STOPPABLE && $this['event'] == self::EVENT_STOP && in_array($this['status'], array(self::STATUS_NEW, self::STATUS_PROCESS));
    }
    final public function isManual(){
        return static::MANUAL;
    }
    /**
     * Получить путь к файлу, на основании которого строится задача
     * @param enum $type short | relative | absolute
     * @return type
     */
    final public function getFile($type = 'short'){
        if (empty($this['data']['ext'])){
            return;
        }
        return self::getFilePath($this->task_id, $type) . $this->task_id . '.' . $this['data']['ext'];
    }
    /**
     * Когда заканчивается итерация в задаче, надо установить процент задачи и проверить, не остановлена ли она
     * @param int $percent
     * @return bool
     */
    final protected function iterationComplete($percent, $data = array()){
        $this->setPercent($percent, $data);
        $event = $this->checkEvent();
        return $event === FALSE;
    }
    /* ****************************** ArrayAccess **************************** */

    /**
     * @param string $offset
     * @return bool
     */
    final public function offsetExists($offset) {
        if ($offset == 'user_email'){
            return isset($this->data['user_id']);
        }
        return isset($this->data[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    final public function offsetGet($offset) {
        if ($offset == 'user_email'){
            return !empty($this->data['user_id']) ? \App\Auth\Users\Factory::getInstance()->getUserById($this->data['user_id']) : NULL;
        }
        return $this->data[$offset];
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws \Exception
     */
    final public function offsetSet($offset, $value) {
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }

    /**
     * @param string $offset
     * @throws \Exception
     */
    final public function offsetUnset($offset) {
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }
}
