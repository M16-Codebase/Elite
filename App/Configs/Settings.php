<?php
namespace App\Configs;

/**
 * Настройки каталога настроек
 *
 * @author olya
 */
class Settings {
    /* ****************** глобальные настройки ********** */
    const KEY_BROKEN_SITE = 'broken';
    const KEY_TEST_MODE = 'test_mode';
    const KEY_THEMES_LEVEL_COUNT = 'themes_level_count';
    const KEY_SITE_LOGO = 'site_logo';
    const KEY_SITE_MAILS_LOGO = 'site_mails_logo';
    const KEY_WATERMARK_LOGO = 'watermark_logo';
    const KEY_COMPANY_NAME = 'company_name';
    const KEY_LETTER_SENDER_NAME = 'letter_sender_name';
    const KEY_LETTER_SENDER_EMAIL = 'letter_sender_email';
    const KEY_SENDSAY_SAMPLE = 'sendsay_sample';
    const KEY_PROJECT_NAME = 'project_name';
    const KEY_OBJECT_CONSULTANT_TEXT = 'consultant_text';
    const KEY_APPART_CONSULTANT_TEXT = 'consultant_text_resale';
    const KEY_REAL_ESTATE_CONSULTANT = 'real_estate_consultant';
    const KEY_RESALE_CONSULTANT = 'resale_consultant';
    const KEY_RESIDENTIAL_CONSULTANT = 'residential_consultant';
    const KEY_ABOUT_COMPANY_PDF = 'company_pdf';
    const KEY_APARTMENT_PDF_COVER = 'apartment_pdf_cover';

    /* ****************** обратная связь **************** */
    const KEY_ORDER_SEND = 'order';
    /**
     * @TODO прописывать его же в поле from в письмах?
     * в письмах, куда отправлять ответ
     */
    const KEY_SERVICE = 'service';
    
    /* ****************** SEO *************************** */
    const KEY_CHANGEFREQ = 'changefreq';
    const KEY_SITEMAP_USERAGENT = 'sitemap_useragent';
    const KEY_SITEMAP_ROOT = 'sitemap_root';
    const KEY_USE_LASTMOD = 'sitemap_use_lastmod';
    const KEY_SITEMAP_CATALOGS = 'sitemap_catalogs';
    const KEY_SITEMAP_POSTS = 'sitemap_posts';

    /* ******************* заказы *********************** */
    /**
     * учитывать количество сущности при добавлении позиции в заказ
     */
    const KEY_POSITION_COUNT_CONSIDER = 'position_count_consider';
    /**
     * учитывать стоимость сущности при добавлении позиции в заказ
     */
    const KEY_POSITION_PRICE_CONSIDER = 'position_price_consider';
    /**
     * Списывать ли остатки на сайте при оформлении заказов и пополнят при отмене
     */
    const KEY_POSITION_RESERVE = 'position_reserve';
    /**
     * начисляются ли бонусы
     */
    const KEY_BONUS_ENABLE = 'bonus_enable';
    /**
     * платежная система
     */
    const KEY_PAY_ONLINE_SYSTEM = 'pay_online_system';
    /**
     * отсылать ли ссылку на оплату (или у нас система с возможностью оплаты сразу)
     */
    const KEY_ORDER_SEND_PAYMENT_LINK = 'send_payment_link';
    /**
     * типы пользователей (enum - fiz, org, fiz_org)
     */
    const KEY_PERSON_TYPE = OrderConfig::KEY_ORDER_PERSON_TYPE;
    /**
     * типы оплаты для физ лица
     */
    const KEY_PAY_TYPE_FIZ = 'pay_type_fiz';
    /**
     * типы оплаты для юр лица
     */
    const KEY_PAY_TYPE_ORG = 'pay_type_org';
    /**
     * Включать ли процент комиссии платежной системы в цену заказа
     */
    const KEY_PAY_ONLINE_COMMISION_PLUS = 'pay_online_commision_plus';
    /**
     * доставка
     */
    const KEY_DELIVER_TYPES = OrderConfig::KEY_ORDER_DELIVERY_TYPE;
    /**
     * Учитывать ли наличие товара при добавлении в корзину
     */
    const KEY_AVAILBALE_LOCK = 'position_available_lock';
    /**
     * с какими статусами наличия можно добавлять в корзину
     */
    const KEY_AVAILBALE_CONSIDER = 'position_available_consider';
	/**
	 * процент начисления бонусов
	 */
	const KEY_ORDER_BONUS_RATIO = 'bonus_ratio';
	/**
	 * комментарий при начислении бонусов
	 */
	const KEY_ORDER_BONUS_TEXT_ADD_NEW = 'bonus_text_add_new';
	/**
	 * комментарий при снятии бонусов за понижение статуса заказа с "Выполнен" на *
	 */
	const KEY_ORDER_BONUS_TEXT_CHANGE_STATUS = 'bonus_text_change_status';
    /**
     * комментарий при снятии бонусов за оплату заказа бонусами
     */
    const KEY_ORDER_BONUS_TEXT_SPEND = 'bonus_text_spend';
    /**
     * комментарий при возвращении бонусов за отмену заказа
     */
    const KEY_ORDER_BONUS_TEXT_UNSPEND = 'bonus_text_unspend';
    /**
     * на что можно потратить бонусы
     */
	const KEY_ORDER_BONUS_SPEND = 'bonus_spend';
    /**
     * Процент от суммы заказа, который можно оплатить бонусами
     */
    const KEY_ORDER_BONUS_TO_ORDER = 'bonus_to_order';
    
    const KEY_NDS_PERCENT = 'nds';
}
