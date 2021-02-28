<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 05.10.15
 * Time: 11:38
 */

namespace App\Configs\Init\Catalog;


use App\Configs\CatalogConfig;
use App\Configs\VideoConfig;
use Models\CatalogManagement\Properties;
use Models\Segments\Lang;

class VideoInit
{
    public static function getInitData()
    {
        return array(
            CatalogConfig::CATALOG_KEY_VIDEO => array(
                'data' => array(
                    'title' => array(
                        Lang::LANG_KEY_RU => 'Видео',
                        Lang::LANG_KEY_EN => 'Video'
                    ),
                    'key' => CatalogConfig::CATALOG_KEY_VIDEO,
                    'parent_id' => 1,
                    'allow_children' => 0,
                    'item_prefix' => NULL,
                    'only_items' => 1,
                    'allow_item_url' => NULL,
                    'allow_item_property' => 1,
                    'allow_segment_properties' => 1
                ),
                'item_title' => 'Видео',
                'properties' => array(
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Заголовок',
                            Lang::LANG_KEY_EN => 'Title'
                        ),
                        'key' => VideoConfig::KEY_TITLE,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1,
                        'segment' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Описание',
                            Lang::LANG_KEY_EN => 'Description'
                        ),
                        'key' => VideoConfig::KEY_DESCRIPTION,
                        'data_type' => Properties\Text::TYPE_NAME,
                        'fixed' => 1,
                        'segment' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Ссылка на видео',
                            Lang::LANG_KEY_EN => 'Link'
                        ),
                        'key' => VideoConfig::KEY_LINK,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Id видео',
                            Lang::LANG_KEY_EN => 'Video Id'
                        ),
                        'key' => VideoConfig::KEY_VIDEO_ID,
                        'data_type' => Properties\Video::TYPE_NAME,
                        'fixed' => 1
                    )
                )
            )
        );
    }
}