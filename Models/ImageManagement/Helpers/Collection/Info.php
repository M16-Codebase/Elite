<?php
namespace Models\ImageManagement\Helpers\Collection;

use Models\ImageManagement\Image;
use Models\ImageManagement\Collection;
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

    public function preCreate($type, &$params, $positions, &$errors){
		$params['data'] = !empty($params['data']) ? $params['data'] : array();
        foreach (self::$fieldList as $f){
            if (array_key_exists($f, $params)){
                $params['data'][$f] = $params[$f];
                unset($params[$f]);
            }
        }
    }
     /**
     * событие перед изменением
     */
    public function preUpdate(Collection $collection, &$params, &$errors){
		$params['data'] = !empty($params['data']) ? $params['data'] : array();
        foreach (self::$fieldList as $f){
            if (array_key_exists($f, $params)){
                $params['data'][$f] = $params[$f];
                unset($params[$f]);
            }
        }
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Collection $collection, $field, $segment_id = NULL){
        if (in_array($field, static::$fieldList)){
            if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE && !empty($image['info'][$field]) && is_array($image['info'][$field])) {
                if (empty($segment_id)) {
                    $segment_id = \App\Segment::getInstance()->getDefault()['id'];
                }
                return !empty($collection['info'][$field][$segment_id]) ? $collection['info'][$field][$segment_id] : NULL;
            } else {
                return !empty($collection['info'][$field]) ? $collection['info'][$field] : NULL;
            }
        }
    }
}
