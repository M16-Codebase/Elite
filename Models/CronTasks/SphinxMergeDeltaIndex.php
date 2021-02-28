<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 06.10.15
 * Time: 16:40
 */

namespace Models\CronTasks;


use App\Configs\SphinxConfig;
use Models\SphinxManagement\SphinxSearch;

class SphinxMergeDeltaIndex extends Task
{
    const TITLE = 'Подготовка данных для индексации Sphinx';
    /**
     * создать задачу
     *
     * @param array $params
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|string $FILE
     * @param string $error
     */
    public static function create($params = array(), $FILE = NULL, &$error = NULL) {}
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
        SphinxSearch::factory(SphinxConfig::CATALOG_KEY)->mergeDeltaIndex();
        SphinxSearch::factory(SphinxConfig::POSTS_KEY)->mergeDeltaIndex();
        $this->setComplete();
    }
}