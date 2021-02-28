<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 06.11.2017
 * Time: 16:09
 */

namespace Models\CatalogManagement\CatalogHelpers\District;

use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Rules\Rule;

class DistrictHelper
{

    public static $instanse;
    private $districts = array();
    private $districtsList = array();
    private $districtsKeysList = array();

    private function __construct() {}



    public static function getInstance()
    {
        if (empty(self::$instanse)) {
            self::$instanse = new self();
        }
        return self::$instanse;
    }

    private function getDistricts() {
        if (empty($this->districts)) {
            $dKey = 'district';
            $this->districts = CatalogSearch::factory($dKey)
                ->setRules(array(
                    Rule::makeRules(array('status'=>array(1, 2, 3)))
                ))
                ->setPublicOnly(false)
                ->searchItems()->getSearch();
        }
        return $this->districts;
    }

    /**
     * @return array id=>key
     */
    public function getDistrictsKeysList() {
        if (empty($this->districtsKeysList)) {
            $dl = $this->getDistricts();
            foreach ($dl as $dli) {
                $this->districtsKeysList[$dli['id']] = $dli['key'];
            }
        }
        return $this->districtsKeysList;
    }

    /**
     * @param bool $back
     * if back=true return title=>key
     * esle return key=>title
     *
     * @return array
     */
    public function getDistrictsList($back = false) {
        if (empty($this->districtsList)) {
            $dl = $this->getDistricts();
            foreach ($dl as $dli) {
                if ($back) {
                    $this->districtsKeysList[$dli['title']] = $dli['key'];
                } else {
                    $this->districtsKeysList[$dli['key']] = $dli['title'];
                }
            }
        }
        return $this->districtsKeysList;
    }

}
