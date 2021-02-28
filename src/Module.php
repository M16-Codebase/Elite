<?php

namespace LPS;

/**
 * фабрика и протатип мoдулей системы
 *
 * @author Shulman A.V.
 * @copyright 2008
 */
abstract class Module {

	const MODULES_SUB_NAMESPACE = 'Modules';

	/**
	 * Реестр созданных модулей
	 * @var Module[]
	 */
	static private $registry = array();

	/**
	 * Весь маршрут
	 * @var string
	 */
	protected $route = null;

	/**
	 * Функция для использования только из роутера
	 * Пытается загрузить и вернуть модуль
	 *
	 * @param Router\Controller $router
	 * @param string $module имя модуля
	 * @param string $error
	 * @return Module
	 */
	static final public function _factory(Router\Controller $router, $module, &$error = NULL) {
		if (empty(self::$registry[$module])) {
			$searchClass = self::MODULES_SUB_NAMESPACE . '\\' . $module;
			if (!class_exists($searchClass)) {//проверка на существование файла модуля есть в autoload
				$error = 'Module class ("' . $searchClass . '") not found';
				return NULL;
			}
			if (!is_subclass_of($searchClass, __CLASS__)) {
				$error = 'Module "' . $searchClass . '" define is incorrect, this is not ' . __CLASS__ . ' child';
				return NULL;
			}
			if (!is_callable(array($searchClass, 'index'))) {
				$error = 'Module "' . $searchClass . '" can\'t be create (abstruct?)';
				return NULL;
			}
			self::$registry[$module] = new $searchClass($router, \App\Builder::getInstance()->getDB());
		}
		return self::$registry[$module];
	}

	/**
	 * Возвращает модуль по имени
	 * @param string $module
	 * @param string $error
	 * @return Module
	 */
	protected function getModule($module, &$error = NULL) {
		return self::_factory($this->router, $module, $error);
	}

	/*	 * *** Методы Экземпляров **** */

	/**
	 * @var \MysqlSimple\Controller
	 */
	protected $db;

	/**
	 * @var Array
	 */
	protected $protectedFunctionsList = array();

	/**
	 * @var \LPS\Router\Controller
	 */
	protected $router;

	/**
	 * @param \LPS\Router\Controller $router
	 * @param \MysqlSimple\Controller $defaultDataBase
	 */
	protected function __construct(Router\Controller $router, \MysqlSimple\Controller $defaultDataBase) {
		$this->router = $router;
		$this->db = $defaultDataBase;
		foreach (get_class_methods(__CLASS__) as $method) {
			//TODO проверь на публичность. Зачем не публичные кидать?
			if (!in_array(strtolower($method), array('index')))
				$this->protectedFunctionsList[strtolower($method)] = $method;
		}
	}

	/**
	 * Запрещает вызов функции через контроллер run
	 * @param mixed(string|array) $methodName
	 */
	final protected function addProtectedMethod($methodName) {
		if (is_array($methodName)) {
			foreach ($methodName as $method) {
				$this->protectedFunctionsList[strtolower($method)] = $method;
			}
		} else {
			$this->protectedFunctionsList[strtolower($methodName)] = $methodName;
		}
	}

	/**
	 * Возвращает имя модуля
	 * @return string
	 */
	public function getName() {
		return get_class($this);
	}

	/**
	 * Проверка того, что метод может являться действием
	 * @param string $action
	 * @return boolean
	 */
	private function checkAction($action = '') {
		if (empty($action)) {
			return FALSE;
		}
		if ($action{0} == '_') {
			// Если он начинается с подчеркивания
			return FALSE;
		}
		if (!method_exists($this, $action)) {
			return FALSE;
		}
		// условия, при которых доступ "закрыт" программистом
		if (isset($this->protectedFunctionsList[strtolower($action)])) {
			// Если это функция этого класса (базового) и не index
			return FALSE;
		}
		$methodReflection = new \ReflectionMethod($this, $action);
		if (!$methodReflection->isPublic()) {
			//если метод не публичный
			return FALSE;
		}
		if ($action != $methodReflection->name) {
			//если регистр неправильный
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Вычисляет название метода из маршрута
	 * @param string $route
	 * @return string
	 */
	protected function route($route) {
		return $route;
	}

	/**
	 * Выполняет произвольное действие класса с пре и пост обработчиками
	 * @param string $route
	 * @return mixed
	 */
	final public function run($route) {
		//echo $route;
		$this->route = $route;
		$action = $this->route($route);
		if (!$this->checkAction($action)) {
			$action = 'notFound';
		}
        \App\Builder::getInstance()->getEventDispatcher()->dispatch(StoreEvents::STORE_PRE_ACTION_WORK, new \Symfony\Component\EventDispatcher\GenericEvent($this, array('action' => $action)));
        if ($action != 'notFound') {
            //событие до отработки метода модуля
			if (!$this->isPermit($action)) {
				$action = 'deny';
			} else {//проверяем галочку "Сайт закрыт на реконструкцию"
                // Конфиг сайта перешел на каталог, из-за этого возникли проблемы, что если миграции БД затрагивают структуру каталога
                // (типы, айтемы, свойства), то движок падает на этом месте
				/** @TODO Сделать флаг «Сайт на реконструкции» на старом конфиге */
//				$account = \App\Builder::getInstance()->getAccount();
//				if (\App\Builder::getInstance()->getSiteConfig()->checkFlag(\App\Configs\Settings::KEY_BROKEN_SITE)
//                    && !($account instanceof \App\Auth\Account\SuperAdmin)) {
//					$action = 'underConstruction';
//				}
			}
		}
        Components\Benchmark::factory()->log('Pre Action Work');
		$this->preAction($action);
		$ans = $this->$action();
		$this->postAction($action, $ans);
        Components\Benchmark::factory()->log('Post Action Work');
		$response = $this->makeResponseContent($ans);
		return $response;
	}

	/**
	 * Проверяет доступ к методу в данном модуле
	 * @param string $action
	 * @return boolean
	 */
	protected function isPermit($action) {
		return TRUE;
	}

	/**
	 * Проверка прав
	 * @param string $action
	 * @return boolean
	 */
	public function isPermission($action) {
		return TRUE;
	}

	/**
	 * Перед работой действия
	 * @param string $action
	 */
	protected function preAction($action) {
		
	}

	/**
	 * После работы действия
	 * @param string $action
	 * @param mixed $ans
	 */
	protected function postAction($action, &$ans = NULL) {
		
	}

	/**
	 * Ошибка: несуществующий документ
	 */
	protected function notFound() {
		trigger_error(get_class($this) . ' has not called method: "' . $this->route . '"');
	}

	/**
	 * Ошибка: доступ запрещен (обычно не хватает прав)
	 */
	protected function deny() {
		trigger_error(get_class($this) . ' access deny: "' . $this->route . '"');
	}

	/*
	 * Сайт закрыт на реконструкцию
	 */

	protected function underConstruction() {
		trigger_error('Site under construction');
	}

	/**
	 * Создаем контент из ответа действия
	 * @param mixed $ans
	 * @return mixed
	 */
	protected function makeResponseContent($ans) {
		return $ans;
	}

	/**
	 * возвращает урл модуля, сконфигурированый в конфиге
	 * @param int $segment_id
	 * @return string
	 */
	public function getModuleUrl($segment_id = null) {
		$modulesMap = \App\Builder::getInstance()->getConfig()->getModulesRouteMap();
		$moduleUrl = array_search(preg_replace('~^' . self::MODULES_SUB_NAMESPACE . '\\\~' , '', $this->getName()), $modulesMap);
		return (!is_null($segment_id) ? \App\Segment::getInstance()->getUrlPrefix($segment_id) : '') . '/' . $moduleUrl . '/';
	}

}

?>