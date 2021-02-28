<?php
/**
 * Description of Stages
 * События в порядке очередности
 *
 * @author olga
 */
namespace App;

use App\Configs\SphinxConfig;
use Models\CatalogManagement\Type AS TypeEntity;
use Models\CatalogManagement\CatalogHelpers;
use App\Configs\Settings;
use App\Configs\OrderConfig;
use App\Configs\CatalogConfig;
/**
 *
 */
class Stages{
    static protected $instance = null;

    public static function get(){
        if (empty(self::$instance)){
            self::$instance = new Stages();
        }
        return self::$instance;
    }
    public function onStart(\Symfony\Component\EventDispatcher\GenericEvent $event){
        if (!\LPS\Config::isCli() || empty($GLOBALS['argv'][3])){
            self::loadHelpers();
        }
    }

    /**
     * Для вызова из тестов хелперы перенесены в отдельный метод
     */
    public static function loadHelpers(){
        \LPS\Components\Benchmark::factory()->log('Start Stages::loadHelpers');
//        CatalogHelpers\Item\SphinxSearchPropUpdate::factory();
//        CatalogHelpers\Variant\SphinxSearchPropUpdate::factory();
//
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE){
            CatalogHelpers\Group\SegmentFields::factory();
            CatalogHelpers\Property\SegmentFields::factory();
            CatalogHelpers\Property\SegmentEnumTitles::factory();
            CatalogHelpers\Item\SegmentMask::factory();
            CatalogHelpers\Variant\SegmentMask::factory();
            CatalogHelpers\Variant\SegmentEnumTitles::factory();
            CatalogHelpers\Item\SegmentEnumTitles::factory();
        }
        // Логи
        if (\LPS\Config::ENABLE_LOGS){
            CatalogHelpers\Type\Logs::factory();
            CatalogHelpers\Property\Logs::factory();
            CatalogHelpers\Item\ValuesLogger::factory();
            CatalogHelpers\Variant\ValuesLogger::factory();
            CatalogHelpers\Settings\ValuesLogger::factory();
            CatalogHelpers\Video\ValuesLogger::factory();
            \Models\ContentManagement\PostHelpers\Logs::factory();
			\Models\ImageManagement\Helpers\Image\Logs::factory();
			\Models\ImageManagement\Helpers\CollectionImage\Logs::factory();
			\Models\ImageManagement\Helpers\Collection\Logs::factory();
            \Models\FilesManagement\Helpers\Logs::factory();
        }
        CatalogHelpers\Type\AdditionalFields::factory();
        CatalogHelpers\Type\DynamicCategory::factory();
        CatalogHelpers\Type\PageRedirectHelper::factory();
        CatalogHelpers\Type\BannerUrls::factory();

        CatalogHelpers\Item\FlatsCount::factory();

//        CatalogHelpers\Type\PriceList::factory();

 //        CatalogHelpers\Item\SpecialProperties::factory();
        CatalogHelpers\Item\VariantProperties::factory();
        CatalogHelpers\Item\PageRedirectHelper::factory();
        CatalogHelpers\Item\InfrastructureHelper::factory();
//
//        CatalogHelpers\Variant\AvailabilityUpdate::factory();
        CatalogHelpers\Variant\PageRedirectHelper::factory();
//        CatalogHelpers\Variant\OrderData::factory();
//        CatalogHelpers\Variant\VariantSubscribeHelper::factory();
//        CatalogHelpers\Variant\Bonus::factory();

        CatalogHelpers\Property\Image::factory();

        /**
         * Следующие три хелпера используются только в связке
         * Отвечают за проброс свойств поиска в кустике
         */
        // Флаг конечности категории кустика
        CatalogHelpers\Type\NestedInFinalFlag::factory();
        // Распространение свойств и их значений
        CatalogHelpers\Property\NestedInSearchProps::factory();
        // Перенос значений при создании и редактировании айтемов
        CatalogHelpers\Item\NestedInEditSearchValue::factory();
        /** ***** Конец проброса значений поиска в кустах ***** */

//        CatalogHelpers\Order\Cost::factory();
//		if (OrderConfig::getParameter(Settings::KEY_BONUS_ENABLE)){
//			CatalogHelpers\OrderItem\Bonus::factory();
//			CatalogHelpers\Order\Bonus::factory();
//		}
//        CatalogHelpers\Order\Mails::factory();
//        CatalogHelpers\Order\Number::factory();
//        CatalogHelpers\OrderItem\ShiftProperties::factory();
//        CatalogHelpers\OrderItem\Price::factory();
//        CatalogHelpers\OrderItem\Checker::factory();
//        if (OrderConfig::getParameter(Settings::KEY_POSITION_RESERVE)){
//            CatalogHelpers\Order\Reserve::factory();
//            CatalogHelpers\OrderItem\Reserve::factory();
//        }
//
        \Models\ContentManagement\PostHelpers\Images::factory();
        \Models\ContentManagement\PostHelpers\PostDateTime::factory();
//        \Models\ContentManagement\PostHelpers\NearestPosts::factory();

        CatalogHelpers\Video\YoutubeVideoData::factory();
        // для перелинковки постов
        if (\App\Configs\SeoConfig::SEO_LINKS_ENABLE){
            \Models\ContentManagement\PostHelpers\SeoLinksPostHelper::factory();
        }
        \Models\ImageManagement\Helpers\CollectionImage\Info::factory();
        \Models\ImageManagement\Helpers\Image\Info::factory();

        \Models\ContentManagement\PostHelpers\PostTags::factory();
        // Дополнительные свойства пользователей
        Auth\Users\Helpers\CustomFields::factory();

//        CatalogHelpers\Brands\ManufacterEnum::factory();
        CatalogHelpers\Brands\BrandsUrlToken::factory();
//        CatalogHelpers\Property\BrandsHelper::factory();

        CatalogHelpers\Review\DateAndStatus::factory();

        /** Обновление привязки метатегов при смене урла сущности */
        CatalogHelpers\Type\MetaTagsBinding::factory();
        CatalogHelpers\Item\MetaTagsBinding::factory();
        CatalogHelpers\Variant\MetaTagsBinding::factory();
        \Models\ContentManagement\PostHelpers\MetaTagsBinding::factory();

        /** Номер обращения */
        CatalogHelpers\Feedback\TreatNumber::factory();
        /**
         * Включаем проперти поиска через сфинкс
         * Важно! Использует CatalogHelpers\Type\AdditionalFields, поэтому должен идти после него
         */
        if (SphinxConfig::ENABLE_SPHINX){
            CatalogHelpers\Type\SphinxSearchProp::factory();
        }

        CatalogHelpers\Item\Favorites::factory();
        CatalogHelpers\Variant\Favorites::factory();

        CatalogHelpers\Item\OverlayImage::factory();

        CatalogHelpers\Settings\PhoneHelper::factory();

        \LPS\Components\Benchmark::factory()->log('Finish Stages::loadHelpers');
    }
    /**
     * Вызывается до работы метода модуля, до проверок действия на доступ, но после проверки на существование
     */
    public function preActionWork(\Symfony\Component\EventDispatcher\GenericEvent $event){
        \Models\Action::getInstance()->registrate($event->getSubject(), $event->getSubject()->getModuleUrl(), $event['action']);
    }
    /**
     * Вызывается только если используется стандартный ответ
     * @staticvar string $pageTitle
     * @staticvar string $pageDescription
     * @staticvar string $pageKeyWords
     * @staticvar string $pageUID
     */
    public function afterDefaultAnsInit(\Symfony\Component\EventDispatcher\GenericEvent $event){
        \LPS\Components\Benchmark::factory()->log('Start Stages::afterDefaultAnsInit');
		/* @var $ans \LPS\Container\WebContentContainer */
        $ans = $event['ans'];
        //сео настройки
        $ans->add('seoPagePersister', \Models\Seo\PagePersister::getInstance()->setContentContainer($ans));
        static $pageTitle='';       //заголовок выводимый в поле title HTML
        static $pageDescription=''; // атрибут страницы, необходимый для сео
        static $pageKeyWords='';    // атрибут страницы, необходимый для сео
        static $pageUID='';         // Уникальный идентификатор страницы
        $pageTitle = Builder::getInstance()->getConfig()->getParametr('site', 'title');
        $pageUID   = $_SERVER['REQUEST_URI'];

        $requestAsArrayWithParams = explode('?', $pageUID);
        //dump($_SERVER);

        $canonical_uri = '';

        if (isset($requestAsArrayWithParams[1])) {
            $canonical_uri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $requestAsArrayWithParams[0];
        }

        $fullUri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $pageUID;

        // переменная для вывода заголовка в метатеги, переопределяется в шаблоне
        $h1 = NULL;
        $ans->addRef('h1', $h1);

        if(null !== \LPS\Config::USE_FRIENDLY_URL) {

            $ans->add('allowFilterFriendlyUrl', (int)\LPS\Config::USE_FRIENDLY_URL);
        }
        $ans->add('page_url', $fullUri);
        $ans->add('no_index_follow', false);
		$ans->add('root_url', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);
        $ans->addRef('pageTitle', $pageTitle);
        $ans->addRef('canonical_uri', $canonical_uri);
        $ans->addRef('pageDescription', $pageDescription);
        $ans->addRef('pageKeyWords', $pageKeyWords);
        $ans->addRef('pageUID',  $pageUID);
        $ans->add('roles', \Models\Roles::getInstance()->get());
		$ans->add('site_url', preg_replace('~^(http:\/\/)?(www\.)?~', '', \LPS\Config::getParametr('site', 'url')));
		$ans->add('allow_variant_status', CatalogMethods::getVariantStatus());
		$this->order($ans);
		//Код пользователя
        $s = Builder::getInstance()->getCurrentSession();
		$ans->add('user_code', $s->get('user_code'))
            ->add('user_code_entered', $s->get('user_code_entered'));
        //блоки данных
		$ans->add('infoBlocks', Blocks::getInstance())->add('infoBlockMethods', Blocks::getInstance()->getAllowTypes());
        // Баннеры
        $uri_path = \App\Builder::getInstance()->getRouter()->getPath();
        $ans->add('page_banners', \Models\Banner::search(array('url' => $uri_path, 'active' => 1, 'date_filter' => 1, 'segment_id' => \App\Segment::getInstance()->getDefault(true)['id'])));
        \LPS\Components\Benchmark::factory()->log('Finish Stages::afterDefaultAnsInit');
        $account = \App\Builder::getInstance()->getAccount();
        $ans->add('favorites_count', $account->getFavoriteCount(CatalogConfig::CATALOG_KEY_REAL_ESTATE) + $account->getFavoriteCount(CatalogConfig::CATALOG_KEY_RESALE));
    }
	/**
	 * Вытаскиваем заказ в корзине
	 * @param type $ans
	 */
	private function order($ans){
		$ans->add('user_order', \Models\CatalogManagement\Positions\Order::getById(
                Builder::getInstance()->getRequest()->cookies->get('order_id')
            )
        );
	}
    /**
     * Действия после работы модуля.
     */
    public function afterModuleWork(\Symfony\Component\EventDispatcher\GenericEvent $event){
        \LPS\Components\Benchmark::factory()->log('Start Stages::afterModuleWork');
        $module = $event->getSubject();
        $ans = $event['ans'];
		if ($module instanceof \LPS\AdminModule){
            $ans->add('admin_page', 1);
        } else {
            $ans->add('page_posts', \App\Segment::getInstance()->getPagePosts(\App\Builder::getInstance()->getRequest()->server->get('REQUEST_URI')));
        }
        \LPS\Components\Benchmark::factory()->log('Finish Stages::afterModuleWork');
    }
    /**
     * Действия перед работой шаблона. Для вывода в шаблон одних и тех же переменных. Будет срабатывать только если используется шаблон.
     */
    public function preTemplaterWork(\Symfony\Component\EventDispatcher\GenericEvent $event){
        \LPS\Components\Benchmark::factory()->log('Start Stages::preTemplaterWork');
        /* @var $ans \LPS\Container\WebContentContainer */
		$ans = $event['ans'];
        //тут можно переопределить путь к шаблонам
        //$ans->setDefaultPath();
		$ans->add('constants', array(
            'errors_debug' => \LPS\Config::ERRORS_DEBUG,
            'enable_sphinx' => SphinxConfig::ENABLE_SPHINX,
            'default_type_id' => TypeEntity::DEFAULT_TYPE_ID,
            'segment_mode' => \LPS\Config::SEGMENT_MODE,
            'segment_default_key' => \App\Segment::getInstance()->getDefaultKey()
        ));
        $mobile_detect = new \Mobile_Detect();
        $device_type = 'desktop';
        if ($mobile_detect->isMobile()) {
            $device_type = $mobile_detect->isTablet() ? 'tablet' : 'phone';
        }
        $ans->add('device_type', $device_type);
        //для создания хэшей
        $ans->add('hash_salt_string', \LPS\Config::HASH_SOLT_STRING);
        $default_public_segment = Segment::getInstance()->getDefault(true);
		// Сегменты
        $ans->add('request_segment', $default_public_segment);
        $ans->add('segments', Segment::getInstance()->getAll());
        //конфиг сайта
        $ans->add('site_config', Builder::getInstance()->getSiteConfig($default_public_segment['id']));
        $ans->add('seo_config', Builder::getInstance()->getSiteConfig($default_public_segment['id'])->get(NULL, CatalogConfig::CONFIG_SEO_KEY));
        $ans->add('contacts', Builder::getInstance()->getSiteConfig($default_public_segment['id'])->get(NULL, CatalogConfig::CONFIG_CONTACTS_KEY));
        //модули сео-счетчиков
        $ans->add('google_analytics', \Models\Seo\GoogleAnalytics::getInstance());
        $ans->add('yandex_metrika', \Models\Seo\YandexMetrika::getInstance());
		//блоки данных
		$ans->add('infoBlocks', Blocks::getInstance())->add('infoBlockMethods', Blocks::getInstance()->getAllowTypes());
        $ans->add('is_release', \LPS\Config::isRelease());
        $ans->add('url_prefix', \App\Segment::getInstance()->getUrlPrefix($default_public_segment['id']));
        $ans->add('lang', \Models\Lang::getInstance());
        \LPS\Components\Benchmark::factory()->log('Finish Stages::preTemplaterWork');
    }
    public function onFinish(\Symfony\Component\EventDispatcher\GenericEvent $event){

    }
}
