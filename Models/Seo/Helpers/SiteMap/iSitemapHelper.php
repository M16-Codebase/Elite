<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 14.10.14
 * Time: 14:12
 */
namespace Models\Seo\Helpers\SiteMap;

interface iSitemapHelper {
    /**
     * @return iSitemapHelper
     */
    public static function getInstance();

    /**
     * @param int $segment_id
     */
    public function writeUrls($segment_id = null);

} 