<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 16.11.2017
 * Time: 14:32
 */

namespace Models\CatalogManagement\CatalogHelpers\Item;

use Models\CatalogManagement\Item as ItemEntity;
use App\Configs\CatalogConfig;
use Symfony\Component\HttpFoundation\Cookie;

class RatingHelper extends ItemHelper
{
    const RATING_PROP = CatalogConfig::RATING_PROP;
    const MARKS_PROP = CatalogConfig::MARKS_PROP;
    const DELIMITER = CatalogConfig::RATING_DELIMITER;

    public static function calculateRating(ItemEntity $item)
    {
        if (isset($item[self::MARKS_PROP]) && is_string($item[self::MARKS_PROP])) {
            $marks = self::getAsArray($item[self::MARKS_PROP]);
            $count = count($marks);
            if (!$count) return 0;
            $sum = 0;
            foreach ($marks as $k => $val) {
                $sum += (int)$val;
            }
            $commonMark = $sum / $count;
            $commonMark = round($commonMark, 1, PHP_ROUND_HALF_ODD);
            return $commonMark;
        } else
            return false;

    }

    public static function getMarksCount($marks)
    {
        return count(self::getAsArray($marks));
    }

    public static function getAsArray($marks)
    {
        $marks = explode(self::DELIMITER, $marks);
        return array_diff($marks, array(''));
    }

    public static function asString($marks)
    {
        if (!is_array($marks)) return $marks;
        $marks = array_diff($marks, array(''));
        return implode(self::DELIMITER, $marks);
    }

    public static function checkRating($objId) {
        if (self::checkRatingCookie($objId)) return true;
        else if(self::checkRatingIP($objId)) return true;
        return false;
    }

    public static function checkRatingCookie($objId) {
        if(isset($_COOKIE[CatalogConfig::RATING_COOKIE_NAME . $objId])) {
            return true;
        } else {
            return false;
        }
    }

    public static function checkRatingIP($objId) {
        $db = \App\Builder::getInstance()->getDB();
        $ip = $_SERVER['REMOTE_ADDR'];
        $table = CatalogConfig::RATING_TABLE;

        $sql = "SELECT * FROM {$table} WHERE `rating_ip`='{$ip}' AND `rating_objId`={$objId}";

        $res = $db->query($sql)->getRow();

        return !empty($res);
    }

    public static function setRatingIP($objId, $rating = null) {
        if (is_null($rating) || is_null($objId)) return;
        $db = \App\Builder::getInstance()->getDB();
        $ip = $_SERVER['REMOTE_ADDR'];
        $host = $_SERVER['SERVER_ADDR'];
        $table = CatalogConfig::RATING_TABLE;
        $timestamp = time();

        $sql = "INSERT INTO {$table} (`rating_objId`, `rating_rating`, `rating_timestamp`, `rating_ip`, `rating_host`)
          VALUES ({$objId},{$rating},'{$timestamp}','{$ip}','{$host}')";

        return $db->query($sql);
    }

}
