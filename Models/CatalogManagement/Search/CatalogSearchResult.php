<?php
namespace Models\CatalogManagement\Search;
/**
 * Объект результатов поиска из CatalogSearch
 * содержит в себе все данные из результатов поиска (массив объектов или айдишников, общее количество объектов,
 * удовлетворяющих фильтрам, количество объектов по типам)
 * Варианты использования
 * $result = CatalogSearch::factory('catalog')->setRules(array(<rules>))->getItems(0, 20);
 *
 * Использование как массива в цикле foreach и в функции count() (Iterator и Countable)
 * foreach($result as $item){....}
 * $items_count = count($result);
 *
 * ArrayAccess
 * $result['searches'] - массив с результатами поиска
 * $result['count'] - количество найденных объектов
 * $result['total_count'] - общее количество объектов, удовлетворяющих фильтрам
 * $result['count_by_type'] - общее количество объектов по типам
 * $result[<item_id>] - объект с id <item_id>, если такой есть в результатах поиска
 *
 * @author charles manson
 */
class CatalogSearchResult implements \Iterator, \ArrayAccess, \Countable{
    protected $result_set;
    protected $total_count;
    protected $count_by_types;
    protected $segment_id;

    protected $item_ids = array();
    protected $variant_ids = array();
    protected $parent_ids = array();
    protected $parent_ids_by_type = array();

    /**
     *
     * @param array $search_data - keys (
     *      searches - список id найденных объектов,
     *      total_count - общее количество объектов, подходящих под условия поиска,
     *      count_by_types - общее количество объектов подходящих под условия поиска в каждом типе)
     * @param int $segment_id
     * @return static
     */
    public static function factory(array $search_data, $segment_id){
        return new static($search_data, $segment_id);
    }
    
    protected function __construct(array $search_data, $segment_id) {
        $this->segment_id = $segment_id;
        $this->result_set = $search_data['searches'];
        reset($this->result_set);
        $this->total_count = $search_data['total_count'];
        $this->count_by_types = $search_data['count_by_types'];
        if ($search_data['mode'] == 'variants') {
            $this->item_ids = array_unique($search_data['searches']);
            $this->variant_ids = array_keys($search_data['searches']);
        } else {
            $this->item_ids = array_keys($search_data['searches']);
            $this->variant_ids = array();
            foreach($search_data['searches'] as $sd){
                if (!empty($sd['find_variants'])) {
                    $this->variant_ids = array_merge($this->variant_ids, explode(',', $sd['find_variants']));
                }
                if (!empty($sd['obj_parent_id']) && !in_array($sd['obj_parent_id'], $this->parent_ids)) {
                    $this->parent_ids[$sd['obj_id']] = $sd['obj_parent_id'];
                }
                if (!empty($sd['obj_parents'])) {
                    $this->parent_ids_by_type[] = $sd['obj_parents'];
                }
            }
            if (!empty($this->parent_ids_by_type)) {
                $parents = array_unique($this->parent_ids_by_type);
                $result = array();
                foreach($parents as $p) {
                    $p = trim($p, '.');
                    $p = explode('.', $p);
                    foreach($p as $type2parent) {
                        $type2parent = explode(':', $type2parent);
                        if (count($type2parent) == 2 && (empty($result[$type2parent[0]]) || !in_array($type2parent[1], $result[$type2parent[0]]))) {
                            $result[$type2parent[0]][] = $type2parent[1];
                        }
                    }
                }
                $this->parent_ids_by_type = $result;
            }
        }
    }

    /**
     * Возвращает массив с найденными объектами
     * @return array
     */
    public function getSearch(){
        return $this->result_set;
    }

    public function getItemIds(){
        return $this->item_ids;
    }

    public function getVariantIds(){
        return $this->variant_ids;
    }

    public function getParentIds(){
        return $this->parent_ids;
    }

    /**
     * Возвращает список айдишников родительских айтемов по заданной категории
     * @param $type_id
     * @return array
     */
    public function getParentIdsByTypeId($type_id) {
        return !empty($this->parent_ids_by_type[$type_id]) ? $this->parent_ids_by_type[$type_id] : array();
    }
    /**
     * Возвращает общее количество объектов, подходящих под условия поиска
     * @return int
     */
    public function getTotalCount(){
        return $this->total_count;
    }

    /**
     * Возвращает общее количество объектов, подходящих под условия поиска в каждом типе
     * @return array
     */
    public function getCountByTypes(){
        return !empty($this->count_by_types) ? $this->count_by_types : array();
    }
    
    /** ***********************  Iterator  ************************ **/

    function rewind() {
        reset($this->result_set);
    }

    function current() {
        return current($this->result_set);
    }

    function key() {
        return key($this->result_set);
    }

    function next() {
        next($this->result_set);
    }

    function valid() {
        return !is_null(key($this->result_set));
    }
    /** **********************  ArrayAccess  ********************** **/
    public function offsetSet($offset, $value) {
        throw new \Exception(get_class($this).' has only immutable Array Access');
    }
    public function offsetExists($offset) {
        return in_array($offset, array('searches', 'count', 'total_count', 'count_by_types')) || isset($this->result_set[$offset]);
    }
    public function offsetUnset($offset) {
        throw new \Exception(get_class($this).' has only immutable Array Access');
    }
    public function offsetGet($offset) {
        if (isset($this->result_set[$offset])){
            return $this->result_set[$offset];
        } else {
            switch ($offset){
                case 'searches':
                    return $this->result_set;
                case 'count':
                    return count($this->result_set);
                case 'total_count':
                    return $this->total_count;
                case 'count_by_type':
                    return $this->getCountByTypes();
                default:
                    throw new \Exception('Notice: '.get_class($this).' #'.$this['id'].' Undefined index: "'.$offset.'"');
            }
        }
    }
    /** **********************  Countable  ************************ **/
    public function count(){
        return count($this->result_set);
    }
}
