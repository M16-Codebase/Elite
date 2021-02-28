<?php
/**
 * Description of iContainer
 *
 * @author olga
 */
namespace LPS\Container;
interface iContainer {
    /**
     * Выставить путь к шаблонам
     * @param string $path
     */
    public function setDefaultPath($path);
    /*
     * Получить путь к шаблонам
     */
	public function getDefaultPath();
    /**
     * Добавить переменную в шаблон
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function add($key, $value);
    /**
     * Добавить переменную в шаблон по ссылке
     * @param $key
     * @param $value
     * @return static
     */
    public function addRef($key, &$value);
    /**
     * 
     * @param string $key
     * @return mixed переменная шаблона
     */
    public function get($key);
    /**
     * @param $key
     * @return mixed переменная шаблона, переданная по ссылке
     */
    public function getRef($key);
    /**
     *
     * @return string путь к шаблону
     */
    public function getTemplate();
    /**
     * Установить определенный шаблон
     * @param string путь к шаблону 
     * @return static
     */
    public function setTemplate($template);
    /**
     *
     * @return string имя шаблонизатора
     */
    public function getTemplater();
    /*
     * установить внутренний шаблон
     */
    public function setInnerTemplate($templ);
    /**
     * внутренний шаблон
     */
    public function getInnerTemplate();
    /**
     *
     * @return array переменные шаблона
     */
    public function getContainer();

    /**
     *
     * @throws \LogicException
     * @return string весь контент
     */
    public function getContent();
}
?>