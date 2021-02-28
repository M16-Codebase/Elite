<?php
namespace LPS\Components\SharedMemory;

/**
 *
 * @author pochepochka
 */
interface iSharedMemory {
    public static function getInstance();
    /**
     * посмотреть id блока памяти
     */
    public function getId();
    /**
     * взять переменную
     * @param int $var_key ключ переменной в памяти
     * @param mixed $id идентификатор данных переменной
     */
    public function get($var_key, $id = NULL);
    /**
     * запись переменной
     * @param int $var_key ключ переменной в памяти
     * @param mixed $id идентификатор данных переменной
     * @param mixed $value
     */
    public function set($var_key, $id = NULL, $value);
    /**
     * удаление переменной
     * @param int $var_key ключ переменной в памяти
     * @param mixed $id идентификатор данных переменной
     */
    public function remove($var_key, $id = NULL);
    /**
     * Удаляем из памяти зарезервированный блок
     */
    public function delete();
}
