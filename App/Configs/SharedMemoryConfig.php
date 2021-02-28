<?php
namespace App\Configs;
/**
 * Description of SharedMemoryConfig
 *
 * @author olya
 */
class SharedMemoryConfig {
    /**
     * резерв разделяемой памяти в байтах, 0 - не использовать
     * в коде используется именно эта константа, берется из конфига т.к. в конфиге можно переопределить
     */
    const MEMORY_LIMIT = \LPS\Config::SHM_MEMORY_LIMIT;
    /**
     * права на доступ
     */
    const PERMISSIONS = 0660;
    /**
     * поля категорий
     */
    const SHM_KEY_CATEGORY = 'type';
    /**
     * для кэша конфига
     */
    const SHM_KEY_SITE_CONFIG = 'site_config';
    /**
     * для просмотра памяти из внешних систем, нужен пароль. должен быть одинаковый на всех проектах
     */
    const SECRET_KEY_VIEW_DATA = 'sTFF49d97{s?UvC$';
    /**
     * ключи переменных в памяти (должны быть целым числом и !=0)
     * отсюда можно регулировать, какие сущности\массивы закидывать в память
     * лучше не удалять, а комментировать, чтобы не потерять все возможности
     * @var array
     */
    private static $entities_keys = array(
        self::SHM_KEY_CATEGORY => 1,
        self::SHM_KEY_SITE_CONFIG => 2
    );
    
    public static function getEntityKey($entity_type = NULL){
        if (is_null($entity_type)){
            return self::$entities_keys;
        }
        return empty(self::$entities_keys[$entity_type]) ? NULL : self::$entities_keys[$entity_type];
    }
}
