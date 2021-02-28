<?php
namespace App\Configs\Init\Catalog;

use App\Configs\ContactsConfig;
use App\Configs\FeedbackConfig;
use Models\CatalogManagement\Properties;
use App\Configs\OrderConfig;
use App\Configs\Settings;
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
class SettingsInit {
    public static function getInitData(){
        return array(
            CatalogConfig::CONFIG_KEY => array(
                'data' => array(
                    'title' => array(
                        Lang::LANG_KEY_RU => 'Настройки',
                        Lang::LANG_KEY_EN => 'Settings'
                    ),
                    'key' => CatalogConfig::CONFIG_KEY,
                    'parent_id' => 1,
                    'item_prefix' => NULL,
                    'only_items' => 1,
                    'allow_children' => 2,
                    'allow_item_url' => 0,
                    'allow_segment_properties' => 1
                ),
                'item_title' => 'Настройка',
                'types' => array(
                    array(
                        'data' => array(
                            'title' => array(
                                Lang::LANG_KEY_RU => 'Параметры сайта',
                                Lang::LANG_KEY_EN => 'Site settings'
                            ),
                            'key' => CatalogConfig::CONFIG_GLOBAL_KEY,
                            'only_items' => 1
                        ),
                        'properties' => array(
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Наименование компани',
                                    Lang::LANG_KEY_EN => 'Company name'
                                ),
                                'key' => Settings::KEY_COMPANY_NAME,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1,
                                'segment' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Наименование проекта',
                                    Lang::LANG_KEY_EN => 'Project name'
                                ),
                                'key' => Settings::KEY_PROJECT_NAME,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1,
                                'segment' => 1
                            ),
//                            array(
//                                'title' => array(
//                                    Lang::LANG_KEY_RU => 'Глубина вложенности тем статей',
//                                    Lang::LANG_KEY_EN => 'Nesting depth of the article themes'
//                                ),
//                                'key' => Settings::KEY_THEMES_LEVEL_COUNT,
//                                'data_type' => Properties\Enum::TYPE_NAME,
//                                'values' => array(
//									//@TODO а почему не сделать Int + (min, max, step)?
//                                    'keys' => array(0,1,2,3),
//                                    'values' => array(0,1,2,3)
//                                ),
//                                'fixed' => 1
//                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Логотип сайта',
                                    Lang::LANG_KEY_EN => 'Site logo'
                                ),
                                'key' => Settings::KEY_SITE_LOGO,
                                'data_type' => Properties\Image::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Логотип для писем с сайта',
                                    Lang::LANG_KEY_EN => 'Logo for site mails'
                                ),
                                'key' => Settings::KEY_SITE_MAILS_LOGO,
                                'data_type' => Properties\Image::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Логотип для водяных знаков на фотографиях',
                                    Lang::LANG_KEY_EN => 'Logo for the watermark on the photos'
                                ),
                                'key' => Settings::KEY_WATERMARK_LOGO,
                                'data_type' => Properties\Image::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Email отправителя писем',
                                    Lang::LANG_KEY_EN => 'Letter sender email'
                                ),
                                'key' => Settings::KEY_LETTER_SENDER_EMAIL,
                                'data_type' => Properties\String::TYPE_NAME,
                                'validation' => array(
                                    'mode' => 'preset',
                                    'preset' => 'email'
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Имя отправителя писем',
                                    Lang::LANG_KEY_EN => 'Letter sender name'
                                ),
                                'key' => Settings::KEY_LETTER_SENDER_NAME,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Email для жалоб и предложений',
                                    Lang::LANG_KEY_EN => 'Letter sender email'
                                ),
                                'key' => 'contact_mail',
                                'data_type' => Properties\String::TYPE_NAME,
                                'validation' => array(
                                    'mode' => 'preset',
                                    'preset' => 'email'
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => 'Образец csv-файла подписчиков Sendsay',
                                'key' => Settings::KEY_SENDSAY_SAMPLE,
                                'data_type' => Properties\File::TYPE_NAME,
                                'values' => array(
                                    'format' => 'csv',
                                    'max' => ''
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => 'pdf презентации компании (Русский)',
                                'key' => Settings::KEY_ABOUT_COMPANY_PDF . '_' . Lang::LANG_KEY_RU,
                                'data_type' => Properties\File::TYPE_NAME,
                                'values' => array(
                                    'format' => 'pdf',
                                    'max' => ''
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => 'pdf презентации компании (English)',
                                'key' => Settings::KEY_ABOUT_COMPANY_PDF . '_' . Lang::LANG_KEY_EN,
                                'data_type' => Properties\File::TYPE_NAME,
                                'values' => array(
                                    'format' => 'pdf',
                                    'max' => ''
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => 'Обложка PDF-презентаций квартир на русском',
                                'key' => Settings::KEY_APARTMENT_PDF_COVER . '_' . Lang::LANG_KEY_RU,
                                'data_type' => Properties\File::TYPE_NAME,
                                'values' => array(
                                    'format' => 'pdf',
                                    'max' => ''
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => 'Обложка PDF-презентаций квартир на английском',
                                'key' => Settings::KEY_APARTMENT_PDF_COVER . '_' . Lang::LANG_KEY_EN,
                                'data_type' => Properties\File::TYPE_NAME,
                                'values' => array(
                                    'format' => 'pdf',
                                    'max' => ''
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Мнение консультанта (первичная)',
                                    Lang::LANG_KEY_EN => 'Opinion adviser (real estate)'),
                                'key' => Settings::KEY_OBJECT_CONSULTANT_TEXT,
                                'data_type' => Properties\Text::TYPE_NAME,
                                'segment' => 1,
                                'visible' => CatalogConfig::V_ADMIN,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Мнение консультанта (вторичная)',
                                    Lang::LANG_KEY_EN => 'Opinion adviser (resale)'),
                                'key' => Settings::KEY_APPART_CONSULTANT_TEXT,
                                'data_type' => Properties\Text::TYPE_NAME,
                                'segment' => 1,
                                'visible' => CatalogConfig::V_ADMIN,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Консультанты первичной недвижимости',
                                    Lang::LANG_KEY_EN => 'Real estate consultants'),
                                'key' => Settings::KEY_REAL_ESTATE_CONSULTANT,
                                'data_type' => Properties\Item::TYPE_NAME,
                                'values' => array(
                                    'catalog_id' => CatalogConfig::CATALOG_KEY_STAFF_LIST,
                                    'edit_mode' => Properties\Item::SELECT_MODE_LIST
                                ),
                                'search_type' => Properties\Factory::SEARCH_SELECT,
                                'filter_visible' => CatalogConfig::FV_ADMIN,
                                'visible' => CatalogConfig::V_ADMIN,
                                'set' => 1,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Консультанты вторичной недвижимости',
                                    Lang::LANG_KEY_EN => 'Resale consultants'),
                                'key' => Settings::KEY_RESALE_CONSULTANT,
                                'data_type' => Properties\Item::TYPE_NAME,
                                'values' => array(
                                    'catalog_id' => CatalogConfig::CATALOG_KEY_STAFF_LIST,
                                    'edit_mode' => Properties\Item::SELECT_MODE_LIST
                                ),
                                'search_type' => Properties\Factory::SEARCH_SELECT,
                                'filter_visible' => CatalogConfig::FV_ADMIN,
                                'visible' => CatalogConfig::V_ADMIN,
                                'set' => 1,
                                'fixed' => 1
                            )
                        )
                    ),
                    array(
                        'data' => array(
                            'title' => 'Получатели уведомлений',
                            'key' => CatalogConfig::CONFIG_NOTIFICATION_KEY,
                            'only_items' => 1
                        ),
                        'properties' => array(
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Заявка/вопрос по первичной недвижимости',
                                    Lang::LANG_KEY_EN => 'The application / question about the primary real estate'
                                ),
                                'key' => FeedbackConfig::TYPE_APART_REQUEST_PRIMARY,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1,
                                'set' => 1,
                                'validation' => array(
                                    'mode' => 'preset',
                                    'preset' => 'email'
                                )
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Заявка/вопрос по вторичной недвижимости',
                                    Lang::LANG_KEY_EN => 'The application / question on the secondary real estate'
                                ),
                                'key' => FeedbackConfig::TYPE_APART_REQUEST_RESALE,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1,
                                'set' => 1,
                                'validation' => array(
                                    'mode' => 'preset',
                                    'preset' => 'email'
                                )
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Заявка от собственника',
                                    Lang::LANG_KEY_EN => 'Owner request',
                                ),
                                'key' => FeedbackConfig::TYPE_OWNER,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1,
                                'set' => 1,
                                'validation' => array(
                                    'mode' => 'preset',
                                    'preset' => 'email'
                                )
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Заявка на подбор квартиры',
                                    Lang::LANG_KEY_EN => 'Application for selection of apartments'
                                ),
                                'key' => FeedbackConfig::TYPE_FLAT_SELECTION,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1,
                                'set' => 1,
                                'validation' => array(
                                    'mode' => 'preset',
                                    'preset' => 'email'
                                )
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Обратная связь',
                                    Lang::LANG_KEY_EN => 'Feedback'
                                ),
                                'key' => FeedbackConfig::TYPE_FEEDBACK,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1,
                                'set' => 1,
                                'validation' => array(
                                    'mode' => 'preset',
                                    'preset' => 'email'
                                )
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Заказ звонка',
                                    Lang::LANG_KEY_EN => 'Callback'
                                ),
                                'key' => FeedbackConfig::TYPE_CALLBACK,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1,
                                'set' => 1,
                                'validation' => array(
                                    'mode' => 'preset',
                                    'preset' => 'email'
                                )
                            )
                        )
                    ),
                    array(
                        'data' => array(
                            'title' => array(
                                Lang::LANG_KEY_RU => 'Контактная информация',
                                Lang::LANG_KEY_EN => 'Contact information'
                            ),
                            'key' => CatalogConfig::CONFIG_CONTACTS_KEY,
                            'only_items' => 1
                        ),
                        'groups' => array(
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Группы в соцсетях',
                                    Lang::LANG_KEY_EN => 'Social networks groups'
                                ),
                                'key' => ContactsConfig::GROUP_KEY_SOCIAL_NETWORKS
                            )
                        ),
                        'properties' => array(
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Основной телефон',
                                    Lang::LANG_KEY_EN => 'Main phone'
                                ),
                                'key' => ContactsConfig::KEY_MAIN_PHONE,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Телефон для регионов',
                                    Lang::LANG_KEY_EN => 'Phone'
                                ),
                                'key' => ContactsConfig::KEY_REGION_PHONE,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Адрес офиса',
                                    Lang::LANG_KEY_EN => 'Office address'
                                ),
                                'key' => ContactsConfig::KEY_OFFICE_ADDRESS,
                                'data_type' => Properties\Address::TYPE_NAME,
                                'fixed' => 1,
                                'segment' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Режим работы офиса',
                                    Lang::LANG_KEY_EN => 'Режим работы офиса'
                                ),
                                'key' => ContactsConfig::KEY_OFFICE_WORK_MODE,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1,
                                'segment' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Основной адрес электронной почты',
                                    Lang::LANG_KEY_EN => 'Main email'
                                ),
                                'key' => ContactsConfig::KEY_MAIN_EMAIL,
                                'data_type' => Properties\String::TYPE_NAME,
                                'validation' => array(
                                    'mode' => 'preset',
                                    'preset' => 'email'
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Группа Вконтакте',
                                    Lang::LANG_KEY_EN => 'VK group'
                                ),
                                'key' => ContactsConfig::KEY_VK_GROUP,
                                'group_key' => ContactsConfig::GROUP_KEY_SOCIAL_NETWORKS,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Группа Facebook',
                                    Lang::LANG_KEY_EN => 'Facebook group'
                                ),
                                'key' => ContactsConfig::KEY_FACEBOOK_GROUP,
                                'group_key' => ContactsConfig::GROUP_KEY_SOCIAL_NETWORKS,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Группа в одноклассниках',
                                    Lang::LANG_KEY_EN => 'Odnoklassniki group'
                                ),
                                'key' => ContactsConfig::KEY_ODNOKLASSNIKI_GROUP,
                                'group_key' => ContactsConfig::GROUP_KEY_SOCIAL_NETWORKS,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Twitter',
                                    Lang::LANG_KEY_EN => 'Twitter'
                                ),
                                'key' => ContactsConfig::KEY_TWITTER_GROUP,
                                'group_key' => ContactsConfig::GROUP_KEY_SOCIAL_NETWORKS,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Группа LinkedIn',
                                    Lang::LANG_KEY_EN => 'LinkedIn group'
                                ),
                                'key' => ContactsConfig::KEY_LINKEDIN_GROUP,
                                'group_key' => ContactsConfig::GROUP_KEY_SOCIAL_NETWORKS,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Instagram',
                                    Lang::LANG_KEY_EN => 'Instagram'
                                ),
                                'key' => ContactsConfig::KEY_INSTAGRAM_GROUP,
                                'group_key' => ContactsConfig::GROUP_KEY_SOCIAL_NETWORKS,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Группа Google+',
                                    Lang::LANG_KEY_EN => 'Google+ group'
                                ),
                                'key' => ContactsConfig::KEY_GOOGLE_PLUS_GROUP,
                                'group_key' => ContactsConfig::GROUP_KEY_SOCIAL_NETWORKS,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1
                            )
                        )
                    ),
                    array(
                        'data' => array(
                            'title' => array(
                                    Lang::LANG_KEY_RU => 'Настройки SEO',
                                    Lang::LANG_KEY_EN => 'Настройки SEO'
                                ),
                            'key' => CatalogConfig::CONFIG_SEO_KEY,
                            'only_items' => 1
                        ),
                        'properties' => array(
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Код перед </head>',
                                    Lang::LANG_KEY_EN => 'Код перед </head>',
                                ),
                                'key' => 'head_content',
                                'data_type' => Properties\Text::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Код после <body>',
                                    Lang::LANG_KEY_EN => 'Код после <body>',
                                ),
                                'key' => 'body_top_content',
                                'data_type' => Properties\Text::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Код перед </body>',
                                    Lang::LANG_KEY_EN => 'Код перед </body>',
                                ),
                                'key' => 'body_content',
                                'data_type' => Properties\Text::TYPE_NAME,
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Changefreq Автоматических Ссылок В Sitemap.xml',
                                    Lang::LANG_KEY_EN => 'Changefreq Автоматических Ссылок В Sitemap.xml'
                                ),
                                'key' => Settings::KEY_CHANGEFREQ,
                                'data_type' => Properties\Enum::TYPE_NAME,
                                'fixed' => 1,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array('always', 'hourly', 'daily', 'weekly', 'monthly'),
                                        Lang::LANG_KEY_EN => array('always', 'hourly', 'daily', 'weekly', 'monthly')
                                    ),
                                    'keys' => array('always', 'hourly', 'daily', 'weekly', 'monthly')
                                )
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Юзерагент Для Построения Sitemap.xml',
                                    Lang::LANG_KEY_EN => 'Юзерагент Для Построения Sitemap.xml'
                                ),
                                'key' => Settings::KEY_SITEMAP_USERAGENT,
                                'data_type' => Properties\Enum::TYPE_NAME,
                                'fixed' => 1,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array('*', 'Yandex', 'Google'),
                                        Lang::LANG_KEY_EN => array('*', 'Yandex', 'Google')
                                    ),
                                    'keys' => array('*', 'Yandex', 'Google')
                                )
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Хранить sitemap.xml в корне сайта',
                                    Lang::LANG_KEY_EN => 'Хранить sitemap.xml в корне сайта'
                                ),
                                'key' => Settings::KEY_SITEMAP_ROOT,
                                'data_type' => Properties\Flag::TYPE_NAME,
                                'values' => array(
                                    'yes' => array(Lang::LANG_KEY_RU => 'Да', Lang::LANG_KEY_EN => 'Yes'),
                                    'no' => array(Lang::LANG_KEY_RU => 'Нет', Lang::LANG_KEY_EN => 'No')
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Писать lastmod в sitemap.xml',
                                    Lang::LANG_KEY_EN => 'Писать lastmod в sitemap.xml'
                                ),
                                'key' => Settings::KEY_USE_LASTMOD,
                                'data_type' => Properties\Flag::TYPE_NAME,
                                'values' => array(
                                    'yes' => array(Lang::LANG_KEY_RU => 'Да', Lang::LANG_KEY_EN => 'Yes'),
                                    'no' => array(Lang::LANG_KEY_RU => 'Нет', Lang::LANG_KEY_EN => 'No')
                                ),
                                'fixed' => 1
                            ),
                            array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Каталоги, участвующие в генерации sitemap.xml',
                                    Lang::LANG_KEY_EN => 'Каталоги, участвующие в генерации sitemap.xml'
                                ),
                                'key' => Settings::KEY_SITEMAP_CATALOGS,
                                'data_type' => Properties\Enum::TYPE_NAME,
                                'set' => 1,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array(
                                            'Первичная недвижимость',
                                            'Вторичная недвижимость',
                                            'Гид по районам города'
                                        ),
                                        Lang::LANG_KEY_EN => array(
                                            'Real estate',
                                            'Resale',
                                            'City districts guide'
                                        )
                                    ),
                                    'keys' => array(
                                        CatalogConfig::CATALOG_KEY_REAL_ESTATE,
                                        CatalogConfig::CATALOG_KEY_RESALE,
                                        CatalogConfig::CATALOG_KEY_DISTRICT
                                    )
                                ),
                                'fixed' => 1
                            ),
//                            array(
//                                'title' => array(
//                                    Lang::LANG_KEY_RU => 'Посты, участвующие в генерации sitemap.xml',
//                                    Lang::LANG_KEY_EN => 'Посты, участвующие в генерации sitemap.xml'
//                                ),
//                                'key' => Settings::KEY_SITEMAP_POSTS,
//                                'data_type' => Properties\Enum::TYPE_NAME,
//                                'set' => 1,
//                                'values' => array(
//                                    'values' => array(
//                                        Lang::LANG_KEY_RU => array(
//                                            'Гид по районам города'
//                                        ),
//                                        Lang::LANG_KEY_EN => array(
//                                            'Guide areas of the city'
//                                        )
//                                    ),
//                                    'keys' => array(
//                                        \Modules\Posts\Areaguide::POSTS_TYPE
//                                    )
//                                ),
//                                'fixed' => 1
//                            )
                        )
                    )
                )
            )
        );
    }
}
