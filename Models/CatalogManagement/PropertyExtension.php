<?php

namespace Models\CatalogManagement;
use Models\CatalogManagement\Properties\Property;
class PropertyExtension implements \ArrayAccess{
    /**
     * @var Property 
     */
    private $p;
    /**
     * @var int|null
     */
    private $segment_id;
    /**
     * @var array
     */
    private $v;

    /**
     * @param Property $p
     * @param $value
     * @param int|null $segment_id
     */
    public function __construct(Property $p, $value, $segment_id = null) {
        $this->p = $p;
        $this->v = $value;
        $this->segment_id = $segment_id;
    }
    public function offsetExists ($offset){
        return $offset == 'property' or isset($this->v[$offset]) or isset($this->p[$offset]);
    }
	/**
	 * @see CatalogPosition::makePropValue()
	 * @param int $offset
	 * @return type
	 */
    public function offsetGet ($offset){
        if ($offset == 'property') {
            return $this->p;
        }elseif(!isset($this->v[$offset]) and !array_key_exists($offset, $this->v)){
            return $this->p[$offset];
        }else{
			if ($offset == 'complete_value'){
				//некоторые значения слишком большие (свойста-объекты), и нет смысла хранить их в базе, поэтому для показухи делаем специальный метод
				return $this->p->getCompleteValue($this->v, $this->segment_id);
			}
            return $this->v[$offset];
        }
    }
    public function offsetSet ($offset, $value ){
        throw new \Exception('PropertyValue has only immutable Array Access');
    }
    public function offsetUnset ($offset ){
        throw new \Exception('PropertyValue has only immutable Array Access');
    }
}
?>
