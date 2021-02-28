<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 29.06.15
 * Time: 13:07
 */

namespace Modules\Catalog;


use App\Configs\BrandsConfig;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;

class Brands extends \LPS\WebModule{
    /**
     * @var \Models\CatalogManagement\Positions\Brand
     */
    protected $brand = NULL;

    protected function route($route){
        $this->brand = \App\Builder::getInstance()->getWebRouter()->getEntity();
        if (!empty($this->brand)){
            $route = ltrim(preg_replace('~^'.$this->brand[BrandsConfig::KEY_BRAND_URL_TOKEN].'~u', '', $route), '/');
            if (empty($route)){
                $action = 'index';
            } else {
                $routeTokens = explode('/', $route, 2);
                $item = CatalogSearch::factory(CatalogConfig::CATALOG_KEY, $this->segment['id'])
                    ->setRules(array(
                        Rule::make(CatalogConfig::KEY_ITEM_BRAND)->setValue($this->brand['id']),
                        Rule::make('key')->setValue($routeTokens[0])
                    ))
                    ->searchItems(0, 1)
                    ->getFirst();
                if (!empty($item)){
                    $this->routeTail = $item['id'];
                    $action = 'viewItem';
                }
            }
        }
        return parent::route($route);
    }

    public function index(){
        $items = CatalogSearch::factory(CatalogConfig::CATALOG_KEY)
            ->setRules(array(Rule::make(CatalogConfig::KEY_ITEM_BRAND)->setValue($this->brand['id'])))
            ->searchItems();
        $ans = $this->getAns();
        $banners = \Models\Banner::search(array('url' => $this->brand['banner_url'], 'active' => 1, 'date_filter' => 1));
        $ans->add('brand', $this->brand)
            ->add('banners', $banners);
        if ($this->brand->isMonoBrand()){
            $ans->add('item', $items->getFirst());
        } else {
            $ans->add('items', $items);
        }
    }

    public function namco(){}
}