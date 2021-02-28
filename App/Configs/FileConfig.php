<?php
/**
 * Конфиг для постов
 *
 * @author poche_000
 */
namespace App\Configs;
use Models\ContentManagement\Post;
class FileConfig {
	/**
     * Названия параметров
     * @var type 
     */
    private static $fields = array(
        'full_name' => 'Название',
        'title' => 'Заголовок',
        'full_size' => 'Размер',
        'cover_id' => 'Обложка',
        'position' => 'Позиция'
    );
    public static function getFields(){
        return self::$fields;
    }
}
