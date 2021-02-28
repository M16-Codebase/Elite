<?php
/**
 * Конфиг для постов
 *
 * @author poche_000
 */
namespace App\Configs;
class BrandsConfig {
    const KEY_BRAND_TITLE = 'title';
    const KEY_BRAND_TITLE_ALT = 'title_alt';
    const KEY_BRAND_LOGO = 'logo';
    const KEY_BRAND_SHOW_STATUS = 'show_brand';
    const KEY_BRAND_LOGO_SHOW_STATUS = 'show_logo';
    const KEY_BRAND_MONO = 'mono_brand';
    const KEY_BRAND_URL_TOKEN = 'url_token';
    const KEY_BRAND_POST = 'brand_post';
    const KEY_BRAND_HEALTH_POSTS = 'health_post';
    const KEY_BRAND_EXPERT_POSTS = 'expert_post';
    const KEY_BRAND_ENUM_ID = 'enum_id';

    const MANUFACTER_ENUM_KEY = 'manufacter';
    /**
     * Бросать исключение, если создано несколько перечислений «manufacter»
     */
    const SCREAM_ON_MULTIPLE_ENUM = TRUE;
    /**
     * Удалять элемент перечисления при удалении бренда
     */
    const TRIGGER_DELETE = TRUE;
}
