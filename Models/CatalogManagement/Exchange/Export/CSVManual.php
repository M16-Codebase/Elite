<?php
namespace Models\CatalogManagement\Exchange\Export;

use Models\CatalogManagement\Type;
use Models\CronTasks\Task;

/**
 * Экспорт для ручных задач
 *
 * @author olya
 */
class CSVManual extends CSV{
    /**
     * Правила поиска айтемов\вариантов
     * @param Type $catalog
     * @param int $segment_id
     * @return Rule[]
     */
    protected static function getDataRules(Type $catalog, $segment_id, Task $task = NULL){
        $rules = array(
            //обязательно первой сортировкой type_id, т.к. запись в файлы идет по очереди, чтобы не открывать\закрывать дескриптор файла по несколько раз.
            Rule::make('type_id')->setValue($task['data']['type_id'])
        );
        return $rules;
    }
}
