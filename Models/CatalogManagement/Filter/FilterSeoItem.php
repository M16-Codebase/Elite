<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 05.11.2017
 * Time: 18:51
 */

namespace Models\CatalogManagement\Filter;

use Models\CatalogManagement\Item;
use Models\CatalogManagement\Filter\FilterMapHelper;

class FilterSeoItem extends Item
{

    public static function getItemIdByKey($key) {
        $db = \App\Builder::getInstance()->getDB();

        $result = $db->query("SELECT `id` FROM " . SELF::TABLE . " WHERE `key` LIKE '%" . $key . "%'")->select();

        if (!empty($result)) {
            return $result[0]['id'];
        } else {
            return null;
        }
    }

    public static function getItemByKey($key, $segment = null, $searchParamsArray = null)
    {
        $itId = self::getItemIdByKey($key);
        $item = self::getById($itId, $segment);

        //if ($item['is_template'] === 1) {
        //    $item = self::replaceTemplateTags($item, $searchParamsArray);
        //}

        return $item;
    }

    public static function replaceTemplateTags(FilterSeoItem $item, $searchParamsArray = null) {

        dump($item['page_title']);
        $item['page_title'] = 'xl;cvk;xvk;xclvk;xcv';
        dump($item['page_title']);
    }


    public static function replaceTemplateTagsInProperty($property = '', $searchParamsArray = null)
    {
        if (empty($property)) return;

        $filterHelper = FilterMapHelper::getInstance();
        $matches = array();
        $pattern = '#\<\%([A-Z_]*)\%\>#';
        preg_match_all( $pattern , $property, $matches );

        if (!empty($matches) && !empty($searchParamsArray)) {

            for ($i = 0; $i < count($matches[0]); $i++) {
                $searchType = $filterHelper->checkMatch($matches[1][$i]);
                if ($searchType !== false) {
                    if (array_key_exists($searchType, $searchParamsArray)) {
                        $typeVal = $searchParamsArray[$searchType][0];
                        $type = $filterHelper->getType($searchType);
                        if ($type == FilterMapHelper::NUMERIC) {
                            $res = $filterHelper->getNumericRule($matches[1][$i], $typeVal);
                        }

                        if ($type == FilterMapHelper::OBJECT) {
                            dump($searchType);
                            //$res = $filterHelper->getNumericRule($matches[1][$i], $typeVal);
                        }

                    }
                }
            }
        }
    }
}