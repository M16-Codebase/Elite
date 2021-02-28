<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 06.10.15
 * Time: 16:43
 */

namespace Models\CronTasks;


use App\Configs\SphinxConfig;
use Models\SphinxManagement\SphinxSearch;

class GenerateSiteLinks extends Task
{
    const TITLE = 'Перелинковка постов';
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
        SphinxSearch::factory(SphinxConfig::METATAGS_KEY)->mergeDeltaIndex();
        \Models\Seo\SeoLinks::getInstance()->buildLinks();
        $this->setComplete();
    }
}