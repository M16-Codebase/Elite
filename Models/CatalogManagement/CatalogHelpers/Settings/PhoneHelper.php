<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 09.10.15
 * Time: 19:02
 */

namespace Models\CatalogManagement\CatalogHelpers\Settings;


use App\Configs\CatalogConfig;
use App\Configs\ContactsConfig;
use Models\CatalogManagement\Item;

class PhoneHelper extends SettingsHelper
{
    protected static $i = null;
    protected static $fieldsList = array('display_phone');
    private $result = null;

    public function get(Item $i, $field){
        if (in_array($field, static::$fieldsList) && $i->getType()['key'] == CatalogConfig::CONFIG_CONTACTS_KEY) {
            if (empty($this->result)) {
                $sxgeo = new \SxGeo('includes/SxGeo/SxGeoCity.dat');
                $ip = \App\Builder::getInstance()->getRequest()->getClientIp();
                if (empty($ip)) {
                    $this->result = $i[ContactsConfig::KEY_MAIN_PHONE];
                } else {
                    $city = $sxgeo->getCity($ip);
                    $this->result = $i[(empty($city) || $city['city']['name_ru'] == 'Санкт-Петербург') ? ContactsConfig::KEY_MAIN_PHONE : ContactsConfig::KEY_REGION_PHONE];
                }
            }
            return $this->result;
        }
    }
}