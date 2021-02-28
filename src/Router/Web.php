<?php
namespace LPS\Router;
use LPS\Config;
use \Symfony\Component\HttpFoundation\Request as HttpRequest;
use \Symfony\Component\HttpFoundation\Response as HttpResponse;
/**
 * роутер урлов
 * Принцип работы: мапит первую часть URI до слеша на модуль, все что дальше до ? отдает на модуль как субпуть
 */
class Web extends Controller{
    const DEFAULT_MODULE = 'Main\View';
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response = NULL;
    /**
     * здесь храним препарированный uri_path, с отрезанным префиксом, если они есть
     * @var null
     */
    protected $path = null;

    public function getPath() {
        return $this->path;
    }

    public function __construct(array $modulesRouteMap, HttpRequest $request){
        $this->request = $request;
        parent::__construct($modulesRouteMap);
    }
    /**
     * парсинг параметров
     * @return array (string module,string subPath)
     */
	protected function parceRoute(){
		$path = $this->request->getPathInfo();
        return $this->getStandardResult($path);
	}
    /**
     * стандартный проход module/tail
     * @param string $path
     * @return array('module' => '', 'tail' => '')
     */
    protected function getStandardResult($paath){
        $result = array('module' => NULL, 'tail'=> NULL);
        $regs = array();
		$path=explode('&',$paath);
		$path=$path[0];
		//echo $path;
        $routeNode = NULL;
        if (preg_match('~^/([a-z][a-zA-Z-_\d]*)(/(.*))?$~', $path, $regs)){
            $routeNode = $regs[1];
            if (isset($this->modulesRouteMap[$routeNode])){
                $result['module'] = $this->modulesRouteMap[$routeNode];
                $result['tail'] = !empty($regs[3]) ? rtrim($regs[3], '/') : self::DEFAULT_ACTION;
			/*if(strpos('|'.$result['tail'],'/&')){
				$result['tail'] = 'index';
			}
			//print_r($result['tail']);
			if('|'.substr($result['tail'],0,1)=='|&'){
				$result['tail']= self::DEFAULT_MODULE;
			}*/
            }elseif (empty($routeNode)){
                $result['module'] = self::DEFAULT_MODULE;
                $result['tail'] = self::DEFAULT_ACTION;
            }
        }else{
            if (strlen(str_replace('/', '', $path)) == 0){
                $result['module'] = self::DEFAULT_MODULE;
                $result['tail'] = self::DEFAULT_ACTION;
            }
        }
		header('params: '.$result['tail']);
				
        $result['requestModule'] = $routeNode;
        $this->path = $path;
        return $result;
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest() {
        return $this->request;
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse() {
        if (empty($this->response)){
            $this->response = \App\Builder::getInstance()->getResponse();
        }
        return $this->response;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setResponse(\Symfony\Component\HttpFoundation\Response $response){
        $this->response = $response;
        return $this->response;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function route(){
        $response = \Models\Seo\PageRedirect::getInstance()->exceptionRedirect();
        if (is_null($response)){
            $response = $this->runModule();
        }
        return $response;
    }
    public function getUrlPrefix(){
        return '';
    }
}
