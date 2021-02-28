<?php
namespace Models\CronTasks;

use Models\CatalogManagement\Type;
use Models\CatalogManagement\Exchange\Export\CSV;
use App\Segment;
use App\Configs\CatalogConfig;
/**
 * Плановый экспорт данных каталога
 *
 * @author olya
 */
class ExportFtpEntitiesCsv extends Task{
    const STOPPABLE = FALSE;
    const CANCELABLE = TRUE;
    const TITLE = 'Автоматический экспорт каталогов в файлы csv';
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
        if (empty($params['catalog_key'])){
            throw new \Exception('Обязательный параметр - catalog_key');
        }
        //сначала проверим, требуется ли
        if (!CSV::checkFiles($params['catalog_key'], $params['segment_id'])){
            return;
        }
        $catalog = Type::getByKey($params['catalog_key']);
        if (empty($catalog)){
            throw new \Exception('Не найден каталог: «' . $params['catalog_key'] . '»');
        }
        if (empty(CSV::getExportItems($catalog, $params['segment_id'], NULL, 0, 1, $count))){
            return;
        }
        $params['type'] = self::getType();
        return parent::add($params);
    }
    public static function createAndStart(){
        $segments = Segment::getInstance()->getAll();
        foreach (self::$catalog_exchange_keys as $catalog_key){
            foreach ($segments as $s){
                $task_id = self::create(array('segment_id' => $s['id'], 'catalog_key' => $catalog_key));
                if (!empty($task_id)){
                    $task = static::getById($task_id);
                    $task->start();
                }
            }
        }
    }
    public function start(){
        CSV::getInstance($this)->setData();
    }
}
