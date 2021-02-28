<?php
namespace LPS\Router;

/**
 * этот класс разбирает исходный запрос URI. 
 * В первом приблежении он вычленяет модуль, но может так же вычленять и другие данные
 *
 */

abstract class Controller{
    /** 
     * модуль по умолчанию
     */
    const DEFAULT_MODULE = 'Main';
    const DEFAULT_ACTION = 'index';
    
    protected $modulesRouteMap = array();
    /**
     * module class name
     * @var string 
     */
    private $moduleClass = NULL;
    /**
     * route for module
     * @var string
     */
    protected $tail = NULL;
    /**
     * request Module
     * @var string 
     */
    public $requestModule = NULL;

    public function __construct(array $modulesRouteMap){
        $this->modulesRouteMap = $modulesRouteMap;
        $this->makeRoute();
	}
    
    /**
     * Получить ответ, запустив роутеризацию
     * @return type
     */
    public function route(){
        return $this->runModule();
    }
    /* *
	 * парсинг параметров
     * @return array (string module,string subPath)
	 */
	abstract protected function parceRoute();
   
    protected function makeRoute(){
        $route = $this->parceRoute();
        $this->tail = $route['tail'];
        $this->moduleClass = $route['module'];
        $this->requestModule = $route['requestModule'];
        if (empty($this->moduleClass)){
            $this->moduleClass = static::DEFAULT_MODULE;
        }
    }
    /**
     * @var \LPS\Module
     */
    protected $module = FALSE;

    /**
     * return module
     * @return \LPS\Module
     */
    public function getModule(){
        if ($this->module === FALSE){
            $error = null;
            $this->module = null;
            $this->module = \LPS\Module::_factory($this, $this->getModuleClass(), $error);
            if (!empty($error)){
                trigger_error('can\'t create Module: '.$error.''); // вообще тут выход
            }elseif (empty($this->module)){
                trigger_error('can\'t create Module, no errors'); // вообще тут выход
            }
        }
        return $this->module;
    }

    /**
     * Запускает модуль, который определен
     * @return mixed
     */
    protected function runModule(){
        $module = $this->getModule();
        if (empty($module))
            return NULL;
        $response = $module->run($this->tail);
        return $response;
    }
    
    public function getModuleClass(){
        return $this->moduleClass;
    }
    
    public function getSubRoute(){
        return $this->tail;
    }
    public function getRequestModule(){
        return $this->requestModule;
    }
}
?>