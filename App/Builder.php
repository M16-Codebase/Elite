<?php
namespace App;
/**
 * Symplify Dependency Injection
 *
 * @author Alexander
 */
class Builder extends \LPS\Builder{
    /**
     * @var Builder
     */
    private static $instance = NULL;
    private static $segmentClasses = array(
        \LPS\Config::SEGMENT_MODE_NONE => '\Models\Segments\None',
        \LPS\Config::SEGMENT_MODE_LANGUAGE => '\Models\Segments\Lang',
        \LPS\Config::SEGMENT_MODE_REGION => '\Models\Segments\Region'
    );
    /**
     * @return Builder
     */
    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new Builder();
        }
        return self::$instance;
    }
    /**
     * пишите сюда возврат специфичных для данного приложения объектов, например, getSpecialModel(){конфигурирование }
     */
    /**
     * @return Router
     */
    public function getWebRouter(){
        $name = __METHOD__;
        if (!$this->exist($name)){
            $routeConfig = $this->getConfig()->getModulesRouteMap();
            $router = \LPS\Config::ENABLE_DYNAMIC_ROUTING
                ? new \App\DynamicRouter($routeConfig, $this->getRequest())
                : (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE
                    ? new \App\SegmentRouter($routeConfig, $this->getRequest())
                    : new \LPS\Router\Web($routeConfig, $this->getRequest()));
            $this->set($name, $router);
        }
        return $this->get($name);
    }

    /**
     * @return string
     * @TODO возможно перенести в LPS\Builder?
     */
    public function getSegmentClass(){
        return self::$segmentClasses[\LPS\Config::SEGMENT_MODE];
    }
}
?>
