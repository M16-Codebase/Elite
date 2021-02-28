<?php
namespace Models\CatalogManagement\CatalogHelpers\Item;
use Models\CatalogManagement\CatalogHelpers\Item\ItemHelper;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Properties\Property;

/**
 * @todo обязательно написать описание
 * @todo отнаследовать от iItemListener
 */
class LastUpdate extends ItemHelper{
    protected static $i = NULL;

    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties){
        if (!empty($updatedProperties)){//мы не хотим менять дату последнего изменения у вариантов при изменении параметра айтема в основной таблице
            $db = \App\Builder::getInstance()->getDB();
            $db->query('UPDATE `' . \Models\CatalogManagement\Variant::TABLE . '` SET `last_update` = NOW() WHERE `item_id` = ?d', $item['id']);
            \Models\CatalogManagement\Variant::clearCache(NULL, $item['id']);
        }
        return $item['id'];
    }
}
