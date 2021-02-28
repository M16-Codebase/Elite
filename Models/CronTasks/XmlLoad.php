<?php
namespace Models\CronTasks;

use Models\XmlLoad\XmlLoadFactory;
/**
 * Description of XmlLoad
 *
 * @author pahus
 */
class XmlLoad extends Task{
    /*const MANUAL = TRUE;
    const STOPPABLE = FALSE;
    const CANCELABLE = TRUE;*/
    const TITLE = 'XML загрузка';
    const UPLOAD_URI_AV = "http://realtyposter.ru/data/export/avito/54c63d4848fa58d659570c63.xml";

    //public function __construct()
    //{
    //}

    /**
     * создать задачу
     *
     * @param array $params
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|string $FILE
     * @param string $error
     */
    public static function create($params = array(), $FILE = NULL, &$error = NULL){
    }
    public static function createAndStart(){
        $task_id = parent::add(array(
            'type' => self::getType()
        ));
        $task = self::getById($task_id);
        $task->start();
    }
    public function start(){
        $this->setStart();
        $loader = XmlLoadFactory::getLoader();
        $loader->loadData();
       
        $this->setComplete();
    }

    public function sync() {
        //$this->setStart();
        $loader = XmlLoadFactory::getLoader();
        $loader->getSyncData();
        //$this->setComplete();
    }


}
