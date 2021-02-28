<?php
namespace Models\CatalogManagement\CatalogHelpers\Item;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Review AS ReviewEntity;
use Models\CatalogManagement\CatalogHelpers\Item\ItemHelper;
/**
 * @todo обязательно написать описание
 */
class Review extends ItemHelper{
    protected static $i = NULL;
    private $loadItemsQuery = array();
    private $dataCache = array();
    protected static $fieldsList = array('reviews');
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Item $item, $field){
        if ($field=='reviews'){
            if (!isset ($this->dataCache[$item['id']]))
                $this->loadData();
            return !empty($this->dataCache[$item['id']]) ? $this->dataCache[$item['id']] : array();
        }
        return NULL;
	}
    /**
     * уведомление, что данные для указанных Items попали в кеш данных и могут быть востребованы
     */
    public function onLoad(Item $item){
        $this->loadItemsQuery[$item['id']] = $item['id'];
    }

    protected function loadData(){
        if (empty ($this->loadItemsQuery))
            return;
        $posts = ReviewEntity::search(array('status' => ReviewEntity::getViewStatus(), 'item_ids' => $this->loadItemsQuery));
        if (!empty($posts)){
            foreach ($posts as $r_id => $r){
                $item_id = $r['item_id'];
                $this->dataCache[$item_id][$r_id] = $r;
            }
        }
        $this->loadItemsQuery = array(); // конечные данные в кеше, так что чистим очередь
    }

    public function onDelete($item_id, $entity, $remove_from_db){
        if (!empty($this->dataCache[$item_id])){
            unset($this->dataCache[$item_id]);
        }
        return ReviewEntity::delete(NULL, $item_id);
    }
    public function onCleanup(){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('DELETE `r` FROM `'.ReviewEntity::TABLE.'` AS `r`
            LEFT JOIN `'.Item::TABLE.'` AS `i` ON (`r`.`item_id` = `i`.`id`)
                WHERE `r`.`id` IS NULL');
    }
}
?>