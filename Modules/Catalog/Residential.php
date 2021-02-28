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

class Residential extends CatalogPublic
{
    const DEFAULT_CATALOG_KEY = CatalogConfig::CATALOG_KEY_RESIDENTIAL;
    const APARTMENTS_LIST_PAGE_SIZE = 20;
    const APARTMENTS_CONCURRENT_LIST_SIZE = 10;

    const PDF_TMP_DIR = 'data/pdf_presentation/';

    public function index() {

    }
    public function items() {
        $title_search = $this->request->query->get('title');
        $this->request->query->set('title', null);
        $order = $this->request->query->get('order');
        if (empty($order)) {
            // 1 значит что сортировка обчная, 0 - DESC
            $this->request->query->set('order', array(RealEstateConfig::KEY_APPART_PRICE => 1));
        }
        $resale_catalog = TypeEntity::getByKey(self::DEFAULT_CATALOG_KEY, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $search_params = \App\CatalogMethods::getSearchableRules($this->request, $resale_catalog['id'], array(), $this->segment['id']);
        $catalog = Catalog::factory(self::DEFAULT_CATALOG_KEY, $this->segment['id']);
        $ajax = $this->request->query->has('ajax');
        $quickView = $this->request->query->has('quickView');
        if ($quickView) {
            $ajax = true;
            $current_id = $this->request->query->get('current_id');
        }
        $ans = $ajax
            ? $this->setJsonAns()->setTemplate($quickView ? 'Modules/Catalog/Residential/quickView.tpl' : 'Modules/Catalog/Residential/apartmentsList.tpl')
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
        $items = CatalogSearch::factory(static::DEFAULT_CATALOG_KEY, $this->segment['id'])
            ->setRules($search_params)
            ->setPublicOnly(true)
            ->searchItems(($page - 1) * self::APARTMENTS_LIST_PAGE_SIZE, self::APARTMENTS_LIST_PAGE_SIZE);
        $item_properties = PropertyFactory::search($resale_catalog['id'], PropertyFactory::P_ITEMS, 'key', 'group', 'parents', array('visible' => CatalogConfig::V_PUBLIC_FULL), $this->segment['id']);
        $search_properties = $catalog->getSearchableProperties($resale_catalog['id'], 'public', null, array('filter_visible' => CatalogConfig::FV_PUBLIC), array(), 'type_group', TRUE);
        $price = [
            'min' => !empty($search_properties['close_price']['search_values']['min']) ? $search_properties['close_price']['search_values']['min'] : 0,
            'max' => !empty($search_properties['close_price']['search_values']['max']) ? $search_properties['close_price']['search_values']['max'] : 0
        ];
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
        $ans->add('search_params', $search_params)
            ->add('search_properties', $search_properties)
			->add('priceVals', $price)
            ->add('foreign_price', \App\CatalogMethods::getForeignPrice($search_properties))
            ->add('type_properties', !empty($item_properties) ? $item_properties : array())
            ->add('items', $items->getSearch())
            ->add('count', $items->getTotalCount())
            ->add('pageSize', self::APARTMENTS_LIST_PAGE_SIZE)
            ->add('search_string', !empty($search_string) ? $search_string : '')
            ->add('pageNum', $page)
            ->add('quick_view_current_id', !empty($current_id) ? $current_id : null);
    }

    public function viewItem() {
        $item_id = substr($this->routeTail, 1);
        $item = ItemEntity::getById($item_id, $this->segment['id']);
        if (empty($item)) {
            return $this->notFound();
        }
        $catalog = Catalog::factory(self::DEFAULT_CATALOG_KEY, $this->segment['id']);
        $concurrents = $catalog->getConcurrents($item->getType()['id'], $item, self::APARTMENTS_CONCURRENT_LIST_SIZE);
        $item_properties = PropertyFactory::search($item['type_id'], PropertyFactory::P_ITEMS, 'key', 'group', 'parents', array('visible' => CatalogConfig::V_PUBLIC_FULL), $this->segment['id']);
        $this->getAns()
            ->add('item', $item)
            ->add('similar_objects', $concurrents)
            ->add('type_properties', !empty($item_properties) ? $item_properties : array())
            ->add('form_type', FeedbackConfig::TYPE_VIEW_APARTMENTS)
            ->add('site_url', \LPS\Config::getParametr('Site', 'url'))
            ->add('lang', \Models\Lang::getInstance());
        if ($this->request->query->has('pdf')){
            if ($this->request->query->has('test') && $this->account->getRole() == 'SuperAdmin'){
                $this->setAjaxResponse('Modules/Catalog/Residential/apartmentPdf.tpl');
            } else {
                ini_set('allow_url_fopen', 1);
                require_once 'includes/dompdf/autoload.inc.php';
                $pdf = new \Dompdf\Dompdf();
                $pdf->getOptions()
                    ->set('enable_remote', true)
                    ->set('enable_css_float', true)
                    ->set('enable_html5_parser', true);
                $pdf->loadHtml($this->getAns()->setTemplate('Modules/Catalog/Residential/apartmentPdf.tpl', 'UTF-8')->getContent());
                $pdf->setPaper('a4', 'landscape');
                $pdf->render();
//                require_once 'vendor/dompdf/dompdf/dompdf_config.inc.php';
//
//                $pdf = new \DOMPDF();
//                $pdf->load_html($this->getAns()->setTemplate('Modules/Catalog/Residential/apartmentPdf.tpl', 'UTF-8')->getContent());
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

    public function request() {
        $id = $this->request->query->get('id');
        if (empty($id)) {
            return $this->notFound();
        } else {
            if (!is_array($id) || count($id) == 1) {
                $id = is_array($id) ? reset($id) : $id;
                $apartment = ItemEntity::getById($id, $this->segment['id']);
                if (empty($apartment) || $apartment->getType()->getCatalog()['key'] != CatalogConfig::CATALOG_KEY_RESIDENTIAL) {
                    return $this->notFound();
                }
                $this->getAns()
                    ->add('mode', 'resale_single')
                    ->add('apartment', $apartment);
            } else {
                $apartments = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESIDENTIAL, $this->segment['id'])
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