<?php
namespace App\Configs\Init\Catalog;

use Models\CatalogManagement\Properties;
use App\Configs\CatalogConfig;
use Models\Segments\Lang;

/**
 * Конфиг для создания сущностей при первом запуске системы
 *  Настройки валидации (validation) - параметры:
 *      mode - режим, используется всегда, значения 'off', 'preset', 'sel_opts', 'regex'
 *      Другие параметры зависят от значения mode:
 *          preset - в случае mode = preset, используется для типовых значений, email, phone и тд,
 *      возможные значения можно найти в CatalogConfig
 *          sel_opts - выбор допустимых символов, массив
 *          пример: sel_opts => array("digits" => "1", "cyrillic" => "1", "english" => "1", "symbols" => "\\s-_")
 *          в данном случае допускаются кириллические и латинские буквы, цифры, пробельные символы, тире и подчеркивание
 *      regex - строка, содержит регулярное выражение для проверки значения
 *
 * @author olya
 */
class DistrictsInit {
    public static function getInitData(){
        return array(
            CatalogConfig::CATALOG_KEY_DISTRICT => array(
                'data' => array(
                    'title' => array(
                        Lang::LANG_KEY_RU => 'Районы',
                        Lang::LANG_KEY_EN => 'Districts'
                    ),
                    'key' => CatalogConfig::CATALOG_KEY_DISTRICT,
                    'parent_id' => 1,
                    'item_prefix' => NULL,
                    'only_items' => 1,
                    'allow_children' => 1,
                    'allow_item_url' => 1,
                    'allow_segment_properties' => 1,
                    'allow_item_property' => 1,
                    'fixed' => 1
                ),
                'item_title' => 'Район',
                'types' => array(
                    array(
                        'data' => array(
                            'title' => array(
                                Lang::LANG_KEY_RU => 'Санкт-Петербург',
                                Lang::LANG_KEY_EN => 'Saint-Petersburg'
                            ),
                            'key' => CatalogConfig::CATEGORY_KEY_DISTRICT_SPB,
                            'only_items' => 1
                        )
                    )
                ),
                'properties' => array(
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Наименование',
                            Lang::LANG_KEY_EN => 'Title'
                        ),
                        'key' => CatalogConfig::KEY_ITEM_TITLE,
                        'data_type' => Properties\String::TYPE_NAME,
                        'segment' => 1,
                        'fixed' => Properties\Property::FIXED_FIX
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Обложка',
                            Lang::LANG_KEY_EN => 'Cover'
                        ),
                        'key' => 'cover',
                        'data_type' => Properties\Image::TYPE_NAME,
                        'fixed' => Properties\Property::FIXED_FIX
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Название в предложном падеже',
                            Lang::LANG_KEY_EN => 'The name in the prepositional'
                        ),
                        'key' => 'prepositional',
                        'data_type' => Properties\String::TYPE_NAME,
                        'segment' => 1,
                        'fixed' => Properties\Property::FIXED_FIX
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Текст о районе',
                            Lang::LANG_KEY_EN => 'Post about district'
                        ),
                        'key' => 'post',
                        'data_type' => Properties\Post::TYPE_NAME,
                        'fixed' => Properties\Property::FIXED_FIX,
                        'segment' => 1,
                        'values' => array(
                            'show_title' => 1,
                            'show_annotation' => 1,
                            'show_status' => 1,
                            'allow_images' => 1
                        )
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Цена за квадратный метр в новостройках',
                            Lang::LANG_KEY_EN => 'Price per square meter in primary estate'),
                        'key' => 'price_primary',
                        'data_type' => Properties\Float::TYPE_NAME,
                        'mask' => array(
                            Lang::LANG_KEY_RU => 'от {!} тыс. руб.',
                            Lang::LANG_KEY_EN => 'from rub {!} thous.'
                        ),
                        'search_type' => Properties\Factory::SEARCH_BETWEEN,
                        'filter_visible' => CatalogConfig::FV_ADMIN,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'major' => '20%',
                        'fixed' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Цена за квадратный метр на вторичке',
                            Lang::LANG_KEY_EN => 'Price per square meter in resale estate'),
                        'key' => 'price_resale',
                        'data_type' => Properties\Float::TYPE_NAME,
                        'mask' => array(
                            Lang::LANG_KEY_RU => 'от {!} тыс. руб.',
                            Lang::LANG_KEY_EN => 'from rub {!} thous.'
                        ),
                        'search_type' => Properties\Factory::SEARCH_BETWEEN,
                        'filter_visible' => CatalogConfig::FV_ADMIN,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'major' => '20%',
                        'fixed' => 1
                    ),
                )
            )
        );
    }
}
