<?php
/**
 * User: Alexander
 * Date: 28.08.12
 * Time: 19:28
 */
namespace LPS;
/**
 * Symplify Dependency Injection
 *
 * @author Alexander
 */
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
abstract class Builder {
    /**
     * реестр созданных сущностей
     * @var array
     */
    private $registry = array();

    /**
     * Устанавливает какую-либо сущность в реестр возваращаемых объектов
     * @param string $name
     * @param Object $obj
     */
    public function set($name, $obj){
        $this->registry[$name] = $obj;
    }

    /**
     *
     * @param string $name
     * @return bool
     */
    public function exist($name){
        return isset($this->registry[$name]);
    }

    /**
     *
     * @param string $name
     * @return mixed
     */
    public function get($name){
        return $this->registry[$name];
    }

    /************** getters ************/

    /**
     * @return \LPS\Config
     */
    public function getConfig(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            $config = \LPS\Config::getInstance();
            $this->set($name, $config);
        }
        return $this->get($name);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            
            
            $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
            $this->set($name, $request);
        }
        return $this->get($name);
    }
    /**
     * @return \LPS\Router\Controller
     */
    public function getRouter(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            if ($this->getConfig()->isCLI()){
                $router = $this->getCliRouter();
            }else{
                $router = $this->getWebRouter();
            }
            $this->set($name, $router);
        }
        return $this->get($name);
    }

    /**
     * @return \LPS\Router\Web
     */
    public function getWebRouter(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            $routeConfig = $this->getConfig()->getModulesRouteMap();
            $router = new \LPS\Router\Web($routeConfig, $this->getRequest());
            $this->set($name, $router);
        }
        return $this->get($name);
    }
    /**
     * 
     * @return \LPS\Router\Cli
     */
    public function getCliRouter(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            $routeConfig = $this->getConfig()->getModulesRouteMap();
            $router = new \LPS\Router\Cli($routeConfig);
            $this->set($name, $router);
        }
        return $this->get($name);
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    public function getEventDispatcher(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            $dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
            $config = $this->getConfig();
            $config->addListeners($dispatcher);
            $this->set($name, $dispatcher);
        }
        return $this->get($name);
    }

    /**
     * @return \MysqlSimple\Controller
     */
    public function getDB(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            /* @var $config Config */
            $db = \MysqlSimple\Controller::factory(Config::getDBString());
            $this->set($name, $db);
        }
        return $this->get($name);
    }

    /**
     * @return \MysqlSimple\Controller
     */
    public function getSphinx(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            $db = \MysqlSimple\Controller::factory(\App\Configs\SphinxConfig::SPHINX_CONNECT_STRING);
            $this->set($name, $db);
        }
        return $this->get($name);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            $response = new \Symfony\Component\HttpFoundation\Response();
            $config = $this->getConfig();
            $config->initResponse($response);
            $this->set($name, $response);
        }
        return $this->get($name);
    }
    /**
     *
     * @return \App\Auth\Controller
     */
    public function getAccountController(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            $controller = \App\Auth\Controller::getInstance();
            $this->set($name, $controller);
        }
        return $this->get($name);
    }
    /**
     *
     * @return \App\Auth\Account\AuthorizedAccount
     */
    public function getAccount(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            $account = $this->getAccountController()->getAccount($this->getRequest());
            $this->set($name, $account);
        }
        return $this->get($name);
    }
    /**
     *
     * @return \Quicky
     */
    public function getQuicky(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            $core=new \Quicky();
            $core->load_filter('pre','optimize');
            $core->compiler_prefs['auto_escape'] = TRUE;
            $this->set($name, $core);
        }
        return $this->get($name);
    }

    /**
     *
     * @param int|null $segment_id
     * @return \Models\SiteConfigManager
     */
	public function getSiteConfig($segment_id = null){
		$name = __METHOD__;
		if (!$this->exist($name)){
			$this->set($name, \Models\SiteConfigManager::getInstance($segment_id));
		}
		return $this->get($name);
	}
    /**
	 *
	 * @return NativeSessionStorage
	 */
    public function getCurrentSession(){
        $name = __METHOD__;
		if (!$this->exist($name)){
            $storage = new NativeSessionStorage(array('name' => 'primary', 'life_time' => 30 * 24 * 3600));
            $sess = new Session($storage);
			$this->set($name, $sess);
		}
		return $this->get($name);
    }
    /**
     * 
     * @return Components\SharedMemory\iSharedMemory
     * @throws \Exception
     */
    public function getSharedMemory(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            $shm = NULL;
            if (!empty(\App\Configs\SharedMemoryConfig::MEMORY_LIMIT) && !Config::isCLI()){//в cli режиме память создается из под другого пользователя, и удалить её из под другого пользователя нельзя
                if (function_exists('ftok')){//для линухов
                    $shm = Components\SharedMemory\None::getInstance();
                }elseif(function_exists('shmop_open')){//под виндой всё равно не работает!!!
//                    $shm = Components\SharedMemory\Shm::getInstance();
                    $shm = Components\SharedMemory\None::getInstance();
                }else{
                    throw new \Exception('Невозможно использование разделяемой памяти: либо надо установить модули php, либо выключить в настройках использование разделяемой памяти (SharedMemoryConfig::MEMORY_LIMIT = 0).');
                }
            }else{
                $shm = Components\SharedMemory\None::getInstance();
            }
            $this->set($name, $shm);
        }
        return $this->get($name);
    }
}