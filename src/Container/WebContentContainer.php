<?php
namespace LPS\Container;
/**
 * Created by JetBrains PhpStorm.
 * User: Vladimir
 * Date: 08.08.12
 * Time: 14:37
 * To change this template use File | Settings | File Templates.
 */
class WebContentContainer extends ContentContainer implements iWebContainer{
    private $formData = array();
    private $enablePersister = TRUE;

    public function enablePersist(){
        $this->enablePersister = TRUE;
        return $this;
    }
    public function disablePersist(){
        $this->enablePersister = FALSE;
        return $this;
    }
    /**
     *
     * @return string весь контент
     */
    public function getContent(){
        $content = parent::getContent();
        return $this->enablePersister ? $this->pagePersist($content) : $content;
    }
    /**
     * Производит предварительные модификации контента: @see LPS\Components\htmlPrepare\PagePersister
     * Подставляет пользовательские данные в формы
     * Подставляет данные в формы из GET И POST
     * @param $content
     * @return \HTML|\Text
     */
    protected function pagePersist($content){
        $post = $_POST;
        foreach($this->formData as $k=>$v){
            if(empty($_POST[$k]) && empty($_GET[$k])){
                $_POST[$k] = $v;
            }
        }
        $pagePersister=new \LPS\Components\htmlPrepare\PagePersister();
        $formPersister=new \HTML_FormPersister();
        $content = $pagePersister->process($content);
        $content = $formPersister->process($content);
        $_POST = $post;
        return $content;
    }
    /**
     * Добавить данные для подстановки в формы
     * @param array $data
     * @return WebContentContainer
     */
    public function setFormData(array $data){
        $this->formData = $data + $this->formData;
        return $this;
    }
    /**
     * Добавить пару ключ/значение для подстановки в формы
     * @param $key
     * @param $value
     * @return WebContentContainer
     */
    public function addFormValue($key, $value){
        $this->formData[$key] = $value;
        return $this;
    }
}