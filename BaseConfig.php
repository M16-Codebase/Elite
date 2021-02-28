<?php

namespace LPS;

/**
 * config file
 * Идея конфига такова, что напрямую к константам из кода доступа быть не должно,
 * все обращения должны ижти через функции-обертки вокруг конфигурируемых параметров.
 * Так можно легко сменить реклизацию конфига: база данных или файл или еще как.
 *
 * Конфиг является не только конфигом, но и конфигуратором - в его конструкторе нужно писать настройки среды исполнения.
 *
 * Правила именования функций в конфиге:
 * Функции, которые предоставляют конфигурационные данные для сущности "Target" должны называться по принципу:
 * function getTargetInfo(){}
 * и возвращать массив конфигурационных данных
 * тоесть чтоб получить конфиг для сущности Target нужно сделать такой вызов:
 * $targetConfig=Config::getInstance()->getTargetInfo();
 *
 * Функции которые конфигурируют сущность target должны называться также:
 * например, функция, которая конфигурирует "smarty" называется "smarty" и может вызываться так:
 * Config::get()->templater($templaterObject);
 *
 * @author Shulman A.V.
 * @version 1.3 (22/07/2009)
 * @copyright 2007-2008
 */
class BaseConfig {

    const ERRORS_DEBUG = true;

    /**
     * вкл ЧПУ
     */
    const USE_FRIENDLY_URL = true;

    const DEBUG_IPS = 'any'; //use 'any' for access any
    const AUTH_EMAIL_VALIDATION = false;
    const DB_CONNECT_STRING = 'mysql://eliteman:eliteman@localhost/elite3';
    const DEV_DB_CONNECT_STRING = 'mysql://sql-estate:L9o8T2j5@localhost/elite';
    const TEST_DB_CONNECT_STRING = '';
    /**
     * резерв разделяемой памяти в байтах, 0 - не использовать.
     * все настройки в App\SharedMemoryConfig, эта настройка тут, чтобы можно было переопределить
     * добавлено самоуничтожение, при несовпадении размеров
     */
    const SHM_MEMORY_LIMIT = 100000;
    const SENDSAY_ENABLE = TRUE;
    const HASH_SOLT_STRING = 'LPS SECURITY STRING IS BLANK';


    const DISABLE_REGISTRATION = false;
    const DOMAIN_NAME = 'm16-elite.ru';//!!!!@TODO менять на каждом проекте (требуется для определения домена через крон)
    const DEV_DOMAIN_NAME = 'dev.m16.webactives.ru';//!!!@TODO
    const LOCK_FILE_PATH = '/data/my_script.lock';
    const DELETE_ENTITIES_FROM_DB = TRUE;//удалять ли объекты сразу из базы?
    const DEFAULT_ROLE = 'User';
    const DEFAULT_SOCIAL_ROLE = 'User';
    const DEFAULT_SOCIAL_PERSON_TYPE = 'fiz';
    const ENABLE_DYNAMIC_ROUTING = FALSE; // использовать динамический роутинг (бренды)?
    const IMAGEMAGICK_PATH = '/usr/bin/convert';
    const IMAGEMAGICK_PATH_WINDOWS = 'C:/imagemagick/convert.exe';
    /**
     * log systems events
     */
    const BENCHMARK_ENABLE = TRUE;
    /**
     * Режим работы сегментов
     * (Выкл, Язык, Регион)
     */
    const SEGMENT_MODE = self::SEGMENT_MODE_LANGUAGE;
    /**
     * Возможные значения режима работы сегментов
     */
    const SEGMENT_MODE_NONE = 'none';
    const SEGMENT_MODE_LANGUAGE = 'lang';
    const SEGMENT_MODE_REGION = 'region';

    const ENABLE_LOGS = TRUE;//логировать ли события?
    const TEMPLATES_BASE_PATH = 'base';//папка с общими шаблонами
    const TEMPLATES_PATH = 'project';//папка с шаблонами текущего проекта
    const CSV_SEPARATOR_CELL = ',';
    const CSV_SEPARATOR_CELL_ALTERNATE = ';';
    const CSV_SEPARATOR_CELL_ALTERNATE_ELSE = "\t";
	const CSV_SEPARATOR_ROW = "\n";
    const CSV_QUOTES = '"';
    const CSV_SEPARATOR_SET_VALUES = '|';
    const IMPORT_FILE_PATH = 'data/exchange/import/';
    const EXPORT_FILE_PATH = 'data/exchange/export/';


    /**
     * Какой xml-фид используется для xml выгрузки
     */
    const XML_LOAD_FORMAT_YANDEX = 'Yandex';
    const XML_LOAD_FORMAT_AVITO = 'Avito';
    const XML_LOAD_FORMAT = self::XML_LOAD_FORMAT_YANDEX;

    const COOKIE_LIFE_TIME = 2592000; // по умолчанию куки живут месяц
    const COOKIE_PATH = '/';
    // Список доменов - основной + алиасы основного, необходимы для определения релиза в шаблонах и при отправке писем
    private static $release_domains = array('m16-elite.ru', 'm16-elite.webactives.ru');
    /**
     * Жесткая аутентификация пользователя.
     * Система запоминает с какого компа и браузера зашел пользователь,
     * таким образом два пользователя не смогут одновременно работать с одного аккаунта
     */
    const HARD_AUTH = FALSE;
    /**
     * @var bool флаг тестового режима, устанавливается из системы тестирования
     */
    private static $test_mode = FALSE;
    /**
     * @TODO Перенести настройки гугл-календаря в отдельных конфиг App\Config\
     */
    // Аутентификация для гугл-календаря
    const GOOGLE_APPLICATION_NAME = 'LPS';
    const GOOGLE_CLIENT_ID = '772870329835-vki39o3b24ortc65qspc2re8jp945j9m.apps.googleusercontent.com';
    const GOOGLE_SERVICE_EMAIL = '772870329835-vki39o3b24ortc65qspc2re8jp945j9m@developer.gserviceaccount.com';
    const GOOGLE_CALENDAR_ID = 'webactives.ru_cfcgh0bfba0lotfs764qk546sg@group.calendar.google.com';
    const GOOGLE_KEY_FILE = 'data/OAuth2/calendarwebactives.p12';
    const GOOGLE_USER_EMAIL = 'calendar@webactives.ru';

    static $googleapi_default_config = array(
        'client_id' => self::GOOGLE_CLIENT_ID,
        'calendar_id' => self::GOOGLE_CALENDAR_ID,
        'service_email' => self::GOOGLE_SERVICE_EMAIL,
        'app_name' => self::GOOGLE_APPLICATION_NAME,
        'key_file' => self::GOOGLE_KEY_FILE,
        'user_email' => self::GOOGLE_USER_EMAIL
    );
    /**
     * @TODO Подумать об автоматизировании
     * Внимание!!! при изменении класса проверить таблицу actions, там хранятся соответствия module_url => module_class
     * Машрутизация модулей
     */
    protected $modulesRouteMap = array(
        'cli' => array(
            'cron' => 'CliModules\Cron',
            'rake' => 'CliModules\Rake',
            'init' => 'CliModules\Init',
            'work' => 'CliModules\Work'
        ),
        'uri' => array(
            'privacy_policy'=> 'Main\View',
            'ratings'=> 'Ratings\Ratings',
            'main' => 'Main\View',
            'contacts' => 'Main\View',
            'company' => 'Main\View',
            'favorites' => 'Main\View',
            'service' => 'Main\View',
            'top16' => 'Main\View',
            'realtysearch' => 'Main\View',
            'special' => 'Main\View',
            'selection' => 'Main\View',
            'owner' => 'Main\View',
            'favorites_request' => 'Main\View',

            'welcome' => 'Welcome\Guest',
            'check' => 'Check\Fields',
            'profile' => 'Profile\My',
            'users-edit' => 'Profile\EditUsers',

            // Контент
            'news-admin' => 'Posts\AdminNews',
            'news' => 'Posts\News',
            'blog-admin' => 'Posts\AdminBlog',
            'blog' => 'Posts\Blog',
            'article-admin' => 'Posts\AdminArticles',
            'article' => 'Posts\Articles',
			'segment-text' => 'Segment\Text',
//            'rss' => 'Posts\Rss',
//            'photo' => 'Catalog\PhotoGallery',
//            'vacancy' => 'Catalog\Vacancy',
//            'offers' => 'Catalog\SpecialOffers',
//            'dynamic' => 'Catalog\Dynamic',

            'subscribe' => 'Site\Subscribe',
            'uses' => 'Posts\Uses',
            'catalog-superadmin' => 'Catalog\SuperAdmin',
            'catalog-admin' => 'Catalog\Admin',
            'catalog' => 'Catalog\Main',
            'catalog-type' => 'Catalog\Type',
            'catalog-item' => 'Catalog\Item',
            'catalog-view' => 'Catalog\Viewer',

            'kustik' => 'Catalog\Kustik', // тестовый классик для кустика;
            'brands' => 'Catalog\Brands',
            'resale' => 'Catalog\Resale',
			'arenda' => 'Catalog\Arenda',
            'residential' => 'Catalog\Residential',
            'real-estate' => 'Catalog\RealEstate',
            'district' => 'Catalog\District',

            'files' => 'Files\Main',
            'files-edit' => 'Files\Admin',
			'site-config' => 'Site\Config',
            'site-logs' => 'Site\Logs',
            'site' => 'Site\View',
            'site-admin' => 'Site\Admin',
            'site-banner' => 'Site\Banner',
            'site-teaser' => 'Site\Teasers',
            'seo' => 'Seo\SuperAdmin',
            'supportSeoLinks' => 'Seo\SupportSeoLinks',
            'seo-sitemap' => 'Seo\SiteMapAdmin',
            'images'    => 'Images\Admin',
            'tmp-images'    => 'Images\TmpImages',
			'order' => 'Order\Main',
            'order-admin' => 'Order\Admin',
            'payment' => 'Payment\Main',
			'site-search' => 'Site\Search',
            'mails' => 'Mails\View',
            'lists' => 'Lists\Admin',
            'questions-admin' => 'Site\QuestionsAdmin',
            'tests-admin' => 'Site\TestsAdmin',
			'exchange' => 'Exchange\Index',
            'permissions' => 'Permissions\Edit',
            'segment'=>'Segment\Main',
			'registration' => 'Welcome\Guest',
            'login' => 'Welcome\Guest',
            'logout' => 'Welcome\Guest',
			'staff' => 'Lists\StaffList',
			'logs-view' => 'Logs\View',
			'city-post' => 'Posts\AdminCity',
			'infra' => 'Site\Infra',
            'segment-collection' => 'Segment\Collection',
			'feedback' => 'Feedback\Main',
			'hr-feedback' => 'Feedback\Vacancy',
            'eventcal' => 'EventCalendar\EventCalendar',
            'menu-editor' => 'Site\MenuEditor',
            'sphinx-wordforms' => 'Site\SphinxWordForms',
            'db-migrations' => 'Site\DbMigrations',
            'logs-cron' => 'Logs\Cron',
            'cron-shedule' => 'Site\CronShedule',
            'shared-memory' => 'Site\SharedMemory',
            'xml' => 'XmlLoad\Load',
            'img-test' => 'XmlLoad\Img'
        )
    );
    /**
     * Названия модулей, используются в настройках прав доступа
     * @TODO  Нужно дозаполнить.
     * @TODO почему не хотим хранить в БД? и дизайн к этому как-нить потом прикрутить?
     * @var array
     */
    protected static $moduleTitles = array(
        'permissions' => 'Параметры доступа',
        'catalog' => 'Каталог',
        'catalog-admin' => 'Админка каталога',
        'catalog-item' => 'Админка товаров',
        'catalog-type' => 'Типы объектов каталога',
        'blog' => 'Блог',
        'blog-admin' => 'Админка блога',
        'sphinx-wordforms' => 'Параметры поиска Sphinx'
    );
    /**
     * Определяет запущен ли движок из командной строки в противном случае считается, что запущен как сервер
     * @return boolean
     */
    final public static function isCLI() {
        if (php_sapi_name() == 'cli') {
            return TRUE;
        }
        if (!empty($_SERVER['HTTP_HOST']) or !empty($_SERVER['SERVER_NAME']))
            return FALSE;
        if (empty($GLOBALS['argv']))
            return FALSE;
        return TRUE;
    }

    public static function setTest($test_mode = TRUE){
        self::$test_mode = $test_mode;
    }

    public static function isTest(){
        return self::$test_mode;
    }
    public static function isRelease(){
        return preg_match('~/www/('.implode('|', self::$release_domains).')~', self::getRealDocumentRoot());
    }
	/**
	 * Проверяет, запущен ли движок на тестируемом сервере
	 * @return boolean
	 */
	public static function isDev(){
		return FALSE !== strpos(self::getRealDocumentRoot(), 'dev.');
	}
    /**
     * Проверяет, запущен ли движок из локалки
     * @return boolean
     */
    public static function isLocal(){
        return FALSE === strpos(self::getRealDocumentRoot(), '/www/');
    }
    public static function isWin(){
        return substr(PHP_OS, 0, 3) == 'WIN' && DIRECTORY_SEPARATOR == '\\';
    }

    private static $instance;

    /**
     * @return Config
     */
    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new Config;
        }
        return self::$instance;
    }

    /**
     * Return is debug access now
     * @return bool
     */
    public static function debugAccess() {
        if (strtolower(self::DEBUG_IPS) === 'any' or in_array($_SERVER['REMOTE_ADDR'], array_map('trim', explode(';', self::DEBUG_IPS)))) {
            return true;
        }
        return false;
    }

    /**
     * @return Config
     */
    public static function get() {
        return self::getInstance();
    }
    /**
     * первоочередные конфигурационные вещи
     *
     * @return Config
     */
    private function __construct() {
        $this->locale();
        if (!$this->isTest()){
            $this->errors();
        }
        if (!$this->isCLI()) {
            $this->session();
        } else {
            $_SERVER['DOCUMENT_ROOT'] = self::getRealDocumentRoot();
        }
        if (static::BenchmarkAccess()){
            Components\Benchmark::factory()->enable(self::BENCHMARK_ENABLE);
        }
    }
    public static function BenchmarkAccess(){
        if (self::BENCHMARK_ENABLE && isset($_GET['benchmark']) && static::debugAccess()){
            return TRUE;
        }
        return FALSE;
    }

    protected static $cliPrepared = false;
    /**
     * Подготовка среды для работы cli
     */
    public static function cliPrepare(){}

    /**
     * Возвращает параметр конфига с ключем $key для сущности $target
     *
     * @param string $target
     * @param string $key
     * @return mixed
     */
    public static function getParametr($target, $key) {
        $methodName = 'get' . $target . 'Info';
        $config = self::$methodName();
        return $config[$key];
    }

    /**
     * @return null
     */
    protected function session() {
        $session = \App\Builder::getInstance()->getCurrentSession();
        $session->start();
    }

    protected function locale() {
        if (extension_loaded('mbstring')) {
            ini_set('mbstring.language', 'Russian');
            mb_internal_encoding('UTF-8');
        }
        $siteInfo = self::getSiteInfo();
        date_default_timezone_set($siteInfo['timezone']);
        setlocale(LC_ALL, $siteInfo['locale']);
    }

    protected function errors() {
        $params = $this->getErrorLogInfo();
        \exceptionHandler\Controller::setup($this->isCli(), $params['file'], $params['enable']);
    }

    public static function getRealDocumentRoot() {
        return dirname(__FILE__) . (self::isWin() ? '\\' : '/');
    }

    /**
     * Настройка темплейтера
     *
     * @param mixed(Quicky|Smarty) $templater
     */
    public function templater($templater, $templates_dir) {
        /* @var $templater \Quicky */
        $dirs = array(
            'template_dir' => $templates_dir,
            'compile_dir' => self::getParametr('dir', 'compile_templates') . '/' . strtolower(get_class($templater)) . '/',
            'cache_dir' => self::getParametr('dir', 'cache') . '/' . strtolower(get_class($templater)) . '/'
        );
        foreach ($dirs as $name => $dir) {
            if (is_string($dir) and !file_exists($dir)) {
                \LPS\Components\FS::makeDirs(Config::getRealDocumentRoot() . '/' . $dir);
            }
            $templater->$name = $dir;
        }
        $templater->assign('template_dir', trim($templates_dir[0], '/'));
    }
    /**
	 * @return string "Quicky" or "Smarty"
	 */
	public static function getTemplaterEngine(){
//		return 'Smarty';
		return 'Quicky';
	}
    /**
     *
     * ********************************************
     * Ниже идут настройки системы
     * Real config data
     * ********************************************
     *
     */
    public function getErrorLogInfo() {
        $config = array();
        $config['file'] = self::getRealDocumentRoot() . '/' . self::getParametr('dir', 'logs') . '/errors.log';
        $config['enable'] = static::ERRORS_DEBUG;
        return $config;
    }

    /**
     * @return Array
     */
    protected static function getSiteInfo() {
        static $info = null;
        if (is_null($info)) {
            $info = array();
            $info['codepage'] = 'utf-8';
            $info['charset'] = 'utf-8';
            $info['locale'] = 'ru_RU.UTF8';
            /*
              $info['codepage'] = 'cp1251';
              $info['charset'] = 'windows-1251';
              $info['locale'] = 'ru_RU.CP1251';
             */
            $info['timezone'] = 'Europe/Moscow';
            if (!self::isCLI()) {
                $info['absolute_url'] = $_SERVER['SERVER_NAME'];
                $info['url'] = $_SERVER['SERVER_NAME'];
                $domain = $_SERVER['SERVER_NAME'];
                // Fix the domain to accept domains with and without 'www.'.
                if ( strtolower( substr($domain, 0, 4) ) == 'www.' ) $domain = substr($domain, 4);
                // Add the dot prefix to ensure compatibility with subdomains
//                if ( substr($domain, 0, 1) != '.' ) $domain = '.'.$domain;
                $info['domain'] = NULL;//название домена для кук
                //$info['title'] = $_SERVER['SERVER_NAME'];
            }else{
				if (self::isRelease()){
					$info['absolute_url'] = self::DOMAIN_NAME;
					$info['url'] = self::DOMAIN_NAME;
					$info['domain'] = self::DOMAIN_NAME;
					//$info['title'] = self::DOMAIN_NAME;
				}else{
					$info['absolute_url'] = self::DEV_DOMAIN_NAME;
					$info['url'] = self::DEV_DOMAIN_NAME;
					$info['domain'] = self::DEV_DOMAIN_NAME;
					//$info['title'] = self::DEV_DOMAIN_NAME;
				}
            }
            $info['disable_registration'] = self::DISABLE_REGISTRATION;
			$info['site_id'] = 1;//для многодоменных сайтов - id сайта
            $info['title']				= self::isRelease() ? 'LPS' : 'LPS DEV';
            $info['release_domains']    = self::$release_domains;
            $info['module_titles']  = self::$moduleTitles;
        }
        return $info;
    }

	protected static function getEmailInfo(){
		static $info = null;
        if (is_null($info)) {
            $info = array();
            //email от кого приходят письма
			$info['from']				= 'info@webactives.ru';
            //от кого приходят письма
			$info['support_name']		= 'Support Webactives';
            //кому отправлять письма. подставляется, если нет email-ов в настройках сайта или отправка происходит с тестовых версий
			$info['to']					= array('in@webactives.ru' => 'Incoming');
            //это email разработчиков, для проверки отправки писем. Не обязательно его менять на каждом проекте
			$info['developers_email']	= array('in@webactives.ru' => 'Incoming');
            //для отправки с локалки используем Smtp транспорт, поэтому нужен аккаунт.
            $info['smtp_account_connect_host']      = 'smtp.gmail.com';
            $info['smtp_account_connect_port']      = 465;
            $info['smtp_account_connect_security']  = 'ssl';
            $info['smtp_account_login']             = '';
            $info['smtp_account_pass']              = '';
            //для rss
            $info['rss_editor']     = 'info@webactives.ru';
            $info['rss_webmaster']  = 'developer@webactives.ru';
        }
        return $info;
	}

    /**
     * Возвращает настройки путей для проекта
     * @staticvar array $info
     * @return array
     */
    protected static function getDirInfo() {
        static $info = null;
        if (is_null($info)) {
            $info = array();
            $info['logs'] = 'logs';
            $info['cache'] = 'cache';
            $info['compile_templates'] = 'cache/templates_c';
            $info['userdata'] = 'data';
            $info['templates_base'] = 'templates/' . self::TEMPLATES_BASE_PATH . '/';
            $info['templates'] = 'templates/' . self::TEMPLATES_PATH . '/';
            $info['templates_dirs'] = array(
                $info['templates'],
                $info['templates_base']
            );
            foreach ($info as $name => $dir) {
                if ($name == 'templates_dirs'){
                    continue;
                }
                $dir = Config::getRealDocumentRoot() . '/' . $dir;
                if (!file_exists($dir)) {
                    //\LPS\Components\FS::makeDirs($dir);//автолоадер может ещё не инициализирован
                    trigger_error('"'.$dir.'" not found!');
                }
            }
        }
        return $info;
    }

    public static function getAutoload() {
        return array(// маппинг маршрутов на FS настраивать здесь!
            '__NAMESPACE__' => 'src', //папка в которой искать классы, из корня текущего пространства имен
            'Components\\' => 'src\Components',
            'Config' => 'config.php',
            'Router\\' => 'src/Router', //папка в которой искать классы, из корня текущего пространства имен
            '\exceptionHandler\Controller' => 'includes/exceptionHandler/Controller.php',
            '\Quicky' => 'includes/templaters/quicky/Quicky.class.php',
            '\Modules\\'=>'Modules',
            '\Models\\'=>'Models',
            '\MysqlSimple\\' => 'includes/MysqlSimple',
            'MysqlSimple\Exceptions\MySQLQueryException' => 'includes/MysqlSimple/Exceptions/MySQLQueryException.php',
            '\App\\' => 'App',
            '\HTML_SemiParser' => 'includes/dklab_libs/HTML/SemiParser.php',
            '\HTML_FormPersister' => 'includes/dklab_libs/HTML/FormPersister.php',
            '\phpThumb' => 'vendor/weotch/phpthumb/src/PhpThumb.inc.php',
            '\SxGeo' => 'includes/SxGeo/SxGeo.php',
            '\Dompdf\\' => 'includes/dompdf/src',
            '\PHPExcel' => 'vendor/phpoffice/phpexcel/Classes/PHPExcel.php',
            '\PHPExcel_IOFactory' => 'vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php',
            '\PHPExcel_Writer_Excel2007' => 'vendor/phpoffice/phpexcel/Classes/PHPExcel/Writer/Excel2007.php',
            '\Mobile_Detect' => 'vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php'
        );
    }

    /**
     * Возвращает строку подключения в формате "mysql://username:password@host/database_name"
     * @return string
     */
    public static function getDBString() {
        /* template "mysql://username:password@host/database_name" */
        if (self::isTest()){
            return static::TEST_DB_CONNECT_STRING;
        }
		if (self::isDev()){
            return static::DEV_DB_CONNECT_STRING;
        }
        return static::DB_CONNECT_STRING;
    }

    public function getModulesRouteMap($force_uri = FALSE) {
        if (!self::isCLI() || $force_uri){
            return $this->modulesRouteMap['uri'];
        }
        return $this->modulesRouteMap['cli'];
    }
    /**
     * @TODO продумать политику кэширования
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function initResponse(\Symfony\Component\HttpFoundation\Response $response){
        $response->setCharset(self::getParametr('Site', 'charset'));
        $response->headers->set('Content-Type', 'text/html');
    }

    public function addListeners(\Symfony\Component\EventDispatcher\EventDispatcher $dispatcher){
        $dispatcher->addListener('start', array(\App\Stages::get(), 'onStart'));
        $dispatcher->addListener('preActionWork', array(\App\Stages::get(), 'preActionWork'));
		$dispatcher->addListener('afterModuleWork', array(\App\Stages::get(), 'afterModuleWork'));
		$dispatcher->addListener('preTemplaterWork', array(\App\Stages::get(), 'preTemplaterWork'));
        $dispatcher->addListener('finish', array(\App\Stages::get(), 'onFinish'));
        $dispatcher->addListener('afterDefaultAnsInit', array(\App\Stages::get(), 'afterDefaultAnsInit'));
    }


    public static function useFriendlyUrl() {
        return self::USE_FRIENDLY_URL;
    }
}
