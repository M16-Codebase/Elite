<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 16.12.14
 * Time: 13:19
 */
namespace Models\ContentManagement\PostHelpers;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Properties\Variant;
use Models\ContentManagement\Post;
use Models\Seo\SeoLinks;

/**
 * @TODO описание
 * Class SeoLinksPostHelper
 * @package Models\ContentManagement\PostHelpers
 */

class SeoLinksPostHelper extends PostHelper{
    protected static $i = NULL;
    protected static $fieldList = array('post_location_url');

    private $dataCache = array();
    private $cleaningCache = array();

    function get(Post $post, $field){
        if (!in_array($field, $this->fieldsList())){
            throw new \InvalidArgumentException('Неверное название дополнительного поля');
        }
        if (!isset($this->dataCache[$post['id']])){
            $url = '';
            switch($post['type']){
                case 'items':
                    //@TODO можно ли сделать через CatalogSearch, причем "постранично"
                    $data = \App\Builder::getInstance()->getDB()->query('
                          SELECT "variant" AS `entity_type`, `variant_id` AS `entity_id`, `segment_id`
                              FROM `variants_properties_int`
                              WHERE `property_id` IN (SELECT `id` FROM `properties` WHERE `multiple` = 1 AND `data_type` = "post")
                                AND `value` = 1
                          UNION SELECT "item" AS `entity_type`, `item_id` AS `entity_id`, `segment_id`
                              FROM `items_properties_int`
                              WHERE `property_id` IN (SELECT `id` FROM `properties` WHERE `multiple` IS NULL AND `data_type` = "post")
                              AND `value` = ?d',
                        $post['id'], $post['id'])->getRow();
                    if (!empty($data)){
                        $entity = ($data['entity_type'] == 'item') ? Item::getById($data['entity_id'], $data['segment_id']) : Variant::getById($data['entity_id'], $data['segment_id']);
                        $url = !empty($entity) ? $entity->getUrl($data['segment_id']) : '';
                    }
                    break;
                case 'types':
                    $type = \Models\CatalogManagement\CatalogHelpers\Type\AdditionalFields::factory()->getTypeByPost($post);
                    if ($type){
                        $url = $type['url'];
                    }
                    break;
                default:
                    $url = $post['url'];
            }
            $this->dataCache[$post['id']] = strlen($url) > 1 ? rtrim($url, '/') : $url;
        }
        return $this->dataCache[$post['id']];
    }
    /**
     * событие после изменения Post
     */
    function onUpdate($post){
        $id = $post['id'];
        if (!empty($this->cleaningCache[$id])){
            \App\Builder::getInstance()->getDB()->query('DELETE FROM `' . SeoLinks::INSERTED_LINKS_LIST_TABLE . '` WHERE `from` = ?s', $this->cleaningCache[$id]);
            unset($this->cleaningCache[$id]);
        }
        return $id;
    }
    /**
     * событие перед изменением
     */
    function preUpdate(Post $post, &$params, &$errors = NULL){
        if (isset($params['text']) && $params['text'] != $post['text']){
            $params['complete_text'] = NULL;
            $this->cleaningCache[$post['id']] = rtrim($post['post_location_url'], '/');
        }
        return $post;
    }
} 