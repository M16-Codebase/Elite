<?php
namespace Models\ContentManagement\PostHelpers;

use Models\Logger AS MainLogger;
/**
 * Логи
 *
 * @author olya
 */
class Logs extends PostHelper{
    const LOG_ENTITY_TYPE = 'post';
    protected static $i = NULL;
    private $old_params = array();
    public function onCreate($id) {
        $post = \Models\ContentManagement\Post::getById($id);
        $data = array(
            'type' => MainLogger::LOG_TYPE_CREATE,
            'entity_type' => self::LOG_ENTITY_TYPE,
            'entity_id' => $id,
            'additional_data' => array(
                't' => !empty($post['title']) ? $post['title'] : $post['id'],
                't_t' => $post['type'],
                't_th' => !empty($post['theme']) ? $post['theme']['title'] : NULL
            )
        );
        MainLogger::add($data);
    }
    public function preUpdate(\Models\ContentManagement\Post $post, &$params, &$errors = NULL) {
        $this->old_params[$post['id']] = $post->asArray();
    }
    public function onUpdate(\Models\ContentManagement\Post $post) {
        $new_params = $post->asArray();
        $logged_fields = \App\Configs\PostConfig::getFields();
        foreach ($new_params as $f => $nd){
            if (!array_key_exists($f, $logged_fields)){
                continue;
            }
            if ($nd != $this->old_params[$post['id']][$f]){
                $diff[$f] = $nd;
            }
        }
        unset($this->old_params[$post['id']]);
        if (empty($diff)){
            return;
        }
        foreach ($diff as $f => $v){
            $data = array(
                'type' => MainLogger::LOG_TYPE_EDIT,
                'entity_type' => self::LOG_ENTITY_TYPE,
                'entity_id' => $post['id'],
                'attr_id' => $f,
                'additional_data' => array(
                    't' => $post['title'],
                    't_t' => $post['type'],
                    't_th' => !empty($post['theme']) ? $post['theme']['title'] : NULL,
                    'v' => $v
                )
            );
            MainLogger::add($data);
        }
        return;
    }
    public function onDelete(\Models\ContentManagement\Post $post) {
        $data = array(
            'type' => MainLogger::LOG_TYPE_DEL,
            'entity_type' => self::LOG_ENTITY_TYPE,
            'entity_id' => $post['id'],
            'additional_data' => array(
                't' => !empty($post['title']) ? $post['title'] : $post['id'],
                't_t' => $post['type'],
                't_th' => !empty($post['theme']) ? $post['theme']['title'] : NULL
            )
        );
        MainLogger::add($data);
    }
}
