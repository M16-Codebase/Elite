<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'top-100');

/** Имя пользователя MySQL */
define('DB_USER', 'top100');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'X0t1I7c9');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'MJdE9f@4lC$ie^+6583i^JL,2I+?uQsU!gQCL|m{S*`GCgAZX>@uy{jm4R!e3:{2');
define('SECURE_AUTH_KEY',  'Ud{kWb.lhH uKK(ZBaP1L6/jUB?T$?R_[@3:|*Y> R|f=)|y6k:UKvPLI%HGX2mS');
define('LOGGED_IN_KEY',    ' 5dH1jHS=4HU8GE%yHezxi24Z50`nMIPkIB?N@P9T(xv1N_/sib21dbG+6]a|Fz=');
define('NONCE_KEY',        'L<@E$&5%=)N%.L5mi)::2K=O>R.~+d!mr)dm~o o#6*I#WvT,Gs=_-Yr(VeX)PB-');
define('AUTH_SALT',        'xiAnO_1Z v$GZ2o >zp`[$y/c[;):=DBx.zZ:QRYt)7wxb_~8i4x/U07}8w+tn* ');
define('SECURE_AUTH_SALT', '|%ANdNSy>[z%}yqIX%Ox.r:8)cSdRdC}^Wp4<C(!bR@lA$xzmL/g9bpXpR{Bwa|+');
define('LOGGED_IN_SALT',   'S@I+,A)h$sX)#TRwYn$onbcG8!bY7b/gyV.(!Vu|7vLWMka>T`y].:]]f|=tW=rB');
define('NONCE_SALT',       '{v$*f?;<EuL<oqAH1<4h)%(i1*7]QN{UR^RhS(r-O:@x!v#zFq(54Um)x&wDxZHc');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');

