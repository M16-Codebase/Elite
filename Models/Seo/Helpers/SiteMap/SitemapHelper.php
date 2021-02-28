<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 14.10.14
 * Time: 14:16
 */
namespace Models\Seo\Helpers\SiteMap;

use Models\Seo\SiteMap;

abstract class SitemapHelper implements iSitemapHelper{
    const USE_SEGMENTS = TRUE;
    /**
     * @var SiteMap
     */
    protected $sitemap_generator = NULL;
    /**
     * @var \Models\Segments\iSegment[]
     */
    protected $segments = array();

    protected $default_segment = null;

    /**
     * @return static
     */
    public static function getInstance(){
        if (empty(static::$i)){
            static::$i = new static;
        }
        return static::$i;
    }

    protected function __construct(){
        SiteMap::addHelper($this);
        $this->sitemap_generator = SiteMap::getInstance();
        if (self::USE_SEGMENTS){
            $this->segments = \App\Segment::getInstance()->getAll();
            $this->default_segment = \App\Segment::getInstance()->getDefault(true);
        }
    }
}