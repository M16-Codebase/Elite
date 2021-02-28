<?php
/**
 * Description of Segment
 *
 * @author olga
 */
namespace App;
class Segment{
    /**
     * @var Segment
     */
    protected static $instance = null;
    /**
     * @var string
     */
    private $className = NULL;
    /**
     * @return Segment
     */
    public static function getInstance() {
        if (empty(self::$instance))
            self::$instance = new Segment();
        return self::$instance;
    }

    private function __construct(){
        $this->className = \App\Builder::getInstance()->getSegmentClass();
    }
    /**
     * @param bool $onSite true - паблик, false - админка
     * @return \Models\Segments\iSegment
     */
    public function getDefault($onSite = false){
        $class = $this->className;
        return $class::getDefault($onSite);
    }

    /**
     * @return string
     */
    public function getDefaultKey(){
        $class = $this->className;
        return $class::DEFAULT_KEY;
    }

    /**
     * 
     * @param int $segment_id
     * @return \Models\Segments\iSegment
     */
	public function getById($segment_id){
        $class = $this->className;
        return $class::getById($segment_id);
	}
    /**
     *
     * @return \Models\Segments\iSegment[]
     */
    public function getAll(){
        $class = $this->className;
        return $class::getAll();
    }
    /**
     * 
     * @param string $key
     * @return \Models\Segments\iSegment
     */
    public function getByKey($key){
        $class = $this->className;
        return $class::getByKey($key);
    }

    /**
     * @param $key
     * @param null $title
     * @param array $errors
     * @return int
     */
    public function create($key, $title = NULL, &$errors = array()){
        $class = $this->className;
        return $class::create($key, $title, $errors);
    }

    /**
     * @param $id
     * @param null $error
     * @return bool
     */
    public function delete($id, &$error = NULL){
        $class = $this->className;
        return $class::delete($id, $error);
    }
    /**
     * Возвращает посты к страницам для текущего урла
     * @param string $uri - uri страницы, для которой нужны посты
     * @return \Models\ContentManagement\SegmentPost[]
     */
    public function getPagePosts($uri){
        $segment = $this->getDefault(true);
        return $segment->getPagePosts($uri);
    }
    /**
     * Возвращает префикс урла в соответствии с текущим сегментом
     * @param $segment_id
     * @return string
     */
    public function getUrlPrefix($segment_id = NULL){
        $segment = $this->getById($segment_id);
        if (empty($segment)){
            /** @TODO нужно ли как-то обрабатывать? Например сегмент удален, а объекты остались */
            throw new \Exception('Неизвестный сегмент #' . $segment_id);
        }
        return $segment->getUrlPrefix();
    }
}