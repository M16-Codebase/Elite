<?php
namespace Models\CatalogManagement\Search;
/**
 * Версия класса результатов поиска для вариантов
 *
 * @author Charles Manson
 */
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Variant;
class CatalogSearchVariantsResult extends CatalogSearchResult{
    protected function __construct(array $search_data, $segment_id) {
        $search_data['mode'] = 'variants';
        parent::__construct($search_data, $segment_id);
        if (!empty($this->result_set)){
            Item::factory($this->item_ids, $this->segment_id);
            $this->result_set = Variant::factory($this->variant_ids, $this->segment_id);
            reset($this->result_set);
        }
    }

    /**
     * Возвращает массив с найденными объектами
     * @return \Models\CatalogManagement\Variant[]
     */
    public function getSearch() {
        return parent::getSearch();
    }
    /**
     * Возвращает первый вариант из результатов поиска
     * @return Variant
     */
    public function getFirst(){
        return reset($this->result_set);
    }
    /**
     * 
     * @return Variant
     */
    public function current() {
        return parent::current();
    }
    
    public function offsetGet($offset) {
        return ($offset == 'variants') ? $this->result_set : parent::offsetGet($offset);
    }
}
