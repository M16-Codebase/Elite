<?php
/**
 * Description of iWebConteiner
 *
 * @author olga
 */
namespace LPS\Container;
interface iWebContainer extends iContainer{
    /**
     * Добавить данные для подстановки в формы
     * @param array $data
     * @return static
     */
    public function setFormData(array $data);
    /**
     * Добавить пару ключ/значение для подстановки в формы
     * @param $key
     * @param $value
     * @return static
     */
    public function addFormValue($key, $value);
}
?>