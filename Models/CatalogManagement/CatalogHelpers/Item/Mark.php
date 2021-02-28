<?php
namespace Models\CatalogManagement\CatalogHelpers\Item;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Review AS ReviewEntity;
use Models\CatalogManagement\CatalogHelpers\Interfaces\iItemDataProvider;
use Models\CatalogManagement\Properties\Property;
/**
 * @todo обязательно написать описание
 */
class Mark extends ItemHelper{
    protected static $i = NULL;
    protected static $fieldsList = array('count_reviews', 'user_rating');
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Item $item, $field){
        if ($field=='count_reviews' || $field=='user_rating'){
            if (!isset ($this->dataCache[$item['id']][$field]))
                $this->dataCache[$item['id']] = ReviewEntity::getItemMark($item['id']);
            return !empty($this->dataCache[$item['id']][$field]) ? 
                ( $field == 'user_rating' ? 
                    \LPS\Components\FormatNumber::getInt($this->dataCache[$item['id']][$field]) :
                    $this->dataCache[$item['id']][$field]
                ) : NULL;
        }
        return NULL;
	}
    private $dataCache = array();
}