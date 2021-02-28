<?php
/**
 * Начальная инициализация проекта
 * User: Charles Manson
 * Date: 17.10.14
 * Time: 14:29
 */
if (php_sapi_name() != 'cli') {
    echo 'Этот скрипт надо запускать из консоли';
    exit;
}
define('HOOKS_DIR', '/.git/hooks/');
define('LOCAL_HOOK_MIGRATIONS_ACTION', "#!/bin/sh\nphp migrations.php\nphp shell_index.php init index");

$local_hooks = array('post-commit', 'post-merge', 'post-rewrite');
$dir_list = array('/logs', '/data', '/cache', '/cache/templates_c');

echo 'LPS engine initializer' . PHP_EOL;
/**
 * загрузка модулей вендоров через композер
 * */
system('php composer.phar self-update'); //system чтобы все выводилось в консоль
system('php composer.phar update');

/**
 * Создание необходимых папок и задание им прав доступа
 */
foreach($dir_list as $dir_name){
    $dir_name = __DIR__ . $dir_name;
    if (!file_exists($dir_name)){
        mkdir($dir_name);
        chmod($dir_name, 0770);
        echo $dir_name . ' created' . PHP_EOL;
    }
}
if (!file_exists(__DIR__ . '/migrations')){
    mkdir(__DIR__ . '/migrations', 0777);
}

if (!file_exists(__DIR__ . '/Config.php')) {
    file_put_contents(__DIR__ . '/Config.php', '<?php

namespace LPS;

require_once \'BaseConfig.php\';

class Config extends BaseConfig
{

}
');
}
/**
 * Создание хуков гита, обслуживающих миграции БД
 */
foreach($local_hooks as $hook_name){
    $file_name = __DIR__ . HOOKS_DIR . $hook_name;
    //временно убрали, т.к. изменилось содержимое
//    if (!file_exists($file_name)){
        $fp = fopen($file_name, 'w');
        fwrite($fp, LOCAL_HOOK_MIGRATIONS_ACTION);
        fclose($fp);
        chmod($file_name, 0755);
        echo 'git hook "' . $hook_name . '" created' . PHP_EOL;
//    }
}

foreach(array('/robots.txt', '/sitemap.xml') as $file_name){
    $file_name = __DIR__ . $file_name;
    if (!file_exists($file_name)){
        touch($file_name);
        chmod($file_name, 0664);
    }
}
// Инициализируем бд, заливаем миграции
require_once 'Config.php';
require_once 'src/Autoload.php'; // LPS Loader
require_once 'vendor/autoload.php';   // Composer Loader
\LPS\Autoload::init(\LPS\Config::getAutoload(), \LPS\Config::getRealDocumentRoot() . '/' . \LPS\Config::getParametr('Dir','logs').'/autoload.log');
$db_service = \Models\DatabaseManagement\Migrations::getInstance();
$db_service->setDataFromFiles();
$db_service->applyMigrations(TRUE);
/* после того, как созданы уже все папки и обновился композер, можно запустить загрузку начальных данных */
system('php shell_index.php init index');