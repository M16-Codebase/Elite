<?php
namespace LPS\Router;

/**
 * роутер запросов командной строки
 * Принцип работы: мапит первый параметр на роутер, а второй отдает как субпуть
 */
class Cli extends Controller{
    const DEFAULT_MODULE = 'Cli';
    /**
     * парсинг параметров
     * @param string $subRoute
     * @return string or NULL
     */
    protected function parceRoute(){
        $result = array('module' => NULL, 'tail'=>NULL);
        /* стандартный проход module/ */
        $routeNode = isset($GLOBALS['argv'][1]) ? $GLOBALS['argv'][1] : '';
        if (!empty($routeNode)){
            if (isset($this->modulesRouteMap[$routeNode])){
                $result['module'] = $this->modulesRouteMap[$routeNode];
                $result['tail'] = isset($GLOBALS['argv'][2]) ? $GLOBALS['argv'][2] : self::DEFAULT_ACTION;
            }else{
                $result['module'] = false;
                $result['tail'] = false;
            }
        }else{
            $result['module'] = self::DEFAULT_MODULE;
            $result['tail'] = self::DEFAULT_ACTION;
        }
		$result['requestModule'] = $routeNode;
        return $result;
	}

    /**
     * @return mixed
     */
    public function route(){
        $ans = NULL;
        $lock_file = fopen(\LPS\Config::getRealDocumentRoot() . \LPS\Config::LOCK_FILE_PATH, 'w');
        if(flock($lock_file, LOCK_EX | LOCK_NB)){
            $ans = parent::runModule();
            flock($lock_file, LOCK_UN);
        }
        fclose($lock_file);
        return $ans;
    }
}
?>