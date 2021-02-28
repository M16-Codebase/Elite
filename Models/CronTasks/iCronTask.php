<?php
namespace Models\CronTasks;

/**
 *
 * @author olya
 */
interface iCronTask {
    /**
     * создать задачу
     * 
     * @param array $params
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|string $FILE
     * @param string $error
     */
    public static function create($params = array(), $FILE = NULL, &$error = NULL);
    /**
     * создать и сразу запустить
     * используется для периодических задач
     *(для ручных задач можно return NULL)
     */
    public static function createAndStart();
    /**
     * начать задачу
     */
    public function start();
}
