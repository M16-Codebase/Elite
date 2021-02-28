<?php

namespace LPS\Components\SharedMemory;

/**
 * Класс ничего не делания, если не хотим использовать разделяемую память
 *
 * @author olya
 */
class None implements iSharedMemory{
    /**
     * Экземпляр класса
     * @var static
     */
    private static $instance = NULL;
    /**
     * 
     * @return static
     */
    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }
    public function delete() {
        
    }
    public function getId(){
        
    }

    public function get($var_key, $id = NULL) {
        
    }

    public function remove($var_key, $id = NULL) {
        
    }

    public function set($var_key, $id = NULL, $value) {
        
    }
}
