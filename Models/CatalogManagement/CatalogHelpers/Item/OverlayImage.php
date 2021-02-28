<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 28.10.15
 * Time: 12:14
 */

namespace Models\CatalogManagement\CatalogHelpers\Item;


use App\Configs\CatalogConfig;
use App\Configs\RealEstateConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;

class OverlayImage extends ItemHelper
{
    const IMG_FOLDER = '/data/overlay/';

    protected static $i = NULL;

    private $upd_ids = array();

    public function fieldsList(){
        return array('floor_overlay', 'apartment_overlay', 'building_overlay');
    }

    public function get(Item $item, $field){
        if (!in_array($field, $this->fieldsList())){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
        $category = $item->getType();
        $catalog = $category->getCatalog();
        if ($catalog['key'] != CatalogConfig::CATALOG_KEY_REAL_ESTATE || $category['key'] != RealEstateConfig::CATEGORY_KEY_FLAT) {
            return null;
        }
        $floor = $item->getParent();
        $building = $floor->getParent();
        switch ($field) {
            case 'apartment_overlay':
                $overlay_id = $item['id'];
                break;
            case 'floor_overlay':
                $overlay_id = $floor['id'];
                break;
            case 'building_overlay':
                $overlay_id = $building['id'];
                break;
            default:
                throw new \LogicException('Incorrect field key #'.$field);
        }
        $file_path = self::IMG_FOLDER . 'ovr_' . $overlay_id . '.jpeg';
        if (!file_exists(\LPS\Config::getRealDocumentRoot() . $file_path)){
            $this->makeOverlay($item);
        }
        return file_exists(\LPS\Config::getRealDocumentRoot() . $file_path) ? $file_path : null;
    }

    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors) {
        $category = $item->getType();
        $catalog = $category->getCatalog();
        if ($catalog['key'] != CatalogConfig::CATALOG_KEY_REAL_ESTATE || !in_array($category['key'], array(RealEstateConfig::CATEGORY_KEY_FLOOR, RealEstateConfig::CATEGORY_KEY_FLAT))) {
            return null;
        }
        switch ($category['key']) {
            case RealEstateConfig::CATEGORY_KEY_FLAT:
                $prop_key = RealEstateConfig::KEY_APPART_COORDS;
                break;
            case RealEstateConfig::CATEGORY_KEY_FLOOR:
                $prop_key = RealEstateConfig::KEY_FLOOR_SHEME_COORDS;
                break;
            case RealEstateConfig::CATEGORY_KEY_HOUSING:
                $prop_key = RealEstateConfig::KEY_HOUSING_SCHEME_COORDS;
                break;
            default:
                throw new \LogicException('Incorrect category key #'.$category['key']);
        }
        if (empty($properties[$prop_key])) {
            return null;
        }
        $item_coords = $item[$prop_key];
        $item_coords = is_array($item_coords)
            ? $item_coords
            : (!empty($item_coords) ? array($item_coords) : array());
        $new_coords = array();
        foreach($properties[$prop_key] as $val) {
            if (!empty($val['value']) && empty($val['options']['delete'])) {
                $new_coords[] = $val['value'];
            }
        }
        foreach($new_coords as $k => $v) {
            $old_index = array_search($v, $item_coords);
            if ($old_index !== false) {
                unset($item_coords[$old_index]);
                unset($new_coords[$k]);
            }
        }
        if (!empty($apart_coords) || !empty($new_coords)) {
            $this->upd_ids[$item['id']] = $item['id'];
        }
    }

    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties) {
        if (!empty($this->upd_ids[$item['id']])) {
            if (is_array($this->upd_ids[$item['id']])) {
                array_map('unlink', glob(\LPS\Config::getRealDocumentRoot() . self::IMG_FOLDER . 'ovr_{' . implode(',', $this->upd_ids[$item['id']]) . '}.jpeg', GLOB_BRACE));
            } else {
                $filename = \LPS\Config::getRealDocumentRoot() . self::IMG_FOLDER . 'ovr_' . $this->upd_ids[$item['id']] . '.jpeg';
                if (file_exists($filename)) {
                    unlink($filename);
                }
            }
            unset($this->upd_ids[$item['id']]);
        }
    }

    private function makeOverlay(Item $item){
        $file_path = \LPS\Config::getRealDocumentRoot() . self::IMG_FOLDER;
        if (!file_exists($file_path)){
            mkdir($file_path, 0770, true);
        }
        $floor = $item->getParent();
        $building = $floor->getParent();
        $complex = $building->getParent();
        foreach($this->fieldsList() as $field) {
            switch ($field) {
                case 'apartment_overlay':
                    $overlay_id = $item['id'];
                    $coords_set = $item[RealEstateConfig::KEY_APPART_COORDS];
                    /** @var \Models\ImageManagement\Image $scheme_image */
                    $scheme_image = $floor[RealEstateConfig::KEY_FLOOR_SHEME_GET];
                    break;
                case 'floor_overlay':
                    $overlay_id = $floor['id'];
                    $coords_set = $floor[RealEstateConfig::KEY_FLOOR_SHEME_COORDS];
                    /** @var \Models\ImageManagement\Image $scheme_image */
                    $scheme_image = $building[RealEstateConfig::KEY_HOUSING_SCHEME_GET];
                    break;
                case 'building_overlay':
                    $overlay_id = $building['id'];
                    $coords_set = $building[RealEstateConfig::KEY_HOUSING_SCHEME_COORDS];
                    /** @var \Models\ImageManagement\Image $scheme_image */
                    $scheme_image = $complex[RealEstateConfig::KEY_OBJECT_SHEME_GET];
                    break;
                default:
                    throw new \LogicException('Incorrect field key #'.$field);
            }
            $file_name =  'ovr_' . $overlay_id . '.jpeg';
            if (file_exists($file_path . $file_name)) {
                continue;
            }
            if (empty($scheme_image)) {
                continue;
            }
            file_put_contents($file_path . $file_name, file_get_contents('http://' . \LPS\Config::getParametr('Site', 'url') . $scheme_image->getUrl(195, 400, false, false, array('gray'))));
            if (file_exists($file_path . $file_name) && !empty($coords_set)){
                $coords_set = !is_array($coords_set) ? array($coords_set) : $coords_set;
                $image_type = image_type_to_mime_type(exif_imagetype($file_path . $file_name));
                $image = $image_type == 'image/png' ? imagecreatefrompng($file_path . $file_name) : imagecreatefromjpeg($file_path . $file_name);
                $size = getimagesize($file_path . $file_name);
                $width = $size[0];
                $height = $size[1];
                foreach($coords_set as $coords) {
                    $flat_coords = array();
                    $coords = explode(',', $coords);
                    $num_points = 0;
                    while(!empty($coords)){
                        $num_points++;
                        array_push($flat_coords,
                            $width * trim(array_shift($coords)) / 100,
                            $height * trim(array_shift($coords)) / 100);
                    }
                    $color = imagecolorallocatealpha($image, 255, 126, 0, 50);
                    imagefilledpolygon($image, $flat_coords, $num_points, $color);
                }
                imagejpeg($image, $file_path . $file_name);
                imagedestroy($image);
            }
        }
    }
}