<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 31.08.15
 * Time: 12:47
 */

namespace App\Configs\Init\Catalog;


use App\Configs\CatalogConfig;
use App\Configs\MetroConfig;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\Properties;
use Models\Segments\Lang;

class MetroInit
{
    public static function getInitData()
    {
        return array(
            0 . CatalogConfig::CATALOG_KEY_METRO => array(
                'data' => array(
                    'title' => array(
                        Lang::LANG_KEY_RU => 'Метро',
                        Lang::LANG_KEY_EN => 'Metro'
                    ),
                    'key' => CatalogConfig::CATALOG_KEY_METRO,
                    'parent_id' => Type::DEFAULT_TYPE_ID,
                    'allow_children' => 0,
                    'item_prefix' => NULL,
                    'only_items' => 0,
                    'allow_item_url' => NULL,
                    'allow_variant_property' => 1,
                    'allow_segment_properties' => 1
                ),
                'item_title' => 'Линия',
                'variant_title' => 'Станция',
                'properties' => array(
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Название линии',
                            Lang::LANG_KEY_EN => 'Line name'
                        ),
                        'key' => MetroConfig::KEY_LINE_TITLE,
                        'data_type' => Properties\String::TYPE_NAME,
                        'necessary' => 1,
                        'fixed' => 1,
                        'segment' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Номер',
                            Lang::LANG_KEY_EN => 'Number'
                        ),
                        'key' => MetroConfig::KEY_LINE_NUMBER,
                        'data_type' => Properties\Int::TYPE_NAME,
                        'fixed' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Цвет',
                            Lang::LANG_KEY_EN => 'Color'
                        ),
                        'key' => MetroConfig::KEY_LINE_COLOR,
                        'data_type' => Properties\Color::TYPE_NAME,
                        'necessary' => 1,
                        'fixed' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Название станции',
                            Lang::LANG_KEY_EN => 'Station name'
                        ),
                        'key' => MetroConfig::KEY_STATION_TITLE,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1,
                        'multiple' => 1,
                        'necessary' => 1,
                        'segment' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Переход на станции',
                            Lang::LANG_KEY_EN => 'Transfer stations'
                        ),
                        'key' => MetroConfig::KEY_TRANSFER_STATION,
                        'data_type' => Properties\Variant::TYPE_NAME,
                        'values' => array(
                            'catalog_id' => CatalogConfig::CATALOG_KEY_METRO,
                            'edit_mode' => Properties\Variant::SELECT_MODE_LIST
                        ),
                        'fixed' => 1,
                        'multiple' => 1,
                        'set' => 1
                    ),
                )
            )
        );
    }
}