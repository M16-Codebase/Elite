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
class RealEstateInit {
    public static function getInitData(){
        return array(
			CatalogConfig::CATALOG_KEY_REAL_ESTATE => array(
                'data' => array(
                    'title' => array(
						Lang::LANG_KEY_RU => 'Первичная недвижимость',
						Lang::LANG_KEY_EN => 'Real estate'
					),
                    'key' => CatalogConfig::CATALOG_KEY_REAL_ESTATE,
                    'parent_id' => Type::DEFAULT_TYPE_ID,
                    'item_prefix' => '',
                    'allow_children' => 1,
                    'allow_item_url' => 1,
                    'nested_in' => 1,
                    'only_items' => 1,
                    'allow_segment_properties' => 1,
                    'allow_item_property' => 1,
                    'fixed' => 1
                ),
                'item_title' => 'Недвижимость',
                'types' => array(
                    array(
                        'data' => array(
                            'title' => array(
								Lang::LANG_KEY_RU => 'Объект', 
								Lang::LANG_KEY_EN => 'Object'
							),
                            'key' => RealEstateConfig::CATEGORY_KEY_COMPLEX,
                            'only_items' => 1,
                            'allow_children' => 0,
                            'fixed' => 1
                        ),
                        'item_title' => 'Объект',
                        'groups' => array(
                            RealEstateConfig::KEY_GROUP_INFORM_BLOCK => array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Информационные блоки',
                                    Lang::LANG_KEY_EN => 'Information blocks'
                                ),
                                'key' => RealEstateConfig::KEY_GROUP_INFORM_BLOCK
                            )
                        ),
                        'properties' => array(
							RealEstateConfig::KEY_OBJECT_TITLE => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Наименование',
									Lang::LANG_KEY_EN => 'Title'),
								'key' => RealEstateConfig::KEY_OBJECT_TITLE,
                                'data_type' => Properties\String::TYPE_NAME,
                                'segment' => 1,
                                'search_type' => Properties\Factory::SEARCH_AUTOCOMPLETE,
                                'filter_visible' => CatalogConfig::FV_ADMIN | CatalogConfig::FV_PUBLIC,
                                'visible' => CatalogConfig::V_ADMIN,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_SNIPPET => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Сниппет',
									Lang::LANG_KEY_EN => 'Snippet'),
								'key' => RealEstateConfig::KEY_OBJECT_SNIPPET,
                                'group_key' => RealEstateConfig::KEY_GROUP_DESCRIPTION,
                                'data_type' => Properties\Text::TYPE_NAME,
                                'segment' => 1,
                                'search_type' => Properties\Factory::SEARCH_AUTOCOMPLETE,
                                'filter_visible' => CatalogConfig::FV_ADMIN | CatalogConfig::FV_PUBLIC,
                                'visible' => CatalogConfig::V_ADMIN,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_TITLE_SEARCH => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Альтернативные наименования для поиска',
									Lang::LANG_KEY_EN => 'Alternative titles for search'),
								'key' => RealEstateConfig::KEY_OBJECT_TITLE_SEARCH,
                                'data_type' => Properties\String::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN,
                                'set' => 1,
                                'fixed' => 1,
                                'segment' => 1
							),
							RealEstateConfig::KEY_OBJECT_PRIORITY => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Приоритет',
									Lang::LANG_KEY_EN => 'Priority'),
								'key' => RealEstateConfig::KEY_OBJECT_PRIORITY,
                                'data_type' => Properties\Int::TYPE_NAME,
                                'values' => array('min' => 1, 'max' => 5, 'step' => 1),
                                'visible' => CatalogConfig::V_ADMIN,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_LOGO => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Логотип',
									Lang::LANG_KEY_EN => 'Logo'),
								'key' => RealEstateConfig::KEY_OBJECT_LOGO,
                                'data_type' => Properties\Image::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_OBJECT_TOP => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Принадлежность к ТОП-16',
									Lang::LANG_KEY_EN => 'Top-16'),
								'key' => RealEstateConfig::KEY_OBJECT_TOP,
                                'data_type' => Properties\Flag::TYPE_NAME,
                                'values' => array(
                                    'yes' => array(Lang::LANG_KEY_RU => 'Да', Lang::LANG_KEY_EN => 'Yes'),
                                    'no' => array(Lang::LANG_KEY_RU => 'Нет', Lang::LANG_KEY_EN => 'No')
                                ),
                                'visible' => CatalogConfig::V_ADMIN,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_ICON => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Ярлык',
									Lang::LANG_KEY_EN => 'Icon'),
								'key' => RealEstateConfig::KEY_OBJECT_ICON,
                                'data_type' => Properties\Enum::TYPE_NAME,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array('Новый объект', 'Эксклюзив', 'Последние квартиры', 'Нет в продаже'),
                                        Lang::LANG_KEY_EN => array('New', 'Exclusive', 'Last appartments', 'Out of stock')
                                    ),
                                    'keys' => array('new', 'exclusive', 'last', 'out'),
                                ),
                                'visible' => CatalogConfig::V_ADMIN,
                                'set' => 1,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_PRICE_METER_FROM => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Цена за метр квадратный',
									Lang::LANG_KEY_EN => 'Price per square meter'),
								'key' => RealEstateConfig::KEY_OBJECT_PRICE_METER_FROM,
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
							RealEstateConfig::KEY_OBJECT_AREA => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Площади квартир',
									Lang::LANG_KEY_EN => 'Appartments areas'),
								'key' => RealEstateConfig::KEY_OBJECT_AREA,
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
							RealEstateConfig::KEY_OBJECT_GALLERY => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Фотогалерея',
									Lang::LANG_KEY_EN => 'Photogallery'),
								'key' => RealEstateConfig::KEY_OBJECT_GALLERY,
                                'data_type' => Properties\Gallery::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN,
                                'fixed' => 1
 							),
							RealEstateConfig::KEY_OBJECT_SHEME_GET => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Изображение для визуального выбора корпуса',
									Lang::LANG_KEY_EN => 'Picture for visual selection body'),
								'key' => RealEstateConfig::KEY_OBJECT_SHEME_GET,
                                'data_type' => Properties\Image::TYPE_NAME,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_SHEME_VIEW => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Схема расположения корпусов',
									Lang::LANG_KEY_EN => 'Arrangement buildings'
                                ),
								'key' => RealEstateConfig::KEY_OBJECT_SHEME_VIEW,
                                'data_type' => Properties\Image::TYPE_NAME,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_DISTRICT => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Район Санкт-Петербурга',
									Lang::LANG_KEY_EN => 'District of St. Petersburg'
                                ),
								'key' => RealEstateConfig::KEY_OBJECT_DISTRICT,
                                'data_type' => Properties\Item::TYPE_NAME,
                                'values' => array(
                                    'catalog_id' => CatalogConfig::CATALOG_KEY_DISTRICT,
                                    'edit_mode' => Properties\Item::SELECT_MODE_LIST
                                ),
                                'search_type' => Properties\Factory::SEARCH_CHECK,
                                'filter_title' => array(
                                    Lang::LANG_KEY_RU => 'Район',
                                    Lang::LANG_KEY_EN => 'District'
                                ),
                                'filter_visible' => CatalogConfig::FV_ADMIN | CatalogConfig::FV_PUBLIC,
                                'fixed' => 1,
                                'major' => 0,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
							),
							RealEstateConfig::KEY_OBJECT_ADDRESS => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Адрес',
									Lang::LANG_KEY_EN => 'Address'
                                ),
								'key' => RealEstateConfig::KEY_OBJECT_ADDRESS,
                                'data_type' => Properties\Address::TYPE_NAME,
                                'search_type' => Properties\Factory::SEARCH_AUTOCOMPLETE,
                                'filter_visible' => CatalogConfig::FV_ADMIN,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'segment' => 1,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_OBJECT_METRO => array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Метро',
                                    Lang::LANG_KEY_EN => 'Metro'
                                ),
                                'key' => RealEstateConfig::KEY_OBJECT_METRO,
                                'data_type' => Properties\Metro::TYPE_NAME,
                                'search_type' => Properties\Factory::SEARCH_CHECK,
                                'filter_visible' => CatalogConfig::FV_ADMIN,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
                            ),
							RealEstateConfig::KEY_OBJECT_CENTER_DISTANCE => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Расстояние до центра',
									Lang::LANG_KEY_EN => 'Distance to the center'
                                ),
								'key' => RealEstateConfig::KEY_OBJECT_CENTER_DISTANCE,
                                'data_type' => Properties\Float::TYPE_NAME,
                                'mask' => array(
                                    Lang::LANG_KEY_RU => '{!} км',
									Lang::LANG_KEY_EN => '{!} km'
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_CENTER_TIME_BYCAR => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Время поездки до центра',
									Lang::LANG_KEY_EN => 'Travel time to the center'),
								'key' => RealEstateConfig::KEY_OBJECT_CENTER_TIME_BYCAR,
                                'data_type' => Properties\Int::TYPE_NAME,
                                'mask' => array(
                                    Lang::LANG_KEY_RU => '{!} мин',
									Lang::LANG_KEY_EN => '{!} min'
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_AIRPORT_DISTANCE => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Расстояние до аэропорта',
									Lang::LANG_KEY_EN => 'Distance to airport'),
								'key' => RealEstateConfig::KEY_OBJECT_AIRPORT_DISTANCE,
                                'data_type' => Properties\Float::TYPE_NAME,
                                'mask' => array(
                                    Lang::LANG_KEY_RU => '{!} км',
									Lang::LANG_KEY_EN => '{!} km'
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_AIRPORT_TIME_BYCAR => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Время поездки до аэропорта',
									Lang::LANG_KEY_EN => 'Driving time to the airport'),
								'key' => RealEstateConfig::KEY_OBJECT_AIRPORT_TIME_BYCAR,
                                'data_type' => Properties\Int::TYPE_NAME,
                                'mask' => array(
                                    Lang::LANG_KEY_RU => '{!} мин',
									Lang::LANG_KEY_EN => '{!} min'
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_KAD_DISTANCE => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Расстояние до КАД',
									Lang::LANG_KEY_EN => 'The distance to the Ring Road'),
								'key' => RealEstateConfig::KEY_OBJECT_KAD_DISTANCE,
                                'data_type' => Properties\Float::TYPE_NAME,
                                'mask' => array(
                                    Lang::LANG_KEY_RU => '{!} км',
									Lang::LANG_KEY_EN => '{!} km'
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_KAD_TIME_BYCAR => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Время поездки до КАД',
									Lang::LANG_KEY_EN => 'Travel time to the Ring Road'),
								'key' => RealEstateConfig::KEY_OBJECT_KAD_TIME_BYCAR,
                                'data_type' => Properties\Int::TYPE_NAME,
                                'mask' => array(
                                    Lang::LANG_KEY_RU => '{!} мин',
									Lang::LANG_KEY_EN => '{!} min'
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_HOUSE_TYPE => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Тип дома',
									Lang::LANG_KEY_EN => 'Type of house'),
								'key' => RealEstateConfig::KEY_OBJECT_HOUSE_TYPE,
                                'data_type' => Properties\Enum::TYPE_NAME,
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
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_NUMBER_STOREYS => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Этажность',
									Lang::LANG_KEY_EN => 'Floors'),
								'key' => RealEstateConfig::KEY_OBJECT_NUMBER_STOREYS,
                                'data_type' => Properties\DiapasonInt::TYPE_NAME,
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
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
                                
							),
							RealEstateConfig::KEY_OBJECT_CEILING_HEIGHT => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Высота потолка',
									Lang::LANG_KEY_EN => 'Ceiling height'),
								'key' => RealEstateConfig::KEY_OBJECT_CEILING_HEIGHT,
                                'data_type' => Properties\DiapasonFloat::TYPE_NAME,
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
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_OBJECT_COMPLETE_YEAR => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Год завершения строительства',
									Lang::LANG_KEY_EN => 'Completion year'),
								'key' => RealEstateConfig::KEY_OBJECT_COMPLETE_YEAR,
                                'data_type' => Properties\Int::TYPE_NAME,
                                'mask' => '9999',
                                'major' => 0,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_COMPLETE => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Срок завершения строительства',
									Lang::LANG_KEY_EN => 'Completion of construction'),
								'key' => RealEstateConfig::KEY_OBJECT_COMPLETE,
                                'data_type' => Properties\Enum::TYPE_NAME,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array(
                                            'I квартал',
                                            'II квартал',
                                            'III квартал',
                                            'IV квартал'
                                        ),
                                        Lang::LANG_KEY_EN => array(
                                            'I quarter',
                                            'II quarter',
                                            'III quarter',
                                            'IV quarter'
                                        )
                                    ),
                                    'keys' => array('first', 'second', 'third', 'fourth'),
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_CONCEPT => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Концепция',
									Lang::LANG_KEY_EN => 'concept'),
								'key' => RealEstateConfig::KEY_OBJECT_CONCEPT,
                                'data_type' => Properties\Post::TYPE_NAME,
                                'group_key' => RealEstateConfig::KEY_GROUP_INFORM_BLOCK,
                                'visible' => CatalogConfig::V_ADMIN,
                                'segment' => 1,
                                'set' => 1,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_MATERIALS => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Материалы, фасад, остекление',
									Lang::LANG_KEY_EN => 'Materials facade glazing'),
								'key' => RealEstateConfig::KEY_OBJECT_MATERIALS,
                                'data_type' => Properties\Post::TYPE_NAME,
                                'group_key' => RealEstateConfig::KEY_GROUP_INFORM_BLOCK,
                                'visible' => CatalogConfig::V_ADMIN,
                                'segment' => 1,
                                'set' => 1,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_ENGINEER_SOLUTION => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Инженерные решения',
									Lang::LANG_KEY_EN => 'Engineering solutions'),
								'key' => RealEstateConfig::KEY_OBJECT_ENGINEER_SOLUTION,
                                'data_type' => Properties\Post::TYPE_NAME,
                                'group_key' => RealEstateConfig::KEY_GROUP_INFORM_BLOCK,
                                'visible' => CatalogConfig::V_ADMIN,
                                'segment' => 1,
                                'set' => 1,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_PARKING => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Паркинг',
									Lang::LANG_KEY_EN => 'Parking'),
								'key' => RealEstateConfig::KEY_OBJECT_PARKING,
                                'data_type' => Properties\Post::TYPE_NAME,
                                'group_key' => RealEstateConfig::KEY_GROUP_INFORM_BLOCK,
                                'visible' => CatalogConfig::V_ADMIN,
                                'segment' => 1,
                                'set' => 1,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_PUBLIC_SPACE => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Места общего пользования',
									Lang::LANG_KEY_EN => 'Common Areas'),
								'key' => RealEstateConfig::KEY_OBJECT_PUBLIC_SPACE,
                                'data_type' => Properties\Post::TYPE_NAME,
                                'group_key' => RealEstateConfig::KEY_GROUP_INFORM_BLOCK,
                                'visible' => CatalogConfig::V_ADMIN,
                                'segment' => 1,
                                'set' => 1,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_PROGRESS => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Ход строительства',
									Lang::LANG_KEY_EN => 'Construction progress'),
								'key' => RealEstateConfig::KEY_OBJECT_PROGRESS,
                                'data_type' => Properties\Post::TYPE_NAME,
                                'group_key' => RealEstateConfig::KEY_GROUP_INFORM_BLOCK,
                                'visible' => CatalogConfig::V_ADMIN,
                                'segment' => 1,
                                'set' => 1,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_CONSULTANT => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Консультант на объекте',
									Lang::LANG_KEY_EN => 'Consultant at the facility'),
								'key' => RealEstateConfig::KEY_OBJECT_CONSULTANT,
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
							RealEstateConfig::KEY_OBJECT_CONSULTANT_TEXT => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Мнение консультанта',
									Lang::LANG_KEY_EN => 'Opinion adviser'),
								'key' => RealEstateConfig::KEY_OBJECT_CONSULTANT_TEXT,
                                'data_type' => Properties\Text::TYPE_NAME,
                                'segment' => 1,
                                'visible' => CatalogConfig::V_ADMIN,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_MALAFEEV_TEXT => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Мнение Вячеслава Малафеева',
									Lang::LANG_KEY_EN => 'Opinion Vyacheslav Malafeev'),
								'key' => RealEstateConfig::KEY_OBJECT_MALAFEEV_TEXT,
                                'data_type' => Properties\Text::TYPE_NAME,
                                'segment' => 1,
                                'visible' => CatalogConfig::V_ADMIN,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_DESCRIPTION => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Описание объекта',
									Lang::LANG_KEY_EN => 'Property description'),
								'key' => RealEstateConfig::KEY_OBJECT_DESCRIPTION,
                                'data_type' => Properties\Post::TYPE_NAME,
                                'segment' => 1,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_ADVANTAGES => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Преимущества объекта',
									Lang::LANG_KEY_EN => 'The benefits of object'),
								'key' => RealEstateConfig::KEY_OBJECT_ADVANTAGES,
                                'data_type' => Properties\String::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN,
                                'segment' => 1,
                                'set' => 1,
                                'fixed' => 1
							),
							RealEstateConfig::KEY_OBJECT_PAYMENT_TYPES => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Способы оплаты',
									Lang::LANG_KEY_EN => 'Payment Methods'),
								'key' => RealEstateConfig::KEY_OBJECT_PAYMENT_TYPES,
                                'data_type' => Properties\Enum::TYPE_NAME,
								'set' => 1,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array('Ипотека', 'Беспроцентная рассрочка', 'Рассрочка', '100% оплата'),
                                        Lang::LANG_KEY_EN => array('Ипотека', 'Беспроцентная рассрочка', 'Рассрочка', '100% оплата')
                                    ),
                                    'keys' => array(),
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'set' => 1,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_APPART_INFRA => array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Инфраструктура',
                                    Lang::LANG_KEY_EN => 'Infrastructure'),
                                'key' => RealEstateConfig::KEY_APPART_INFRA,
                                'data_type' => Properties\Item::TYPE_NAME,
                                'values' => array(
                                    'catalog_id' => CatalogConfig::CATALOG_KEY_INFRASTRUCTURE
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1,
                                'set' => 1
                            ),
                            RealEstateConfig::KEY_OBJECT_APART_IN_COMPLEX => array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Квартир в комплексе',
                                    Lang::LANG_KEY_EN => 'Apartments in complex'),
                                'key' => RealEstateConfig::KEY_OBJECT_APART_IN_COMPLEX,
                                'data_type' => Properties\Int::TYPE_NAME,
								'group_key' => 'tehnicheskie-harakteristiki',
                                'visible' => 0,
                                'fixed' => 1
                            ),
                            RealEstateConfig::KEY_OBJECT_APART_IN_SALE => array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Квартир в продаже',
                                    Lang::LANG_KEY_EN => 'Apartments in sale'),
                                'key' => RealEstateConfig::KEY_OBJECT_APART_IN_SALE,
                                'data_type' => Properties\Int::TYPE_NAME,
								'group_key' => 'tehnicheskie-harakteristiki',
                                'visible' => 0,
                                'fixed' => 1
                            )
                        )
                    ),
                    array(
                        'data' => array(
                            'title' => array(
								Lang::LANG_KEY_RU => 'Корпус', 
								Lang::LANG_KEY_EN => 'Housing'
							),
                            'key' => RealEstateConfig::CATEGORY_KEY_HOUSING,
                            'only_items' => 1,
                            'allow_children' => 0,
                            'nested_in' => RealEstateConfig::CATEGORY_KEY_COMPLEX,
                            'fixed' => 1
                        ),
                        'item_title' => 'Корпус',
                        'properties' => array(
                            RealEstateConfig::KEY_HOUSING_TITLE => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Наименование',
									Lang::LANG_KEY_EN => 'Title'),
								'key' => RealEstateConfig::KEY_HOUSING_TITLE,
                                'data_type' => Properties\String::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'segment' => 1,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_HOUSING_STATE => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Статус',
									Lang::LANG_KEY_EN => 'Status'),
								'key' => RealEstateConfig::KEY_HOUSING_STATE,
                                'data_type' => Properties\Enum::TYPE_NAME,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array('В продаже', 'Скоро в продаже', 'Недоступен'),
                                        Lang::LANG_KEY_EN => array('В продаже', 'Скоро в продаже', 'Недоступен')
                                    ),
                                    'keys' => array('sale', 'soon', 'not'),
                                ),
                                'visible' => CatalogConfig::V_ADMIN,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_HOUSING_SCHEME_GET => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Изображение для визуального выбора корпуса',
									Lang::LANG_KEY_EN => 'Picture for visual selection body'),
								'key' => RealEstateConfig::KEY_HOUSING_SCHEME_GET,
                                'data_type' => Properties\Image::TYPE_NAME,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_HOUSING_SCHEME_COORDS => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Координаты на изображении жилого комплекса',
									Lang::LANG_KEY_EN => 'The coordinates of the image of the residential complex'),
								'key' => RealEstateConfig::KEY_HOUSING_SCHEME_COORDS,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1,
                                'set' => 1
							),
                        )
                    ),
                    array(
                        'data' => array(
                            'title' => array(
								Lang::LANG_KEY_RU => 'Этаж', 
								Lang::LANG_KEY_EN => 'Floor'
							),
                            'key' => RealEstateConfig::CATEGORY_KEY_FLOOR,
                            'only_items' => 1,
                            'allow_children' => 0,
                            'nested_in' => RealEstateConfig::CATEGORY_KEY_HOUSING,
                            'fixed' => 1
                        ),
                        'item_title' => 'Этаж',
                        'properties' => array(
                            RealEstateConfig::KEY_FLOOR_NUMBER => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Номер этажа',
									Lang::LANG_KEY_EN => 'Floor number'),
								'key' => RealEstateConfig::KEY_FLOOR_NUMBER,
                                'data_type' => Properties\Int::TYPE_NAME,
                                'search_type' => Properties\Factory::SEARCH_BETWEEN,
                                'filter_visible' => CatalogConfig::FV_ADMIN,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_FLOOR_TITLE => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Номер',
									Lang::LANG_KEY_EN => 'Number'),
								'key' => RealEstateConfig::KEY_FLOOR_TITLE,
                                'data_type' => Properties\View::TYPE_NAME,
                                'values' => '{'.RealEstateConfig::KEY_FLOOR_NUMBER.'}',
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_FLOOR_SHEME_GET => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'План',
									Lang::LANG_KEY_EN => 'Plan'),
								'key' => RealEstateConfig::KEY_FLOOR_SHEME_GET,
                                'data_type' => Properties\Image::TYPE_NAME,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_FLOOR_SHEME_COORDS => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Координаты на изображении корпуса',
									Lang::LANG_KEY_EN => 'The coordinates in the image body'),
								'key' => RealEstateConfig::KEY_FLOOR_SHEME_COORDS,
                                'data_type' => Properties\String::TYPE_NAME,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_FLOOR_APPART_NUMBER => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Количество квартир на этаже',
									Lang::LANG_KEY_EN => 'Number of apartments on the floor'),
								'key' => RealEstateConfig::KEY_FLOOR_APPART_NUMBER,
                                'data_type' => Properties\Int::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_FLOOR_APPART_NUMBER_SALE => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Количество квартир в продаже',
									Lang::LANG_KEY_EN => 'Number of flats for sale'),
								'key' => RealEstateConfig::KEY_FLOOR_APPART_NUMBER_SALE,
                                'data_type' => Properties\Int::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
                        )
                    ),
                    array(
                        'data' => array(
                            'title' => array(
								Lang::LANG_KEY_RU => 'Квартира', 
								Lang::LANG_KEY_EN => 'Appartment'
							),
                            'key' => RealEstateConfig::CATEGORY_KEY_FLAT,
                            'only_items' => 1,
                            'allow_children' => 0,
                            'nested_in' => RealEstateConfig::CATEGORY_KEY_FLOOR,
                            'fixed' => 1
                        ),
                        'item_title' => 'Квартира',
                        'properties' => array(
                            RealEstateConfig::KEY_APPART_STATE => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Статус',
									Lang::LANG_KEY_EN => 'Status'),
								'key' => RealEstateConfig::KEY_APPART_STATE,
                                'data_type' => Properties\Enum::TYPE_NAME,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array('В продаже', 'Продано'),
                                        Lang::LANG_KEY_EN => array('For sale', 'Sold')
                                    ),
                                    'keys' => array(RealEstateConfig::KEY_APPART_STATE_FOR_SALE, RealEstateConfig::KEY_APPART_STATE_SOLD),
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_APPART_COORDS => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Координаты на плане этажа',
									Lang::LANG_KEY_EN => 'The coordinates in the floor plan'),
								'key' => RealEstateConfig::KEY_APPART_COORDS,
                                'data_type' => Properties\String::TYPE_NAME,
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
                                'search_type' => Properties\Factory::SEARCH_BETWEEN,
                                'filter_title' => array(
                                    Lang::LANG_KEY_RU => 'Цена',
                                    Lang::LANG_KEY_EN => 'Price'
                                ),
                                'filter_visible' => CatalogConfig::FV_ADMIN | CatalogConfig::FV_PUBLIC,
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
                                'visible' => CatalogConfig::V_ADMIN,
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
                                'search_type' => Properties\Factory::SEARCH_BETWEEN,
                                'filter_title' => array(
                                    Lang::LANG_KEY_RU => 'Площадь',
                                    Lang::LANG_KEY_EN => 'Area'
                                ),
                                'filter_visible' => CatalogConfig::FV_ADMIN | CatalogConfig::FV_PUBLIC,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
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
                                'search_type' => Properties\Factory::SEARCH_CHECK,
                                'filter_visible' => CatalogConfig::FV_ADMIN | CatalogConfig::FV_PUBLIC,
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
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'mask' => array(
                                    Lang::LANG_KEY_RU => '{!} м',
									Lang::LANG_KEY_EN => '{!} m'
                                ),
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_APPART_OVERHANG => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Балкон, терраса, лоджия',
									Lang::LANG_KEY_EN => 'Balcony, terrace, loggia'),
								'key' => RealEstateConfig::KEY_APPART_OVERHANG,
                                'data_type' => Properties\Enum::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array('Балкон', 'Терраса', 'Лоджия'),
                                        Lang::LANG_KEY_EN => array('Balcony', 'Terrace', 'Loggia')
                                    ),
                                    'keys' => array(),
                                ),
                                'set' => 1,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_APPART_FINISHING => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Отделка',
									Lang::LANG_KEY_EN => 'Finishes'),
								'key' => RealEstateConfig::KEY_APPART_FINISHING,
                                'data_type' => Properties\Enum::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array('Без отделки', 'Предчистовая отделка', 'Чистовая отделка'),
                                        Lang::LANG_KEY_EN => array('Without finishing', 'Predchistovaya finish', 'Fine finishing')
                                    ),
                                    'keys' => array(),
                                ),
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_APPART_SPECIAL_OFFER => array(
								'title' => array(
									Lang::LANG_KEY_RU => 'Акция',
									Lang::LANG_KEY_EN => 'Special offer'),
								'key' => RealEstateConfig::KEY_APPART_SPECIAL_OFFER,
                                'data_type' => Properties\Enum::TYPE_NAME,
                                'values' => array(
                                    'values' => array(
                                        Lang::LANG_KEY_RU => array('Скидка', 'Подарок'),
                                        Lang::LANG_KEY_EN => array('Discount', 'Gift')
                                    ),
                                    'keys' => array(RealEstateConfig::KEY_APART_SPECIAL_OFFER_DISCOUNT, RealEstateConfig::KEY_APART_SPECIAL_OFFER_GIFT),
                                ),
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => 1
							),
                            RealEstateConfig::KEY_APPART_SPECIAL_OFFER_COMMENT => array(
                                'title' => array(
                                    Lang::LANG_KEY_RU => 'Комментарий к акции',
                                    Lang::LANG_KEY_EN => 'Special offer comment'
                                ),
                                'key' => RealEstateConfig::KEY_APPART_SPECIAL_OFFER_COMMENT,
                                'data_type' => Properties\String::TYPE_NAME,
                                'visible' => CatalogConfig::V_ADMIN | CatalogConfig::V_PUBLIC_FULL | CatalogConfig::V_PUBLIC_ITEM,
                                'fixed' => Properties\Property::FIXED_FIX
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
                        )
                    )
                )
            )
        );
    }
}
