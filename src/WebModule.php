<?php
namespace LPS;
use App\Configs\AccessConfig;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;
use LPS\Container\WebContentContainer;
use Models\CatalogManagement\Filter\FilterMap;
/**
 * Прототип мoдулей системы
 *
 * @author Shulman A.V.
 * @copyright 2008
 */
abstract class WebModule extends Module {
	const TPL_ERROR_404 = 'notFound.tpl';
	const TPL_ERROR_403 = 'deny.tpl';
	const TPL_NO_TEMPLATE = 'no_template.html';
	const TPL_UNDER_CONSTRUCT = 'underConstruction.tpl';
    const DEFAULT_TEMPLATE = 'index.tpl';
    
    /**
     * @var Models\CatalogManagement\Filter\FilterMap
     */
    protected $filterMap;
    
    /**
     * @var Container\iWebContainer
     */
    private static $contentContainerObj;

    /**
     * @var \LPS\Router\Web
     */
    protected $router;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    /**
     * остаток пути после названия метода
     * @var string
     */
    protected $routeTail;

    /**
     * контроллер авторизации
     * @var \App\Auth\Controller
     */
    protected $authController = NULL;

    /**
     * Контроллер авторизации, если пользователь не авторизован, то NULL
     * @var \App\Auth\Account\AuthorizedAccount
     */
    protected $account = NULL;
    /**
     *
     * @var \Models\Segments\iSegment
     */
    protected $segment = NULL;
    /**
     * Здесь хранятся персональные правила доступа модуля, которые суммируются с общими правилами доступа из AccessConfig
     * формат - RoleKey => true/false
     * @var array
     */
    protected static $module_custom_permissions = array();
    /**
     * Ключ группы прав доступа по умолчанию
     */
    const DEFAULT_ACCESS = AccessConfig::ACCESS_WEB_MODULE;

    /**
     * Проверяет права доступа по умолчанию для заданной роли
     * @param string $role
     * @return bool
     */
    final public static function getDefaultRolePermission($role = NULL){
        //@TODO упихать static::$module_custom_permissions в AccessConfig::getAccessList
        $std_permissions = AccessConfig::getAccessList(static::DEFAULT_ACCESS);
        $permissions = static::$module_custom_permissions + $std_permissions;
        if (is_null($role)){
            return $permissions;
        } else {
            return isset($permissions[$role]) ? $permissions[$role] : FALSE;
        }
    }

    /**
     * Конструктор веб модулей
     * @param \LPS\Router\Web $router
     * @param \MysqlSimple\Controller $defaultDataBase
     */
    final protected function __construct(\LPS\Router\Web $router, \MysqlSimple\Controller $defaultDataBase) {
        parent::__construct($router, $defaultDataBase);
        $this->request = $router->getRequest();
        $this->response = $router->getResponse();
		//логгер sql
        $logger = $this->request->query->get('sqllog');
        if(isset($logger) && Config::debugAccess()){
            $logger = \MysqlSimple\Logger::factory();
            $this->db->setLogger($logger);
        }
        if (empty(self::$contentContainerObj)){
            self::$contentContainerObj = new WebContentContainer();
        }
        foreach (get_class_methods(__CLASS__) as $method){
            if (!in_array(strtolower($method), array('index')) && is_callable($method)){
                $this->protectedFunctionsList[strtolower($method)]=$method;
            }
        }
        $this->filterMap = new FilterMap();
        $this->account = \App\Builder::getInstance()->getAccount();
        $account_class = get_class($this->account);
        $accountType = substr($account_class, strrpos($account_class, '\\')+1);
        $this->getAns()->add('account', $this->account)->add('accountType', $accountType);
        $this->_init();
        $this->init();
    }
    /**
     * инициализация родительского модуля
     * метод вызывается только для определения глобальных переменных в не конечных в цепочке иерархии классах
     */
    protected function _init(){
        $this->segment = \App\Segment::getInstance()->getDefault(TRUE);
    }
    /**
     * инициализация модуля
     */
    protected function init(){}

    /**
     * действие модуля, делаемое "по умолчанию"
     */
    abstract public function index();

     /**
     * Вычисляет название метода из маршрута
     *
     * @param string $route
     * @return string
     */
    protected function route($route){
        $routeTokens = explode('/', $route, 2);
        $action = $routeTokens[0];
        $this->routeTail = isset($routeTokens[1]) ? $routeTokens[1] : (!empty($this->routeTail) ? $this->routeTail : false);
        return $action;
    }
    /**
     * Проверяет доступ к методу в данном модуле
     * @param string $action
     * @return bool
     */
    protected function isPermit($action){
        if (AccessConfig::USE_DB_PERMISSION){//если в конфиге прописано, что проверяем по базе для каждой роли
            return $this->account->isPermission($this->getModuleUrl(), $action);
        }
        return $this->isPermission($action);
    }

    private static $lastSelectedAction = null;

    private static $lastSelectedModule = null;

    protected function preAction($action){
        self::$lastSelectedAction = $action;
        self::$lastSelectedModule = $this->getName();
    }

    /**
     * @param string $action
     * @param mixed $ans
     * @return null|void
     */
    protected function postAction($action, &$ans = NULL){
        if (self::$lastSelectedAction!=$action and self::$lastSelectedModule!=$this->getName()){
           //тут просто Event кидать можно
            return NULL;
        }
        /*
          чтобы больше не отрабатывала эта функция для последующих вызовов из стека.
          тоесть она работает 1 раз для первого самого глубокого вызова "run"
          это логично, т.к. если нужен не внутренний редирект, а просто результат работы, то
          можно использовать запись $module->function() которая не производит внутренний редирект.
        */
        self::$lastSelectedAction = NULL;
        self::$lastSelectedModule = NULL;
        if ($ans === NULL){
            if (!$this->getAns()->getTemplate()){
                //Только если ответ не был возвращен в явном виде
                //и используется стандартная модель включения шаблонов, то
                //делается работа по собиранию стандартного ответа
                $this->defaultAnsInit($action);
            }
            $ans = $this->getAns();
            $ans
                ->add('account', $this->account)
                ->add('module', $this)
                ->add('moduleName', $this->getName())
                ->add('moduleUrl', trim($this->getModuleUrl(), '/'))
                ->add('action', $action);
        }
        //событие после отработки экшона
        \App\Builder::getInstance()->getEventDispatcher()->dispatch(StoreEvents::STORE_AFTER_MODULE_WORK, new \Symfony\Component\EventDispatcher\GenericEvent($this, array('action' => $action, 'ans' => $this->getAns())));
    }

    /**
     * Формирует ответ стандартным образом, может быть переопределена для расширения функционала
     * @param string $action
     */
    protected function defaultAnsInit($action){
        $template_found = $this->setActionFiles($action);
        $this->getAns()
            ->add('debug_mode', (Config::debugAccess() and isset($_GET['debug'])));
			if ($template_found){//если шаблон найден, записываем его в innerTemplate, а обвязку делаем стандартной
				$this->getAns()->setTemplate(self::DEFAULT_TEMPLATE);
			}
        //событие после создание дефолтного ответа
        \App\Builder::getInstance()->getEventDispatcher()->dispatch(StoreEvents::STORE_AFTER_DEFAULT_ANS_INIT, new \Symfony\Component\EventDispatcher\GenericEvent($this, array('ans' => $this->getAns())));
    }

    /**
     * Выбирает шаблон из папки модуля __templatedir__/$module/$action.tpl
     * если шаблон выбран, то из папки шаблона ищутся к загрузке файлы
     * script.js, style.css, $action.js, $action.css
     * @param string $action
     */
    private function setActionFiles($action){
        $templatePath = $this->getTemplatePath();
        $innerTemplate = $this->getAns()->getInnerTemplate();
        $template = $templatePath . '/' . (!empty($innerTemplate) ? $innerTemplate : $action) . '.tpl';
		$this->getAns('request_template', $template);
        if ($this->fileExistsInTemplates($template)){
            // загружаем всю атирбутику: все связаные js и css файлы
            $files = array(//массив потенциально подключаемых файлов
                'style.css' => 'css',
                'script.js' => 'js',
                $templatePath.'/style.css' => 'css',
                $templatePath.'/script.js' => 'js',
                $templatePath.'/'.$action.'.css' => 'css',
                $templatePath.'/'.$action.'.js' => 'js'
            );
            $includes = array('js'=>array(), 'css'=>array());
            foreach ($files as $file_name => $file_type){//если файл существует, подключаем
                if ($this->fileExistsInTemplates($file_name)){
                    $includes[$file_type][$file_name] = $file_name;
                }
            }
            $this->getAns()
                ->add('includeCss',    $includes['css'])
                ->add('includeJS',     $includes['js'])
				->add('innerTemplate', $template)
				->add('template_notFound', FALSE);
			return TRUE;
        }else{
            $this->template_not_found();
			return FALSE;
        }
    }

    /**
     * Проверка существования шаблона
     * @param string $file
     * @param string $exists_path путь к файлу, в котором точно есть 
     * @return bool
     */
    private function fileExistsInTemplates($file){
        $templatesPath = $this->getAns()->getDefaultPath();
        if (is_array($templatesPath)){
            foreach ($templatesPath as $path){
                if (file_exists($path . $file)){
                    return TRUE;
                }
            }
            return FALSE;
        }
        if (file_exists($templatesPath . $file)){
            return TRUE;
        }
        return FALSE;
    }

    /*
     * Ошибка: не найден шаблон!
     */
    private function template_not_found(){
        $this->getAns()
            ->add('template_notFound', TRUE)
            ->setTemplate(self::TPL_NO_TEMPLATE);
        $this->errorResponse('500');
    }

    /**
     * Установить шаблон текущего действия основным
     * @return ContentContainer
     */
    protected function setAjaxResponse($template = NULL){
        return $this->getAns()->setTemplate(!is_null($template) ? $template : $this->getTemplate4LastAction());
    }

    /**
     * найти шаблон для последнего вызванного действия
     **/
    private function getTemplate4LastAction(){
        if (is_null(self::$lastSelectedAction))
            return false;
        $templatePath = $this->getTemplatePath(self::$lastSelectedAction);
        return $templatePath.'/'.self::$lastSelectedAction . '.tpl';
    }

    /**
     * Возвращает путь к шаблону
     * @return string
     */
    private function getTemplatePath(){
        $moduleSubDir = str_replace('\\', '/', str_replace('LPS\\', '', $this->getName()));
        return $moduleSubDir;
    }
    /**
     * @return Container\iWebContainer
     */
    protected function getAns(){
        return self::$contentContainerObj;
    }
    /**
     * Подменяет объект WebContentContainer на объект JsonContentContainer
     * @throws \LogicException
     * @return Container\JsonContentContainer
     */
    protected function setJsonAns($template = NULL){
        $this->setAjaxResponse($template);
        if (empty(self::$contentContainerObj)){
            throw new \LogicException('$contentContainerObj must not be empty');
        }
        if (! self::$contentContainerObj instanceof Container\JsonContentContainer){
            self::$contentContainerObj = new Container\JsonContentContainer(self::$contentContainerObj);
        }
        return self::$contentContainerObj;
    }
    /**
     * Устанавливает статус страницы
     * @param int|string $code HTTP status code
     * @return void
     */
    private function errorResponse($code = '404'){
        $this->response->setStatusCode($code);
    }

    /**
     * Создаем контент из ответа действия
     * @param string|iWebContainer|\Symfony\Component\HttpFoundation\Response $ans
     * @return \Symfony\Component\HttpFoundation\Response
     */
    final protected function makeResponseContent($ans){
        if ($ans instanceof \Symfony\Component\HttpFoundation\Response){
            $response = $ans;
        }else{
            $response = $this->router->getResponse();
            if ($ans instanceof Container\iWebContainer){
                //событие до обработки шаблона
                \App\Builder::getInstance()->getEventDispatcher()->dispatch(StoreEvents::STORE_PRE_TEMPLATER_WORK, new \Symfony\Component\EventDispatcher\GenericEvent($this, array('ans' => $this->getAns())));
                $response->setContent($ans->getContent());
            }else{
                $response->setContent(strval($ans));
            }
        }
        return $response;
    }

    /*
     * Ошибка: несуществующий документ
     */
    protected function notFound(){
//        $container = new WebContentContainer();
//        $container->setTemplate(self::TPL_ERROR_404);
		$this->getAns()->setTemplate(self::TPL_ERROR_404);
        $this->errorResponse('404');
//        return $container->getContent();
    }

    /*
     * Ошибка: доступ запрещен (обычно не хватает прав)
     */
	protected function deny(){
//	    $container = new WebContentContainer();
//        $container->setTemplate(self::TPL_ERROR_403);
		$this->getAns()->setTemplate(self::TPL_ERROR_403);
        $this->errorResponse('403');
//        return $this->response->setContent($container->getContent());
    }

	/*
     * Сайт закрыт на реконструкцию
     */
	protected function underConstruction(){
//	    $container = new WebContentContainer();
//        $container->setTemplate(self::TPL_UNDER_CONSTRUCT);
		$this->getAns()->setTemplate(self::TPL_UNDER_CONSTRUCT);
        $this->errorResponse('503');
        $this->response->headers->set('Retry-After', 3600);
//        return $this->response->setContent($container->getContent());
    }

    /**
     * Редирект
     * @param string $uri
     * @param array $params массив параметров, которые надо записать в урл
     * @param string $code
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function redirect($uri='/', $params = null, $code = '301'){
        $uri = self::getUrl($uri, $params);
        return new RedirectResponse($uri, $code);
    }
    /**
     * Составляет url из параметров переданных в массиве $params
     * @param string $uri
     * @param string|array ["paramName"=>"paramValue"] $params
     * @return string
     */
    static protected function getUrl($uri='/', $params = array()) {
        if (empty($uri))
            $uri='/';
        if (is_array($params)) {
            foreach ($params as $name => $value) {
                if (is_array($value)){
                    foreach ($value as $k => $v){
                        if (is_array($v) || $v == ''){
                            continue;
                        }
                        $url_params[$name . '_' . $k] = urlencode($name) .'['.$k.']='. urlencode($v);
                    }
                }elseif($value != ''){
                    $url_params[$name] = urlencode($name) .'='. urlencode($value);
                }
            }
        }
        $result = $uri.(!empty($url_params) ? (strpos($uri, '?')!==false ? '&':'?') . implode('&', $url_params) : '');
        return $result;
    }

    /**
     * @param $key
     * @param $value
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    final protected function setCookie($key, $value, $expires = NULL, $response = NULL){
        if (empty($response)){
            $response = $this->response;
        }
        $response->headers->setCookie(new \Symfony\Component\HttpFoundation\Cookie($key, $value, ($expires ? $expires : time() + \LPS\Config::COOKIE_LIFE_TIME), \LPS\Config::COOKIE_PATH, \LPS\Config::getParametr('site', 'domain')));
    }

    /**
     * @param $key
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    final protected function clearCookie($key, $response = NULL){
        if (empty($response)){
            $response = $this->response;
        }
        $response->headers->clearCookie($key, \LPS\Config::COOKIE_PATH, \LPS\Config::getParametr('site', 'domain'));
    }
    /**
     * Заголовки для скачивания файла
     * @param string $file_path путь к файлу
     * @param string $file_name название файла при скачивании
     * @param bool $delete надо ли удалить файл при окончании скачивания
     * @param \Symfony\Component\HttpFoundation\Response $response возможно хотим подменить ответ
     * @return \Symfony\Component\HttpFoundation\Response
     */
    final protected function downloadFile($file_path, $file_name, $delete = NULL, $response = NULL){
//        if (empty($response)){
//            $response = $this->response;
//        }
        $server_params = $this->request->server->all();
        $from = $to = 0;
        $cr = NULL;
        if (isset($server_params['HTTP_RANGE'])) {
            $range = substr($server_params[ 'HTTP_RANGE'], strpos($server_params[ 'HTTP_RANGE'], '=') + 1);
            $from = strtok($range, '-');
            $to = strtok('/');
            if ($to > 0){
                $to++;
            }
            if ($to){
                $to-=$from;
            }
//            $response->headers->set('status', '206');
            header('HTTP/1.1 206 Partial Content');
            $cr = /*'Content-Range: bytes ' . */$from . '-' . (($to) ? ($to . '/' . $to + 1) : filesize($file_path));
        } else{
//            $response->headers->set('status', '200');
            header('HTTP/1.1 200 Ok');
        }
        $etag = md5($file_path);
        $etag = substr($etag, 0, 8) . '-' . substr($etag, 8, 7) . '-' . substr($etag, 15, 8);
//        $response->headers->set('ETag', '"' . $etag . '"');
        header('ETag: "' . $etag . '"');
//        $response->headers->set('accept-ranges', 'bytes');
        header('Accept-Ranges: bytes');
//        $response->headers->set('content-length', (filesize($file_path) - $to + $from));
        header('Content-Length: ' . (filesize($file_path) - $to + $from));
        if ($cr){
//            $response->headers->set('Content-Range', 'bytes ' . $cr);
            header($cr);
        }
//        $response->headers->set('connection', 'close');
        header('Connection: close');
//        $response->headers->set('content-type',  Components\FS::getMime($file_path));
        header('Content-Type: ' . Components\FS::getMime($file_path));
//        $response->headers->set('Last-Modified', gmdate('r', filemtime($file_path)));
        header('Last-Modified: ' . gmdate('r', filemtime($file_path)));
        $f = fopen($file_path, 'r');
//        $response->headers->set('content-disposition', 'attachment; filename="' . (!empty($file_name) ? $file_name : basename($file_path)) . '";');
        header('Content-Disposition: attachment; filename="' . (!empty($file_name) ? $file_name : basename($file_path)) . '";');
        if ($from){
            fseek($f, $from, SEEK_SET);
        }
        if (!isset($to) or empty($to)) {
            $size = filesize($file_path) - $from;
        } else {
            $size = $to;
        }
        $downloaded = 0;
        while(!feof($f) and !connection_status() and ($downloaded < $size)) {
            echo fread($f, 512000);
            $downloaded+=512000;
            flush();
        }
        fclose($f);
        if ($downloaded > $size && $delete){
            unlink($file_path);
        }
//        return $response;
    }
    /**
     * Кусочная загрузка файла (работает в связке с определенным js)
	 * @TODO что будет, если недоделанный файл уже удалился, а прислали следующий кусок.
	 * @TODO через крон чистить недозаписанные файлы
     * @param string $filename
	 * @return string ok|done|abort
     */
    final protected function uploadFile(&$filename){
        $this->setJsonAns()->setEmptyContent();
		$server_params = $this->request->server->all();
		$uploaddir = \LPS\Config::getRealDocumentRoot() . 'data/files-upload/tmp';
		if (!file_exists($uploaddir)){
			\LPS\Components\FS::makeDirs($uploaddir);
		}
		$hash = $server_params['HTTP_UPLOAD_ID'];
        if (!preg_match('/^[0123456789abcdef]{32}$/i', $hash)) {
            throw new \Exception('wrong hash');
        }
        $filename = $uploaddir . '/' . $hash . '.html5upload';
        if ($server_params['REQUEST_METHOD'] == 'GET') {
            $action = $this->request->query->get('action');
            if ($action == 'abort') {
                if (is_file($filename)){
                    unlink($filename);
                }
                return 'abort';
            }elseif ($action == 'done') {
                return 'done';
            }
            throw new \Exception('No action request');
        }elseif ($server_params['REQUEST_METHOD'] == 'POST') {
            if (intval($server_params['HTTP_PORTION_FROM']) == 0){
                $fout = fopen($filename, 'wb');
            }else{
                $fout = fopen($filename, 'ab');
            }
            if (!$fout) {
                throw new \Exception('failed writing');
            }
            $fin = fopen('php://input', 'rb');
            if ($fin) {
                while (!feof($fin)) {
                    $data = fread($fin, 1024 * 1024);
                    fwrite($fout, $data);
                }
                fclose($fin);
            }
            fclose($fout);
            return 'ok';
        }
        throw new \Exception('No request method');
    }
}
