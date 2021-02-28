<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 29.06.15
 * Time: 13:10
 */

namespace App;


use App\Configs\BrandsConfig;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;

class DynamicRouter extends SegmentRouter{
    /**
     * Объект бренда
     * @var \Models\CatalogManagement\Positions\Brand|NULL
     */
    private $brand = NULL;

    /**
     * Парсинг брендов с поддержкой сегментов
     * @return string
     */
    protected function getPathInfo() {
        $path = \LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_NONE
            ? $this->request->getPathInfo()
            : parent::getPathInfo();
        $is_main_page = strlen(str_replace('/', '', $path)) == 0;
        if (!$is_main_page){
            $module_key = trim(substr($path, 0, strpos(ltrim($path, '/'), '/') + 1), '/');
            $route_map = \LPS\Config::getInstance()->getModulesRouteMap(TRUE);
            if (empty($route_map[$module_key])){
                $brand = CatalogSearch::factory(CatalogConfig::BRANDS_KEY)->setRules(array(Rule::make(BrandsConfig::KEY_BRAND_URL_TOKEN)->setValue($module_key)))->searchItems()->getFirst();
                if (!empty($brand)){
                    $path = '/brands' . $path;
                    $this->brand = $brand;
                }
            }
        }
        return $path;
    }

    public function getEntity(){
        return $this->brand;
    }

    public function getSegment(){
        return \LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_NONE
            ? \App\Segment::getInstance()->getDefault()
            : parent::getSegment();
    }
}