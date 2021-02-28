<?php
namespace Models\CatalogManagement\Exchange\Export;

use Models\CatalogManagement\Type;
use Models\CronTasks\Task;
/**
 * Description of ExportCatalogEntities
 *
 * @author olya
 */
abstract class ExportCatalogEntities implements iExport{
    /**
     *
     * @var static[]
     */
    protected static $instances = array();
    /**
     * 
     * @param Task $task
     * @return static
     */
    public static final function getInstance(Task $task){
        if (!isset(self::$instances[$task['id']])){
            self::$instances[$task['id']] = new static($task);
        }
        return self::$instances[$task['id']];
    }
    /**
     *
     * @var Type
     */
    protected $catalog = NULL;
    /**
     *
     * @var int
     */
    protected $segment_id = NULL;
    /**
     *
     * @var Task
     */
    protected $task = NULL;
    
    protected final function __construct(Task $task) {
        if (empty($task['data']['catalog_key'])){
            throw new \Exception('В крон задаче для экспорта каталога должен быть известен каталог');
        }
        $this->catalog = Type::getByKey($task['data']['catalog_key']);
        if (empty($this->catalog)){
            throw new \Exception('Неверно задан каталог «' . $task['data']['catalog_key'] . '»');
        }
        $this->segment_id = $task['segment_id'];
        $this->task = $task;
    }
}
