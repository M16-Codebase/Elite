<?php
/**
 * Роутер для сегментированного сайта
 *
 * @author olga
 */
namespace App;
class SegmentRouter extends \LPS\Router\Web{
    /**
     * Объект сегмента
     * @var type 
     */
    private $segment = NULL;

    /**
     * Парсинг сегмента вынесен за пределы parseRoute, поскольку мы можем навешивать дополнительные обработки в дочерних роутерах
     * @return string
     */
    protected function getPathInfo() {
        $path = $this->request->getPathInfo();
        $is_main_page = strlen(str_replace('/', '', $path)) == 0;
        if (!$is_main_page){
            $segment_key = trim(substr($path, 0, strpos(ltrim($path, '/'), '/') + 1), '/');
            $this->segment = Segment::getInstance()->getByKey($segment_key);
            if (!empty($this->segment)){
                $path = substr($path, strpos(ltrim($path, '/'), '/') + 1);
            }
        }
        return $path;
    }
    /**
     * парсинг параметров
     * @return array (string module,string tail)
     */
	protected function parceRoute(){
		$path = $this->getPathInfo();
        return $this->getStandardResult($path);
	}
    
    public function getSegment(){
        return $this->segment;
    }
}