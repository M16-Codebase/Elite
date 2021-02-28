<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 25.06.15
 * Time: 19:09
 */

namespace Models\CatalogManagement\Positions;


use App\Configs\BrandsConfig;
use Models\CatalogManagement\Item;

class Brand extends Item {
    /**
     * Свои хелперы
     * @var array
     */
    static protected $dataProviders = array();
    static protected $dataProvidersByFields = array();

    public function isMonoBrand(){
        return $this['properties'][BrandsConfig::KEY_BRAND_MONO]['value'];
    }
}