<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 14.09.15
 * Time: 20:52
 */


namespace Modules\Catalog;

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use App\Configs\CatalogConfig;
use App\Configs\FeedbackConfig;
use App\Configs\RealEstateConfig;
use App\Configs\Settings;
use Models\CatalogManagement\Catalog;
use Models\CatalogManagement\Filter\FilterMap;
use Models\CatalogManagement\Item as ItemEntity;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Rules\RuleAggregator;
use Models\CatalogManagement\Type as TypeEntity;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\CatalogHelpers\District\DistrictHelper;
use Models\CatalogManagement\Filter\FilterMapHelper;
use Models\CatalogManagement\CatalogHelpers\Item\RatingHelper;
use Models\CatalogManagement\Filter\FilterSeoItem;
use Models\CatalogManagement\SeoElements\SeoLinks;

class RealEstate extends CatalogPublic
{
    const DEFAULT_CATALOG_KEY = CatalogConfig::CATALOG_KEY_REAL_ESTATE;
    const CONCURRENT_LIST_COUNT = 10;
    const APARTMENTS_LIST_PAGE_SIZE = 20;
    const COMPLEX_LIST_PAGE_SIZE = 20;
    const BEST_OFFERS_PAGE_SIZE = 6;

    const PDF_TMP_DIR = 'data/pdf_presentation/';
	

    private function prepareQueryParams($filter_visible) {
        $get_sort = $this->request->query->get('order');
        $search_params = $this->request->query->all();
        unset($search_params['sort']);
        unset($search_params['order']);
        if (!empty($get_sort) || !empty($search_params)) {
            $real_estate_catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
            $apartment_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_FLAT, $real_estate_catalog['id'], $this->segment['id']);
            $final_props = PropertyFactory::search($apartment_category['id'], PropertyFactory::P_ALL, 'id', 'type_group', 'self', array('transfered_prop' => 1));
            if (!empty($final_props)) {
                foreach($final_props as $p) {
                    $origin_category = TypeEntity::getById($p['origin_type_id'], $this->segment['id']);
                    $origin_key = strpos($p['key'], $origin_category['key'] . '_') === 0 ? substr($p['key'], strlen($origin_category['key']) + 1) : null;
                    if (empty($origin_key)) {
                        continue;
                    }
                    if (!empty($get_sort[$origin_key])) {
                        $get_sort[$p['key']] = $get_sort[$origin_key];
                        unset($get_sort[$origin_key]);
                    }
                    if (!empty($search_params[$origin_key])) {
                        $this->request->query->set($p['key'], $search_params[$origin_key]);
                        $this->request->query->set($origin_key, null);
                    }
                }
            }
        }
        $this->request->query->set('order', $get_sort);
    }

    public function index() {

        $filterHelper = FilterMapHelper::getInstance();
        $request = $this->request->getRequestUri();
		$request=explode('&',$request);
		$request=$request[0];
        $request_params = $this->cleanrequestUri($request, self::DEFAULT_CATALOG_KEY);

        //если после фильтр-параметра передали еще что-то вызываем 404
        if (count($request_params) > 1) {
            return $this->notFound();
        }

        if (!empty($request_params)) {
            // заполнить $this->request->query параметрами
            $request = $this->filterMap->injectSearchParams($this->request);
            // если вернулся false, значит признаков фильтра нет
            // возвращаем 404
            if (!$request) {
                return $this->notFound();
            }
        }

        // ЧПУ из фильтра или не ЧПУ
        $isFrUrl = $this->filterMap->isFriendlyUrl();
        //dump($isFrUrl);
        $for_catalog = false;
        if ($isFrUrl) {
            $searchParamsArray = $this->filterMap->getSearchParams();
            // ищем ключ для сео-айтема
            $seo_filter_item_key = $this->filterMap->getSeoFilterItemKey();
            //dump($seo_filter_item_key);
            if (!empty($seo_filter_item_key)) {
                // если он равен просто district то достаем район,
                // т.к. сео текст для района хранится там
                if ($seo_filter_item_key == 'district') {
                    // если в посике есть запрос на район и если он один
                    // то достаем сео текст района, если он не один, то
                    // не будем ломать голову какой текст доставать,
                    // а просто пропустим
                    if (count($searchParamsArray['district']) === 1) {
                        $seoItem = FilterSeoItem::getById($searchParamsArray['district'][0], $this->segment['id']);
                        if (in_array($request_params[0], ['petrogradskij-ra','vasileostrovskij', 'zolotoj-treugolj',
                            'moskovskij-rajon', 'krestovskij-ostrov'])) {
                            $canonical_uri = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] .
                                '/district/saint-petersburg/' . $request_params[0] . '/';
                        }
                        $for_catalog = true;
                    }
                } else {
                    // если все равно нужен район
                    if (strpos($seo_filter_item_key, 'district') !== false) {
                        $seoDistrict = FilterSeoItem::getById($searchParamsArray['district'][0], $this->segment['id']);
                    }
                    $seoItem = FilterSeoItem::getItemByKey($seo_filter_item_key, $this->segment['id'], $searchParamsArray);
                }
            }
        }

        // Параметры поиска по свойствам ЖК в самом начале
        $title_search = $this->request->query->get('title');
        $this->request->query->set('title', null);
        $district_filter = $this->request->query->get(RealEstateConfig::KEY_OBJECT_DISTRICT);
        $this->request->query->set(RealEstateConfig::KEY_OBJECT_DISTRICT, null);
        // Потом преобработка get-параметров для поиска по квартирам
        $this->prepareQueryParams(CatalogConfig::FV_COMPLEX_LIST);
        $catalog = Catalog::factory(self::DEFAULT_CATALOG_KEY, $this->segment['id']);
        $real_estate_catalog = TypeEntity::getByKey(self::DEFAULT_CATALOG_KEY, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $type = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_COMPLEX, $real_estate_catalog['id'], $this->segment['id']);
        if (empty($type)) {
            return $this->notFound();
        }
        $ajax = $this->request->query->has('ajax');
        $quickView = $this->request->query->has('quickView');
        if ($quickView) {
            $ajax = true;
            $current_id = $this->request->query->get('current_id');
        }
        $ans = $ajax
            ? $this->setJsonAns()->setTemplate($quickView ? 'Modules/Catalog/RealEstate/quickView.tpl' : 'Modules/Catalog/RealEstate/complexList.tpl')
            : $this->getAns();
        $page = $this->request->query->get('page', 1);
        if ($page < 1 || !is_numeric($page)){
            $params['page'] = 1;
            if ($ajax){
                $ans->addErrorByKey('page', \Models\Validator::ERR_MSG_INCORRECT)->setEmptyContent();
                return;
            }else{
                $params = $this->request->query->all();
                unset($params['page']);
                return $this->redirect(($this->segment['key'] != \Models\Segments\Lang::DEFAULT_KEY ? '/'.$this->segment['key'] : '') . '/' . (!empty($params) ? '?' . http_build_query($params) : ''));
            }
        }
        $order = $this->request->query->get('order');
        $sort_by_title = !empty($order['complex_title']);
        // Подменяем сортировку по закрытой цене сортировкой по цене за кв м
        $sort_by_price = isset($order['close_price']) ? $order['close_price'] : false;
        unset($order['close_price']);
        $this->request->query->set('order', $order);
        $apart_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_FLAT, $real_estate_catalog['id'], $this->segment['id']);
        $search_params = \App\CatalogMethods::getSearchableRules($this->request, $apart_category['id'], array(), $this->segment['id']);
        $aggr = reset($search_params);
        $only_complex = empty($aggr->getRules());
        if (!$only_complex) {
            $apart_ids = CatalogSearch::factory(static::DEFAULT_CATALOG_KEY, $this->segment['id'])->setTypeId($apart_category['id'])->setPublicOnly(true)->setRules($search_params)->searchItems();
            $parent_ids = $apart_ids->getParentIdsByTypeId($type['id']);
        }

        if (!empty($parent_ids) || $only_complex) {
            /** @var Rule[] $item_rules */
            $item_rules = !empty($parent_ids)
                ? array(
                    'id' => Rule::make('id')->setValue($parent_ids)
                )
                : array();
            if (!empty($district_filter)) {
                $item_rules[RealEstateConfig::KEY_OBJECT_DISTRICT] = Rule::make(RealEstateConfig::KEY_OBJECT_DISTRICT)->setValue($district_filter);
            }
            if ($sort_by_title) {
                $item_rules['title'] = Rule::make('title')->setOrder(false);
            } elseif ($sort_by_price !== false) {
                $item_rules[RealEstateConfig::KEY_OBJECT_PRICE_METER_FROM] = Rule::make(RealEstateConfig::KEY_OBJECT_PRICE_METER_FROM)->setOrder(!$sort_by_price);
            } elseif (!empty($order) && !empty($parent_ids)) {
                // сортировка от квартир
                $item_rules['id']->setOrder($parent_ids);
            } else {
                // дефолтная сортировка — по приоритету
                $item_rules[RealEstateConfig::KEY_OBJECT_PRIORITY] = Rule::make(RealEstateConfig::KEY_OBJECT_PRIORITY)->setOrder(true);
            }
            if (!empty($title_search)) {
                $title_kb = \LPS\Components\FormatString::keyboardLayout($title_search, 'both');
                $item_rules = array(
                    RuleAggregator::make(RuleAggregator::LOGIC_AND, $item_rules),
                    RuleAggregator::make(RuleAggregator::LOGIC_OR, array(
                        Rule::make(RealEstateConfig::KEY_OBJECT_TITLE)->setValue($title_search, Rule::SEARCH_LIKE),
                        Rule::make(RealEstateConfig::KEY_OBJECT_TITLE_SEARCH)->setValue($title_search, Rule::SEARCH_LIKE),
                        Rule::make(RealEstateConfig::KEY_OBJECT_TITLE)->setValue($title_kb, Rule::SEARCH_LIKE),
                        Rule::make(RealEstateConfig::KEY_OBJECT_TITLE_SEARCH)->setValue($title_kb, Rule::SEARCH_LIKE)
                    ))
                );
            }
            $items = CatalogSearch::factory(static::DEFAULT_CATALOG_KEY, $this->segment['id'])
                ->setTypeId($type['id'])
                ->setPublicOnly(true)
                ->setRules($item_rules)
                ->searchItems(($page - 1) * self::COMPLEX_LIST_PAGE_SIZE, self::COMPLEX_LIST_PAGE_SIZE);
        }
        $kustik_search_props = $catalog->getSearchableProperties($type['id'], 'public', null, array('filter_visible' => CatalogConfig::FV_COMPLEX_LIST), array(), 'type_group', TRUE, TRUE);
        $district_search_props = $catalog->getSearchableProperties($type['id'], 'public', null, array('filter_visible' => CatalogConfig::FV_COMPLEX_LIST, 'key' => RealEstateConfig::KEY_OBJECT_DISTRICT), array(), 'type_group', TRUE, FALSE);
        if (!empty($district_search_props[RealEstateConfig::KEY_OBJECT_DISTRICT])) {
            $kustik_search_props[RealEstateConfig::KEY_OBJECT_DISTRICT] = $district_search_props[RealEstateConfig::KEY_OBJECT_DISTRICT];
            unset($kustik_search_props['complex_district']);
        }
        uksort($kustik_search_props, function($a, $b) {
            $keys = array(
                'bed_number' => 1,
                'area_all' => 2,
                'close_price' => 3,
                'district' => 4
            );
            $a_pos = !empty($keys[$a]) ? $keys[$a] : 666;
            $b_pos = !empty($keys[$b]) ? $keys[$b] : 666;
            return $a_pos > $b_pos;
        });
        //dump($kustik_search_props);
        $districtsList = DistrictHelper::getInstance()->getDistrictsKeysList();

        // это все извлечено для формирования ссылок
        $items_list_for_seo_links = CatalogSearch::factory(static::DEFAULT_CATALOG_KEY, $this->segment['id'])
                ->setTypeId($type['id'])
                ->setPublicOnly(true)
                ->searchItems();
        $bedroom_count_filters = !empty($items_list_for_seo_links) ? \App\CatalogMethods::getRealEstateFilters($items_list_for_seo_links->getItemIds(), $this->segment['id']) : array();
        //$seo_links = SeoLinks::getInstance()->generateSeoLinks($items_list_for_seo_links->getSearch(), $bedroom_count_filters, self::DEFAULT_CATALOG_KEY);

        // сгенерить случайные ссылки

        $seo_links_dinamyc = SeoLinks::getInstance()->generateSeoLinks($items_list_for_seo_links->getSearch(), $bedroom_count_filters, self::DEFAULT_CATALOG_KEY);
        $seo_links ['dinamyc'] = !empty($seo_links_dinamyc) ? $seo_links_dinamyc : [];
        $static_seo_links = SeoLinks::getInstance()->getStaticSeoLinks(self::DEFAULT_CATALOG_KEY);
        //dump($static_seo_links);
        $seo_links ['static'] = !empty($static_seo_links) ? $static_seo_links : [];


        $ans->add('bedroom_count_filters', !empty($items) ? \App\CatalogMethods::getRealEstateFilters($items->getItemIds(), $this->segment['id']) : array())
            ->add('items', !empty($items) ? $items->getSearch() : array())
            ->add('seoLinks', $seo_links)
            ->add('search_params', $search_params)
            ->add('districtList', $districtsList)
            ->add('search_properties', $kustik_search_props)
            ->add('search_properties_count', count($kustik_search_props))
            ->add('foreign_price', \App\CatalogMethods::getForeignPrice($kustik_search_props))
            ->add('count', !empty($items) ? $items->getTotalCount() : 0)
            ->add('pageSize', self::COMPLEX_LIST_PAGE_SIZE)
            ->add('search_string', !empty($search_string) ? $search_string : '')
            ->add('pageNum', $page)
            ->add('catalogKey', self::DEFAULT_CATALOG_KEY)
            ->add('for_catalog', $for_catalog)
            ->add('quick_view_current_id', !empty($current_id) ? $current_id : null);
        if (!empty($canonical_uri)) {
            $ans->add('canonical', $canonical_uri);
        }
        if (empty($items) || empty($items->getSearch())) {
            $best_offers = CatalogSearch::factory(static::DEFAULT_CATALOG_KEY, $this->segment['id'])
                ->setTypeId($type['id'])
                ->setPublicOnly(true)
                ->setRules(array(
                    Rule::make(RealEstateConfig::KEY_OBJECT_PRIORITY)->setOrder(true)
                ))
                ->searchItems(0, self::BEST_OFFERS_PAGE_SIZE);
            $ans->add('best_offers', $best_offers->getSearch());
        }


        // если был ЧПУ запрос из фильтра
        if ($isFrUrl) {
            $ans->add('is_friendly_url', true);
            // если не пуст район, значит по нему был ЧПУ запрос через фильтр
            // выводим сео-текст по району
            if (!empty($seoItem)) {
                if (isset($searchParamsArray['district'])) {
                    $ans->add('filter_district_id', $searchParamsArray['district'][0]);
                }
                $ans->add('seoItem', $seoItem);
            }

            if (!empty($seoDistrict)) {
                $ans->add('seoDistrict', $seoDistrict);
            }

            $beds_number = $this->request->query->get('bed_number');
            if (!empty($beds_number) || count($beds_number) === 1) {
                $ans->add('filter_beds_number_synonym', $filterHelper->word_analog($beds_number[0]));
                $ans->add('filter_beds_number', $beds_number[0]);
            }
        }
    }

    public function items(){
        return $this->notFound();
    }

    public function request() {
        $id = $this->request->query->get('id');
        if (empty($id)) {
            return $this->notFound();
        } else {
            if (!is_array($id) || count($id) == 1) {
                $id = is_array($id) ? reset($id) : $id;
                $item = ItemEntity::getById($id, $this->segment['id']);
                if (empty($item) || $item->getType()->getCatalog()['key'] != CatalogConfig::CATALOG_KEY_REAL_ESTATE) {
                    return $this->notFound();
                }
                $category = $item->getType();
                switch ($category['key']) {
                    case RealEstateConfig::CATEGORY_KEY_COMPLEX:
                        $this->getAns()
                            ->add('mode', 'complex')
                            ->add('complex', $item);
                        break;

                    case RealEstateConfig::CATEGORY_KEY_FLAT:
                        $this->getAns()
                            ->add('mode', 'single_apartment')
                            ->add('apartment', $item);
                        break;

                    default:
                        return $this->notFound();
                }
            } else {
                $catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
                $category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_FLAT, $catalog['id'], $this->segment['id']);
                $apartments = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $this->segment['id'])
                    ->setTypeId($category['id'])
                    ->setPublicOnly(false)
                    ->setRules(array(
                        Rule::make('id')->setValue($id),
                        Rule::make('status')->setValue(array(ItemEntity::S_PUBLIC, ItemEntity::S_TEMPORARY_HIDE))
                    ))
                    ->searchItems()
                    ->getSearch();
                if (empty($apartments)) {
                    return $this->notFound();
                }
                $this->getAns()
                    ->add('mode', 'list')
                    ->add('apartments', $apartments);
            }
        }
        $this->getAns()
            ->add('form_type', FeedbackConfig::TYPE_APART_REQUEST);
    }

    public function viewItem(){
        $routes = explode('/', $this->routeTail);
        $item = ItemEntity::getById($routes[0], $this->segment['id']);
        if (empty($item)) {
            return $this->notFound();
        }
        $type = $item->getType();
        return $this->run($type['key'].'Page');
    }

    public function complexPage() {
        $routes = explode('/', $this->routeTail);
        $item_id = $routes[0];
        $page_type = !empty($routes[1]) ? $routes[1] : null;
        if (!empty($page_type)) {
            if (in_array($page_type, array('apartments', 'scheme'))) {
                $this->routeTail = $item_id;
                return $this->run($page_type);
            } else {
                return $this->run('informationBlock');
            }
        }
        $item = ItemEntity::getById($routes[0], $this->segment['id']);
        if (empty($item)) {
            return $this->notFound();
        }
        $type = $item->getType();
        $catalog = Catalog::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $this->segment['id']);
        $concurrents = $catalog->getConcurrents($type['id'], $item, self::CONCURRENT_LIST_COUNT);
        $filter_data = \App\CatalogMethods::getRealEstateFilters($item['id'], $this->segment['id']);

        $marksCount = RatingHelper::getMarksCount($item[CatalogConfig::MARKS_PROP]);
        $isVotedRating = RatingHelper::checkRating($item['id']);


        setlocale(LC_NUMERIC, 'en_US.UTF8');

        $childrens = $this->db->query(
            "SELECT id FROM `items` WHERE `parents` LIKE '%{$item['id']}%' AND `type_id`= 16"
        )->getCol('id', 'id');
        $ids = [];
        foreach ($childrens as $k=>$children) {
            $ids[] = (int)$children;
        }
        $ids = implode(',', $ids);
        $maxP = null;
        $minP = null;

        if (!empty($ids)) {
            $minP = $this->db->query(
                "SELECT MIN(`value`) as `val` FROM `items_properties_int` WHERE `item_id` IN ({$ids}) AND `property_id` = 133"
            )->select();

            if (isset($minP[0]['val'])) {
                $minP = number_format($minP[0]['val'] * 1000000, 0, ',', ' ');
            } else {
                $minP = null;
            }
            $maxP = $this->db->query(
                "SELECT MAX(`value`) as `val` FROM `items_properties_int` WHERE `item_id` IN ({$ids}) AND `property_id` = 134"
            )->select();

            if (isset($maxP[0]['val'])) {
                $maxP =number_format($maxP[0]['val'] * 1000000, 0, ',', ' ');
            } else {
                $maxP = null;
            }
        }
		$minPrice=explode('—',$item['properties']['app_area']['value']);
		$mxPrice=explode('—',$item['properties']['app_area']['value']);
		if(count($minPrice)>1 && count($mxPrice)>1 && isset($item['properties']['price_meter_from']['value'])){
			$fminPrice=((int)$minPrice[0]*(int)$item['properties']['price_meter_from']['value']*1.15)*1000;
			$minPrice=number_format(((int)$minPrice[0]*(int)$item['properties']['price_meter_from']['value']*1.15)/1000, 2, ',', ' ');
			$fmxPrice=((int)$mxPrice[1]*(int)$item['properties']['price_meter_from']['value'])*1000;
			$mxPrice=number_format(((int)$mxPrice[1]*(int)$item['properties']['price_meter_from']['value'])/1000, 2, ',', ' ');
			$fullpage=1;
		}else{
			$fullpage=0;
			//print_r($minPrice);
			$fminPrice=0;
			$fmxPrice=0;
			$minPrice=0;
			$mxPrice=0;
		}
        $this->getAns()
            ->add('item', $item)
			->add('fullpage', $fullpage)
            ->add('min_price', $minPrice)
			->add('f_min_price', $fminPrice)
            ->add('max_price', $mxPrice)
			->add('f_max_price', $fmxPrice)
            ->add('isVotedRating', $isVotedRating)
            ->add('marksCount', $marksCount)
            ->add('maxMark', $item['properties']['rating']['values']['max'])
            ->add('page_type', $page_type)
            ->add('filter_data', !empty($filter_data[$item['id']]) ? $filter_data[$item['id']] : array())
            ->add('similar_objects', $concurrents)
            ->add('customJs', array('js/stars.min.js', 'js/jquery.raty.js',  'js/rating.js'))
            ->add('outCss', array('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css'))
            ->add('customCss', array('css/stars.css', 'css/jquery.raty.css'));
    }

    public function housingPage() {
        return $this->notFound();
        $routes = explode('/', $this->routeTail);
        $page_type = !empty($routes[1]) ? $routes[1] : null;
        $item = ItemEntity::getById($routes[0], $this->segment['id']);
        if (empty($item)) {
            return $this->notFound();
        }
        $this->getAns()
            ->add('item', $item)
            ->add('page_type', $page_type);
    }

    public function floorPage() {
        return $this->notFound();
        $routes = explode('/', $this->routeTail);
        $page_type = !empty($routes[1]) ? $routes[1] : null;
        $item = ItemEntity::getById($routes[0], $this->segment['id']);
        if (empty($item)) {
            return $this->notFound();
        }
        $this->getAns()
            ->add('item', $item)
            ->add('page_type', $page_type);
    }

    public function flatPage() {
        $routes = explode('/', $this->routeTail);
        $page_type = !empty($routes[1]) ? $routes[1] : null;
        $item = ItemEntity::getById($routes[0], $this->segment['id']);
        if (empty($item)) {
            return $this->notFound();
        }
        $item_properties = PropertyFactory::search($item['type_id'], PropertyFactory::P_ITEMS, 'key', 'group', 'self', array('visible' => CatalogConfig::V_PUBLIC_FULL), $this->segment['id']);
        $this->getAns()
            ->add('item', $item)
            ->add('page_type', $page_type)
            ->add('type_properties', !empty($item_properties) ? $item_properties : array())
            ->add('site_url', \LPS\Config::getParametr('Site', 'url'))
            ->add('lang', \Models\Lang::getInstance())
			->add('request_segment', \App\Segment::getInstance()->getDefault(TRUE));
        if ($this->request->query->has('pdf')){
            if ($this->request->query->has('test') && $this->account->getRole() == 'SuperAdmin'){
                $this->setAjaxResponse('Modules/Catalog/RealEstate/apartmentPdf.tpl');
            } else {
                ini_set('allow_url_fopen', 1);
                require_once 'includes/dompdf/autoload.inc.php';

                $pdf = new \Dompdf\Dompdf();
                $pdf->getOptions()
                    ->set('enable_remote', true)
                    ->set('enable_css_float', true)
                    ->set('enable_html5_parser', true)
                    ->set('pdf_backend', 'PDFLib');
                $pdf->loadHtml($this->getAns()->setTemplate('Modules/Catalog/RealEstate/apartmentPdf.tpl', 'UTF-8')->getContent());
                $pdf->setPaper('a4', 'landscape');
                $pdf->render();
                $file_name = $item['id'].'.pdf';
                $path = \LPS\Config::getRealDocumentRoot() . self::PDF_TMP_DIR;
                if (!file_exists($path)) {
                    mkdir($path, 0770, true);
                }
                file_put_contents($path . $file_name, $pdf->output());
                $site_config = \App\Builder::getInstance()->getSiteConfig($this->segment['id']);
                $presentation = $site_config[Settings::KEY_APARTMENT_PDF_COVER . '_' . $this->segment['key']];
                if (!empty($presentation)) {
                    $command_line = 'pdftk';
                    $command_line .= ' ' . escapeshellarg($path . $file_name) . ' ' . escapeshellarg($presentation->getUrl('absolute'));
                    $result_file_name = $item['id'] . '.result.pdf';
                    $command_line .= ' cat output ' . $path . $result_file_name;
                    exec($command_line, $output);
                    if (file_exists($path . $result_file_name)){
                        chmod($path . $result_file_name, 0770);
                        unlink($path . $file_name);
                        rename($path . $result_file_name, $path . $file_name);
                    }else{
                        throw new \ErrorException('Не удалось записать файл');
                    }
                }
//                if (\App\Builder::getInstance()->getDeviceDetect()->isMobile()){
//                    $email = \Models\Validator::getInstance($this->request)->checkResponseValue('email', 'checkEmail', $error);
//                    if (empty($error)){
//                        $pdf = $pdf->output();
//                        $dir_name = \LPS\Config::getRealDocumentRoot() . '/data/tmp';
//                        if (!file_exists($dir_name)){
//                            mkdir($dir_name);
//                        }
//                        $file_path = $dir_name . '/' . $request_variant_id . '.pdf';
//                        file_put_contents($file_path, $pdf);
//                        $mail_template = new \LPS\Container\WebContentContainer('mails/sendPdf.tpl');
//                        \Models\Email::send($mail_template, array($email), 'sales@solo-group.ru', 'Жилой комплекс «Гранвиль»', array(array('file_name' => $file_name, 'url' => $file_path)));
//                        unlink($file_path);
//                        return json_encode(array('errors' => NULL));
//                    } else {
//                        return json_encode(array('errors' => array('email' => $error)));
//                    }
//                } else {
                $complex = $item->getParent()->getParent()->getParent();
                    $this->downloadFile($path . $file_name,
                        $complex[RealEstateConfig::KEY_OBJECT_TITLE] . ' ' . $complex[RealEstateConfig::KEY_OBJECT_ADDRESS] . ' ' . $site_config[Settings::KEY_COMPANY_NAME] . '.pdf');
//                    $pdf->stream($file_name);
//                }
            }
        }
    }

    public function informationBlock() {
        $routes = explode('/', $this->routeTail);
        $item_id = $routes[0];
        $page_type = !empty($routes[1]) ? $routes[1] : null;
        $item = ItemEntity::getById($item_id, $this->segment['id']);
        if (empty($item)) {
            return $this->notFound();
        }
        $type = $item->getType();
        // Для каждого инфоблока есть своя пропертя. Если такой нет — 404
        if (empty($item['properties'][$page_type])
            || empty($item['properties'][$page_type]['property']['group'])
            || $item['properties'][$page_type]['property']['group']['key'] != RealEstateConfig::KEY_GROUP_INFORM_BLOCK
        ) {
            return $this->notFound();
        }
        $this->getAns()
            ->add('complex', $item)
            ->add('infoblock_type', $page_type);
    }

    public function apartments() {
		if(strpos('|'.$_SERVER['REQUEST_URI'],'manifest')){
			return $this->notFound();
		}
        $this->prepareQueryParams(CatalogConfig::FV_PUBLIC);
        $complex = ItemEntity::getById($this->routeTail, $this->segment['id']);
        if (empty($complex)) {
            return $this->notFound();
        }
        $catalog = Catalog::factory(self::DEFAULT_CATALOG_KEY, $this->segment['id']);
        $complex_category = $complex->getType();
        $real_estate_catalog = $complex_category->getCatalog();
        $housing_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_HOUSING, $real_estate_catalog['id'], $this->segment['id']);
        $flat_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_FLAT, $real_estate_catalog['id'], $this->segment['id']);
        $page = $this->request->query->get('page', 1);
        $ajax = $this->request->query->has('ajax');
        $ans = $ajax ? $this->setJsonAns()->setTemplate('Modules/Catalog/RealEstate/apartmentsList.tpl') : $this->getAns();
        if ($page < 1 || !is_numeric($page)){
            if ($ajax){
                $ans->addErrorByKey('page', \Models\Validator::ERR_MSG_INCORRECT)->setEmptyContent();
                return;
            }else{
                $params = $this->request->query->all();
                unset($params['page']);
                return $this->redirect($complex->getUrl($this->segment['id']) . 'flatList/?' . http_build_query($params));
            }
        }
        $order_params = $this->request->query->get('order');
        if (empty($order_params)) {
            // Дефолтный режим сортировки — количество спален
            $this->request->query->set('order', array(RealEstateConfig::KEY_APPART_BED_NUMBER => 1));
        }
        $search_params = \App\CatalogMethods::getSearchableRules($this->request, $flat_category['id'], array(), $this->segment['id']);
        $search_params[] = RuleAggregator::make(RuleAggregator::LOGIC_AND, array(
            Rule::make('parents')->setValue('.'.$complex_category['id'].':'.$complex['id'].'.', Rule::SEARCH_LIKE_LEFT),
            Rule::make(RealEstateConfig::KEY_APPART_STATE)->setValue('for_sale')->setSearchByEnumKey()
        ));
        $all_apart_ids = $this->db->query('SELECT `id` FROM `' . ItemEntity::TABLE . '` WHERE `parents` LIKE ?s', '.'.$complex_category['id'].':'.$complex['id'].'.%')->getCol('id', 'id');
        if (empty($all_apart_ids)) {
            return $this->notFound();
        }
        $search_properties = $catalog->getSearchableProperties($housing_category['id'], 'public', null, array('filter_visible' => CatalogConfig::FV_PUBLIC), array(Rule::make('id')->setValue($all_apart_ids)), 'type_group', TRUE, TRUE);
        uksort($search_properties, function($a, $b) {
            $keys = array(
                'bed_number' => 1,
                'area_all' => 2,
                'floor_floor_number' => 3,
                'close_price' => 4
            );
            $a_pos = !empty($keys[$a]) ? $keys[$a] : 666;
            $b_pos = !empty($keys[$b]) ? $keys[$b] : 666;
            return $a_pos > $b_pos;
        });
        $flats = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $complex['segment_id'])
            ->setTypeId($flat_category['id'])
            ->setRules($search_params)
            ->setSortMode(CatalogSearch::SORT_RANDOM)
            ->searchItems(($page - 1) * self::APARTMENTS_LIST_PAGE_SIZE, self::APARTMENTS_LIST_PAGE_SIZE);
        $flatsList = $flats->getSearch();
        if (empty($flatsList)) {
            return $this->notFound();
        }
        $ans->add('complex', $complex)
            ->add('flats', $flatsList)
            ->add('search_params', $search_params)
            ->add('search_properties', $search_properties)
            ->add('foreign_price', \App\CatalogMethods::getForeignPrice($search_properties))
            ->add('count', $flats->getTotalCount())
            ->add('pageSize', self::APARTMENTS_LIST_PAGE_SIZE)
            ->add('pageNum', $page);
    }

    public function scheme() {
		//echo'dfsdfdfdf';
        $complex = ItemEntity::getById($this->routeTail, $this->segment['id']);
        if (empty($complex) || $complex->getType()['key'] != RealEstateConfig::CATEGORY_KEY_COMPLEX) {
            return $this->notFound();
        }
		//echo($this->segment['id']);
		//print_r($complex->getUrl());
        $real_estate_catalog = TypeEntity::getByKey(self::DEFAULT_CATALOG_KEY);
        $housing_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_HOUSING, $real_estate_catalog['id'], $this->segment['id']);
        $housing = CatalogSearch::factory(self::DEFAULT_CATALOG_KEY, $this->segment['id'])
            ->setPublicOnly(true)
            ->setTypeId($housing_category['id'])
            ->setRules(array(Rule::make('parent_id')->setValue($complex['id'])))
            ->searchItems();

        if (empty($housing->count())) {
            return $this->notFound();
        }
        if ($floor_id = $this->request->query->get('floor')) {
            return $this->apartSelect($floor_id);
        } elseif ($housing_id = $this->request->query->get('housing')) {
            return $this->floorSelect($housing_id);
        } else {
            return $this->housingSelect($complex['id']);
        }
    }

    public function housingSelect($complex_id = null) {
        $ans = !empty($complex_id) ? $this->getAns() : $this->setJsonAns();
        $complex = ItemEntity::getById(!empty($complex_id) ? $complex_id : $this->request->request->get('id'), $this->segment['id']);
        if (empty($complex)) {
            if (!empty($complex_id)) {
                return $this->notFound();
            }
            $ans->setEmptyContent()
                ->addErrorByKey('id', \Models\Validator::ERR_MSG_EMPTY);
            return;
        } elseif ($complex->getType()['key'] != RealEstateConfig::CATEGORY_KEY_COMPLEX) {
            if (!empty($complex_id)) {
                return $this->notFound();
            }
            $ans->setEmptyContent()
                ->addErrorByKey('id', \Models\Validator::ERR_MSG_INCORRECT);
            return;
        }
        $real_estate_catalog = TypeEntity::getByKey(self::DEFAULT_CATALOG_KEY);
        $housing_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_HOUSING, $real_estate_catalog['id'], $this->segment['id']);
        $housing = CatalogSearch::factory(self::DEFAULT_CATALOG_KEY, $this->segment['id'])
            ->setPublicOnly(true)
            ->setTypeId($housing_category['id'])
            ->setRules(array(Rule::make('parent_id')->setValue($complex['id'])))
            ->searchItems();
        if (!empty($complex_id) && $housing->count() == 1) {
            // При внутреннем вызове комплекса с одним корпусом перекидываем на выбор этажа
            $housing = $housing->getFirst();
            $ans->add('single_housing', true);
            return $this->floorSelect($housing['id']);
        }
        $bedroom_count_filters =\App\CatalogMethods::getRealEstateFilters($housing->getItemIds(), $this->segment['id'], $housing_category['id']);
        if (empty($housing->getSearch())) {
            return $this->notFound();
        }

        $this->getAns()
            ->add('complex', $complex)
            ->add('housing', $housing->getSearch())
            ->add('select_mode', 'housing')
            ->add('bedroom_count_filters', $bedroom_count_filters)
            ->add('area_range', $this->getAreaRange($bedroom_count_filters));
        if (empty($complex_id)) {
            // Урл прямой ссылки при аяксовом запросе
            $ans->addData('url', $complex->getUrl($this->segment['id']) . 'scheme/');
        }
    }

    public function floorSelect($housing_id = null) {
        $ans = !empty($housing_id) ? $this->getAns() : $this->setJsonAns();
        $housing = ItemEntity::getById(!empty($housing_id) ? $housing_id : $this->request->request->get('id'), $this->segment['id']);
        if (empty($housing)) {
            if (!empty($housing_id)) {
                return $this->notFound();
            }
            $ans->setEmptyContent()
                ->addErrorByKey('id', \Models\Validator::ERR_MSG_EMPTY);
            return;
        } elseif ($housing->getType()['key'] != RealEstateConfig::CATEGORY_KEY_HOUSING) {
            if (!empty($housing_id)) {
                return $this->notFound();
            }
            $ans->setEmptyContent()
                ->addErrorByKey('id', \Models\Validator::ERR_MSG_INCORRECT);
            return;
        }
        $floor_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_FLOOR, $housing->getType()->getCatalog()['id'], $this->segment['id']);
        $floors = CatalogSearch::factory(self::DEFAULT_CATALOG_KEY, $this->segment['id'])
            ->setPublicOnly(true)
            ->setTypeId($floor_category['id'])
            ->setRules(array(Rule::make('parent_id')->setValue($housing['id'])))
            ->searchItems();
        if (empty($floors->getSearch())) {
            return $this->notFound();
        }
        $bedroom_count_filters = \App\CatalogMethods::getRealEstateFilters($floors->getItemIds(), $this->segment['id'], $floor_category['id']);
        $complex = $housing->getParent();
        $this->getAns()
            ->add('complex', $complex)
            ->add('housing', $housing)
            ->add('select_mode', 'floor')
            ->add('floors', $floors->getSearch())
            ->add('bedroom_count_filters', $bedroom_count_filters)
			->add('mnarea', 22)
            ->add('area_range', $this->getAreaRange($bedroom_count_filters));
        if (empty($housing_id)) {
            // Урл прямой ссылки при аяксовом запросе
            $ans->addData('url', $complex->getUrl($this->segment['id']) . 'scheme/?housing=' . $housing['id']);
        }
    }

    private function getAreaRange($bedroom_count_filters) {
        $result = array();
        foreach($bedroom_count_filters as $obj_id => $obj_data) {
            foreach($obj_data as $data) {
                if (empty($result[$obj_id]['area_max']) || $result[$obj_id]['area_max'] < $data['area_max']) {
                    $result[$obj_id]['area_max'] = $data['area_max'];
                }
                if (empty($result[$obj_id]['area_min']) || $result[$obj_id]['area_min'] > $data['area_min']) {
                    $result[$obj_id]['area_min'] = $data['area_min'];
                }
            }
        }
        return $result;
    }

    /**
     * Выбор квартиры на плане этажа
     * @param null $floor_id — id этажа для внутренних вызовов
     * @throws \Exception
     */
    public function apartSelect($floor_id = null) {
        $ans = !empty($floor_id) ? $this->getAns() : $this->setJsonAns();
        $floor = ItemEntity::getById(!empty($floor_id) ? $floor_id : $this->request->request->get('id'), $this->segment['id']);
        if (empty($floor)) {
            if (!empty($floor_id)) {
                return $this->notFound();
            }
            $ans->setEmptyContent()
                ->addErrorByKey('id', \Models\Validator::ERR_MSG_EMPTY);
            return;
        } elseif ($floor->getType()['key'] != RealEstateConfig::CATEGORY_KEY_FLOOR) {
            if (!empty($floor_id)) {
                return $this->notFound();
            }
            $ans->setEmptyContent()
                ->addErrorByKey('id', \Models\Validator::ERR_MSG_INCORRECT);
            return;
        }
        $apart_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_FLAT, $floor->getType()->getCatalog()['id'], $this->segment['id']);
        $apartments = CatalogSearch::factory(self::DEFAULT_CATALOG_KEY, $this->segment['id'])
            ->setPublicOnly(true)
            ->setTypeId($apart_category['id'])
            ->setRules(array(Rule::make('parent_id')->setValue($floor['id'])))
            ->searchItems();
        $housing = $floor->getParent();
        $complex = $housing->getParent();
        if (empty($apartments->getSearch())) {
            return $this->notFound();
        }
        $ans
            ->add('complex', $complex)
            ->add('housing', $housing)
            ->add('floor', $floor)
            ->add('select_mode', 'apartment')
            ->add('apartments', $apartments->getSearch());
        if (empty($floor_id)) {
            // Урл прямой ссылки при аяксовом запросе
            $ans->addData('url', $complex->getUrl($this->segment['id']) . 'scheme/?floor=' . $floor['id']);
        }
    }

}
