<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 13.09.2017
 * Time: 13:24
 */

namespace App\Configs;


use Models\CatalogManagement\Type;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
use LPS\BaseConfig;

/**
 * Настройки каталогов
 */
class UrlManager {

    //const FURL = BaseConfig::USE_FRIENDLY_URL;
    const FURL = true;

    private static $urlsList = [
        '/real-estate/complex/' => '/real-estate/'
    ];

    // список синонимов для подстановки
    // при формировании ЧПУ
    private static $fUrlSinonyms = [
        'housing' => 'korpus',
        'floor' => 'etazh'
    ];

    static function getTypeUrl($urlKey) {
        if (array_key_exists($urlKey, self::$urlsList)) {
            return self::$urlsList[$urlKey];
        }
        return $urlKey;
    }

    static function getFriendlyUrlSinonym($urlKey) {
        if (array_key_exists($urlKey, self::$fUrlSinonyms)) {
            return self::$fUrlSinonyms[$urlKey];
        }
        return $urlKey;
    }

    public function createUrl(Item $item, $segment_id = NULL) {
        if (self::FURL) {
            $url = $this->createFriendlyUrl($item, $segment_id);
        } else {
            $url = $this->createCommonUrl($item, $segment_id);
        }
        return $url;
    }

    public function parseUrl() {

    }

    private function createFriendlyUrl (Item $item, $segment_id = NULL) {
        $type = $item->getType();
        $catalog = $type->getCatalog();
        if (!$catalog['allow_item_url']) {
            return null;
        }
        $segment_id = !empty($segment_id) ? $segment_id : $item['segment_id'];
        $parent = $item->getParent();
        $hasChildren = $item->checkChildren();
        //dump(empty($parent));
        if (!empty($parent)){
            //$url_end = '_';
            //если потомков нет, то это будет конец урла ставим в конце "/"
            /*if (!$hasChildren) {
                $url_las_part = '';
                // пока только для типа flat
                // тут мы формируем урл вида , например korpus-3_sekts-2_7-etazh_1-spalnya-48-08_1223/
                // и при добавлении айтема сделать просто чтобы ключ формировался автоматически
                // но по идее можно просто изменить keys в таблице с айтемами
                // @todo изменить keys в таблице с айтемами и
                if ($type['key'] == 'flat') {
                    $area = str_replace(".","-", $item['properties']['area_all']['value']);
                    $bn = $item['properties']['bed_number']['value'];
                    $bn_key = 'spalnya';
                    if ($bn > 1 && $bn < 5) { $bn_key = 'spalni'; }
                    else if ($bn > 5) { $bn_key = 'spalen'; }
                    $bn_key = $bn . '-' . $bn_key;
                    $url_las_part = $bn_key . '_' . $area . '_' . $item['key'];
                }
                $url_end = $url_las_part . '/';
                return $parent->getUrl($segment_id) . $url_end;
            }*/
            //$type_url_key = UrlManager::getFriendlyUrlSinonym($type['key']);
            //dump($type_url_key . '-' .intval($item['key']));
            //return $parent->getUrl($segment_id) . $type_url_key . '-' .intval($item['key']) . $url_end;




            return $parent->getUrl($segment_id) . $item['key'] . '/';
        } else {
            $typeUrl = UrlManager::getTypeUrl($type->getUrl($segment_id));
            return $typeUrl . $catalog['item_prefix'] . $item['key'] . '/'; // у кустика префикс айтема всегда пустой
        }
    }

    private function createCommonUrl (Item $item, $segment_id = NULL) {
        $type = $item->getType();
        $catalog = $type->getCatalog();
        if (!$catalog['allow_item_url']) {
            return null;
        }
        $segment_id = !empty($segment_id) ? $segment_id : $item['segment_id'];
        $parent = $item->getParent();

        if (!empty($parent)){
            return $parent->getUrl($segment_id) . $item['key'] . '/';
        } else {
            return $type->getUrl($segment_id) . $catalog['item_prefix'] . $item['key'] . '/'; // у кустика префикс айтема всегда пустой
        }
    }


}