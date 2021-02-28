<?php
namespace Models\CatalogManagement\Search;
/**
 * Версия класса результатов поиска для айтемов
 *
 * @author Charles Manson
 */
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Variant;
class CatalogSearchItemsResult extends CatalogSearchResult{
    private $item_data_by_id = array();
    private $variants_cache = array();
    private $variant_ids_cache = array();
    
    protected function __construct(array $search_data, $segment_id) {
        $search_data['mode'] = 'items';
        parent::__construct($search_data, $segment_id);
        if (!empty($this->result_set)){
//            if (!empty($this->parent_ids)) {
//                Item::prepare(array_unique($this->parent_ids));
//            }
            $this->result_set = Item::factory(array_keys($this->result_set), $this->segment_id);
            reset($this->result_set);
        }
    }

    /**
     * Список айдишников вариантов айтема, подходящик под фильтрацию
     * @param $item_id
     * @return bool|int[] массив айдишников вариантов, если айтем есть в результатах поиска, false, если нет
     */
    public function getFoundVariantIdsByItem($item_id){
        if (!empty($this->item_data_by_id[$item_id])){
            if (empty($this->variant_ids_cache[$item_id])){
                if (empty($this->variants_cache[$item_id])){
                    $this->getFoundVariantsByItem($item_id);
                }
                $this->variant_ids_cache[$item_id] = array_keys($this->variants_cache[$item_id]);
            }
            return $this->variant_ids_cache[$item_id];
        }
        return FALSE;
    }

    /**
     * Возвращает массив вариантов айтема, удовлетворяющих фильтрам
     * @param $item_id
     * @return bool|Variant[]
     */
    public function getFoundVariantsByItem($item_id){
        if (!empty($this->item_data_by_id[$item_id])){
            if (empty($this->variants_cache[$item_id])){
                if (!empty($this->item_data_by_id[$item_id]['find_variants'])){
                    $variant_ids = explode(',', $this->item_data_by_id[$item_id]['find_variants']);
                    $this->variants_cache[$item_id] = Variant::factory($variant_ids, $this->segment_id);
                } else {
                    $this->variants_cache[$item_id] = Item::getById($item_id, $this->segment_id)->getVariants();
                }
            }
            return $this->variants_cache[$item_id];
        }
        return FALSE;
    }

    /**
     * Возвращает массив с найденными объектами
     * @return \Models\CatalogManagement\Item[]
     */
    public function getSearch() {
        return parent::getSearch();
    }

    /**
     * Возвращает массив вариантов по айтемам, удовлетворяющих фильтрам
     * @return array - array(<item_id> => Variant[])
     */
    public function getFoundVariants(){
        foreach($this->item_data_by_id as $item_id=>$v){
            $this->getFoundVariantsByItem($item_id);
        }
        return $this->variants_cache;
    }
    /**
     * Возвращает первый айтем из результатов поиска
     * @return Item
     */
    public function getFirst(){
        return reset($this->result_set);
    }
    /**
     * @return Item
     */
    public function current() {
        return parent::current();
    }
    
    public function offsetGet($offset) {
        return ($offset == 'items') ? $this->result_set : parent::offsetGet($offset);
    }
    
}
