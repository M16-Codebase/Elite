<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 08.09.14
 * Time: 15:57
 */

namespace Models\MenuManagement;


class Menu implements \ArrayAccess{
    const TABLE = 'menu';

    public static function getById($id){

    }

    public static function getByKey($key){

    }

    private function __construct(){

    }
    // ArrayAccess
    public function offsetExists($offset){

    }

    public function offsetGet($offset){

    }

    public function offsetSet($offset, $value){
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }

    public function offsetUnset($offset){
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }
} 