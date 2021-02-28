<?php
namespace App\Configs\Init\Catalog;

use Models\CatalogManagement\Properties;
use App\Configs\CatalogConfig;
use App\Configs\RealEstateConfig;
use Models\CatalogManagement\Type;
use Models\Segments\Lang;
/**
 * Конфиг для создания сущностей при первом запуске системы
 *  Настройки валидации строковых пропертей (validation) - параметры:
 *      mode - режим, используется всегда, значения NULL, 'preset', 'sel_opts', 'regex'
 *      Другие параметры зависят от значения mode:
 *          preset - в случае mode = preset, используется для типовых значений, email, phone и тд,
 *      возможные значения можно найти в CatalogConfig
 *          sel_opts - выбор допустимых символов, массив
 *          пример: sel_opts => array("digits" => "1", "cyrillic" => "1", "english" => "1", "symbols" => "\\s-_")
 *          в данном случае допускаются кириллические и латинские буквы, цифры, пробельные символы, тире и подчеркивание
 *      regex - строка, содержит регулярное выражение для проверки значения
 * /** TEMP COMMENT для помощи замены констант из конфига при копирровании
 * const (KEY_[a-z_]*) = '[a-z_]*';
 * RealEstateConfig::$1 => array(\n\t\t\t\t\t\t\t\t'title' => array(\n\t\t\t\t\t\t\t\t\tLang::LANG_KEY_RU => '',\n\t\t\t\t\t\t\t\t\tLang::LANG_KEY_EN => ''),\n\t\t\t\t\t\t\t\t'key' => RealEstateConfig::$1,\n\t\t\t\t\t\t\t),
 * @author olya
 */
class ResidentialInit {
    public static function getInitData(){
        return array(
			CatalogConfig::CATALOG_KEY_RESIDENTIAL => array(
                'data' => array(
                    'title' => array(
						Lang::LANG_KEY_RU => 'Загородная',
						Lang::LANG_KEY_EN => 'Residential'
					),
                    'key' => CatalogConfig::CATALOG_KEY_RESIDENTIAL,
                    'parent_id' => Type::DEFAULT_TYPE_ID,
                    'item_prefix' => '',
                    'allow_children' => 0,
                    'allow_item_url' => 1,
                    'only_items' => 1,
                    'allow_item_property' => 1,
                    'allow_segment_properties' => 1,
                    'fixed' => 1
                ),
                'item_title' => 'Квартира',
                'properties' => array(
                    RealEstateConfig::KEY_APPART_TITLE => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Наименование',
                            Lang::LANG_KEY_EN => 'Title'),
                        'key' => RealEstateConfig::KEY_APPART_TITLE,
                        'data_type' => Properties\View::TYPE_NAME,
                        'values' => '{'.RealEstateConfig::KEY_APPART_ADDRESS.'}',
                        'visible' => CatalogConfig::V_ADMIN,
                        'segment' => 1,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_PRIORITY => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Приоритет',
                            Lang::LANG_KEY_EN => 'Priority'),
                        'key' => RealEstateConfig::KEY_APPART_PRIORITY,
                        'data_type' => Properties\Int::TYPE_NAME,
                        'values' => array('min' => 1, 'max' => 5, 'step' => 1),
                        'visible' => CatalogConfig::V_ADMIN,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_ICON => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Ярлык',
                            Lang::LANG_KEY_EN => 'Icon'),
                        'key' => RealEstateConfig::KEY_APPART_ICON,
                        'data_type' => Properties\Enum::TYPE_NAME,
                        'values' => array(
                            'values' => array(
                                Lang::LANG_KEY_RU => array('Новый объект', 'Эксклюзив'),
                                Lang::LANG_KEY_EN => array('New', 'Exclusive')
                            ),
                            'keys' => array('new', 'exclusive'),
                        ),
                        'visible' => CatalogConfig::V_ADMIN,
                        'set' => 1,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_TOP => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Принадлежность к ТОП-16',
                            Lang::LANG_KEY_EN => 'Top-16'),
                        'key' => RealEstateConfig::KEY_APPART_TOP,
                        'group_key' => 'nastrojki',
                        'data_type' => Properties\Flag::TYPE_NAME,
                        'values' => array(
                            'yes' => array(Lang::LANG_KEY_RU => 'Да', Lang::LANG_KEY_EN => 'Yes'),
                            'no' => array(Lang::LANG_KEY_RU => 'Нет', Lang::LANG_KEY_EN => 'No')
                        ),
                        'visible' => CatalogConfig::V_ADMIN,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_GALLERY => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Фотогалерея',
                            Lang::LANG_KEY_EN => 'Photogallery'),
                        'key' => RealEstateConfig::KEY_APPART_GALLERY,
                        'data_type' => Properties\Gallery::TYPE_NAME,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_DISTRICT => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Район Санкт-Петербурга',
                            Lang::LANG_KEY_EN => 'District of St. Petersburg'
                        ),
                        'key' => RealEstateConfig::KEY_APPART_DISTRICT,
                        'data_type' => Properties\Item::TYPE_NAME,
                        'values' => array(
                            'catalog_id' => CatalogConfig::CATALOG_KEY_DISTRICT,
                            'edit_mode' => Properties\Item::SELECT_MODE_LIST
                        ),
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'major' => 0,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_ADDRESS => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Адрес',
                            Lang::LANG_KEY_EN => 'Address'
                        ),
                        'key' => RealEstateConfig::KEY_APPART_ADDRESS,
                        'data_type' => Properties\Address::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'segment' => 1,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_METRO => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Метро',
                            Lang::LANG_KEY_EN => 'Metro'
                        ),
                        'key' => RealEstateConfig::KEY_APPART_METRO,
                        'data_type' => Properties\Metro::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_CENTER_DISTANCE => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Расстояние до центра',
                            Lang::LANG_KEY_EN => 'Distance to the center'
                        ),
                        'key' => RealEstateConfig::KEY_APPART_CENTER_DISTANCE,
                        'data_type' => Properties\Float::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'mask' => array(
                            Lang::LANG_KEY_RU => '{!} км',
                            Lang::LANG_KEY_EN => '{!} km'
                        ),
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_CENTER_TIME_BYCAR => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Время поездки до центра',
                            Lang::LANG_KEY_EN => 'Travel time to the center'),
                        'key' => RealEstateConfig::KEY_APPART_CENTER_TIME_BYCAR,
                        'data_type' => Properties\Int::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'mask' => array(
                            Lang::LANG_KEY_RU => '{!} мин',
                            Lang::LANG_KEY_EN => '{!} min'
                        ),
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_AIRPORT_DISTANCE => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Расстояние до аэропорта',
                            Lang::LANG_KEY_EN => 'Distance to airport'),
                        'key' => RealEstateConfig::KEY_APPART_AIRPORT_DISTANCE,
                        'data_type' => Properties\Float::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'mask' => array(
                            Lang::LANG_KEY_RU => '{!} км',
                            Lang::LANG_KEY_EN => '{!} km'
                        ),
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_AIRPORT_TIME_BYCAR => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Время поездки до аэропорта',
                            Lang::LANG_KEY_EN => 'Driving time to the airport'),
                        'key' => RealEstateConfig::KEY_APPART_AIRPORT_TIME_BYCAR,
                        'data_type' => Properties\Int::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'mask' => array(
                            Lang::LANG_KEY_RU => '{!} мин',
                            Lang::LANG_KEY_EN => '{!} min'
                        ),
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_KAD_DISTANCE => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Расстояние до КАД',
                            Lang::LANG_KEY_EN => 'The distance to the Ring Road'),
                        'key' => RealEstateConfig::KEY_APPART_KAD_DISTANCE,
                        'data_type' => Properties\Float::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'mask' => array(
                            Lang::LANG_KEY_RU => '{!} км',
                            Lang::LANG_KEY_EN => '{!} km'
                        ),
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_KAD_TIME_BYCAR => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Время поездки до КАД',
                            Lang::LANG_KEY_EN => 'Travel time to the Ring Road'),
                        'key' => RealEstateConfig::KEY_APPART_KAD_TIME_BYCAR,
                        'data_type' => Properties\Int::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'mask' => array(
                            Lang::LANG_KEY_RU => '{!} мин',
                            Lang::LANG_KEY_EN => '{!} min'
                        ),
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_HOUSE_TYPE => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Тип дома',
                            Lang::LANG_KEY_EN => 'Type of house'),
                        'key' => RealEstateConfig::KEY_APPART_HOUSE_TYPE,
                        'data_type' => Properties\Enum::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'values' => array(
                            'values' => array(
                                Lang::LANG_KEY_RU => array(
                                    'Монолит',
                                    'Кирпич-монолит',
                                    'Кирпичный'
                                ),
                                Lang::LANG_KEY_EN => array(
                                    'Монолит',
                                    'Кирпич-монолит',
                                    'Кирпичный'
                                )
                            ),
                            'keys' => array(),
                        ),
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_NUMBER_STOREYS => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Этажность',
                            Lang::LANG_KEY_EN => 'Floors'),
                        'key' => RealEstateConfig::KEY_APPART_NUMBER_STOREYS,
                        'data_type' => Properties\DiapasonInt::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'values' => array(
                            Lang::LANG_KEY_RU => array(
                                'min_max' => '{min}—{max}',
                                'min' => '{min}',
                                'max' => '{max}'
                            ),
                            Lang::LANG_KEY_EN => array(
                                'min_max' => '{min}—{max}',
                                'min' => '{min}',
                                'max' => '{max}'
                            )
                        ),
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_CONSULTANT => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Консультант на объекте',
                            Lang::LANG_KEY_EN => 'Consultant at the facility'),
                        'key' => RealEstateConfig::KEY_APPART_CONSULTANT,
                        'data_type' => Properties\Item::TYPE_NAME,
                        'values' => array(
                            'catalog_id' => CatalogConfig::CATALOG_KEY_STAFF_LIST,
                            'edit_mode' => Properties\Item::SELECT_MODE_LIST
                        ),
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_CONSULTANT_TEXT => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Мнение консультанта',
                            Lang::LANG_KEY_EN => 'Opinion adviser'),
                        'key' => RealEstateConfig::KEY_APPART_CONSULTANT_TEXT,
                        'data_type' => Properties\Text::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'segment' => 1,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_DESCRIPTION => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Описание объекта',
                            Lang::LANG_KEY_EN => 'Property description'),
                        'key' => RealEstateConfig::KEY_APPART_DESCRIPTION,
                        'data_type' => Properties\Post::TYPE_NAME,
                        'segment' => 1,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_STATE => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Статус',
                            Lang::LANG_KEY_EN => 'Status'),
                        'key' => RealEstateConfig::KEY_APPART_STATE,
                        'data_type' => Properties\Enum::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'values' => array(
                            'values' => array(
                                Lang::LANG_KEY_RU => array('В продаже', 'Продано'),
                                Lang::LANG_KEY_EN => array('For sale', 'Sold')
                            ),
                            'keys' => array(RealEstateConfig::KEY_APPART_STATE_FOR_SALE, RealEstateConfig::KEY_APPART_STATE_SOLD),
                        ),
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_SHEMES => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Планировки',
                            Lang::LANG_KEY_EN => 'Layouts'),
                        'key' => RealEstateConfig::KEY_APPART_SHEMES,
                        'data_type' => Properties\Gallery::TYPE_NAME,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_CLOSE_PRICE => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Закрытая цена',
                            Lang::LANG_KEY_EN => 'Closed price'),
                        'key' => RealEstateConfig::KEY_APPART_CLOSE_PRICE,
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
                        'visible' => CatalogConfig::V_ADMIN,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_PRICE => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Открытая цена',
                            Lang::LANG_KEY_EN => 'Open price'),
                        'key' => RealEstateConfig::KEY_APPART_PRICE,
                        'data_type' => Properties\Float::TYPE_NAME,
                        'mask' => array(
                            Lang::LANG_KEY_RU => '{!} млн. руб.',
                            Lang::LANG_KEY_EN => '{!} mln. RUB'
                        ),
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'major' => '20%',
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_FLOORS => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Количество уровней',
                            Lang::LANG_KEY_EN => 'Number of levels'),
                        'key' => RealEstateConfig::KEY_APPART_FLOORS,
                        'data_type' => Properties\Enum::TYPE_NAME,
                        'values' => array(
                            'values' => array(
                                Lang::LANG_KEY_RU => array('Не указано', '2 уровня', '3 уровня', '4 уровня'),
                                Lang::LANG_KEY_EN => array('Not indicated', '2 levels', '3 levels', '4 levels')
                            ),
                            'keys' => array('one', 'two', 'three', 'four'),
                        ),
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_AREA_ALL => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Общая площадь',
                            Lang::LANG_KEY_EN => 'Total area'),
                        'key' => RealEstateConfig::KEY_APPART_AREA_ALL,
                        'data_type' => Properties\Float::TYPE_NAME,
                        'mask' => array(
                            Lang::LANG_KEY_RU => '{!} м²',
                            Lang::LANG_KEY_EN => '{!} m²'
                        ),
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'major' => '20%',
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_AREA_LIVING => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Жилая площадь',
                            Lang::LANG_KEY_EN => 'Living space'),
                        'key' => RealEstateConfig::KEY_APPART_AREA_LIVING,
                        'data_type' => Properties\Float::TYPE_NAME,
                        'mask' => array(
                            Lang::LANG_KEY_RU => '{!} м²',
                            Lang::LANG_KEY_EN => '{!} m²'
                        ),
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_AREA_KITCHEN => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Площадь кухни',
                            Lang::LANG_KEY_EN => 'Kitchen area'),
                        'key' => RealEstateConfig::KEY_APPART_AREA_KITCHEN,
                        'data_type' => Properties\Float::TYPE_NAME,
                        'mask' => array(
                            Lang::LANG_KEY_RU => '{!} м²',
                            Lang::LANG_KEY_EN => '{!} m²'
                        ),
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_BED_NUMBER => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Количество спален',
                            Lang::LANG_KEY_EN => 'Number of bedrooms'),
                        'key' => RealEstateConfig::KEY_APPART_BED_NUMBER,
                        'data_type' => Properties\Int::TYPE_NAME,
                        'major' => 0,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_WC_NUMBER => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Количество санузлов',
                            Lang::LANG_KEY_EN => 'Number of bathrooms'),
                        'key' => RealEstateConfig::KEY_APPART_WC_NUMBER,
                        'data_type' => Properties\Int::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_CEILING_HEIGHT => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Высота потолка',
                            Lang::LANG_KEY_EN => 'Ceiling height'),
                        'key' => RealEstateConfig::KEY_APPART_CEILING_HEIGHT,
                        'data_type' => Properties\Float::TYPE_NAME,
                        'mask' => array(
                            Lang::LANG_KEY_RU => '{!} м',
                            Lang::LANG_KEY_EN => '{!} m'
                        ),
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_OVERHANG => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Балкон, терраса, лоджия',
                            Lang::LANG_KEY_EN => 'Balcony, terrace, loggia'),
                        'key' => RealEstateConfig::KEY_APPART_OVERHANG,
                        'data_type' => Properties\Enum::TYPE_NAME,
                        'values' => array(
                            'values' => array(
                                Lang::LANG_KEY_RU => array('Балкон', 'Терраса', 'Лоджия'),
                                Lang::LANG_KEY_EN => array('Balcony', 'Terrace', 'Loggia')
                            ),
                            'keys' => array(),
                        ),
                        'set' => 1,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_REPAIR => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Ремонт',
                            Lang::LANG_KEY_EN => 'Repair'),
                        'key' => RealEstateConfig::KEY_APPART_REPAIR,
                        'data_type' => Properties\Enum::TYPE_NAME,
                        'values' => array(
                            'values' => array(
                                Lang::LANG_KEY_RU => array('Без отделки', 'Чистовая отделка', 'Дизайнерский ремонт', 'Авторский ремонт'),
                                Lang::LANG_KEY_EN => array('Without finishing', 'Fine finishing', 'Designer repair', 'Authors repair')
                            ),
                            'keys' => array(),
                        ),
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_FEATURES => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Особенности',
                            Lang::LANG_KEY_EN => 'Features'),
                        'key' => RealEstateConfig::KEY_APPART_FEATURES,
                        'data_type' => Properties\Enum::TYPE_NAME,
                        'values' => array(
                            'values' => array(
                                Lang::LANG_KEY_RU => array('Видовая', 'Вид на воду', 'Возможна перепланировка', 'Возможна установка камина', 'Возможно остекление лоджии', 'Выход на крышу'),
                                Lang::LANG_KEY_EN => array('Species', 'Water View', 'possible redevelopment', 'Installation of fire', 'Maybe glazed loggias', 'Access to the roof')
                            ),
                            'keys' => array('species', 'water_view', 'redevelopment', 'installation_fire', 'glazed_loggias', 'access_roof'),
                        ),
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'set' => 1,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_TOUR => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => '3D-Тур',
                            Lang::LANG_KEY_EN => '3D-Tour'),
                        'key' => RealEstateConfig::KEY_APPART_TOUR,
                        'data_type' => Properties\File::TYPE_NAME,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_TOUR_ZIP => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => '3D-Тур zip',
                            Lang::LANG_KEY_EN => '3D-Tour zip'),
                        'key' => RealEstateConfig::KEY_APPART_TOUR_ZIP,
                        'data_type' => Properties\File::TYPE_NAME,
                        'values' => array('formats' => 'zip', 'swfzip' => 1),
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_TOUR_URL => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Ссылка на 3D-Тур',
                            Lang::LANG_KEY_EN => '3D-Tour url'),
                        'key' => RealEstateConfig::KEY_APPART_TOUR_URL,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_VIDEO => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Видео',
                            Lang::LANG_KEY_EN => 'Video'),
                        'key' => RealEstateConfig::KEY_APPART_VIDEO,
                        'data_type' => Properties\Video::TYPE_NAME,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_OBJECT_TITLE => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Наименование жилого комплекса',
                            Lang::LANG_KEY_EN => 'Name of residential complex'),
                        'key' => RealEstateConfig::KEY_APPART_OBJECT_TITLE,
                        'data_type' => Properties\String::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1,
                        'segment' => 1
                    ),
                    RealEstateConfig::KEY_APPART_FLOOR => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Этаж',
                            Lang::LANG_KEY_EN => 'Floor'),
                        'key' => RealEstateConfig::KEY_APPART_FLOOR,
                        'data_type' => Properties\Int::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1
                    ),
                    RealEstateConfig::KEY_APPART_FURNITURE => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Мебель',
                            Lang::LANG_KEY_EN => 'Furniture'),
                        'key' => RealEstateConfig::KEY_APPART_FURNITURE,
                        'data_type' => Properties\Flag::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1,
                        'values' => array(
                            'yes' => array(Lang::LANG_KEY_RU => 'Да', Lang::LANG_KEY_EN => 'Yes'),
                            'no' => array(Lang::LANG_KEY_RU => 'Нет', Lang::LANG_KEY_EN => 'No')
                        )
                    ),
                    RealEstateConfig::KEY_APPART_INFRA => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Объекты инфраструктуры',
                            Lang::LANG_KEY_EN => 'Infrastructure objects'),
                        'key' => RealEstateConfig::KEY_APPART_INFRA,
                        'group_key' => 'mestopolozhenie',
                        'data_type' => Properties\Item::TYPE_NAME,
                        'values' => array(
                            'catalog_id' => CatalogConfig::CATALOG_KEY_INFRASTRUCTURE
                        ),
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'fixed' => 1,
                        'set' => 1
                    ),
                    RealEstateConfig::KEY_APPART_INFRA_TEXT => array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Инфраструктура',
                            Lang::LANG_KEY_EN => 'Infrastructure'),
                        'key' => RealEstateConfig::KEY_APPART_INFRA_TEXT,
                        'group_key' => 'mestopolozhenie',
                        'data_type' => Properties\Text::TYPE_NAME,
                        'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                        'segment' => 1,
                        'fixed' => 1
                    )
                )
            )
        );
    }
}
