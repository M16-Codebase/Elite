<?php
/**
 * Description of Rss
 *
 * @author mac-proger
 */
namespace Modules\Posts;

class Rss extends \LPS\WebModule{
    //put your code here
    
    public function route($route){
        $routeTokens = explode('/', $route, 2);
        $chunks = explode('.', $routeTokens[0]);
        if (count($chunks) == 2 && $chunks[1] == 'rss'){
            $seg = \App\Segment::getInstance()->getDefault(true);
            $actions = \Models\Rss::getChannels($seg['id']);
            if (isset($actions[$chunks[0]])){
                $action = 'index';
                $this->routeTail = $actions[$chunks[0]];
            } else {
                $action = 404;
            }
        } else {
            $action = 404;
        }
        return $action;
    }
    public function index(){
        $filename = \LPS\Config::getRealDocumentRoot() . 'data/rss/' . $this->routeTail;
        if (!file_exists($filename)){
            return $this->notFound();
        }
        return file_get_contents($filename);
    }
}
