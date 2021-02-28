<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 31.08.15
 * Time: 19:47
 */

namespace App\Configs\Init\Catalog;


use App\Configs\CatalogConfig;
use Models\CatalogManagement\Properties;
use Models\CatalogManagement\Type;
use Models\Segments\Lang;

class InfrastructureInit
{
    public static function getInitData()
    {
        return array(
            CatalogConfig::CATALOG_KEY_INFRASTRUCTURE => array(
                'data' => array(
                    'title' => array(
                        Lang::LANG_KEY_RU => 'Объекты инфраструктуры',
                        Lang::LANG_KEY_EN => 'Infrastructure objects'
                    ),
                    'key' => CatalogConfig::CATALOG_KEY_INFRASTRUCTURE,
                    'parent_id' => Type::DEFAULT_TYPE_ID,
                    'allow_children' => 0,
                    'item_prefix' => NULL,
                    'only_items' => 1,
                    'allow_item_url' => NULL,
                    'allow_item_property' => 1,
                    'allow_segment_properties' => 1
                ),
                'item_title' => 'Объект',
                'properties' => array(
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Тип',
                            Lang::LANG_KEY_EN => 'Type'
                        ),
                        'key' => CatalogConfig::KEY_INFRA_TYPE,
                        'data_type' => Properties\Enum::TYPE_NAME,
                        'necessary' => 1,
                        'fixed' => 1,
                        'values' => array(
                            'values' => array(
                                Lang::LANG_KEY_RU => array(
                                    'Ресторан',
                                    'Кафе',
                                    'Бар',
                                    'Клуб',
                                    'Отделение почты',
                                    'Отделение банка',
                                    'Банкоматы',
                                    'Супермаркет',
                                    'Магазин',
                                    'Торговый комплекс',
                                    'Парковка / Паркинг',
                                    'Фитнес-центр',
                                    'Бассейн',
                                    'Гостиница',
                                    'Парк',
                                    'Театр',
                                    'Кинотеатр',
                                    'Музей',
                                    'Стадион',
                                    'Аэропорт',
                                    'Салон красоты',
                                    'АЗС',
                                    'Университет',
                                    'Аптека'
                                ),
                                Lang::LANG_KEY_EN => array(
                                    'Restaurant',
                                    'Cafe',
                                    'Bar',
                                    'Club',
                                    'Post office',
                                    'Bank\'s filial',
                                    'ATMs',
                                    'Supermarket',
                                    'Shop',
                                    'Shopping center',
                                    'Parking',
                                    'Fitness Centre',
                                    'Pool',
                                    'Hotel',
                                    'Park',
                                    'Theater',
                                    'Cinema',
                                    'Museum',
                                    'ballpark',
                                    'Airport',
                                    'Beauty salon',
                                    'gas station',
                                    'University',
                                    'Pharmacy'
                                )
                            ),
                            'keys' => array(
                                'restaurant',
                                'cafe',
                                'bar',
                                'club',
                                'post_office',
                                'bank_filial',
                                'atm',
                                'supermarket',
                                'shop',
                                'shopping_center',
                                'parking',
                                'fitness_centre',
                                'pool',
                                'hotel',
                                'park',
                                'theater',
                                'cinema',
                                'museum',
                                'ballpark',
                                'airport',
                                'beauty_salon',
                                'gas_station',
                                'university',
                                'pharmacy'
                            )
                        )
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Наименование',
                            Lang::LANG_KEY_EN => 'Title'
                        ),
                        'key' => CatalogConfig::KEY_INFRA_TITLE,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1,
                        'segment' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Адрес',
                            Lang::LANG_KEY_EN => 'Address'
                        ),
                        'key' => CatalogConfig::KEY_INFRA_ADDRESS,
                        'data_type' => Properties\Address::TYPE_NAME,
                        'fixed' => 1,
                        'segment' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Фотография',
                            Lang::LANG_KEY_EN => 'Photo'
                        ),
                        'key' => CatalogConfig::KEY_INFRA_PHOTO,
                        'data_type' => Properties\Image::TYPE_NAME,
                        'fixed' => 1
                    )
                )
            )
        );
    }
}