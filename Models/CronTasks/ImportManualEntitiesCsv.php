<?php
namespace Models\CronTasks;

/**
 * Плановый экспорт данных каталога
 *
 * @author olya
 */
class ImportManualEntitiesCsv extends Task{
    const MANUAL = TRUE;
    const STOPPABLE = TRUE;
    const CANCELABLE = TRUE;
    const TITLE = 'Ручной импорт каталога файлами csv';
    /**
     * какие каталоги мы хотим синхронизировать
     * (используется только для экспорта, т.к. импорт по названию директории может понять какой каталог используется)
     * @TODO возможно стоит для проверки импорта использовать
     * @var array 
     */
    private static $catalog_exchange_keys = array(
        CatalogConfig::CATALOG_KEY,
        CatalogConfig::CATALOG_KEY_TEST
    );
    /**
     * создать задачу
     * 
     * @param array $params
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|string $FILE
     * @param string $error
     */
    public static function create($params = array(), $FILE = NULL, &$error = NULL){
        \Models\CatalogManagement\Exchange\Import\CSV::createTask($params, $FILE, $error);
    }
    public static function createAndStart(){
        return;
    }
    public function start(){
        \Models\CatalogManagement\Exchange\Import\CSV::getInstance($this)->getData();
    }
}
