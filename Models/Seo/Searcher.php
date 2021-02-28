<?php
/**
 * Description of Searcher
 *
 * @author olga
 */
namespace Models\Seo;
use LPS\Container\ContentContainer;
class Searcher {
    const TABLE = 'search';
    public static function registrateUrl($url, ContentContainer $ans){
        self::setData($url, $ans->get('GlobalSearchType'), $ans->get('GlobalSearchData'), $ans->getContent());
    }
    private static function setData($url, $type, $data, $html){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('INSERT INTO `'.self::TABLE.'` SET `url` = ?s, `type` = ?s, `data` = ?s, `text` = ?s, `html` = ?s', $url, $type, $data, strip_tags($html), $html);
    }
}

?>
