<?php
/**
 * Менеджер миграций баз данных
 * User: Charles Manson
 * Date: 16.10.14
 * Time: 16:02
 */

namespace Modules\Site;


use Models\DatabaseManagement\Migrations;

class DbMigrations extends \LPS\AdminModule{

    public function index(){
        $this->getAns()
            ->add('is_local', \LPS\Config::isLocal())
            ->add('migrations_list', Migrations::getInstance()->getMigrationsList());
    }

    /**
     * Добавление миграции
     */
    public function addMigration(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $sql = $this->request->request->get('sql');
        if (!\LPS\Config::isLocal()){
            $ans->addErrorByKey('access', 'local_only');
        } elseif (empty($sql)){
            $ans->addErrorByKey('sql', 'empty');
        } else {
            Migrations::getInstance()->addMigration($sql, $this->account->getUser()->getEmail());
        }
    }

    /**
     * Применение миграций
     */
    public function applyMigrations(){
        $this->setJsonAns()
            ->setEmptyContent()
            ->addData('apply_count', Migrations::getInstance()->applyMigrations());
    }

} 