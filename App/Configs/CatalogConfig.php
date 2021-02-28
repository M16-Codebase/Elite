<?php
namespace App\Configs;

use Models\CatalogManagement\Type;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\Properties;
/**
 * Настройки каталогов
 */
class CatalogConfig {

    /**
     * для рейтинга
     * такие ключи должны иметь свойства для рейтинга: оценки (все оценки айтема) и рейтинг - среднее арифметическое
     */
    const RATING_TABLE = 'ratings';
    const RATING_PROP = 'rating';
    const MARKS_PROP = 'marks';
    const RATING_DELIMITER = '|';
    const RATING_COOKIE_NAME = 'rating_';

    /**
     * разделитель для обозначения свойств-объектов разных каталогов в шаблоне
     * @TODO ввести одинаковый префикс CATALOG_KEY_*
     */
    const PROP_DATA_TYPE_SEPARATOR = '__';
    /* ** ключи каталогов */
    const CATALOG_KEY = 'catalog';
    /**
     * каталог недвижимости
     */
    const CATALOG_KEY_REAL_ESTATE = 'real-estate';
    /**
     * каталог вторичной недвижимости
     */
    const CATALOG_KEY_RESALE = 'resale';
	const CATALOG_KEY_ARENDA = 'arenda';
    const CATALOG_KEY_RESIDENTIAL = 'residential';
    /**
     * каталог районов
     */
    const CATALOG_KEY_DISTRICT = 'district';
    const CATALOG_KEY_VIDEO = 'video';
//    const ORDERS_KEY = 'orders';
//    const BUILDINGS_KEY = 'buildings';
//    const KUSTIK_KEY = 'kustik';
//    const BRANDS_KEY = 'brands';
    const VIDEO_GALLERY_KEY = 'video_gallery';
    const PHOTO_GALLERY_KEY = 'photo';
//    const SPECIAL_OFFER_KEY = 'offers';
//    const VACANCY_KEY = 'vacancy';
    const CONFIG_KEY = 'config';
    const FEEDBACK_KEY = 'treat';
//    const CATALOG_KEY_TEST = 'test';
    const CATALOG_KEY_STAFF_LIST = 'staff_list';
    const CATALOG_KEY_METRO = 'metro';
    const CATALOG_KEY_INFRASTRUCTURE = 'infrastructure';
    const REVIEWS_AND_QUESTIONS_KEY = 'reviews_question';
    /* ** ключи некоторых типов */
    const CONFIG_GLOBAL_KEY = 'global';
    const CONFIG_SYSTEM_KEY = 'system';
    const CONFIG_EXCHANGE = 'exchange';
    const CONFIG_NOTIFICATION_KEY = 'notification';
    const CONFIG_CONTACTS_KEY = 'contacts';
    const CONFIG_SEO_KEY = 'seo';
    const CONFIG_FILTER_SEO = 'filter_seo';
    const CONFIG_FILTER_SEO_TYPE = 'filter_seo_type';
    /**
     * категории для районов должны иметь ключ == коду города
     */
    const CATEGORY_KEY_DISTRICT_SPB = 'saint-petersburg';
//    const CONFIG_ORDERS_KEY = 'order';
//    const CATALOG_KEY_ORDERS_FIZ = 'orders_fiz';
//    const CATALOG_KEY_ORDERS_ORG = 'orders_org';
//    const CONFIG_PAYMENT = 'settings_payment';
    /**
     * Ключ каталога для поиска в строке шапки админки
     */
    const MAIN_SEARCH_CATALOG_KEY = self::CATALOG_KEY;

    const ENABLE_REVIEWS = TRUE;
    const ENABLE_QUESTIONS = TRUE;
    const PRODUCT_CATALOG_FOR_REVIEWS_AND_QUESTIONS = self::CATALOG_KEY;
    /**
     * Максимальное количество избранных предложений
     */
    const FAVORITES_LIST_SIZE = 30;

    /**
     * @var array Ключи составных свойств, которые могут содержать составные свойства
     */
    private static $props_can_contains_views = array(SphinxConfig::CATALOG_SEARCH_PROP_KEY);
    /**
     * @return string[]
     */
    public static function getPropKeysCanContainViews(){
        return self::$props_can_contains_views;
    }

    /**
     * Префикс ключа айтема для урла по умолчанию
     */
    const DEFAULT_ITEM_KEY_PREFIX = 'i_';

    const DELETE_ON_PROP_DISABLE_SEARCH = true;
    /**
     * @TODO подумать об автоматизации
     * @var array
     */
    private static $catalog_keys = array(
        self::CATALOG_KEY_REAL_ESTATE => array(
            'item' => '\Models\CatalogManagement\Item',
            'variant' => '\Models\CatalogManagement\Variant'
        ),
        self::CATALOG_KEY_RESALE => array(
            'item' => '\Models\CatalogManagement\Item',
            'variant' => '\Models\CatalogManagement\Variant'
        ),
		self::CATALOG_KEY_ARENDA => array(
            'item' => '\Models\CatalogManagement\Item',
            'variant' => '\Models\CatalogManagement\Variant'
        ),
        self::CATALOG_KEY_RESIDENTIAL => array(
            'item' => '\Models\CatalogManagement\Item',
            'variant' => '\Models\CatalogManagement\Variant'
        ),
        self::CATALOG_KEY_VIDEO => array(
            'item' => '\Models\CatalogManagement\Positions\Video'
        ),
        self::PHOTO_GALLERY_KEY => array(
            'item' => '\Models\CatalogManagement\Item'
        ),
//        self::SPECIAL_OFFER_KEY => array(
//            'item' => '\Models\CatalogManagement\Item',
//            'variant' => '\Models\CatalogManagement\Variant'
//        ),
//        self::VACANCY_KEY => array(
//            'item' => '\Models\CatalogManagement\Item'
//        ),
        self::CONFIG_KEY => array(
            'item' => '\Models\CatalogManagement\Positions\Settings'
        ),
//        self::BRANDS_KEY => array(
//            'item' => '\Models\CatalogManagement\Positions\Brand'
//        ),
        self::REVIEWS_AND_QUESTIONS_KEY => array(
            'item' => '\Models\CatalogManagement\Positions\Review'
        ),
        self::FEEDBACK_KEY => array(
            'item' => '\Models\CatalogManagement\Positions\Feedback'
        ),
//        self::CATALOG_KEY_TEST => array(
//            'item' => '\Models\CatalogManagement\Item'
//        ),
        self::CATALOG_KEY_STAFF_LIST => array(
            'item' => '\Models\CatalogManagement\Item'
        ),
        self::CATALOG_KEY_INFRASTRUCTURE => array(
            'item' => '\Models\CatalogManagement\Item'
        ),
        self::CATALOG_KEY_DISTRICT => array(
            'item' => '\Models\CatalogManagement\Item'
        ),
        self::CATALOG_KEY_METRO => array(
            'item' => '\Models\CatalogManagement\Item',
            'variant' => '\Models\CatalogManagement\Variant'
        ),

        self::CONFIG_FILTER_SEO => array(
            'item' => '\Models\CatalogManagement\Filter\FilterSeoItem',
            'variant' => '\Models\CatalogManagement\Variant'
        ),
        self::CONFIG_FILTER_SEO_TYPE => array(
            'item' => '\Models\CatalogManagement\Filter\FilterSeoType',
            'variant' => '\Models\CatalogManagement\Variant'
        ),
    );

    private static $catalog_default_status = array(
        self::CATALOG_KEY_REAL_ESTATE => Item::S_HIDE,
        self::CATALOG_KEY_RESALE => Item::S_HIDE,
		self::CATALOG_KEY_ARENDA => Item::S_HIDE,
        self::CATALOG_KEY_RESIDENTIAL => Item::S_HIDE,
        self::CATALOG_KEY_INFRASTRUCTURE => Item::S_HIDE,
        self::CATALOG_KEY_DISTRICT => Item::S_HIDE,
        self::CATALOG_KEY_STAFF_LIST => Item::S_PUBLIC,
        self::FEEDBACK_KEY => Item::S_HIDE,
        self::CATALOG_KEY_VIDEO => Item::S_HIDE,
        self::PHOTO_GALLERY_KEY => Item::S_HIDE,
        self::CONFIG_KEY => Item::S_PUBLIC,
        self::REVIEWS_AND_QUESTIONS_KEY => Item::S_PUBLIC,
        self::CATALOG_KEY_METRO => Item::S_HIDE
    );

    /** ключи специальных свойств
     * @TODO разнести по разным конфигам? */
    /**
     * обязательно должно быть на каждом проекте, где используется каталог @see Item::ENTITY_TITLE_KEY
     */
    const KEY_ITEM_TITLE = 'title';
    const KEY_ITEM_GALLERY = 'gallery';
    const KEY_ITEM_REVIEW_COUNT = 'review_count';
    const KEY_ITEM_NEW_REVIEW_COUNT = 'new_review_count';
    const KEY_ITEM_QUESTION_COUNT = 'question_count';
    const KEY_ITEM_NEW_QUESTION_COUNT = 'new_question_count';
    const KEY_ITEM_CODE = 'code';
    const KEY_ITEM_BRAND = 'brand';
    /**
     * обязательно должно быть на каждом проекте, где используется каталог @see Variant::ENTITY_TITLE_KEY
     */
    const KEY_VARIANT_TITLE = 'variant_title';
    const KEY_VARIANT_TITLE_SEARCH = 'search_string'; /** @TODO вписать правильный ключ. Свойство для поиска без сфинкса */
    const KEY_VARIANT_GALLERY = 'variant_gallery';
    const KEY_VARIANT_PRICE = 'price';
    const KEY_VARIANT_COUNT = 'count';//количество на складе
    const KEY_VARIANT_COUNT_RESERVED = 'count_reserved';//зарезервированное количество
    const KEY_VARIANT_AVAILABLE = 'available';
    const KEY_VARIANT_CODE = 'variant_code';

    /** Свойства объектов инфраструктуры */

    const KEY_INFRA_TITLE = 'title';
    const KEY_INFRA_TYPE = 'type';
    const KEY_INFRA_ADDRESS = 'address';
    const KEY_INFRA_PHOTO = 'photo';

    /** ******************************** */

    const KEY_VIDEO_URL = 'url';
    const KEY_VIDEO_DATA = 'data';
	/**
	 * методы сортировки значений свойств
	 */
	const SORT_VALUES_DEF = 'default';
	const SORT_VALUES_ALF = 'alphabet';
	const SORT_VALUES_FIN = 'financial';

    /**
     * Видимость свойства
     */
    const V_ADMIN = 1; //Админка
    const V_PUBLIC_FULL = 2;//Страница товара на сайте
    const V_PUBLIC_ITEM = 4;//Карточка товара на сайте (в списке товаров)
    const V_PUBLIC_VARIANT = 8;//Попап вариантов
    //используется в поиске, поэтому так просто удалить отсюда нельзя
    const V_EXPORT = 16;//для экспорта
    /**
     * Видимость свойства в фильтре
     */
    const FV_ADMIN = 1; //Админ
    const FV_PUBLIC = 2; //Паблик для товаров
	const FV_PUBLIC_VARIANT = 4;//Паблик для вариантов
    const FV_COMPLEX_LIST = 8;


    const FILT_MAIN_PRT_ELEMS_CNT_COMMON = 5;
    const FILT_MAIN_PRT_ELEMS_CNT_RESALE = 5;
    const FILT_MAIN_PRT_ELEMS_CNT_RESIDENTIAL = 5;
    const FILT_MAIN_PRT_ELEMS_CNT_REEAL_ESTATE = 5;


    const AVAILABLE_YES = 'yes';
    const AVAILABLE_SOON = 'soon';
    const AVAILABLE_NO = 'no';
    public static $entity_available = array(
        self::AVAILABLE_NO => 'Нет в наличии',
        self::AVAILABLE_SOON => 'Под заказ',
        self::AVAILABLE_YES => 'Есть в наличии'
    );
    /**
     * Информация о типах хранения, поиска и отображения свойств
     * @var Array
     */
    private static $properties_rus = array(
        'data_type' => array(//возможные типы свойств, которыми может обладать товар
            \Models\CatalogManagement\Properties\Int::TYPE_NAME => 'Целое число',
            \Models\CatalogManagement\Properties\Float::TYPE_NAME => 'Дробное число',
            \Models\CatalogManagement\Properties\String::TYPE_NAME => 'Строка',
            \Models\CatalogManagement\Properties\Text::TYPE_NAME => 'Текст',
            \Models\CatalogManagement\Properties\Enum::TYPE_NAME => 'Перечисление',
            \Models\CatalogManagement\Properties\Flag::TYPE_NAME => 'Флаг',
            \Models\CatalogManagement\Properties\View::TYPE_NAME => 'Составной',
            \Models\CatalogManagement\Properties\Post::TYPE_NAME => 'Статья',
            \Models\CatalogManagement\Properties\File::TYPE_NAME => 'Файл',
            \Models\CatalogManagement\Properties\Date::TYPE_NAME => 'Дата',
            \Models\CatalogManagement\Properties\Time::TYPE_NAME => 'Время',
            \Models\CatalogManagement\Properties\DateTime::TYPE_NAME => 'Дата и время',
            \Models\CatalogManagement\Properties\DiapasonDate::TYPE_NAME => 'Диапазон дат',
//            \Models\CatalogManagement\Properties\Region::TYPE_NAME => 'Регион',
            \Models\CatalogManagement\Properties\Image::TYPE_NAME => 'Изображение',
            \Models\CatalogManagement\Properties\Gallery::TYPE_NAME => 'Коллекция',
            \Models\CatalogManagement\Properties\Video::TYPE_NAME => 'Ссылка на видео',
            \Models\CatalogManagement\Properties\Item::TYPE_NAME => 'Товар',
            \Models\CatalogManagement\Properties\Variant::TYPE_NAME => 'Вариант товара',
            \Models\CatalogManagement\Properties\Range::TYPE_NAME => 'Диапазон',
            \Models\CatalogManagement\Properties\User::TYPE_NAME => 'Пользователь',
            \Models\CatalogManagement\Properties\Color::TYPE_NAME => 'Цвет',
            \Models\CatalogManagement\Properties\Rule::TYPE_NAME => 'Параметры поиска',
            \Models\CatalogManagement\Properties\Address::TYPE_NAME => 'Адрес',
            \Models\CatalogManagement\Properties\Metro::TYPE_NAME => 'Станция метро',
            \Models\CatalogManagement\Properties\Coords::TYPE_NAME => 'Координаты на карте',
            \Models\CatalogManagement\Properties\DiapasonInt::TYPE_NAME => 'Диапазон целых чисел',
            \Models\CatalogManagement\Properties\DiapasonFloat::TYPE_NAME => 'Диапазон дробных чисел'
        ),
        'search_type' => array(//возможные механизмы поиска
            \Models\CatalogManagement\Properties\Factory::SEARCH_BETWEEN => 'Диапазон',
            \Models\CatalogManagement\Properties\Factory::SEARCH_SELECT => 'Выбор',
            \Models\CatalogManagement\Properties\Factory::SEARCH_AUTOCOMPLETE => 'Автозаполнение',
            \Models\CatalogManagement\Properties\Factory::SEARCH_CHECK => 'Мультиселект',
            \Models\CatalogManagement\Properties\Factory::SEARCH_NONE => 'Нет'
        ),
        'visible' => array(//Варианты отображения свойств (где отображать?)
//            self::V_ADMIN => 'CMS',
            self::V_PUBLIC_FULL => 'Страница айтема на сайте',
            self::V_PUBLIC_ITEM => 'Карточка айтема на сайте',
//            self::V_PUBLIC_VARIANT => 'Попап вариантов',
            self::V_EXPORT => 'Экспорт'
        ),
        'filter_visible' => array(//видимость в фильтре
            self::FV_ADMIN => 'CMS',
            self::FV_PUBLIC => 'Список айтемов на сайте',
//			self::FV_PUBLIC_VARIANT => 'Список вариантов на сайте',
            self::FV_COMPLEX_LIST => 'Список жилых комплексов на сайте',
        ),
		'sort' => array(//сортировка значений
			self::SORT_VALUES_DEF => 'Пользовательская',
			self::SORT_VALUES_ALF => 'По алфавиту',
//			self::SORT_VALUES_FIN => 'Финансовая'
		),
        'string_prop_validation' => array(
            'email' => array(
                'key' => 'email',
                'title' => 'Электронная почта',
                'validatorMethod' => 'checkEmail'
            ),
            'phone' => array(
                'key' => 'phone',
                'title' => 'Телефон',
                'validatorMethod' => 'checkPhone'
            ),
        )
    );
    /**
     * Названия параметров категории айтемов и свойств
     * @var type
     */
    private static $fields = array(
        'type' => array(
            'title' => 'Название',
            'key' => 'Ключ',
            'url' => 'Url',
            'parent_id' => 'Родительская категория',
            'annotation' => 'Аннотация',
            'only_items' => 'Без вариантов',
            'position' => 'Позиция',
            'status' => 'Статус',
            'allow_children' => 'Разрешить создавать дочерние категории',
            'nested_in' => 'Вложен в категорию',
            'enable_view_mode' => 'Разрешить режим просмотра',
            'allow_item_url' => 'Объекты каталога имеют публичный URL',
            'search_by_sphinx' => 'Включить поиск через сфинкс',
            'dynamic_category' => 'Каталог c динамическими категориями',
            'dynamic_for' => 'Каталог для динамических категорий',
            'item_prefix' => 'Префикс айтема',
            'word_cases' => 'Названия сущностей',
            'allow_item_property' => 'Разрешить создавать свойства из айтемов данного каталога',
            'allow_variant_property' => 'Разрешить создавать свойства из вариантов данного каталога',
            'allow_segment_properties' => 'Разрешить создавать сегментированные свойства',
            'number_prefix' => 'Префикс номера обращения'
        ),
        'property' => array(
            'title' => 'Название',
            'description' => 'Пояснение для CMS',
            'public_description' => 'Пояснение для публичной части',
            'key' => 'Ключ',
            'data_type' => 'Тип свойства',
            'major' => 'Подбор похожих',
            'search_type' => 'Возможности фильтрации',
            'visible' => 'Показывать',
            'values' => 'Значения',
            'mask' => 'Шаблон вывода',
            'filter_title' => 'Название в фильтре',
            'necessary' => 'Обязательное',
            'unique' => 'Уникальное',
            'multiple' => 'Расщепляемый',
            'group_id' => 'Группа',
            'position' => 'Позиция',
            'read_only' => 'Не редактируемое',
            'set' => 'Множественное',
            'filter_visible' => 'Где выводить',
            'filter_slide' => 'Разворачивать в фильтре',
            'segment' => 'Сегментированное',
            'context' => 'Контекст',
            'sort' => 'Сортировка значений',
            'external_key' => 'Ключ 1С',
            'default_prop' => 'Свойство по умолчанию',
            'default_value' => 'Значение по умолчанию'
        )
    );
    public static function getFields($entity = NULL){
        if (!empty($entity) && !isset(self::$fields[$entity])){
            throw new \Exception('Не найдены настройки для сущности ' . $entity);
        }
        return self::$fields[$entity];
    }
    /**
     * У некоторых каталогов поведение класса объекта каталога может отличаться.
     * Данный метод возвращает имя класса, соответствующего объекту каталога
     * @param string $catalog_key
     * @param string $entity_type
     * @return string
     * @throws \Exception
     */
    public static function getEntityClass($catalog_key, $entity_type){
        if (!isset(static::$catalog_keys[$catalog_key])){
            return NULL;
        }
        if ($entity_type != 'item' && $entity_type != 'variant'){
            throw new \Exception('Возможны только два вида сущностей item или variant');
        }
        return array_key_exists($entity_type, static::$catalog_keys[$catalog_key]) ? static::$catalog_keys[$catalog_key][$entity_type] : NULL;
    }
    public static function getCatalogKeys(){
        return array_keys(static::$catalog_keys);
    }

    /**
     * @param string $catalog_key
     * @return int|null
     */
    public static function getDefaultItemVisible($catalog_key) {
        return isset(self::$catalog_default_status[$catalog_key])
            ? self::$catalog_default_status[$catalog_key]
            : null;
    }
    /**
     * Получить список типов данных, которые можно установить характеристике
     * @return type
     */
    public static function getPropertiesKeys(){
        $properties_keys = self::$properties_rus;
        if (isset($properties_keys['data_type'][Properties\Item::TYPE_NAME])){
            unset($properties_keys['data_type'][Properties\Item::TYPE_NAME]);
        }
        if (isset($properties_keys['data_type'][Properties\Variant::TYPE_NAME])){
            unset($properties_keys['data_type'][Properties\Variant::TYPE_NAME]);
        }
        if (isset($properties_keys['data_type'][Properties\Gallery::TYPE_NAME])){
            unset($properties_keys['data_type'][Properties\Gallery::TYPE_NAME]);
        }
        $catalogs = Type::getById(Type::DEFAULT_TYPE_ID)->getChildren();
        foreach ($catalogs as $c){
            if ($c['allow_item_property']){
                $properties_keys['data_type'][Properties\Item::TYPE_NAME . self::PROP_DATA_TYPE_SEPARATOR . $c['id']] = !empty($c['word_cases']['i'][1]['i']) ? $c['word_cases']['i'][1]['i'] : 'Айтем';
            }
            if ($c['allow_variant_property']){
                $properties_keys['data_type'][Properties\Variant::TYPE_NAME . self::PROP_DATA_TYPE_SEPARATOR . $c['id']] = !empty($c['word_cases']['v'][1]['i']) ? $c['word_cases']['v'][1]['i'] : 'Вариант';
            }
        }
        return $properties_keys;
    }
    /**
     * Получить название конкретной настройки характеристики
     * @param string $field название настройки
     * @param string $val значение
     * @return string
     * @throws \Exception
     */
    public static function getPropertyFieldsData($field, $val = NULL){
        if (!isset(self::$properties_rus[$field])){
            throw new \Exception('Не найден параметр ' . (!is_array($field) && !is_object($field) ? $field : serialize($field)) . ' у свойств');
        }
        if (!empty($val) && !isset(self::$properties_rus[$field][$val])){
            throw new \Exception('Неверное значение параметра ' . $field . ' => ' . $val);
        }
        return empty($val) ? self::$properties_rus[$field] : self::$properties_rus[$field][$val];
    }
    /**
     * Сущности, которые можно добавлять в заказ
     * @var type
     */
    public static $orderPositionEntities = array(
        'variant' => 'Models\CatalogManagement\Variant'
    );
    /**
     * Ссылка на паблик варианта
     * @param Variant $variant
     * @param int $segment_id
     * @return string
     */
    public static function getVariantUrl(Variant $variant, $segment_id = NULL){
        return $variant->getItem()->getUrl($segment_id) . $variant['key'] . '/';
    }
    /**
     * Сссылка на паблик айтема
     * @param Item $item
     * @param int|null $segment_id
     * @return string
     * @throws \Exception
     */
    public static function getItemUrl(Item $item, $segment_id = NULL){
        /*$catalog = $item->getType()->getCatalog();
        if (!$catalog['allow_item_url']) {
            return null;
        }
        $segment_id = !empty($segment_id) ? $segment_id : $item['segment_id'];
        $parent = $item->getParent();
        if (!empty($parent)){
            // Если у айтема есть родительский айтем, то просто добавляем ключ айтема к урлу родительского айтема
            return $parent->getUrl($segment_id) . $item['key'] . '/';
        } else {
            // Для верхнего айтема кустика и обычных айтемов все одинаково
            $type = $item->getType();
            $catalog = $type->getCatalog();
            return $type->getUrl($segment_id) . $catalog['item_prefix'] . $item['key'] . '/'; // у кустика префикс айтема всегда пустой
        }*/

        $ulrManager = new UrlManager();
        return $ulrManager->createUrl($item, $segment_id);

    }
    /**
     * Ссылка на паблик категории
     * @param Type $type
     * @param int $segment_id
     * @return string
     */
    public static function getTypeUrl(Type $type, $segment_id = NULL){
        $catalog = $type->getCatalog();
        if ($catalog['allow_item_url']) {
            if (is_null($segment_id)){
                $segment = \App\Segment::getInstance()->getDefault(true);
            }else{
                $segment = \App\Segment::getInstance()->getById($segment_id);
            }
            return $segment->getUrlPrefix() . $type['url'];
        } else {
            return null;
        }
    }
}
