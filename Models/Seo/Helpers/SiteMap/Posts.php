<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 14.10.14
 * Time: 19:07
 * Сбор урлов постов для sitemap.xml
 */

namespace Models\Seo\Helpers\SiteMap;


use App\Configs\SeoConfig;
use Models\ContentManagement\Post;
use Modules\Posts\Articles;
use Modules\Posts\News;

class Posts extends SitemapHelper{
    protected static $i = NULL;

    const PAGE_SIZE = 1000;

    /**
     * @param int $segment_id
     * @return bool
     */
    public function writeUrls($segment_id = null)
    {
        $seo_config = \Models\CatalogManagement\Positions\Settings::getConfigByKey(\App\Configs\CatalogConfig::CONFIG_SEO_KEY);
        $posts_types = $seo_config['properties'][\App\Configs\Settings::KEY_SITEMAP_POSTS]['value_key'];
        if (empty($posts_types)){
            return FALSE;
        }
        $offset = 0;
        do{
            $posts = Post::search(array('type' => $posts_types, 'status' => array(Post::STATUS_PUBLIC, Post::STATUS_CLOSE)), $count, $offset, self::PAGE_SIZE);
            foreach($posts as $post){
                $this->sitemap_generator->addUrlToSiteMapFile($post->getUrl(), $post['dt'], SeoConfig::POST_DEFAULT_PRIORITY);
            }
            Post::clearRegistry();
            $offset += self::PAGE_SIZE;
        }while(count($posts));
    }
}