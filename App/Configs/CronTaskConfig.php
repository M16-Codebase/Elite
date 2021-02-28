<?php
/**
 * Настройки крон задач
 */
namespace App\Configs;

class CronTaskConfig {
    const TASK_UPDATE_PROPS = 'update_props';
    const TASK_IMPORT_ENTITIES_CSV = 'import_entities_csv';
    const TASK_EXPORT_ENTITIES_CSV = 'export_entities_csv';
    const TASK_EXPORT_MANUAL_ENTITIES_CSV = 'export_manual_entities_csv';
    const TASK_SPHINX_RECREATE_PROPS = 'sphinx_recreate_props';
    const TASK_SPHINX_WORDFORMS_GENERATE = 'sphinx_wordforms_generate';
    const TASK_SPHINX_REBUILD_INDEX = 'sphinx_index_rebuild';
    const TASK_SENDSAY_SYNCHRONIZE = 'sendsay_synchronize';
    const TASK_RECREATE_VIEW = 'recreate_view';
    private static $types = array(
        self::TASK_UPDATE_PROPS => 'Массовое обновление свойств',
        self::TASK_IMPORT_ENTITIES_CSV => 'Импорт сущностей каталога через csv',
        self::TASK_EXPORT_ENTITIES_CSV => 'Экспорт сущностей каталога в csv (ftp)',
        self::TASK_EXPORT_MANUAL_ENTITIES_CSV => 'Экспорт сущностей каталога в csv (email)',
        self::TASK_SPHINX_RECREATE_PROPS => 'Подготовка данных для индексации Sphinx',
        self::TASK_SPHINX_WORDFORMS_GENERATE => 'Генерация файла синонимов Sphinx',
        self::TASK_SPHINX_REBUILD_INDEX => 'Обновление индекса Sphinx',
        self::TASK_SENDSAY_SYNCHRONIZE => 'Синхронизация с Sendsay',
        self::TASK_RECREATE_VIEW => 'Пересчет составных свойств'
    );
    public static function getTypes(){
        return self::$types;
    }
    public static function isTypeExists($type){
        return isset(self::$types[$type]) ? self::$types[$type] : FALSE;
    }
} 