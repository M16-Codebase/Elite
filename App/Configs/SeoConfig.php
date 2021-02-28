<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 07.10.14
 * Time: 14:29
 */

namespace App\Configs;


use Models\SiteConfigManager;

class SeoConfig {
    /**
     * Используется ли перелинковка
     */
    const SEO_LINKS_ENABLE = FALSE;
    /**
     * Максимальная длина ключа категории, айтема и варианта
     * ВАЖНО! При смене длины необходимо изменить длину поля key в БД
     * ENTITY_KEY_MAX_LENGTH + 2
     */
    const ENTITY_KEY_MAX_LENGTH = 30;

    const CUSTOM_URLS_DEFAULT_PRIORITY = '0.46';
    const CATALOG_DEFAULT_PRIORITY = '0.64';
    const POST_DEFAULT_PRIORITY = '0.64';
    const STATIC_PAGE_DEFAULT_PRIORITY = '0.64';

    /**
     * Параметр сайт конфига, разрешающий использовать sitemap.xml в корне сайта
     */
    const USE_ROOT_SITEMAP = 'sitemap_root';

    const DEFAULT_CHANGEFREQ = 'weekly';
    const DEFAULT_USERAGENT = '*';
    const DEFAULT_USE_LASTMOD = TRUE;
    /**
     * Переменные для метатегов, которые есть на всех страницах - заголовок, переменные из сайт-конфига и прочее
     * требуется корректировать в зависимости от проекта
     * @var array
     */
    private static $meta_tags_variables = array(
        '{$h1}' => 'Заголовок H1',
        '{$site_config.company_name}' => 'Наименование компании'
    );

    public static function getMetaTagVariables(){
        return self::$meta_tags_variables;
    }

    private static $params = array(
        'priority_list' => array('0.28', '0.46', '0.64', '0.80', '1.00'),
        'allow_changefreq' => array('always', 'hourly', 'daily', 'weekly', 'monthly'),
        'google_analytics_targets' => array(                // цели гугл-аналитики
            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             * Встраивание событий в шаблон делается так:
             * {$google_analytics.<target_key>.<attr>}
             * Содержит следующие атрибуты:
             * enabled - включена ли цель
             * category, action, label - аттрибуты для вызова функции ga('send', 'event', <category>, <action>, <label>);
             * Значения аттрибутов планируется запихивать в data-attr html-тегов
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            'feedback_send' => array(
                'title' => 'Отправка формы обратной связи',
                'code' => "ga('send', 'event', 'Form.Feedback', 'Send', '');",
                'category' => 'Form.Feedback',
                'action' => 'Send',
                'label' => ''
            ),
            'feedback_click' => array(
                'title' => 'Нажатие на кнопку «Отправить» формы обратной связи',
                'code' => "ga('send', 'event', 'Form.Feedback', 'Click', '');",
                'category' => 'Form.Feedback',
                'action' => 'Click',
                'label' => ''
            ),
            'callback_send' => array(
                'title' => 'Отправка формы заказа обратного звонка',
                'code' => "ga('send', 'event', 'Form.Callback', 'Send', '');",
                'category' => 'Form.Callback',
                'action' => 'Send',
                'label' => ''
            ),
            'callback_click' => array(
                'title' => 'Нажатие на кнопку «Отправить» формы заказа обратного звонка',
                'code' => "ga('send', 'event', 'Form.Callback', 'Click', '');",
                'category' => 'Form.Callback',
                'action' => 'Click',
                'label' => ''
            ),
            'service_button_click' => array(
                'title' => 'Нажатие на кнопку «Сервис»',
                'code' => "ga('send', 'event', 'Button', 'Click', 'Service');",
                'category' => 'Button',
                'action' => 'Click',
                'label' => 'Service'
            ),
            'mailto_click' => array(
                'title' => 'Нажатие на mailto',
                'code' => "ga('send', 'event', 'Link', 'Click', 'email');",
                'category' => 'Link',
                'action' => 'Click',
                'label' => 'email'
            )
        ),
        'yandex_metrika_targets' => array(                // цели гугл-аналитики
            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             * Встраивание событий в шаблон делается так:
             * {$google_analytics.<target_key>.<attr>}
             * Содержит следующие атрибуты:
             * enabled - включена ли цель
             * category, action, label - аттрибуты для вызова функции ga('send', 'event', <category>, <action>, <label>);
             * Значения аттрибутов планируется запихивать в data-attr html-тегов
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            'feedback_send' => array(
                'title' => 'Отправка формы обратной связи',
                'code' => "yaCounter{id}.reachGoal('Form.Feedback.Send');",
                'category' => 'Form.Feedback',
                'action' => 'Send',
                'label' => ''
            ),
            'feedback_click' => array(
                'title' => 'Нажатие на кнопку «Отправить» формы обратной связи',
                'code' => "yaCounter{id}.reachGoal('Form.Feedback.Click');",
                'category' => 'Form.Feedback',
                'action' => 'Click',
                'label' => ''
            ),
            'callback_send' => array(
                'title' => 'Отправка формы заказа обратного звонка',
                'code' => "yaCounter{id}.reachGoal('Form.Callback.Send');",
                'category' => 'Form.Callback',
                'action' => 'Send',
                'label' => ''
            ),
            'callback_click' => array(
                'title' => 'Нажатие на кнопку «Отправить» формы заказа обратного звонка',
                'code' => "yaCounter{id}.reachGoal('Form.Callback.Click');",
                'category' => 'Form.Callback',
                'action' => 'Click',
                'label' => ''
            ),
            'service_button_click' => array(
                'title' => 'Нажатие на кнопку «Сервис»',
                'code' => "yaCounter{id}.reachGoal('Button.Click.Service');",
                'category' => 'Button',
                'action' => 'Click',
                'label' => 'Service'
            ),
            'mailto_click' => array(
                'title' => 'Нажатие на mailto',
                'code' => "yaCounter{id}.reachGoal('Link.Click.email');",
                'category' => 'Link',
                'action' => 'Click',
                'label' => 'email'
            )
        )
    );

    /**
     * Получение параметров конфига. Для параметров из конфига в БД возвращает значения из констант, в случае если в БД
     * значение не установлено
     * @param $param_name
     * @return mixed
     */
    public static function getParam($param_name){
        if (!isset(self::$params[$param_name])){
            if ($param_name == Settings::KEY_CHANGEFREQ){
                $changefreq = SiteConfigManager::getInstance()->get(Settings::KEY_CHANGEFREQ, CatalogConfig::CONFIG_SEO_KEY);
                self::$params[Settings::KEY_CHANGEFREQ] = !empty($changefreq) ? $changefreq : self::DEFAULT_CHANGEFREQ;
            } elseif ($param_name == Settings::KEY_SITEMAP_USERAGENT) {
                $useragent = SiteConfigManager::getInstance()->get(Settings::KEY_SITEMAP_USERAGENT, CatalogConfig::CONFIG_SEO_KEY);
                self::$params[Settings::KEY_SITEMAP_USERAGENT] = !empty($useragent) ? $useragent : self::DEFAULT_USERAGENT;
            } elseif ($param_name == Settings::KEY_USE_LASTMOD){
                $use_lastmod = SiteConfigManager::getInstance()->checkFlag(Settings::KEY_USE_LASTMOD, CatalogConfig::CONFIG_SEO_KEY);
                self::$params[Settings::KEY_USE_LASTMOD] = is_null($use_lastmod) ? self::DEFAULT_USE_LASTMOD : $use_lastmod;
            }
        }
        return isset(self::$params[$param_name]) ? self::$params[$param_name] : NULL;
    }

    /**
     * Здесь хелперы, необходимые для генератора sitemap.xml
     * Отвечают за автоматический сбор урлов сущностей сайта (посты, объекты каталога)
     */
    public static function loadSiteMapHelpers(){
        \Models\Seo\Helpers\SiteMap\CatalogItems::getInstance();
//        \Models\Seo\Helpers\SiteMap\Posts::getInstance();
        \Models\Seo\Helpers\SiteMap\StdPages::getInstance();
    }
} 