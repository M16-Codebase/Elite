<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 22.10.14
 * Time: 18:31
 */

namespace App\Configs;

/**
 * Class AccessConfig
 * @package App\Configs
 * Здесь все настройки по правам доступа, ролям пользователей
 */
class AccessConfig {
    /**
     * Использовать параметры доступа из БД, если отключено - права берутся из WebModule::isPermission()
     */
    const USE_DB_PERMISSION = TRUE;
    /**
     * Используемые в проекте роли пользователей
     */
    const ROLE_SUPER_ADMIN = 'SuperAdmin';
    const ROLE_SEO_ADMIN = 'SeoAdmin';
    const ROLE_BROKER = 'Broker';
    const ROLE_ADMIN = 'Admin';
    const ROLE_USER = 'User';
    const ROLE_USER_FIZ = 'UserFiz';
    const ROLE_USER_ORG = 'UserOrg';
    const ROLE_GUEST = 'Guest';

    private static $usesRoles = array(
        self::ROLE_SUPER_ADMIN,
        self::ROLE_SEO_ADMIN,
        self::ROLE_ADMIN,
        self::ROLE_BROKER,
        self::ROLE_GUEST
    );
    /**
     * Базовые группы прав доступа
     * по-умолчанию у \LPS\WebModule - ACCESS_WEB_MODULE, \LPS\AdminModule - ACCESS_ADMIN_MODULE
     * чтобы выбрать для какого-то модуля другую группу нужно переопределить константу DEFAULT_ACCESS
     * например const DEFAULT_ACCESS = AccessConfig::ACCESS_WEB_MODULE;
     *
     * также можно изменять отдельные строки в правах для конкретного модуля
     * для этого необходимо переопределить массив $module_custom_permissions в нужном модуле
     * protected static $module_custom_permissions = array(
     *     AccessConfig::ROLE_GUEST => false
     * );
     */
    const ACCESS_WEB_MODULE = 'WebModule';
    const ACCESS_ADMIN_MODULE = 'AdminModule';
    const ACCESS_NO_BROKERS = 'NoBrokers';
    const ACCESS_DISALLOW_ALL = 'DisallowAll';

    private static $default_permission = array(
        self::ACCESS_WEB_MODULE => array(
            self::ROLE_SUPER_ADMIN => true,
            self::ROLE_SEO_ADMIN => true,
            self::ROLE_ADMIN => true,
            self::ROLE_BROKER => true,
            self::ROLE_GUEST => true
        ),
        self::ACCESS_ADMIN_MODULE => array(
            self::ROLE_SUPER_ADMIN => true,
            self::ROLE_SEO_ADMIN => true,
            self::ROLE_ADMIN => true,
            self::ROLE_BROKER => true,
            self::ROLE_GUEST => false
        ),
        self::ACCESS_NO_BROKERS => array(
            self::ROLE_SUPER_ADMIN => true,
            self::ROLE_SEO_ADMIN => true,
            self::ROLE_ADMIN => true,
            self::ROLE_BROKER => false,
            self::ROLE_GUEST => false
        ),
        self::ACCESS_DISALLOW_ALL => array(
            self::ROLE_SUPER_ADMIN => false,
            self::ROLE_SEO_ADMIN => false,
            self::ROLE_ADMIN => false,
            self::ROLE_BROKER => false,
            self::ROLE_GUEST => false
        )
    );

    /**
     * Возвращает группу прав доступа по ключу
     * @param string $module_type
     * @return array
     */
    public static function getAccessList($module_type){
        return isset(self::$default_permission[$module_type]) ? self::$default_permission[$module_type] : NULL;
    }

    /**
     * возвращает используемые в проекте роли
     * @return array
     */
    public static function getUsesRoles(){
        return self::$usesRoles;
    }

} 