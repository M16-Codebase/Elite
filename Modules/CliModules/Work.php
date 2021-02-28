<?php
namespace Modules\CliModules;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\CatalogHelpers\Property\NestedInSearchProps;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Positions\Settings;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
use Models\ContentManagement\SegmentPost;
use Models\CronTasks\SendsaySynchronize;
use Models\CronTasks\Task;

/**
 * Класс - мусор. для тестов и одноразовых скриптов запускаемых из консоли
 *
 * @author olya
 */
class Work extends \LPS\Module{
    protected function log($msg){
        echo date('H:i d.m.Y')."\t".$msg."\n";
    }
    public function index(){
        echo 'select method'."\n";
    }
    public function clearSharedMemory(){
        $keys = \App\Configs\SharedMemoryConfig::getEntityKey();
        $shm = \App\Builder::getInstance()->getSharedMemory();
        foreach ($keys as $k){
            $shm->remove($k);
        }
    }
    /**
     * проверяем массив в CatalogConfig, если какого-то ключа нет, а в базе есть, то надо удалить всё нахрен (осталось от экспериментов или старых проектов)
     */
    public function clearUnusedCatalogs(){
        
    }
    const CATALOG_KEY = 'test';
    const COUNT = 30000;
    const STACK_SIZE = 100;
    public function catalogItemsGenerator(){
        $start_time = microtime(TRUE);
        $time = time();
        $catalog = \Models\CatalogManagement\Type::getByKey(static::CATALOG_KEY);
        $properties = $catalog->getProperties();
        for ($i=1; $i < self::COUNT; $i++){
            foreach ($properties as $p){
                switch ($p['data_type']){
                    case \Models\CatalogManagement\Properties\String::TYPE_NAME:
                        $values[$p['key']] = $p['title'].' TIME '.$time.' ITERATOR '. $i;
                        break;
                    default: NULL;
                }
            }
            if (!empty($values)){
                $errors = array();
                $item_id = \Models\CatalogManagement\Item::create($catalog['id'], \Models\CatalogManagement\Item::S_PUBLIC);
                $item = \Models\CatalogManagement\Item::getById($item_id);
                $item->updateValues(\Models\CatalogManagement\Item::prepareUpdateData($catalog['id'], $values, 0), $errors);
                if (!empty($errors)){
                    var_dump($errors);
                }
            }
            if ($i%self::STACK_SIZE == 0){
                \Models\CatalogManagement\Item::clearCache(NULL, NULL, FALSE);
                \Models\CatalogManagement\Variant::clearCache(NULL, NULL, FALSE);
                echo round(microtime(TRUE) - $start_time, 2) . PHP_EOL;
            }
        }
        echo round(microtime(TRUE) - $start_time, 2);
    }

    public function test(){
//        \Models\Seo\SiteMap::getInstance()->generateSiteMap();
//        \Models\CatalogManagement\Catalog::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE)->checkBase(true);
//        \Models\CatalogManagement\Catalog::factory(CatalogConfig::CATALOG_KEY_RESALE)->checkBase(true);
//        \Models\CronTasks\SendsaySchedule::createAndStart();
//        var_dump(SendsaySynchronize::getNext(SendsaySynchronize::getType(), array(Task::STATUS_NEW, Task::STATUS_PROCESS))->start());
//        Item::getById(121, 1)->update(array('key' => 'sobranie'));
    }
    public function email_test(){
        $mail_ans = new \LPS\Container\WebContentContainer();
        $mail_ans->setTemplate('mails/simple.tpl');
        $mail_ans->add('message', 'OK');
        \Models\Email::send($mail_ans,  array('op@webactives.ru' => 'Olga'));
    }
    public function importTest(){
        $this->log('Подготавливаем задачи для импорта');
        \App\Exchange::importFromFtp();
        $this->log('Импортируем');
        \App\Exchange::catalogCsvImport();
        $this->log('Экспортируем');
        \App\Exchange::catalogCsvExport();
    }
    public function propertiesReposition(){
        \App\Builder::getInstance()->getDB()->query('UPDATE `properties` SET `position` = `id`');
    }
}
