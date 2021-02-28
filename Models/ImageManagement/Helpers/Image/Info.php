<?php
namespace Models\ImageManagement\Helpers\Image;

use Models\ImageManagement\Image;
/**
 * Description of Logs
 *
 * @author olya
 */
class Info extends Helper{
    protected static $i = NULL;
    protected static $fieldList = array(
        'Название' => 'title', 
        'Текст' => 'text'
    );

    public function preCreate($file, &$params, &$errors){
        $params['info'] = !empty($params['info']) ? $params['info'] : array();
        foreach (self::$fieldList as $f){
            if (array_key_exists($f, $params)){
                $params['info'][$f] = $params[$f];
                unset($params[$f]);
            }
        }
    }
     /**
     * событие перед изменением
     */
    public function preUpdate(Image $image, &$params, &$errors){
        $params['info'] = !empty($params['info']) ? $params['info'] : array();
        foreach (self::$fieldList as $f){
            if (array_key_exists($f, $params)){
                $params['info'][$f] = $params[$f];
                unset($params[$f]);
            }
        }
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Image $image, $field, $segment_id = NULL){
        if (in_array($field, static::$fieldList)){
            if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE && !empty($image['info'][$field]) && is_array($image['info'][$field])) {
                if (empty($segment_id)) {
                    $segment_id = \App\Segment::getInstance()->getDefault()['id'];
                }
                return !empty($image['info'][$field][$segment_id]) ? $image['info'][$field][$segment_id] : NULL;
            } else {
                return !empty($image['info'][$field]) ? $image['info'][$field] : NULL;
            }
        }
    }
}
