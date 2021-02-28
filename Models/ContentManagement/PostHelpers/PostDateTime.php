<?php
namespace Models\ContentManagement\PostHelpers;
use Models\ContentManagement\Post;
/**
 * Description of PostDateTime
 *
 * @author charles manson
 */
class PostDateTime extends PostHelper{
    protected static $i = NULL;
    private $dataCache = array();
    protected static $fieldList = array('date', 'time');
    /**
     * возвращает значение дополнительного поля
     */
    function get(Post $post, $field){
        if (!in_array($field, $this->fieldsList())){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
        if (!isset ($this->dataCache[$post['id']])){
            $this->dataCache[$post['id']] = array(
                'date' => date('d.m.Y', $post['pub_timestamp']),
                'time' => date('H:i:s', $post['pub_timestamp'])
            );
        }
        return $this->dataCache[$post['id']][$field];
    }
    function preUpdate(Post $post, &$params, &$errors = NULL){
        if (!empty($params['date']) && !empty($params['time'])){
            $params['pub_date'] = date('Y-m-d H:i:s', strtotime($params['date'] . ' ' . $params['time']));
        }
        return $post;
    }
}
