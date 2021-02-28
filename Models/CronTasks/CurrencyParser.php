<?php
namespace Models\CronTasks;

/**
 * @author olya
 */
class CurrencyParser extends Task
{
    const TITLE = 'Парсер валют с ЦБ';
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
        \Models\Currency::parse();
        $this->setComplete();
    }
}