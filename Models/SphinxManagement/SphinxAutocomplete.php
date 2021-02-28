<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 02.10.14
 * Time: 17:43
 */

namespace Models\SphinxManagement;

use App\Configs\CatalogConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\ContentManagement\Post;

class SphinxAutocomplete {
    const MIN_SEARCH_WORD_LEN = 3;
    const MAX_ITEMS_COUNT = 10;
    const MAX_POSTS_COUNT = 10;

    public static function search($search_string){
        if (strlen($search_string) < self::MIN_SEARCH_WORD_LEN){
            return array(
                'items' => array(),
                'posts' => array()
            );
        }
        $catalog_search = SphinxSearch::factory('lps_catalog');
        $posts_search = SphinxSearch::factory('lps_posts');
        $item_ids = $catalog_search->setGroup('item_id')->select('GROUP_CONCAT(`variant_id`) AS `variant_ids`, `item_id`', $search_string)->getCol('item_id', 'item_id');
        $post_ids = $posts_search->select('`id`', $search_string)->getCol('id', 'id');
        $default_segment = \App\Segment::getInstance()->getDefault(true);
        $allow_post_status = array(Post::STATUS_CLOSE, Post::STATUS_PUBLIC);
        return array(
            'items' => CatalogSearch::factory(CatalogConfig::CATALOG_KEY, $default_segment['id'])->setPublicOnly(true)->setRules(array(Rule::make('id')->setValue($item_ids)))->searchItems(0, self::MAX_ITEMS_COUNT)->getSearch(),
            'posts' => Post::search(array('id' => $post_ids, 'status' => $allow_post_status), $count, 0, self::MAX_POSTS_COUNT)
        );
    }

}