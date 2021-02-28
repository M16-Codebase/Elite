<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 14.09.15
 * Time: 20:52
 */

namespace Modules\Catalog;


use App\Configs\CatalogConfig;
use App\Configs\FeedbackConfig;
use App\Configs\RealEstateConfig;
use App\Configs\Settings;
use Models\CatalogManagement\Catalog;
use Models\CatalogManagement\Item as ItemEntity;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Rules\RuleAggregator;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type as TypeEntity;
use Models\CatalogManagement\Filter\FilterMap;
use Models\CatalogManagement\CatalogHelpers\District\DistrictHelper;
use Models\CatalogManagement\Filter\FilterSeoItem;
use Models\CatalogManagement\Filter\FilterMapHelper;
use Models\CatalogManagement\SeoElements\SeoLinks;

class Arenda extends CatalogPublic
{
    const DEFAULT_CATALOG_KEY = CatalogConfig::CATALOG_KEY_RESALE;
    const APARTMENTS_LIST_PAGE_SIZE = 20;
    const APARTMENTS_CONCURRENT_LIST_SIZE = 10;
    const FILTER_MAIN_PART_SIZE = 5;

    const PDF_TMP_DIR = 'data/pdf_presentation/';

    public function index() {

    }
    public function items() {
        $filterHelper = FilterMapHelper::getInstance();
        $request = $this->request->getRequestUri();
		$request=explode('&',$request);
		$request=$request[0];
        $request_params = $this->cleanrequestUri($request, 'arenda');

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

        if (isset($_REQUEST['debug'])) {
            
        }

        $title_search = $this->request->query->get('title');
        $this->request->query->set('title', null);
        $order = $this->request->query->get('order');

        //dump($order);
		
		
        if (empty($order)) {
            $this->request->query->set('order', array(RealEstateConfig::KEY_APPART_PRICE => 0));
        }

        $resale_catalog = TypeEntity::getByKey(self::DEFAULT_CATALOG_KEY, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $search_params = \App\CatalogMethods::getSearchableRules($this->request, $resale_catalog['id'], array(), $this->segment['id']);

        $catalog = Catalog::factory(self::DEFAULT_CATALOG_KEY, $this->segment['id']);
		
		
		
        //dump($search_params);
        $ajax = $this->request->query->has('ajax');
        $quickView = $this->request->query->has('quickView');
        if ($quickView) {
            $ajax = true;
            $current_id = $this->request->query->get('current_id');
        }
        $ans = $ajax
            ? $this->setJsonAns()->setTemplate($quickView ? 'Modules/Catalog/Arenda/quickView.tpl' : 'Modules/Catalog/Arenda/apartmentsList.tpl')
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
                return $this->redirect($this->getModuleUrl() . (!empty($params) ? '?' . http_build_query($params) : ''));
            }
        }
        if (!empty($title_search)) {
            $kb_phrase = \LPS\Components\FormatString::keyboardLayout($title_search, 'both');
            $search_params[] = RuleAggregator::make(RuleAggregator::LOGIC_OR, array(
                    Rule::make(RealEstateConfig::KEY_APPART_ADDRESS)->setValue($title_search, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_ADDRESS)->setValue($kb_phrase, Rule::SEARCH_LIKE)
                )
            );
        }

        $items = CatalogSearch::factory(static::DEFAULT_CATALOG_KEY, $this->segment['id'],1)
            ->setRules($search_params)
            ->setPublicOnly(true)
            ->searchItems(($page - 1) * self::APARTMENTS_LIST_PAGE_SIZE, self::APARTMENTS_LIST_PAGE_SIZE);

        $items_list_for_seo_links = CatalogSearch::factory(static::DEFAULT_CATALOG_KEY, $this->segment['id'])
                        ->setPublicOnly(true)
                        ->searchItems();
						
						


        $item_properties = PropertyFactory::search($resale_catalog['id'], PropertyFactory::P_ITEMS, 'key', 'group', 'parents', array('visible' => CatalogConfig::V_PUBLIC_FULL), $this->segment['id']);
        $search_properties = $catalog->getSearchableProperties($resale_catalog['id'], 'public', null, array('filter_visible' => CatalogConfig::FV_PUBLIC), array(), 'type_group', TRUE);
        //var_dump(empty($search_properties));
        $price = [
            'min' => !empty($search_properties['close_price']['search_values']['min']) ? $search_properties['close_price']['search_values']['min'] : 0,
            'max' => !empty($search_properties['close_price']['search_values']['max']) ? $search_properties['close_price']['search_values']['max'] : 0
        ];
		$itemi=$items->getSearch();
		if(!$itemi) { http_response_code(404); echo '<div style="width: 100%; height: 100%;position: absolute; top: 0; left: 0;display:flex;flex-direction:column;align-items:center;justify-content:center;"><h1>Объекты не найдены</h1><a href="#" style="margin-top:30px;" onclick="goBack()">Назад</a></div><script>function goBack() {  window.history.back(); }</script>'; exit(); }
		$itemmxmn=array();
		foreach($itemi as $value){

			//$value['properties']['price']['value']);
			if(isset($value['properties']['price'])){
				array_push($itemmxmn,$value['properties']['price']['value']);
			}
			else{
				http_response_code(404); echo '<div style="width: 100%; height: 100%;position: absolute; top: 0; left: 0;display:flex;flex-direction:column;align-items:center;justify-content:center;"><h1>Критическая ошибка, обратитесь к разработчику</h1><p>В списке найдены недопустимые объекты либо отсутсвуют цены.</p><a href="#" style="margin-top:30px;" onclick="goBack()">Назад</a></div><script>function goBack() {  window.history.back(); }</script>'; exit();
			}
		}
		
		if($itemmxmn){
			$price['max']=max($itemmxmn);
			$price['min']=min($itemmxmn);
		}
        uksort($search_properties, function($a, $b) {
            $keys = array(
                'district' => 1,
                'bed_number' => 2,
                'area_all' => 3,
                'close_price' => 4,
                'typerk' => 5
            );
            $a_pos = !empty($keys[$a]) ? $keys[$a] : 666;
            $b_pos = !empty($keys[$b]) ? $keys[$b] : 666;
            return $a_pos > $b_pos;
        });
        //dump($search_properties);
		
		

        $main_filter_part_props = $search_properties;
		/*$additional_filter_part_props = array();
		$counter = 1;
		foreach ($search_properties as $key => $search_property) {
			if ($counter <= CatalogConfig::FILT_MAIN_PRT_ELEMS_CNT_RESALE) {
				$main_filter_part_props[$key] = $search_property;
			} else {
				$additional_filter_part_props[$key] = $search_property;
			}
			$counter ++;
		}*/
		
		

        $districtsList = DistrictHelper::getInstance()->getDistrictsKeysList();
		
        //dump($districtsList);

        // сгенерить случайные ссылки
        $seo_links_dinamyc = SeoLinks::getInstance()->generateSeoLinks($items_list_for_seo_links->getSearch(), [],
            self::DEFAULT_CATALOG_KEY);
        $seo_links ['dinamyc'] = !empty($seo_links_dinamyc) ? $seo_links_dinamyc : [];
        $static_seo_links = SeoLinks::getInstance()->getStaticSeoLinks(self::DEFAULT_CATALOG_KEY);
        //dump($static_seo_links);
        $seo_links ['static'] = !empty($static_seo_links) ? $static_seo_links : [];

		//print_r($price);



        $ans->add('search_params', $search_params)
            //->add('districtList', $search_properties['district']["search_values"])
            ->add('seoLinks', $seo_links)
            ->add('districtList', $districtsList)
            ->add('priceVals', $price)
			->add('priceValsMx', max($price))
			->add('priceValsMn', min($price))
            ->add('search_properties_count', count($search_properties))
            ->add('main_search_properties', $main_filter_part_props)
            //->add('additional_search_properties', $additional_filter_part_props)
            ->add('foreign_price', \App\CatalogMethods::getForeignPrice($search_properties))
            ->add('type_properties', !empty($item_properties) ? $item_properties : array())
            ->add('items', $items->getSearch())
            ->add('count', $items->getTotalCount())
            ->add('pageSize', self::APARTMENTS_LIST_PAGE_SIZE)
            ->add('search_string', !empty($search_string) ? $search_string : '')
            ->add('catalogKey', self::DEFAULT_CATALOG_KEY)
            ->add('for_catalog', $for_catalog)
            ->add('pageNum', $page)
            ->add('quick_view_current_id', !empty($current_id) ? $current_id : null);

        if (!empty($canonical_uri)) {
            $ans->add('canonical', $canonical_uri);
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

    /**
     * Generate random list of links for filter by friendly url
     * for publish that in content for indexing
     *
     * @param array $items
     * @return array
     */
    protected function generateSeoLinks(array $items)
    {
        $links = [];
        $this->dBrLinks($items, $links);
        $this->distLinks($items, $links);
        $links = array_map("unserialize", array_unique(array_map("serialize", $links)));
        foreach ($links as & $link) {
            $link['href'] = '/'.$link['href'];
        }
        return $links;
    }

    /**
     * Generate for district and bed_number links
     *
     * @param array $items
     * @return array
     */
    protected function dBrLinks(array $items, & $links)
    {
        $count = (count($items) > 10) ? 10 : count($items);
        $indexes = array_rand ( $items, $count );
        if (is_array($indexes)) {
            foreach ($indexes as $index) {
                $it = $items[$index];
                $this->addSeoLink($it, $links);
            }
        } else {
            $it = $items[$indexes];
            $this->addSeoLink($it, $links);
        }
        return;
    }

    protected function distLinks(array $items, & $links)
    {
        $count = 2;
        $indexes = array_rand ( $items, $count );
        if (is_array($indexes)) {
            foreach ($indexes as $index) {
                $link = [
                    'href' => self::DEFAULT_CATALOG_KEY . '/' . $items[$index]['district']['key'],
                    'text' => 'Квартиры ' . $items[$index]['district']["prepositional"]
                ];
                $links[] = $link;
            }
        } else {
            $link = [
                'href' => self::DEFAULT_CATALOG_KEY . '/' . $items[$indexes]['district']['key'],
                'text' => 'Квартиры ' . $items[$indexes]['district']["prepositional"]
            ];
            $links[] = $link;
        }
        return;
    }

    /**
     * To avoid duplicate code, this function
     *
     * @param ItemEntity $item
     * @param $links
     */
    private function addSeoLink(\Models\CatalogManagement\Item $item, & $links)
    {
        $bed_number = $this->filterMap->getBedNumberKey($item['bed_number']);
        $bedNumWord = FilterMapHelper::getInstance()->word_analog($item['bed_number']);
        $bedNumWord = mb_strtoupper(mb_substr($bedNumWord, 0, 1)) . mb_substr($bedNumWord, 1);
        $link = [
            'href' => self::DEFAULT_CATALOG_KEY . '/' . $item['district']['key'] . '__' . $bed_number,
            'text' => $bedNumWord . 'комнатные квартиры ' . $item['district']['prepositional']
        ];
        $links[] = $link;
        return;
    }


    public function viewItem() {

        global $item_id;
		$item_id = substr($this->routeTail, 1);
        $item = ItemEntity::getById($item_id, $this->segment['id']);
        if (empty($item)) {
            return $this->notFound();
        }
        //dump($item);
        $catalog = Catalog::factory(self::DEFAULT_CATALOG_KEY, $this->segment['id']);
        $concurrents = $catalog->getConcurrents($item->getType()['id'], $item, self::APARTMENTS_CONCURRENT_LIST_SIZE);
        $item_properties = PropertyFactory::search($item['type_id'], PropertyFactory::P_ITEMS, 'key', 'group', 'parents', array('visible' => CatalogConfig::V_PUBLIC_FULL), $this->segment['id']);
        //dump($item_properties);
		include('/var/www/estate/data/www/m16-elite.ru/templates/base/Admin/components/rprc_get.php');
        $this->getAns()
            ->add('item', $item)
            ->add('similar_objects', $concurrents)
            ->add('type_properties', !empty($item_properties) ? $item_properties : array())
            ->add('form_type', FeedbackConfig::TYPE_VIEW_APARTMENTS)
            ->add('site_url', \LPS\Config::getParametr('Site', 'url'))
			->add('rent_price', $rpc)
            ->add('lang', \Models\Lang::getInstance());
        if ($this->request->query->has('pdf')){
            if ($this->request->query->has('test') && $this->account->getRole() == 'SuperAdmin'){
                $this->setAjaxResponse('Modules/Catalog/Resale/apartmentPdf.tpl');
            } else {
                ini_set('allow_url_fopen', 1);
                require_once 'includes/dompdf/autoload.inc.php';
                $pdf = new \Dompdf\Dompdf();
                $pdf->getOptions()
                    ->set('enable_remote', true)
                    ->set('enable_css_float', true)
                    ->set('enable_html5_parser', true);
                $pdf->loadHtml($this->getAns()->setTemplate('Modules/Catalog/Resale/apartmentPdf.tpl', 'UTF-8')->getContent());
                $pdf->setPaper('a4', 'landscape');
                $pdf->render();
//                require_once 'vendor/dompdf/dompdf/dompdf_config.inc.php';
//
//                $pdf = new \DOMPDF();
//                $pdf->load_html($this->getAns()->setTemplate('Modules/Catalog/Resale/apartmentPdf.tpl', 'UTF-8')->getContent());
//                $pdf->set_paper('a4', 'landscape');
//                $pdf->set_option('enable_remote', true);
//                $pdf->set_option('enable_css_float', true);
//                $pdf->render();
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
                return $this->downloadFile($path . $file_name,
                    $item[RealEstateConfig::KEY_APPART_TITLE] . ' ' . $site_config[Settings::KEY_COMPANY_NAME] . '.pdf');
//                    $pdf->stream($file_name);
//                }
//                var_dump($this->response);
//                return $this->response;
            }
        }
    }
public function getArda($id){
			$mysqli = new mysqli('localhost', 'eliteman', 'eliteman', 'elite3');
			if ($mysqli->connect_errno) {
				printf("Connect failed: %s\n", $mysqli->connect_error);
				exit();
			}
			if($result = $mysqli->query("SELECT `rent_price` FROM `elite3`.`items` WHERE `id` = '$id'")){
				$row = $result->fetch_array(MYSQLI_NUM);
				$rtprc = $row[0];
			}else{
				echo("eror: ".$mysqli->error);
			}
			$mysqli->close();
			return $rtprc;
		}

    public function request() {

        $id = $this->request->query->get('id');

        if (empty($id)) {
            return $this->notFound();
        } else {
            if (!is_array($id) || count($id) == 1) {
                $id = is_array($id) ? reset($id) : $id;
                $apartment = ItemEntity::getById($id, $this->segment['id']);
                if (empty($apartment) || $apartment->getType()->getCatalog()['key'] != CatalogConfig::CATALOG_KEY_RESALE) {
                    return $this->notFound();
                }
                $this->getAns()
                    ->add('mode', 'resale_single')
                    ->add('apartment', $apartment);
            } else {
                $apartments = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESALE, $this->segment['id'])
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
                    ->add('mode', 'resale_list')
                    ->add('apartments', $apartments);
            }
        }
        $this->getAns()
            ->add('form_type', FeedbackConfig::TYPE_APART_REQUEST);
    }

}
