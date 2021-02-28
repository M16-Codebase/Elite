<?php
namespace Models\CronTasks;

use Models\CatalogManagement\Type;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\Search\CatalogSearch;
/**
 * Description of RecreateViews
 *
 * @author olya
 */
class RecreateViews extends Task{
    const STACK_SIZE = 100;
    const STOPPABLE = FALSE;
    const CANCELABLE = TRUE;
    const TITLE = 'Пересчет составных свойств';
    /**
     * создать задачу
     * 
     * @param array $params
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|string $FILE
     * @param string $error
     */
    public static function create($params = array(), $FILE = NULL, &$error = NULL){
        
    }
    public static function createAndStart(){        
        $catalogs = Type::getCatalogs();
        //т.к. view пересчитываются для всех сегментов, то нам без разницы какой сегмент взять
        $segment_id = \App\Segment::getInstance()->getDefault()['id'];
        $count_items = 0;
        $count_variants = 0;
        foreach ($catalogs as $c){
            $item_class = \App\Configs\CatalogConfig::getEntityClass($c->getKey(), Item::CATALOG_IDENTITY_KEY);
            if (!empty($item_class)){
                $count_items += CatalogSearch::factory($c->getKey(), $segment_id)->setRules(array('recreate_view' => 1))->setPublicOnly(FALSE)->searchItems(0, 1)->getTotalCount();
            }
            $variant_class = \App\Configs\CatalogConfig::getEntityClass($c->getKey(), Variant::CATALOG_IDENTITY_KEY);
            if (!empty($variant_class)){
                $count_variants += CatalogSearch::factory($c->getKey(), $segment_id)->setRules(array('variant.recreate_view' => 1))->setPublicOnly(FALSE)->searchVariants(0, 1)->getTotalCount();
            }
        }
        if ($count_items + $count_variants > 0){
            $task_id = parent::add(array(
                'type' => self::getType(),
                'all_count' => $count_items + $count_variants
            ));
            $task = static::getById($task_id);
            $task->start();
        }
        return;
    }
    public function start(){
        self::cancelBrokenTasks(self::getType());
        $this->setStart();
        //т.к. view пересчитываются для всех сегментов, то нам без разницы какой сегмент взять
        $segment_id = \App\Segment::getInstance()->getDefault()['id'];
        $catalogs = Type::getCatalogs();
        $items_count = 0;
        $variants_count = 0;
        foreach ($catalogs as $c){
            $item_class = \App\Configs\CatalogConfig::getEntityClass($c->getKey(), Item::CATALOG_IDENTITY_KEY);
            if (!empty($item_class)){
                do{
                    //пересоздание view вызывается при создании объектов
                    $items = CatalogSearch::factory($c->getKey(), $segment_id)->setRules(array('recreate_view' => 1))->setPublicOnly(FALSE)->searchItems(0, self::STACK_SIZE)->getSearch();
                    if (empty($items)){
                        break;
                    }
                    $items_count += count($items);
                    Item::clearCache(null, null, false);
                    Variant::clearCache(null, null, false);
                    $percent = round(($items_count + $variants_count)/$this['data']['all_count']*100);
                    if (!$this->iterationComplete($percent)){
                        return;
                    }
                }while(TRUE);
            }
            $variant_class = \App\Configs\CatalogConfig::getEntityClass($c->getKey(), Variant::CATALOG_IDENTITY_KEY);
            if (!empty($variant_class)){
                do{
                    $variants = CatalogSearch::factory($c->getKey(), $segment_id)->setRules(array('variant.recreate_view' => 1))->setPublicOnly(FALSE)->searchVariants(0, self::STACK_SIZE)->getSearch();
                    if (empty($variants)){
                        break;
                    }
                    $variants_count += count($variants);
                    Item::clearCache(null, null, false);
                    Variant::clearCache(null, null, false);
                    $percent = round(($items_count + $variants_count)/$this['data']['all_count']*100);
                    if (!$this->iterationComplete($percent)){
                        return;
                    }
                }while(TRUE);
            }
        }
        Item::clearCache(null, null, false);
        Variant::clearCache(null, null, false);
        $this->setComplete(array(
            'data' => $this['data'] + array(
                'items_count' => $items_count,
                'variants_count' => $variants_count
            )
        ));
        return TRUE;
    }
}
