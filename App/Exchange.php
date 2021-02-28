<?php
namespace App;
/**
 * Класс задач синхронизации
 *
 * @author olga
 */
use Models\CronTask;
use App\Configs\CronTaskConfig;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Exchange\Export\CSVManual;

class Exchange {
    /**
     * Типы импортов, связка названий категорий файловой системы и классов импорта
     * разделяется на типы сущностей - каталоги и всё остальное (пользователи и т.п.)
     * @var array
     */
    private static $import_types = array(
        'catalog' => array(
            'csv' => '\Models\CatalogManagement\Exchange\Import\CSV',
            'csvByCategory' => '\Models\CatalogManagement\Exchange\Import\CSV'
        )
    );
    const FILE_PATH_CSV = 'csv';
	const FILE_PATH_CSV_BY_CATEGORY = 'csvByCategory';
    /**
     * какие каталоги мы хотим синхронизировать
     * (используется только для экспорта, т.к. импорт по названию директории может понять какой каталог используется)
     * @TODO возможно стоит для проверки импорта использовать
     * @var array 
     */
    private static $catalog_exchange_keys = array(
        CatalogConfig::CATALOG_KEY_DISTRICT,
        CatalogConfig::CATALOG_KEY_METRO
    );
    /**
     * Запуск импорта csv каталогов
     */
    public static function catalogCsvImport(){
        $task = CronTask::getNext(CronTaskConfig::TASK_IMPORT_ENTITIES_CSV, array(CronTask::STATUS_NEW, CronTask::STATUS_PROCESS));
        if (!empty($task)){
            \Models\Logger::getInstance()->setUserId($task['user_id']);
            \Models\CatalogManagement\Exchange\Import\CSV::getInstance($task)->getData();
        }
    }
    /**
     * запуск экспорта csv каталогов
     */
    public static function catalogCsvExport(){
        //ручной экспорт
        $task = CronTask::getNext(CSVManual::CRON_TASK);
        if (!empty($task)){
            CSVManual::getInstance($task)->setData();
        }
        //плановый экспорт
        $segments = Segment::getInstance()->getAll();
        foreach (self::$catalog_exchange_keys as $catalog_key){
            foreach ($segments as $s){
                $task_id = \Models\CatalogManagement\Exchange\Export\CSV::createTask(array('segment_id' => $s['id'], 'catalog_key' => $catalog_key));
                if (empty($task_id)){
                    continue;
                }
                $task = CronTask::getById($task_id);
                \Models\CatalogManagement\Exchange\Export\CSV::getInstance($task)->setData();
            }
        }
    }
	/**
	 * забираем файлы импорта с фтп
     * self::IMPORT_FILE_PATH . '/ключ каталога/вид импорта/ключ сегмента/id категории/'
     * ключ сегмента нужен только если сегменты используются
     * id категории только для типа импорта csvByCategory
	 */
	public static function importFromFtp(){
        $catalogs = \Models\CatalogManagement\Type::getCatalogs();
        $catalog_keys = array();
        foreach ($catalogs as $c){
            $catalog_keys[$c['key']] = $c['id'];
        }
        $file_path = \LPS\Config::getRealDocumentRoot() . (\LPS\Config::isWin() ? str_replace('/', '\\', \LPS\Config::IMPORT_FILE_PATH) : \LPS\Config::IMPORT_FILE_PATH);
        $iterator = new \RecursiveDirectoryIterator($file_path);
        $files = new \RecursiveIteratorIterator($iterator);
        /* @var $file \SplFileInfo */
        foreach($files as $file) {
            $filename = $file->getFilename();
            if ($filename === '.' || $filename === '..') {
                continue;
            }
            $params = array();//параметры для создания задачи крона
            $file_real_path = $file->getRealPath();
            if (!\LPS\Components\FS::isFileWhole($file_real_path)){
                continue;
            }
            $relative_path = str_replace($file_path, '', $file_real_path);
            //теперь начинаем разбирать путь к файлу, от него зависит тип задачи
            $rel_path_arr = preg_split('~[/\\\\]~', $relative_path);
            $entity_key = array_shift($rel_path_arr);
            $import_type = array_shift($rel_path_arr);
            if (empty($entity_key) || $entity_key == $filename || empty($import_type) || $import_type == $filename){
                continue;
            }
            if (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE){
                $segment_key = array_shift($rel_path_arr);
                if (empty($segment_key) || $segment_key == $filename){
                    continue;
                }
                $segment = Segment::getInstance()->getByKey($segment_key);
                if (empty($segment)){
                    continue;
                }
                $params['segment_id'] = $segment['id'];
            }
            if ($import_type == 'csvByCategory'){
                $category_id = array_shift($rel_path_arr);
                if (empty($category_id) || $category_id == $filename){
                    continue;
                }
                $type = \Models\CatalogManagement\Type::getById($category_id);
                if (empty($type)){
                    continue;
                }
                $params['type_id'] = $type['id'];
            }
            if (count($rel_path_arr) > 1){
                continue;
            }
            if (array_key_exists($entity_key, $catalog_keys)){
                $params['catalog_key'] = $entity_key;
                $entity_key = 'catalog';//для каталогов пока что один класс импорта
            }
            if (!isset(static::$import_types[$entity_key][$import_type])){
                continue;
            }
            $import_class = static::$import_types[$entity_key][$import_type];
            $import_class::createTask($params, $file_real_path);
        }
	}
}
