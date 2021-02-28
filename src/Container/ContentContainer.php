<?php
namespace LPS\Container;
use LPS\Config;
//TODO продумать управление кэшем
class ContentContainer implements \Countable, \ArrayAccess, iContainer {
    const DEF_TEMPLATER = 'Quicky';
    
    const TEMPLATER_QUICKY = 'Quicky';
    const TEMPLATER_XSLT = 'XSLT';
    const TEMPLATER_TWING = 'Twing';

    private $container = array();
    private $refsContainer = array();
    protected $template = NULL;
    protected $innerTemplate = NULL;
    protected $templater = NULL;
    protected $defaultPath = NULL;
    /**
     * @var Object Templater object
     */
    protected $core = null;
    static protected $availableTemplaters = array(self::TEMPLATER_QUICKY, self::TEMPLATER_TWING, self::TEMPLATER_XSLT);

    /**
     * @param string $template переопределить общий шаблон
     * @param string $innerTemplate переопределить внутренний шаблон
     * @param Object $templater шаблонизатор
     * @param array $data переменные в шаблоне
     * @param string $default_path добавить путь к шаблонам, в котором будет в первую очередь искаться шаблон. путь от корня проекта (Например: templates/mobile/)
     */
    public function __construct($template = '', $innerTemplate = NULL, $templater = NULL, array $data = array(), $default_path = NULL) {
        $this->setTemplater($templater ? $templater : self::DEF_TEMPLATER);
        $this->setTemplate($template);
        $this->setInnerTemplate($innerTemplate);
        $this->defaultPath = Config::getParametr('dir', 'templates_dirs');
        if (!is_null($default_path)){
			$this->setDefaultPath($default_path);
		}
        $this->container = $data;
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
     * ArrayAccess interface
     */
    public function offsetSet($key, $value) {
        if (is_null($key)) {
            trigger_error('Response add: set key only');
        } else {
            $this->container[$key] = $value;
        }
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key) {
        return isset($this->container[$key]) || isset($this->refsContainer[$key]);
    }

    /**
     * @param mixed $key
     */
    public function offsetUnset($key) {
        unset($this->container[$key]);
    }

    /**
     * @param mixed $key
     * @return null
     */
    public function offsetGet($key) {
        return isset($this->container[$key]) ? $this->container[$key] : (isset($this->refsContainer[$key]) ? $this->refsContainer[$key] : null);
    }
    /*
     * Countable interface
     */
    /**
     * @return int
     */
    public function count(){
        return count($this->container); 
    }
    /**
     * Добавить переменную в шаблон
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function add($key, $value){
        $this->offsetSet($key, $value);
        return $this;
    }
    /**
     * Добавить переменную в шаблон по ссылке
     * @param $key
     * @param $value
     * @return static
     */
    public function addRef($key, &$value){
        if (is_null($key)) {
            trigger_error('Response addRef: set key only');
        } else {
            $this->refsContainer[$key] = &$value;
        }
        return $this;
    }
    /**
     * 
     * @param string $key
     * @return mixed переменная шаблона
     */
    public function get($key){
        return $this->offsetGet($key);
    }
    /**
     * @param $key
     * @return mixed переменная шаблона, переданная по ссылке
     */
    public function getRef($key){
        return isset($this->refsContainer[$key]) ? $this->refsContainer[$key] : null;
    }
    /**
     *
     * @return string путь к шаблону
     */
    public function getTemplate() {
        return $this->template;
    }
    /**
     * Установить определенный шаблон
     * @param string путь к шаблону 
     * @return static
     */
    public function setTemplate($template) {
        $this->template = $template;
        return $this;
    }
    /**
     * Можно подменить внутренний шаблон
     * @param string $templ
     * @return static
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
     * Установить шаблонизатор
     * @param string $templater название шаблонизатора
     * @throws \InvalidArgumentException
     * @return static
     */
    protected function setTemplater($templater = ''){
        if (!empty($templater)){
            if (!in_array($templater, self::$availableTemplaters)){
                throw new \InvalidArgumentException('Templater incorrect use '.__CLASS__.'::TEMPLATER_* constants');
            }
            $this->templater = $templater;
        }else{
            $this->templater = self::DEF_TEMPLATER;
        }
        return $this;
    }
    /**
     *
     * @return string имя шаблонизатора
     */
    public function getTemplater() {
        return $this->templater;
    }
    /**
     *
     * @return array переменные шаблона
     */
    public function getContainer(){
        return $this->container;
    }

    /**
     *
     * @throws \LogicException
     * @return string весь контент
     */
    public function getContent(){
        if (empty($this->templater)){
            throw new \LogicException('Template не задан');
        }
        $this->initTemplater();
        if ($this->templater == 'Quicky'){
            foreach ($this->container as $k => $v){
                $this->core->assign($k, $v);
            }
            foreach ($this->refsContainer as $k => &$v){
                $this->core->assign_by_ref($k, $v);
            }
            $before_template_mark = microtime(1);
            $before_template = $before_template_mark-$GLOBALS['startProgrammTime'];
            $content = $this->core->fetch($this->template);
            $time_after_template = microtime(1);
            $db = \App\Builder::getInstance()->getDB();
            return str_replace('<!--GenerateTime-->', 
                'страница создана за '.round($time_after_template-$GLOBALS['startProgrammTime'], 4).' с: '
                . 'генерация данных ' . round($before_template, 4) . ' c, '
                . 'генерация шаблонов '.round($time_after_template-$before_template_mark, 4).' с  '
                . (method_exists($db, 'getAllTime') ? ('(запросы в бд ' . round($db->getAllTime(), 4) . ' с) ') : ''), $content);
        }else{
            throw new \LogicException('В данный момент работает только шаблонизатор Quicky');
        }
    }
    /**
     * инизиализация шаблонизатора
     */
    private function initTemplater(){
        if ($this->templater != 'Quicky'){
            throw new \LogicException('В данный момент работает только шаблонизатор Quicky');
        }
        if (empty($this->core)){
            if ($this->templater == 'Quicky'){
                \LPS\Components\Benchmark::factory()->log('Quicky init');
                $this->core = \App\Builder::getInstance()->getQuicky();
                $config = \App\Builder::getInstance()->getConfig();
                $config->templater($this->core, $this->getDefaultPath());
            }
        }
    }
}