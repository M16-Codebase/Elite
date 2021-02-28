<?php
/**
 * Модуль для служебных экшенов командной строки (то что не относится к крону)
 * User: Charles Manson
 * Date: 16.10.14
 * Time: 17:40
 */

namespace Modules\CliModules;


use Models\DatabaseManagement\Migrations;

class Rake extends \LPS\Module{
    const HOOKS_DIR = '.git/hooks/';
    const LOCAL_HOOK_MIGRAGION_ACTION = 'php src/index.php rake dbmigrate';

    function index(){
        echo 'select method'.PHP_EOL;
    }

    /**
     * Применение миграции базы данных
     */
    public function dbmigrate(){
        Migrations::getInstance()->applyMigrations(TRUE);
    }

    /**
     * Импорт данных о подписках с сабскрайба
     */
    public function sendsayImport() {
        \Models\SubscribeManagement\SubscribeController::getInstance()->importDataFromSendSay();
    }

    /**
     * Начальная настройка аккаунта сабскрайба
     */
    public function initSendsayAccount() {
        $sc = \Models\SubscribeManagement\SubscribeController::getInstance();
        $sc->checkAnketa();
        $sc->formatSetup();
        $sc->recreateStdGroups();
    }
}