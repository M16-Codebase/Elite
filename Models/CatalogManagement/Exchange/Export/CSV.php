<?php
/**
 * Плановый экспорт данный в CSV
 *
 * @author pochka
 */
namespace Models\CatalogManagement\Exchange\Export;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\Rules\Rule;
use Models\CronTasks\Task;
class CSV extends ExportCatalogEntities{
    const FILE_EXT = 'csv';
    const STACK_SIZE = 200;
    const FILE_PATH = 'csv';
    /**
     * Сколько строк данных выгружено
     * @var int 
     */
    protected $rows_count = 0;
    /**
     * Указатель на файл для текущей позиции
     * @var source 
     */
    private $file_source = NULL;
    /**
     * Список свойств для текущей позиции
     * @var \Models\CatalogManagement\Properties\Property[] 
     */
    private $properties = array();
    /**
     * Проверка, забрала ли внешняя система файлы
     * @param type $catalog_key
     * @param type $segment_id
     * @return boolean
     */
    public static function checkFiles($catalog_key, $segment_id = NULL){
        $folder = static::getFileFolder($catalog_key, $segment_id);
        $files = glob($folder . '*.' . static::FILE_EXT);
        //если файлы присутствуют, значит внешняя система их ещё не скушала
        if (!empty($files)){
            return FALSE;
        }
        return TRUE;
    }
    /**
     * Полный путь до файла
     * @return string
     */
    protected static function getFileFolder($catalog_key, $segment_id = NULL){
        return \LPS\Config::getRealDocumentRoot() . \LPS\Config::EXPORT_FILE_PATH . $catalog_key . '/' . static::FILE_PATH . '/' . (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE && !is_null($segment_id) ? ($segment_id . '/') : '');
    }
    /**
     * Забираем нужные айтемы\варианты
     * @param Type $catalog
     * @param int $segment_id
     * @param int $start
     * @param int $limit
     * @param int $count
     * @return CatalogPosition[]
     */
    public static function getExportItems(Type $catalog, $segment_id, Task $task = NULL, $start, $limit, &$count){
        $rules = static::getDataRules($catalog, $segment_id, $task);
        $searcher = \Models\CatalogManagement\Search\CatalogSearch::factory($catalog['key'], $segment_id)->setPublicOnly(FALSE);
        if (is_null($count)){
            $searcher->enableTotalCount(FALSE);
        }else{
            $searcher->enableTotalCount(TRUE);
        }
        if ($catalog['only_items']){
            $items = $searcher->setRules($rules)->searchItems($start, $limit);
        }else{
            $items = $searcher->setRules($rules)->searchVariants($start, $limit);
        }
        if (!is_null($count)){
            $count = $items->getTotalCount();
        }
        return $items;
    }
    /**
     * главный метод экспорта
     */
    public function setData(){
        $this->task->setStart();
        $page = 1;
        $prev_type_id = NULL;
        $count = 0;
        do{
            $entities = $this->getData(($page-1)*static::STACK_SIZE, static::STACK_SIZE, $count);
            //хак, чтобы не запрашивать в последующих итерациях общее количество
            if ($page == 1){
                $all_count = $count;
                $count = NULL;
            }
            if (count($entities) == 0){
                break;
            }
            foreach ($entities as $e){
                if ($prev_type_id != $e['type_id']){
                    if (!empty($this->file_source)){
                        fclose($this->file_source);
                    }
                    $file_folder = static::getFileFolder($this->catalog['key'], $this->segment_id);
                    if (!file_exists($file_folder)){
                        \LPS\Components\FS::makeDirs($file_folder, 0770);
                    }
                    $this->file_source = fopen($this->getFileName($e['type_id']), 'w');
                    $prev_type_id = $e['type_id'];
                    $this->properties = $this->getProperties($e['type_id']);
                    $this->setFirstRowData();
                }
                $this->setFileContent($e);
            }
            //запишем процент выполнения задачи:
            $this->task->setPercent(round($this->rows_count / $all_count * 100));
            $page++;
            \Models\CatalogManagement\Catalog::clearAllRegistry();
        }while(!empty($entities));
        if (!empty($this->file_source)){
            fclose($this->file_source);
        }
        $this->task->setComplete(array(
            'data' =>  array('rows_count' => $this->rows_count) + $this->task['data']
        ));
        //@TODO обновить last_udate в настройках
//        if ($this->task['type'] == \Models\Task::TASK_EXPORT_ITEMS_CSV && array_key_exists('csv_last_update', $this->segment)){
//            $this->segment->update(array('csv_last_update' => date('Y-m-d H:i:s', $this->exportTime)));
//        }
    }
    /**
     * Запись первой строки
     * @param type $type_properties
     * @return type
     */
    protected function setFirstRowData(){
        $header = array();
        if (!$this->catalog['only_items']){
           $header[] = \LPS\Components\Encoding::getBom() . 'id';
        }
        $header[] = (empty($header) ? \LPS\Components\Encoding::getBom() : '') . 'item_id';
        $header[] = 'type_id';
		foreach ($this->properties as $tp){
			$header[] = $tp['key'];
		}
        \LPS\Components\CsvData::filePut($this->file_source, $header);
    }
    /**
     * Получение данных, которые всегда присутствуют в каждой строке
     * @param type $type_id
     * @param Item $item
     * @param Variant $variant
     * @param type $data
     * @return type
     */
    protected function getRowStaticParams($entity){
        $data = array();
        if (!$this->catalog['only_items']){
           $data[] = $entity['id'];
        }
        $data[] = $entity instanceof Variant ? $entity['item_id'] : $entity['id'];
        $data[] = $entity['type_id'];
        return $data;
    }
    /**
     * Получаем то, что будем выгружать
     * @param type $start
     * @param type $limit
     * @param type $count
     * @return type
     */
    protected function getData($start, $limit, &$count){
        return static::getExportItems($this->catalog, $this->segment_id, $this->task, $start, $limit, $count);
    }
    /**
     * Правила поиска айтемов\вариантов
     * @param Type $catalog
     * @param int $segment_id
     * @return Rule[]
     */
    protected static function getDataRules(Type $catalog, $segment_id, Task $task = NULL){
        $last_update = \Models\SiteConfigManager::getInstance()->get('csv_' . $catalog['key'] . '_last_update', \App\Configs\CatalogConfig::CONFIG_EXCHANGE, $segment_id);
        $rules = array(
            //обязательно первой сортировкой type_id, т.к. запись в файлы идет по очереди, чтобы не открывать\закрывать дескриптор файла по несколько раз.
            Rule::make('type_id')->setOrder(TRUE)
        );
        if (!empty($last_update)){
            if (!$catalog['only_items']){
                $rules[] = Rule::make('variant.last_update')->setMin($last_update);
            }else{
                $rules[] = Rule::make('last_update')->setMin($last_update);
            }
        }
        return $rules;
    }
    /**
     * Полный путь файла с названием файла
     * @param int $type_id
     * @return string
     */
    protected function getFileName($type_id){
        return static::getFileFolder($this->catalog['key'], $this->segment_id) . $type_id . '.' . static::FILE_EXT;
    }
    /**
     * Свойства для выгрузки
     * @param type $type_id
     * @return type\
     */
    protected function getProperties($type_id){
		return PropertyFactory::search($type_id, PropertyFactory::P_EXPORT, 'key');
	}
    /**
     * Построчная запись в файл
     * @param \Models\CatalogManagement\Properties\CatalogPosition
     */
    protected function setFileContent(\Models\CatalogManagement\CatalogPosition $entity){
        $data = $this->getRowStaticParams($entity);
        $propsValues = $entity->getSegmentProperties($this->segment_id);
        if ($entity instanceof Variant){
            $propsValues += $entity->getItem()->getSegmentProperties($this->segment_id);
        }
        foreach ($this->properties as $prop){
            $data[$prop['key']] = isset($propsValues[$prop['key']]) ? (is_array($propsValues[$prop['key']]['real_value']) ? implode(\LPS\Config::CSV_SEPARATOR_SET_VALUES, $propsValues[$prop['key']]['real_value']) : $propsValues[$prop['key']]['real_value']) : '';
        }
        \LPS\Components\CsvData::filePut($this->file_source, $data);
        $this->rows_count++;
    }
}
