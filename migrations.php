<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 08.07.15
 * Time: 14:08
 * Миграции бд в отдельном приложении, независимом от движка. Необходимо для возможности накатывать миграции
 * в моменты невозможности функционирования движка по причине несоответствия структуры бд
 */
require_once 'Config.php';
require_once 'src/Autoload.php'; // LPS Loader
require_once 'vendor/autoload.php';   // Composer Loader
\LPS\Autoload::init(\LPS\Config::getAutoload(), \LPS\Config::getRealDocumentRoot() . '/' . \LPS\Config::getParametr('Dir','logs').'/autoload.log');
\Models\DatabaseManagement\Migrations::getInstance()->applyMigrations(TRUE);
