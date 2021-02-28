<?php
/**
 * Класс обертка для WebContentContainer 
 * Делегирует все методы объекту класса WebContentContainer, но 
 * в качестве контента отдает json строку в которой содержится массив 
 * array(
 *  'content' => 'содержимое шаблона', 
 *  'errors' => mixed ошибки, 
 *  'data' => array(произвольные данные)
 * )
 *
 * @author olga
 */
namespace LPS\Container;
class JsonContentContainer implements iWebContainer, \ArrayAccess, \Countable{
    private $errors = NULL;
    private $data = array();
    private $status = NULL;
    private $emptyContent = FALSE;
    protected $innerTemplate = NULL;
    protected $defaultPath = NULL;
    /**
     *
     * @var WebContentContainer
     */
    private $contentContainer = NULL;
    /**
     * @param string $template
     * @param Object $templater
     * @param array $data
     */
    public function __construct(WebContentContainer $container) {
        $this->contentContainer = $container;
    }
    /**
     * Добавляем пути к шаблонам, которые будем инклюдить в шаблонах
     * @param string $path
     * @return \LPS\Container\ContentContainer
     */
    public function setDefaultPath($path){
        if (in_array($path, $this->defaultPath)){
            return $this;
        }
        array_unshift($this->defaultPath, $path);//вставляем в начало, т.к. ищется по очереди
        return $this;
    }
    /**
     * Получить пути к шаблонам
     * @return array
     */
    public function getDefaultPath(){
        return $this->defaultPath;
    }
    /**
     * Можно подменить внутренний шаблон
     * @param string $templ
     * @return \LPS\Container\ContentContainer
     */
    public function setInnerTemplate($templ){
        $this->innerTemplate = $templ;
        return $this;
    }
    /**
     * Внутренний шаблон
     * @return string
     */
    public function getInnerTemplate(){
        return $this->innerTemplate;
    }
    /**
     * Добавить переменную в шаблон
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function add($key, $value){
        $this->contentContainer->add($key, $value);
        return $this;
    }
    /**
     * Добавить переменную в шаблон по ссылке
     * @param $key
     * @param $value
     * @return static
     */
    public function addRef($key, &$value){
        $this->contentContainer->addRef($key, $value);
        return $this;
    }
    /**
     * 
     * @param string $key
     * @return mixed переменная шаблона
     */
    public function get($key){
        return $this->contentContainer->get($key);
    }
    /**
     * @param $key
     * @return mixed переменная шаблона, переданная по ссылке
     */
    public function getRef($key){
        return $this->contentContainer->getRef($key);
    }
    /**
     *
     * @return string путь к шаблону
     */
    public function getTemplate() {
        return $this->contentContainer->getTemplate();
    }
    /**
     * Установить определенный шаблон
     * @param string путь к шаблону 
     * @return static
     */
    public function setTemplate($template) {
        $this->contentContainer->setTemplate($template);
        return $this;
    }
    /**
     *
     * @return string имя шаблонизатора
     */
    public function getTemplater() {
        return $this->contentContainer->getTemplater();
    }
    /**
     *
     * @return array переменные шаблона
     */
    public function getContainer(){
        return $this->contentContainer->getContainer();
    }

    /**
     * @return static
     */
    public function setEmptyContent(){
        $this->emptyContent = TRUE;
        return $this;
    }

    /**
     *
     * @throws \LogicException
     * @return string весь контент JSON
     */
    public function getContent(){
        $result = array();
        $result['errors'] = $this->errors;
        $result['data'] = $this->data;
        $result['status'] = $this->status;
        $result['content'] = $this->emptyContent ? '' : $this->contentContainer->getContent();
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    /**
     * Добавить данные для подстановки в формы
     * @param array $data
     * @return static
     */
    public function setFormData(array $data){
        $this->contentContainer->setFormData($data);
        return $this;
    }
    /**
     * Добавить пару ключ/значение для подстановки в формы
     * @param $key
     * @param $value
     * @return static
     */
    public function addFormValue($key, $value){
        $this->contentContainer->addFormValue($key, $value);
        return $this;
    }
    /**
     * Установить ошибки
     * @param array $errors
     * @return static
     */
    public function setErrors(array $errors){
        // Сменился формат ошибки, пока код не переписан, перелопачиваем ошибки и загоняем под новый формат
        foreach($errors as $key => $error){
            if (is_array($error)){
                $this->addError($error);
            } else {
                $this->addErrorByKey($key, $error);
            }
        }
//        $this->errors = $errors;
        return $this;
    }
    /**
     * @TODO возможность вместо error указывать message
     * Добавить ошибку
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function addErrorByKey($key, $value){
        $this->addError(array(
            'key' => $key,
            'error' => $value
        ));
        return $this;
    }

    /**
     * @TODO чтобы вместо error можно было указывать message (xor)
     * Добавить ошибку.
     * @param array $error – информация об ошибке
     *                      обязательные поля:
     *                          key – ключ свойства
     *                          error – код ошибки
     *                      необязательные:
     *                          title – заголовок свойства
     *                          message – человеческое сообщение об ошибке
     *
     * @return $this
     */
    public function addError(Array $error){
        if (!isset($error['key']) || empty($error['error'])){
            throw new \LogicException('Ключи key и error в ошибке обязательны');
        }
        $this->errors[] = $error;
        return $this;
    }
    /**
     * Установить доп данные
     * @param array $data
     * @return static
     */
    public function setData($data){
        $this->data = $data;
        return $this;
    }
    /**
     * Добавить доп данные
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function addData($key, $value){
        $this->data[$key] = $value;
        return $this;
    }
    /**
     * Установить статус
     * @param array $status
     * @return static
     */
    public function setStatus($status){
        $this->status = $status;
        return $this;
    }
    /**
     * ArrayAccess interface
     */
    public function offsetSet($key, $value) {
        $this->contentContainer[$key] = $value;
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key) {
        return isset($this->contentContainer[$key]);
    }

    /**
     * @param mixed $key
     */
    public function offsetUnset($key) {
        unset($this->contentContainer[$key]);
    }

    /**
     * @param mixed $key
     * @return null
     */
    public function offsetGet($key) {
        return $this->contentContainer[$key];
    }
    /*
     * Countable interface
     */
    /**
     * @return int
     */
    public function count(){
        return count($this->contentContainer); 
    }
}
?>