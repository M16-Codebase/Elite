<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 06.10.15
 * Time: 15:36
 */

namespace Models\CronTasks;


class SiteMap extends Task
{
    const TITLE = 'Генерация Sitemap.xml';
    /**
     * создать задачу
     *
     * @param array $params
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|string $FILE
     * @param string $error
     */
    public static function create($params = array(), $FILE = NULL, &$error = NULL) {

    }
    /**
     * создать и сразу запустить
     * используется для периодических задач
     *(для ручных задач можно return NULL)
     */
    public static function createAndStart() {
        $task_id = parent::add(array(
            'type' => self::getType()
        ));
        $task = static::getById($task_id);
        $task->start();
    }
    /**
     * начать задачу
     */
    public function start() {
        $this->setStart();
        
        \Models\Seo\SiteMap::getInstance()->generateSiteMap();
        $this->setComplete();
    }

}