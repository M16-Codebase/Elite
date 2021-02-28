<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 07.10.2017
 * Time: 10:30
 */

namespace Models\CatalogManagement\Filter;

use Models\CatalogManagement\CatalogHelpers\District\DistrictHelper;

/*
 * по умолчанию на сайте релаизована система фильтрации с использованием ajax
 * которая подразумевает запрос через get параметры.
 * Сейчас заказчику понадобилось чтобы фильтр стал поддерживать ЧПУ
 * Самы простой способ рещения - это подобрать структуру урла для запроса в фильтр
 *
 * например: m16-elite.ru/resale/odnokomnatnye_kvartiry__area_40-50__price_1200000-2000000
 * параметры фильтра разделяются двойным нижним дефисом, внутри параметра название и его значение одинарным
 *
 */

class FilterMap
{

    /**
     * if key ot the seo-item compare district and bedrooms
     * we be see at this item as template-item
     */
    const DISTRICT_BEDROOMS_IS_TEMPLATE = true;

    const USE_TEMPLATE_FOR_N_BEDROOMS = false;

    const APARTS_SECTOR = 'apartments';
    const RESALE_SECTOR = 'resale';
    const RESIDENTIAL_SECTOR = 'residential';
    const REAL_ESTATE_SECTOR = 'real-estate';

    const N_BEDROOMS = 'n_bedrooms';

    const CLOUD_SEPARATOR = '#';
    const BEDROOMS = self::CLOUD_SEPARATOR . '_bedrooms';
    const _BEDROOMS = 'bedrooms';

    /* filter vars */
    const BED_NUMBER_ONE = ['odnokomnatnyye_kvartiry', 'odnokomnatnye_kvartiry'];
    const BED_NUMBER_TWO = ['dvukhkomnatnyye_kvartiry', 'dvukhkomnatnye_kvartiry', 'dvuhkomnatnyye_kvartiry', 'dvuhkomnatnye_kvartiry'];
    const BED_NUMBER_THREE = ['trekhkomnatnyye_kvartiry', 'trekhkomnatnye_kvartiry', 'trehkomnatnye_kvartiry', 'trehkomnatnyye_kvartiry'];
    const BED_NUMBER_FOUR = ['chetyrekhkomnatnyye_kvartiry', 'chetyrekhkomnatnyye kvartiry', 'chetyrehkomnatnye_kvartiry', 'chetyrehkomnatnyye_kvartiry'];
    const BED_NUMBER_FIVE = ['pyatikomnatnyye_kvartiry', 'pyatikomnatnye_kvartiry'];

    private static $bedRooms = [
        1 => self::BED_NUMBER_ONE,
        2 => self::BED_NUMBER_TWO,
        3 => self::BED_NUMBER_THREE,
        4 => self::BED_NUMBER_FOUR,
        5 => self::BED_NUMBER_FIVE,

        6 => ['shestikomnatnyye_kvartiry'],
        7 => ['semikomnatnyye_kvartiry'],
        8 => ['vosmi_kvartiry'],
        9 => ['devyati_kvartiry'],
        10 => ['desyati_kvartiry'],
    ];

    private static $districts = [
        87 => "Адмиралтейский район",
        88 => "Василеостровский район",
        89 => "Выборгский район",
        103 => "Крестовский остров",
        36 => "Петроградский район",
        41 => "Центральный район",
    ];

    const AREA = 'area_all';
    const PRICE = 'close_price';
    const BED_NUMBER = 'bed_number';
    const DISTRICT = 'district';

    const DISTRICT_SYNONYM = 'rayon';

    //интервальные параметры вида param_valMin-valMax
    private static $intervalParams = [
        'price' => 'close_price',
        'area' => 'area_all'
    ];

    /**
     * хранит копию предыдущего запроса
     * для того, чтобы обнулить запрос по старым параметрам
     * чтбы они не "залипали"
     * @var array $searchCache
     */
    private $searchCache = [];
    private $searchParams;
    public $isFilter;
    private $catalog;

    /**
     * @param mixed $catalog
     */
    public function setCatalog($catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * @return array
     */
    public static function getBedRooms()
    {
        return self::$bedRooms;
    }


    /**
     * При парсинге если имеются признаки ЧПУ то ставим true
     * и по ней можно понять был ли ЧПУ запрос от фильтре или нет
     * Это в свою очередь нужно чтобы понимать выводить определенный контент или нет
     * @var bool
     */
    private $isFriendliUrl = false;


    /**
     * Был передан ЧПУ или нет
     * @return bool
     */
    public function isFriendlyUrl() {
        return $this->isFriendliUrl;
    }

    /**
     * Возвращает массив с разделами, которым разрешены
     * дополнительные фишки в конце урла
     * Например /real-estate/complex/krestovskiy-de-luxe/aratments/some_url_part/
     * изначально не будет будет пропущен, но если его вписать сюда, то apatrts будет вызван с передачей ему
     * остальной части урла
     * @return array
     */
    public static function allowedSectors()
    {
        return [
            self::APARTS_SECTOR,
            self::RESALE_SECTOR,
            self::RESIDENTIAL_SECTOR,
            self::REAL_ESTATE_SECTOR
        ];
    }



    public function getSearchParams() {
        return $this->searchParams;
    }

    /**
     * синонимы для параметров фильтра
     */
    public static function filterSynonims()
    {

    }

    public function parseParams($urlParams)
    {
        // by default we allow parsing friendly url's
        // but if there is a corresponding constant in the configuration
        // we pass it's vaule to local variable
        $usedFU = true;
        if (null !== \LPS\Config::USE_FRIENDLY_URL) {
            $usedFU = !!\LPS\Config::USE_FRIENDLY_URL;
        }

        if ($usedFU) {
            $this->searchParams = [];
            $this->isFriendliUrl = false;
            //dump($urlParams);
            $urlParams = (array)$urlParams;
            foreach ($urlParams as $urlParam) {
                $params = explode('__', $urlParam);
                foreach ($params as $param) {
                    self::checkBedRoomsParam($param, $this->searchParams);
                    self::checkDistrictsParam($param, $this->searchParams);
                    self::checkCommonParam($param, $this->searchParams);
                }
            }

            if (empty($this->searchParams)) {
                return false;
            }
            $this->searchCache = $this->searchParams;
            $this->isFriendliUrl = true;

            return true;
        }

        return false;

    }

    public function injectSearchParams($request)
    {
        if ($request instanceof \Symfony\Component\HttpFoundation\Request) {
            $this->refreshRequest($request);
            foreach ($this->searchParams as $param => $value) {
                $request->query->set($param, $value);
            }
            //unset($request->query['district']);
            //dump($request->query->all());
            return true;
        }
        return false;
    }


    public function getRequestParams() {
        return $this->searchParams;
    }

    /**
     * очищаем параметры запроса от тех, которые были в прошлом запросе
     *
     * @param $request
     * @return bool
     */
    private function refreshRequest($request)
    {
        if ($request instanceof \Symfony\Component\HttpFoundation\Request) {
            $all = $request->query->all();
            foreach ($this->searchCache as $param => $value) {
                unset($all[$param]);
            }
            $request->query->replace($all);
            return true;
        }
        return false;
    }

    private static function checkBedRoomsParam($param, & $searchParams)
    {
        foreach (self::$bedRooms as $value => $bedRoom) {
            if (in_array($param, $bedRoom)) {
                $searchParams[self::BED_NUMBER][] = $value;
                break;
            }
        }
        return true;
    }

    private static function checkDistrictsParam($param, & $searchParams)
    {
        foreach ( DistrictHelper::getInstance()->getDistrictsKeysList() as $key => $dis) {
            if (false !== strpos($dis, $param)) {
                $searchParams[self::DISTRICT][] = $key;
            }
        }
        return true;
    }

    private static function checkCommonParam($param, & $searchParams)
    {
        $param = explode('_', $param);
        //если параметр интервальный
        if (array_key_exists($param[0], self::$intervalParams)) {

            $vals = explode('-', $param[1]);
            if (isset($vals[1])) {
                if ($vals[1] > $vals[0]) {
                    $searchParams[self::$intervalParams[$param[0]]]['min'] = $vals[0];
                    $searchParams[self::$intervalParams[$param[0]]]['max'] = $vals[1];
                    return true;
                }
            } else {
                $searchParams[self::$intervalParams[$param[0]]]['min'] = 0;
                $searchParams[self::$intervalParams[$param[0]]]['max'] = $vals[0];
                return true;
            }

            return false;
        } else {
            // пока вернем false, потом реализовать можно будет параметры в виде
            // paramMin_value paramMax_value
            return false;
        }
    }


    public function getSeoFilterItemKey()
    {
        if (empty($this->catalog)) {
            return null;
        }
        $sfi = '';
        if (array_key_exists(self::DISTRICT, $this->searchParams)) {
            $sfi = self::DISTRICT;
        }
        //dump(in_array(self::BED_NUMBER, $this->searchParams));
        if (array_key_exists(self::BED_NUMBER, $this->searchParams)) {
            if (!empty($sfi)) {
                $sfi .= '_';
            }
            if (self::USE_TEMPLATE_FOR_N_BEDROOMS) {
                $sfi.= self::N_BEDROOMS;
            } else {
                $sfi .= str_replace(self::CLOUD_SEPARATOR, $this->searchParams[self::BED_NUMBER][0], self::BEDROOMS);
            }
        }
        $cat = $this->catalog;
        if ($this->catalog === 'real-estate') {
            $cat = 'real';
        }
        if (!empty($sfi) && $sfi !== self::DISTRICT) {
            $sfi .= '_' . $cat;
        }

        if (strpos($sfi, self::DISTRICT) !== false && strpos($sfi, self::_BEDROOMS) !== false) {
            if (self::DISTRICT_BEDROOMS_IS_TEMPLATE) {
                $sfi = str_replace($this->searchParams[self::BED_NUMBER][0], 'n', $sfi);
            }
        }

        return $sfi;
    }

    public function getBedNumberKey($bedNumber)
    {
        return self::$bedRooms[(int)$bedNumber][0];
    }

}
