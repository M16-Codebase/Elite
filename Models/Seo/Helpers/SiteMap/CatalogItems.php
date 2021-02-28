<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 14.10.14
 * Time: 14:26
 * Сбор урлов объектов каталога для sitemap.xml
 * Используемые каталоги можно выбрать в seo-конфиге
 */

namespace Models\Seo\Helpers\SiteMap;


use App\Configs\CatalogConfig;
use App\Configs\RealEstateConfig;
use App\Configs\SeoConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\Variant;
use Models\ContentManagement\Post;

class CatalogItems extends SitemapHelper {
    protected static $i = NULL;

    const PAGE_SIZE = 1000;
    const USE_VARIANTS = TRUE;
    
    protected function __construct(){
        parent::__construct();
    }

    /**
     * @param int $segment_id
     * @return bool
     */
    public function writeUrls($segment_id = null)
    {
        $seo_config = \Models\CatalogManagement\Positions\Settings::getConfigByKey(\App\Configs\CatalogConfig::CONFIG_SEO_KEY);
        $catalog_keys = $seo_config['properties'][\App\Configs\Settings::KEY_SITEMAP_CATALOGS]['value_key'];
        if (empty($catalog_keys)) {
            return false;
        }
        foreach($catalog_keys as $key){
            $method_name = 'writeUrls_' . $key;
            if (is_callable(array($this, $method_name))) {
                call_user_func(array($this, $method_name), $segment_id);
            } else {
                $this->writeCatalogUrls(Type::getByKey($key), $segment_id);
            }
        }
    }

    private function writeCatalogUrls(\Models\CatalogManagement\Type $catalog, $segment_id){
        $types = $catalog->getAllChildren();
        $this->writeUrl($catalog);
        foreach($types as $type_list){
            foreach($type_list as $type){
                $this->writeUrl($type);
            }
        }
        $search = CatalogSearch::factory($catalog['key'])->setPublicOnly(TRUE)->setEnableCountByTypes(FALSE);
        $offset = 0;
        do{
            $items = $search->searchItems($offset, self::PAGE_SIZE);
            foreach($items as $item){
                $this->writeUrl($item);
            }
            Item::clearCache(null, null, false);
            $offset += self::PAGE_SIZE;
        }while(count($items));
        if (!$catalog['only_items'] && self::USE_VARIANTS) {
            $offset = 0;
            do{
                $variants = $search->searchVariants($offset, self::PAGE_SIZE);
                foreach($variants as $variant){
                    $this->writeUrl($variant);
                }
                Variant::clearCache(null, null, false);
                $offset += self::PAGE_SIZE;
            }while(count($items));
        }
    }

    private function writeUrls_real_estate($segment_id) {
        $catalog = Type::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE);
        $this->writeUrl($catalog, '', $segment_id);
        $complex_category = Type::getByKey(RealEstateConfig::CATEGORY_KEY_COMPLEX, $catalog['id']);
        $apartment_category = Type::getByKey(RealEstateConfig::CATEGORY_KEY_FLAT, $catalog['id']);
        $complex_search = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE)
            ->setTypeId($complex_category['id']);
        $inform_blocks = PropertyFactory::search($complex_category['id'], PropertyFactory::P_ALL, 'id', 'type_group', 'self', array('group_key' => RealEstateConfig::KEY_GROUP_INFORM_BLOCK));
        $offset = 0;
        do{
            $items = $complex_search->searchItems($offset, self::PAGE_SIZE);
            foreach($items as $item){
                $this->writeUrl($item, '', $segment_id);
                $this->writeUrl($item, 'apartments/', $segment_id);
                $this->writeUrl($item, 'scheme/', $segment_id);
                foreach($inform_blocks as $block) {
                    $this->writeInfoBlockUrl($item, $block, $segment_id);
                }
            }
            Item::clearCache(null, null, false);
            $offset += self::PAGE_SIZE;
        }while(count($items));
        $apartment_search = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE)
            ->setTypeId($apartment_category['id']);
        $offset = 0;
        do{
            $items = $apartment_search->searchItems($offset, self::PAGE_SIZE);
            foreach($items as $item){
                $this->writeUrl($item, '', $segment_id);
            }
            Item::clearCache(null, null, false);
            $offset += self::PAGE_SIZE;
        }while(count($items));
    }

    private function writeUrls_resale($segment_id) {
        $catalog = Type::getByKey(CatalogConfig::CATALOG_KEY_RESALE);
        $apartment_search = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESALE);
        $this->writeUrl($catalog, '', $segment_id);
        $offset = 0;
        do{
            $items = $apartment_search->searchItems($offset, self::PAGE_SIZE);
            foreach($items as $item){
                $this->writeUrl($item, '', $segment_id);
            }
            Item::clearCache(null, null, false);
            $offset += self::PAGE_SIZE;
        }while(count($items));
    }

    private function writeUrls_district($segment_id) {
        $catalog = Type::getByKey(CatalogConfig::CATALOG_KEY_DISTRICT);
        $district_search = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_DISTRICT);
        $this->writeUrl($catalog, '', $segment_id);
        $offset = 0;
        do{
            $items = $district_search->searchItems($offset, self::PAGE_SIZE);
            foreach($items as $district){
                $this->writeDistrictUrl($district, $segment_id);
            }
            Item::clearCache(null, null, false);
            $offset += self::PAGE_SIZE;
        }while(count($items));
    }

    /**
     * Запись урла айтема/варианта в sitemap
     * @param Type|Item|Variant $entity
     * @param string $url_tail
     */
    private function writeUrl($entity, $url_tail = '', $segment_id){
        if (self::USE_SEGMENTS){
            $this->sitemap_generator->addUrlToSiteMapFile($entity->getUrl($segment_id) . $url_tail, $entity['last_update'], SeoConfig::CATALOG_DEFAULT_PRIORITY);
        } else {
            $this->sitemap_generator->addUrlToSiteMapFile($entity->getUrl() . $url_tail, $entity['last_update'], SeoConfig::CATALOG_DEFAULT_PRIORITY);
        }
    }

    /**
     * @param Item $district
     */
    private function writeDistrictUrl($district, $segment_id){
        if (self::USE_SEGMENTS){
            $district->setSegment($segment_id);
            if (!empty($district['post']) && ($district['post']['status'] == Post::STATUS_CLOSE)) {
                $this->sitemap_generator->addUrlToSiteMapFile($district->getUrl($segment_id), $district['last_update'], SeoConfig::CATALOG_DEFAULT_PRIORITY);
            }
        } else {
            if (!empty($district['post']) && ($district['post']['status'] == Post::STATUS_CLOSE)) {
                $this->sitemap_generator->addUrlToSiteMapFile($district->getUrl(), $district['last_update'], SeoConfig::CATALOG_DEFAULT_PRIORITY);
            }
        }
    }

    /**
     * @param Item $entity
     * @param Property $property
     */
    private function writeInfoBlockUrl($entity, $property, $segment_id) {
        if (self::USE_SEGMENTS){
            $entity->setSegment($segment_id);
            if (!empty($entity[$property['key']])) {
                $this->sitemap_generator->addUrlToSiteMapFile($entity->getUrl($segment_id) . $property['key'] . '/', $entity['last_update'], SeoConfig::CATALOG_DEFAULT_PRIORITY);
            }
        } else {
            if (!empty($entity[$property['key']])) {
                $this->sitemap_generator->addUrlToSiteMapFile($entity->getUrl() . $property['key'] . '/', $entity['last_update'], SeoConfig::CATALOG_DEFAULT_PRIORITY);
            }
        }
    }
}