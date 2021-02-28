<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 13.07.15
 * Time: 16:25
 *
 * Хелпер добавляет в sitemap.xml урлы статичных страниц
 */

namespace Models\Seo\Helpers\SiteMap;


use App\Configs\SeoConfig;

class StdPages extends SitemapHelper
{
    protected static $i = NULL;
    /**
     * @var array список урлов статичных страниц
     * @TODO необходимо прописывать по каждому проекту
     */
    private $url_list = array(
        '/',
        '/contacts/',
        '/company/',
        '/service/',
        '/top100/',
        '/top16/'
    );

    /**
     * @param int $segment_id
     */
    public function writeUrls($segment_id = null) {
        $segment = self::USE_SEGMENTS ? $this->segments[$segment_id] : null;
        foreach($this->url_list as $url){
            if (self::USE_SEGMENTS) {
                $this->sitemap_generator->addUrlToSiteMapFile(($segment['key'] != $this->default_segment['key'] ? '/'.$segment['key'] : '') . $url, date('Y-m-d H:i:s'), SeoConfig::STATIC_PAGE_DEFAULT_PRIORITY);
            } else {
                $this->sitemap_generator->addUrlToSiteMapFile($url, date('Y-m-d H:i:s'), SeoConfig::STATIC_PAGE_DEFAULT_PRIORITY);
            }
        }
    }
}