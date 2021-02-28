<?php
namespace Models\CronTasks;

/**
 * Ручной экспорт данных каталога
 *
 * @author olya
 */
class ExportManualEntitiesCsv extends Task{
    const MANUAL = TRUE;
    const STOPPABLE = FALSE;
    const CANCELABLE = TRUE;
    const TITLE = 'Ручной экспорт каталогов в файлы csv';
    /**
     * создать задачу
     * 
     * @param array $params
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|string $FILE
     * @param string $error
     */
    public static function create($params = array(), $FILE = NULL, &$error = NULL){
        if (empty($params['type_id'])){
            throw new \Exception('Для ручных задач обязателен параметр type_id');
        }
        $type = Type::getById($params['type_id']);
        if (empty($type)){
            throw new \Exception('Неверно задан id категории: «' . $params['type_id'] . '»');
        }
        $params['data']['type_title'] = $type['title'];
        $params['type'] = self::getType();
        return Task::add($params);
    }
    public static function createAndStart(){
        return;
    }
    public function start(){
        \Models\CatalogManagement\Exchange\Export\CSVManual::getInstance($this)->setData();
    }
}
