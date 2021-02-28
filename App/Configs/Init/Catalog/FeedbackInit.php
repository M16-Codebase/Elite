<?php

namespace App\Configs\Init\Catalog;

use \App\Configs\CatalogConfig;
use App\Configs\FeedbackConfig;
use \Models\CatalogManagement\Properties;
use Models\Segments\Lang;

class FeedbackInit{
    public static function getInitData()
    {
        return array(
            CatalogConfig::FEEDBACK_KEY => array(
                'data' => array(
                    'title' => 'База обращений',
                    'key' => CatalogConfig::FEEDBACK_KEY,
                    'parent_id' => 1,
                    'item_prefix' => 'i_',
                    'allow_children' => 1,
                    'only_items' => 1,
                    'allow_item_url' => 0
                ),
                'item_title' => 'Отклик',
                'types' => array(
                    array(
                        'data' => array(
                            'title' => 'Заявка на просмотр квартиры',
                            'key' => FeedbackConfig::TYPE_VIEW_APARTMENTS,
                            'number_prefix' => 'П',
                            'allow_children' => 0
                        ),
                        'properties' => array(
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Интересующая квартира',
                                    Lang::LANG_KEY_EN => 'Resale apartment'),
                                'key' => 'apartment',
                                'data_type' => Properties\Item::TYPE_NAME,
                                'values' => array(
                                    'catalog_id' => CatalogConfig::CATALOG_KEY_RESALE
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1,
                                'necessary' => 1
                            )
                        )
                    ),
                    array(
                        'data' => array(
                            'title' => 'Заявка/вопрос по недвижимости',
                            'key' => FeedbackConfig::TYPE_APART_REQUEST,
                            'number_prefix' => 'Н',
                            'allow_children' => 0
                        ),
                        'properties' => array(
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Интересующий жилой комплекс',
                                    Lang::LANG_KEY_EN => 'Complex'),
                                'key' => FeedbackConfig::KEY_REQUEST_COMPLEX,
                                'data_type' => Properties\Item::TYPE_NAME,
                                'values' => array(
                                    'catalog_id' => CatalogConfig::CATALOG_KEY_REAL_ESTATE
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Интересующие квартиры (первичка)',
                                    Lang::LANG_KEY_EN => 'Apartments'),
                                'key' => FeedbackConfig::KEY_REQUEST_APARTMENT,
                                'data_type' => Properties\Item::TYPE_NAME,
                                'values' => array(
                                    'catalog_id' => CatalogConfig::CATALOG_KEY_REAL_ESTATE
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1,
                                'set' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Интересующие квартиры (вторичка)',
                                    Lang::LANG_KEY_EN => 'Resale apartments'),
                                'key' => FeedbackConfig::KEY_REQUEST_APARTMENT_RESALE,
                                'data_type' => Properties\Item::TYPE_NAME,
                                'values' => array(
                                    'catalog_id' => CatalogConfig::CATALOG_KEY_RESALE
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1,
                                'set' => 1
                            )
                        )
                    ),
                    array(
                        'data' => array(
                            'title' => 'Заявка на подбор жилья',
                            'key' => FeedbackConfig::TYPE_FLAT_SELECTION,
                            'number_prefix' => 'Ж',
                            'allow_children' => 0
                        ),
                        'properties' => array(
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Район',
                                    Lang::LANG_KEY_EN => 'District'),
                                'key' => FeedbackConfig::KEY_SELECTION_DISTRICT,
                                'data_type' => Properties\Item::TYPE_NAME,
                                'values' => array(
                                    'catalog_id' => CatalogConfig::CATALOG_KEY_DISTRICT
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1,
                                'set' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Площадь, м²',
                                    Lang::LANG_KEY_EN => 'Area, m²',
                                ),
                                'key' => FeedbackConfig::KEY_SELECTION_AREA,
                                'data_type' => Properties\DiapasonInt::TYPE_NAME,
                                'mask' => array(
                                    Lang::LANG_KEY_RU => '{!} м²',
                                    Lang::LANG_KEY_EN => '{!} m²'
                                ),
                                'values' => array(
                                    Lang::LANG_KEY_RU => array(
                                        'min_max' => '{min}—{max}',
                                        'min' => 'от {min}',
                                        'max' => 'до {max}'
                                    ),
                                    Lang::LANG_KEY_EN => array(
                                        'min_max' => '{min}—{max}',
                                        'min' => 'from {min}',
                                        'max' => 'to {max}'
                                    )
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Цена, млн руб.',
                                    Lang::LANG_KEY_EN => 'Price, mln ₽',
                                ),
                                'key' => FeedbackConfig::KEY_SELECTION_PRICE,
                                'data_type' => Properties\DiapasonInt::TYPE_NAME,
                                'mask' => array(
                                    Lang::LANG_KEY_RU => '{!} руб',
                                    Lang::LANG_KEY_EN => '{!} RUB'
                                ),
                                'values' => array(
                                    Lang::LANG_KEY_RU => array(
                                        'min_max' => '{min}—{max}',
                                        'min' => 'от {min}',
                                        'max' => 'до {max}'
                                    ),
                                    Lang::LANG_KEY_EN => array(
                                        'min_max' => '{min}—{max}',
                                        'min' => 'from {min}',
                                        'max' => 'to {max}'
                                    )
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'В строящемся доме',
                                    Lang::LANG_KEY_EN => 'Primary estate',
                                ),
                                'key' => FeedbackConfig::KEY_SELECTION_PRIMARY,
                                'data_type' => Properties\Flag::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'values' => array(
                                    'yes' => array(Lang::LANG_KEY_RU => 'Да', Lang::LANG_KEY_EN => 'Yes'),
                                    'no' => array(Lang::LANG_KEY_RU => 'Нет', Lang::LANG_KEY_EN => 'No')
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'На вторичном рынке',
                                    Lang::LANG_KEY_EN => 'Resale estate',
                                ),
                                'key' => FeedbackConfig::KEY_SELECTION_RESALE,
                                'data_type' => Properties\Flag::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'values' => array(
                                    'yes' => array(Lang::LANG_KEY_RU => 'Да', Lang::LANG_KEY_EN => 'Yes'),
                                    'no' => array(Lang::LANG_KEY_RU => 'Нет', Lang::LANG_KEY_EN => 'No')
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Видовая квартира',
                                    Lang::LANG_KEY_EN => 'Species apartment',
                                ),
                                'key' => FeedbackConfig::KEY_SELECTION_SPECIES,
                                'data_type' => Properties\Flag::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'values' => array(
                                    'yes' => array(Lang::LANG_KEY_RU => 'Да', Lang::LANG_KEY_EN => 'Yes'),
                                    'no' => array(Lang::LANG_KEY_RU => 'Нет', Lang::LANG_KEY_EN => 'No')
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Число спален',
                                    Lang::LANG_KEY_EN => 'Bed number',
                                ),
                                'key' => FeedbackConfig::KEY_SELECTION_BED_NUMBER,
                                'data_type' => Properties\Enum::TYPE_NAME,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array('1', '2', '3', '4', '5+'),
                                        Lang::LANG_KEY_EN => array('1', '2', '3', '4', '5+')
                                    ),
                                    'keys' => array(1, 2, 3, 4, 5)
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1,
                                'set' => 1
                            ),
                        )
                    ),
                    array(
                        'data' => array(
                            'title' => 'Заявка от собственника',
                            'key' => FeedbackConfig::TYPE_OWNER,
                            'number_prefix' => 'С',
                            'allow_children' => 0
                        ),
                        'properties' => array(
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Адрес',
                                    Lang::LANG_KEY_EN => 'Address'),
                                'key' => FeedbackConfig::KEY_OWNER_ADDRESS,
                                'data_type' => Properties\String::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Число спален',
                                    Lang::LANG_KEY_EN => 'Bed number'),
                                'key' => FeedbackConfig::KEY_OWNER_BED_NUMBER,
                                'data_type' => Properties\String::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Площадь, м²',
                                    Lang::LANG_KEY_EN => 'Area, m²'),
                                'key' => FeedbackConfig::KEY_OWNER_AREA,
                                'data_type' => Properties\String::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Цена, млн руб',
                                    Lang::LANG_KEY_EN => 'Price, mln rub'),
                                'key' => FeedbackConfig::KEY_OWNER_PRICE,
                                'data_type' => Properties\String::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Тип недвижимости',
                                    Lang::LANG_KEY_EN => 'Estate type',
                                ),
                                'key' => FeedbackConfig::KEY_OWNER_ESTATE_TYPE,
                                'data_type' => Properties\Enum::TYPE_NAME,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array('В строящемся доме', 'На вторичном рынке'),
                                        Lang::LANG_KEY_EN => array('Primary estate', 'Resale')
                                    ),
                                    'keys' => array('primary', 'resale')
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Видовая квартира',
                                    Lang::LANG_KEY_EN => 'Species apartment',
                                ),
                                'key' => FeedbackConfig::KEY_OWNER_SPECIES,
                                'data_type' => Properties\Flag::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'values' => array(
                                    'yes' => array(Lang::LANG_KEY_RU => 'Да', Lang::LANG_KEY_EN => 'Yes'),
                                    'no' => array(Lang::LANG_KEY_RU => 'Нет', Lang::LANG_KEY_EN => 'No')
                                ),
                                'fixed' => 1
                            )
                        )
                    ),
                    array(
                        'data' => array(
                            'title' => 'Обратная связь',
                            'key' => FeedbackConfig::TYPE_FEEDBACK,
                            'number_prefix' => 'О',
                            'allow_children' => 0
                        )
                    ),
//                    array(
//                        'data' => array(
//                            'title' => 'Заказ звонка',
//                            'key' => FeedbackConfig::TYPE_CALLBACK,
//                            'number_prefix' => 'З',
//                            'allow_children' => 0
//                        )
//                    )
                ),
                'properties' => array(
                    array(
                        'title' => 'Номер обращения',
                        'key' => FeedbackConfig::KEY_FEEDBACK_NUMBER,
                        'data_type' => Properties\Int::TYPE_NAME,
                        'fixed' => 1
                    ),
                    array(
                        'title' => 'Имя отправителя',
                        'key' => FeedbackConfig::KEY_FEEDBACK_AUTHOR,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => '1',
                        'necessary' => 1
                    ),
                    array(
                        'title' => 'Электронная почта',
                        'key' => FeedbackConfig::KEY_FEEDBACK_EMAIL,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1,
                        'necessary' => 1,
                        'validation' => array(
                            'mode' => 'preset',
                            'preset' => 'email'
                        )
                    ),
                    array(
                        'title' => 'Телефон',
                        'key' => FeedbackConfig::KEY_FEEDBACK_PHONE,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1,
                        'necessary' => 1,
                    ),
                    array(
                        'title' => 'Сообщение',
                        'key' => FeedbackConfig::KEY_FEEDBACK_MESSAGE,
                        'data_type' => Properties\Text::TYPE_NAME,
                        'fixed' => 1,
                        'necessary' => 1
                    ),
                    array(
                        'title' => 'Ссылка, с которой была отправлена форма',
                        'key' => FeedbackConfig::KEY_FEEDBACK_REFERRER_URL,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1
                    ),
                    array(
                        'title' => 'Статус обращения',
                        'key' => FeedbackConfig::KEY_FEEDBACK_STATUS,
                        'data_type' => Properties\Enum::TYPE_NAME,
                        'fixed' => 1,
                        'values' => array(
                            'values' => array(
                                Lang::LANG_KEY_RU => array('Новое', 'Обработано', 'Отклонено'),
                                Lang::LANG_KEY_EN => array('New', 'Processed', 'Rejected')
                            ),
                            'keys' => array(FeedbackConfig::STATUS_NEW, FeedbackConfig::STATUS_PROCESSED, FeedbackConfig::STATUS_REJECTED)
                        )
                    )
                )
            )
        );
    }
}