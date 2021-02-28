<?php
namespace Modules\Main;
use App\Configs\CatalogConfig;
use App\Configs\FeedbackConfig;
use App\Configs\RealEstateConfig;
use App\Configs\StaffConfig;
use Models\CatalogManagement\Catalog;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
use Models\CatalogManagement\PropertyExtension;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Rules\RuleAggregator;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type as TypeEntity;

class View extends \LPS\WebModule{
    const COMPLEX_LIST_SIZE = 10;
    const RESALE_LIST_SIZE = 20;
    const REALTY_SEARCH_PAGE_SIZE = 20;
    const AUTOCOMPLETE_PAGE_SIZE = 2;
    const SPECIAL_OFFERS_PAGE_SIZE = 20;

    private $special_offers_allow_order = array(
        RealEstateConfig::KEY_APPART_AREA_ALL,
        RealEstateConfig::KEY_APPART_BED_NUMBER,
        'complex_title'
    );

    private $favorites_order_fields = array(
        'title',
        RealEstateConfig::KEY_APPART_AREA_ALL,
        RealEstateConfig::KEY_APPART_FLOOR,
        RealEstateConfig::KEY_APPART_CLOSE_PRICE,
        RealEstateConfig::KEY_APPART_BED_NUMBER,
        RealEstateConfig::KEY_APPART_FLOORS,
        RealEstateConfig::KEY_APPART_WC_NUMBER
    );

    public function index()
    {
        $rm = $this->router->getRequestModule();
        if (is_callable(array($this, $rm))) {
            return $this->run($rm);
        }
        $real_estate_catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $complex_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_COMPLEX, $real_estate_catalog['id'], $this->segment['id']);
        $resale_catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_RESALE, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $residential_catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_RESIDENTIAL, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $real_estate_params = Catalog::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $this->segment['id'])
            ->getSearchableProperties($complex_category['id'], 'public', null, array('filter_visible' => CatalogConfig::FV_COMPLEX_LIST), array(), 'type_group', TRUE, TRUE);
        $resale_params = Catalog::factory(CatalogConfig::CATALOG_KEY_RESALE, $this->segment['id'])
            ->getSearchableProperties($resale_catalog['id'], 'public', null, array('filter_visible' => CatalogConfig::FV_PUBLIC), array(), 'type_group', TRUE);
        $residential_params = Catalog::factory(CatalogConfig::CATALOG_KEY_RESIDENTIAL, $this->segment['id'])
            ->getSearchableProperties($residential_catalog['id'], 'public', null, array('filter_visible' => CatalogConfig::FV_PUBLIC), array(), 'type_group', TRUE);
        $search_properties = array();
        foreach ($real_estate_params as $k => $v) {
            if (empty($resale_params[$v['key']])) {
                $search_properties[$v['key']] = $v;
            } elseif (empty($residential_params[$v['key']])) {
                $search_properties[$v['key']] = $v;
            } else {
                $prop = $v['property'];
                if ($prop['data_type'] == 'flag') {
                    $search_properties[$v['key']] = $v;
                } elseif ($prop['search_type'] == $resale_params[$v['key']]['search_type']) {
                    $search_values = array();
                    $search_objects = $v['search_objects'] + $resale_params[$v['key']]['search_objects'];
                    if ($prop['search_type'] == 'between') {
                        $search_values['min'] = (!empty($v['search_values']['min']) && !empty($resale_params[$v['key']]['search_values']['min']))
                            ? min($v['search_values']['min'], $resale_params[$v['key']]['search_values']['min'])
                            : (!empty($v['search_values']['min']) ? $v['search_values']['min'] : $resale_params[$v['key']]['search_values']['min']);
                        $search_values['max'] = max($v['search_values']['max'], $resale_params[$v['key']]['search_values']['max']);
                        $search_values['step'] = (!empty($v['search_values']['step']) && !empty($resale_params[$v['key']]['search_values']['step']))
                            ? min($v['search_values']['step'], $resale_params[$v['key']]['search_values']['step'])
                            : (!empty($v['search_values']['step']) ? $v['search_values']['step'] : $resale_params[$v['key']]['search_values']['step']);
                    } elseif (in_array($prop['search_type'], array('check', 'select'))) {
                        $search_values = $v['search_values'] + $resale_params[$v['key']]['search_values'];
                        asort($search_values);
                    }
                    $search_properties[$v['key']] = new PropertyExtension($prop, array('search_values' => $search_values, 'search_objects' => $search_objects), $this->segment['id']);
                } elseif ($prop['search_type'] == $residential_params[$v['key']]['search_type']) {
                    $search_values = array();
                    $search_objects = $v['search_objects'] + $residential_params[$v['key']]['search_objects'];
                    if ($prop['search_type'] == 'between') {
                        $search_values['min'] = (!empty($v['search_values']['min']) && !empty($residential_params[$v['key']]['search_values']['min']))
                            ? min($v['search_values']['min'], $residential_params[$v['key']]['search_values']['min'])
                            : (!empty($v['search_values']['min']) ? $v['search_values']['min'] : $residential_params[$v['key']]['search_values']['min']);
                        $search_values['max'] = max($v['search_values']['max'], $residential_params[$v['key']]['search_values']['max']);
                        $search_values['step'] = (!empty($v['search_values']['step']) && !empty($residential_params[$v['key']]['search_values']['step']))
                            ? min($v['search_values']['step'], $residential_params[$v['key']]['search_values']['step'])
                            : (!empty($v['search_values']['step']) ? $v['search_values']['step'] : $residential_params[$v['key']]['search_values']['step']);
                    } elseif (in_array($prop['search_type'], array('check', 'select'))) {
                        $search_values = $v['search_values'] + $residential_params[$v['key']]['search_values'];
                        asort($search_values);
                    }
                    $search_properties[$v['key']] = new PropertyExtension($prop, array('search_values' => $search_values, 'search_objects' => $search_objects), $this->segment['id']);
                }

                unset($resale_params[$v['key']]);
                unset($residential_params[$v['key']]);
            }
        }
        if (!empty($resale_params)) {
            $search_properties += $resale_params;
        }
        if (!empty($residential_params)) {
            $search_properties += $residential_params;
        }
        uksort($search_properties, function($a, $b) {
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
        $complex_list = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $this->segment['id'])
            ->setTypeId($complex_category['id'])
            ->setRules(array(Rule::make(RealEstateConfig::KEY_OBJECT_PRIORITY)->setOrder(true)))
            ->searchItems(0, self::COMPLEX_LIST_SIZE);
        $resale_list = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESALE, $this->segment['id'])
            ->setRules(array(Rule::make(RealEstateConfig::KEY_APPART_PRIORITY)->setOrder(true)))
            ->searchItems(0, self::RESALE_LIST_SIZE);
        $residential_list = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESIDENTIAL, $this->segment['id'])
            ->setRules(array(Rule::make(RealEstateConfig::KEY_APPART_PRIORITY)->setOrder(true)))
            ->searchItems(0, self::RESALE_LIST_SIZE);
        $this->getAns()
            ->add('search_properties', $search_properties)
            ->add('foreign_price', \App\CatalogMethods::getForeignPrice($search_properties))
            ->add('resale_list', $resale_list->getSearch())
            ->add('resale_count', $resale_list->getTotalCount())
            ->add('residential_list', $residential_list->getSearch())
            ->add('residential_count', $residential_list->getTotalCount())
            ->add('complex_list', $complex_list->getSearch())
            ->add('complex_count', $complex_list->getTotalCount());
    }
    
    public function privacy_policy() {}
    public function top100() {}

    public function realtysearch() {
        $phrase = $this->request->query->get('phrase');
        if (!empty($phrase)) {
            $search_manager = \Models\Search::getInstance();
            $phrase_url = $search_manager->getUrl($phrase);//ищем по предустановленным значениям
            $search_manager->log($phrase);
            if (!empty($phrase_url)){
//                $search_manager->log($phrase);
                return $this->redirect($phrase_url);
            }
            $url = \Models\Search::getInstance()->getUrl($phrase);
            if (!empty($url)) {
                return $this->redirect($url);
            }
            $resale = $this->resalesearch(true);
            $real_estate = $this->realestatesearch(true);
            $residential = $this->residentialsearch(true);

            if (empty($resale->count()) && $real_estate->count() == 1) {
                return $this->redirect($real_estate->getFirst()->getUrl($this->segment['id']));
            } elseif (empty($real_estate->count()) && $resale->count() == 1) {
                return $this->redirect($resale->getFirst()->getUrl($this->segment['id']));
            } elseif (empty($real_estate->count()) && empty($resale->count()) && $residential->count() == 1) {
                return $this->redirect($residential->getFirst()->getUrl($this->segment['id']));
            }

            $this->getAns()
                ->add('real_estate', $real_estate->getSearch())
                ->add('real_estate_count', $real_estate->getTotalCount())
                ->add('bedroom_count_filters', $real_estate->count() ? \App\CatalogMethods::getRealEstateFilters($real_estate->getItemIds(), $this->segment['id']) : array())
                ->add('resale', $resale->getSearch())
                ->add('resale_count', $resale->getTotalCount())
                ->add('residential', $residential->getSearch())
                ->add('residential_count', $residential->getTotalCount())
                ->add('pageNum', 1)
                ->add('pageSize', self::REALTY_SEARCH_PAGE_SIZE);
        } else {
            $this->getAns()
                ->add('real_estate', array())
                ->add('real_estate_count', 0)
                ->add('bedroom_count_filters', array())
                ->add('resale', array())
                ->add('resale_count', 0)
                ->add('residential', array())
                ->add('residential_count', 0)
                ->add('pageNum', 1)
                ->add('pageSize', self::REALTY_SEARCH_PAGE_SIZE);
        }
    }

    /**
     * @param bool|false $inner
     * @return \Models\CatalogManagement\Search\CatalogSearchItemsResult|void
     * @throws \Exception
     */
    public function realestatesearch($inner = false, $page_size = self::REALTY_SEARCH_PAGE_SIZE) {
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        $phrase = $this->request->query->get('phrase', $this->request->request->get('phrase'));
        $page = $inner ? 1 : $this->request->query->get('page', 1);
        if (!$inner && empty($phrase)) {
            $ans->addErrorByKey('phrase', \Models\Validator::ERR_MSG_EMPTY)
                ->setEmptyContent();
            return;
        }
        if ($page < 1 || !is_numeric($page)){
            $ans->addErrorByKey('page', \Models\Validator::ERR_MSG_INCORRECT)->setEmptyContent();
            return;
        }
        $kb_phrase = \LPS\Components\FormatString::keyboardLayout($phrase, 'both');
        $catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_COMPLEX, $catalog['id'], $this->segment['id']);
        $items = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $this->segment['id'])
            ->setTypeId($category['id'])
            ->setRules(array(
                RuleAggregator::make(RuleAggregator::LOGIC_OR, array(
                    Rule::make(RealEstateConfig::KEY_OBJECT_TITLE)->setValue($phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_OBJECT_TITLE_SEARCH)->setValue($phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_OBJECT_ADDRESS)->setValue($phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_OBJECT_TITLE)->setValue($kb_phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_OBJECT_TITLE_SEARCH)->setValue($kb_phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_OBJECT_ADDRESS)->setValue($kb_phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_OBJECT_PRIORITY)->setOrder(true)
                ))
            ))
            ->searchItems(($page - 1) * $page_size, $page_size);
        if ($inner) {
            return $items;
        } else {
            $ans->add('items', $items->getSearch())
                ->add('bedroom_count_filters', $items->count() ? \App\CatalogMethods::getRealEstateFilters($items->getItemIds(), $this->segment['id']) : array())
                ->add('count', $items->getTotalCount())
                ->add('pageNum', $page)
                ->add('pageSize', $page_size);
        }
    }


    /**
     * @param bool|false $inner
     * @return \Models\CatalogManagement\Search\CatalogSearchItemsResult|void
     */
    public function resalesearch($inner = false, $page_size = self::REALTY_SEARCH_PAGE_SIZE) {
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        $phrase = $this->request->query->get('phrase', $this->request->request->get('phrase'));
        $page = $inner ? 1 : $this->request->query->get('page', 1);
        if (!$inner && empty($phrase)) {
            $ans->addErrorByKey('phrase', \Models\Validator::ERR_MSG_EMPTY)
                ->setEmptyContent();
            return;
        }
        if ($page < 1 || !is_numeric($page)){
            $ans->addErrorByKey('page', \Models\Validator::ERR_MSG_INCORRECT)->setEmptyContent();
            return;
        }
        $kb_phrase = \LPS\Components\FormatString::keyboardLayout($phrase, 'both');
        $items = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESALE, $this->segment['id'])
            ->setRules(array(
                RuleAggregator::make(RuleAggregator::LOGIC_OR, array(
                    Rule::make(RealEstateConfig::KEY_APPART_ADDRESS)->setValue($phrase, Rule::SEARCH_LIKE),
//                    Rule::make(RealEstateConfig::KEY_APPART_OBJECT_ADDRESS)->setValue($phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_TITLE)->setValue($phrase, Rule::SEARCH_LIKE),
//                    Rule::make(RealEstateConfig::KEY_APPART_OBJECT_TITLE)->setValue($phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_ADDRESS)->setValue($kb_phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_TITLE)->setValue($kb_phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_PRIORITY)->setOrder(true)
                ))
            ))
            ->searchItems(($page - 1) * $page_size, $page_size);
        if ($inner) {
            return $items;
        } else {
            $ans->add('items', $items->getSearch())
                ->add('count', $items->getTotalCount())
                ->add('pageNum', $page)
                ->add('pageSize', $page_size);
        }
    }
	
	public function arendasearch($inner = false, $page_size = self::REALTY_SEARCH_PAGE_SIZE) {
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        $phrase = $this->request->query->get('phrase', $this->request->request->get('phrase'));
        $page = $inner ? 1 : $this->request->query->get('page', 1);
        if (!$inner && empty($phrase)) {
            $ans->addErrorByKey('phrase', \Models\Validator::ERR_MSG_EMPTY)
                ->setEmptyContent();
            return;
        }
        if ($page < 1 || !is_numeric($page)){
            $ans->addErrorByKey('page', \Models\Validator::ERR_MSG_INCORRECT)->setEmptyContent();
            return;
        }
        $kb_phrase = \LPS\Components\FormatString::keyboardLayout($phrase, 'both');
        $items = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESALE, $this->segment['id'])
            ->setRules(array(
                RuleAggregator::make(RuleAggregator::LOGIC_OR, array(
                    Rule::make(RealEstateConfig::KEY_APPART_ADDRESS)->setValue($phrase, Rule::SEARCH_LIKE),
//                    Rule::make(RealEstateConfig::KEY_APPART_OBJECT_ADDRESS)->setValue($phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_TITLE)->setValue($phrase, Rule::SEARCH_LIKE),
//                    Rule::make(RealEstateConfig::KEY_APPART_OBJECT_TITLE)->setValue($phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_ADDRESS)->setValue($kb_phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_TITLE)->setValue($kb_phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_PRIORITY)->setOrder(true)
                ))
            ))
            ->searchItems(($page - 1) * $page_size, $page_size, true);
        if ($inner) {
            return $items;
        } else {
            $ans->add('items', $items->getSearch())
                ->add('count', $items->getTotalCount())
                ->add('pageNum', $page)
                ->add('pageSize', $page_size);
        }
    }

    public function residentialsearch($inner = false, $page_size = self::REALTY_SEARCH_PAGE_SIZE) {
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        $phrase = $this->request->query->get('phrase', $this->request->request->get('phrase'));
        $page = $inner ? 1 : $this->request->query->get('page', 1);
        if (!$inner && empty($phrase)) {
            $ans->addErrorByKey('phrase', \Models\Validator::ERR_MSG_EMPTY)
                ->setEmptyContent();
            return;
        }
        if ($page < 1 || !is_numeric($page)){
            $ans->addErrorByKey('page', \Models\Validator::ERR_MSG_INCORRECT)->setEmptyContent();
            return;
        }
        $kb_phrase = \LPS\Components\FormatString::keyboardLayout($phrase, 'both');
        $items = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESIDENTIAL, $this->segment['id'])
            ->setRules(array(
                RuleAggregator::make(RuleAggregator::LOGIC_OR, array(
                    Rule::make(RealEstateConfig::KEY_APPART_ADDRESS)->setValue($phrase, Rule::SEARCH_LIKE),
//                  Rule::make(RealEstateConfig::KEY_APPART_OBJECT_ADDRESS)->setValue($phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_TITLE)->setValue($phrase, Rule::SEARCH_LIKE),
//                  Rule::make(RealEstateConfig::KEY_APPART_OBJECT_TITLE)->setValue($phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_ADDRESS)->setValue($kb_phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_TITLE)->setValue($kb_phrase, Rule::SEARCH_LIKE),
                    Rule::make(RealEstateConfig::KEY_APPART_PRIORITY)->setOrder(true)
                ))
            ))
            ->searchItems(($page - 1) * $page_size, $page_size);
        if ($inner) {
            return $items;
        } else {
            $ans->add('items', $items->getSearch())
                ->add('count', $items->getTotalCount())
                ->add('pageNum', $page)
                ->add('pageSize', $page_size);
        }
    }

    public function realtysearchautocomplete() {
        $ans = $this->setJsonAns();
        $phrase = $this->request->request->get('phrase');
        if (empty($phrase)) {
            $ans->setEmptyContent()
                ->addErrorByKey('phrase', \Models\Validator::ERR_MSG_EMPTY);
            return;
        }
        $resale = $this->resalesearch(true, self::AUTOCOMPLETE_PAGE_SIZE);
		$arenda = $this->arendasearch(true, self::AUTOCOMPLETE_PAGE_SIZE);
        $real_estate = $this->realestatesearch(true, self::AUTOCOMPLETE_PAGE_SIZE);
		$resd = $this->residentialsearch(true, self::AUTOCOMPLETE_PAGE_SIZE);
        $ans->add('phrase', $phrase)
            ->add('list_size', self::AUTOCOMPLETE_PAGE_SIZE)
            ->add('real_estate', $real_estate->getSearch())
            ->add('resale', $resale->getSearch())
			->add('arenda', $arenda->getSearch())
			->add('residential', $resd->getSearch())
            ->add('resale_count', $resale->getTotalCount())
			->add('arenda_count', $arenda->getTotalCount())
            ->add('real_estate_count', $real_estate->getTotalCount())
			->add('residential_count', $resd->getTotalCount());
    }

    public function special() {
        $gift_items = $this->specialItems(RealEstateConfig::KEY_APART_SPECIAL_OFFER_GIFT);
        $discount_items = $this->specialItems(RealEstateConfig::KEY_APART_SPECIAL_OFFER_DISCOUNT);
        $this->getAns()
            ->add('gift', $gift_items->getSearch())
            ->add('gift_count', $gift_items->getTotalCount())
            ->add('discount', $discount_items->getSearch())
            ->add('discount_count', $discount_items->getTotalCount())
            ->add('pageNum', 1)
            ->add('pageSize', self::REALTY_SEARCH_PAGE_SIZE);
    }

    /**
     * @param null $offer_type
     * @return \Models\CatalogManagement\Search\CatalogSearchItemsResult|void
     */
    public function specialItems($offer_type = null) {
        $inner = !empty($offer_type);
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        if (!$inner) {
            $offer_type = $this->request->request->get('type');
            if (!in_array($offer_type, array(RealEstateConfig::KEY_APART_SPECIAL_OFFER_DISCOUNT, RealEstateConfig::KEY_APART_SPECIAL_OFFER_GIFT))) {
                $ans->setEmptyContent()
                    ->addErrorByKey('type', empty($offer_type) ? \Models\Validator::ERR_MSG_EMPTY : \Models\Validator::ERR_MSG_INCORRECT);
                return;
            }
        } elseif (!in_array($offer_type, array(RealEstateConfig::KEY_APART_SPECIAL_OFFER_DISCOUNT, RealEstateConfig::KEY_APART_SPECIAL_OFFER_GIFT))) {
            throw new \LogicException('Неверный тип спецпредложения, допустимые значения: ' . implode(', ', array(RealEstateConfig::KEY_APART_SPECIAL_OFFER_DISCOUNT, RealEstateConfig::KEY_APART_SPECIAL_OFFER_GIFT)));
        }
        $page = $inner ? 1 : $this->request->request->get('page');
        if ($page < 1 || !is_numeric($page)){
            $ans->addErrorByKey('page', \Models\Validator::ERR_MSG_INCORRECT)->setEmptyContent();
            return;
        }
        $rules = array(
            RealEstateConfig::KEY_APPART_SPECIAL_OFFER
                => Rule::make(RealEstateConfig::KEY_APPART_SPECIAL_OFFER)->setValue($offer_type)->setSearchByEnumKey()
        );
        $order = $this->request->request->get('order');
        if ($inner || empty($order)) {
            $rules['sort'] = Rule::make(RealEstateConfig::KEY_APPART_AREA_ALL)->setOrder(false);
        } else {
            foreach($order as $field => $direction) {
                if (!in_array($field, $this->special_offers_allow_order)) {
                    $ans->setEmptyContent()
                        ->addErrorByKey('order', \Models\Validator::ERR_MSG_INCORRECT);
                    return;
                } else {
                    $rules['sort'] = Rule::make($field)->setOrder(!$direction);
                }
            }
        }
        $catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_FLAT, $catalog['id'], $this->segment['id']);
        $items = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $this->segment['id'])
            ->setTypeId($category['id'])
            ->setRules($rules)
            ->searchItems(($page - 1) * self::SPECIAL_OFFERS_PAGE_SIZE, self::SPECIAL_OFFERS_PAGE_SIZE);
        if ($inner) {
            return $items;
        } else {
            $ans->add('items', $items->getSearch())
                ->add('count', $items->getTotalCount())
                ->add('pageNum', $page)
                ->add('pageSize', self::SPECIAL_OFFERS_PAGE_SIZE);
        }
    }

    public function company() {}

    public function contacts() {
        $managers = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_STAFF_LIST, $this->segment['id'])
            ->setRules(array(
                Rule::make(StaffConfig::KEY_SHOW_IN_CONTACTS)->setValue(1)
            ))
            ->searchItems()
            ->getSearch();
        $this->getAns()
            ->add('managers', $managers);
    }

    public function favorites() {
        $ajax = $this->request->query->has('ajax');
        $ans = $ajax
            ? $this->setJsonAns('Modules/Main/View/favoritesInner.tpl')
            : $this->getAns();
        $order = $this->request->query->get('order');
        $resale_data = $this->account->getFavoriteData(CatalogConfig::CATALOG_KEY_RESALE);
        if (!empty($resale_data['entity_ids'])) {
            /** @var Rule[] $rules */
            $rules = array(
                'id' => Rule::make('id')->setValue($resale_data['entity_ids'])
            );
            if (empty($order) || isset($order['date'])) {
                $dates = !empty($resale_data['dates']) ? $resale_data['dates'] : array();
                if (empty($order) || !empty($order['date'])) {
                    arsort($dates);
                } else {
                    asort($dates);
                }
                $rules['id']->setOrder(array_keys($dates));
            } else {
                foreach($order as $field => $direction) {
                    if (in_array($field, $this->favorites_order_fields)) {
                        $rules[$field] = Rule::make($field)->setOrder(!$direction);
                    }
                }
            }
            $resale = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESALE, $this->segment['id'])
                ->setRules($rules)
                ->searchItems()
                ->getSearch();
        }
        $real_estate_data = $this->account->getFavoriteData(CatalogConfig::CATALOG_KEY_REAL_ESTATE);
        if (!empty($real_estate_data['entity_ids'])) {
            $real_estate_catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
            $apartments_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_FLAT, $real_estate_catalog['id'], $this->segment['id']);
            /** @var Rule[] $rules */
            $rules = array(
                'id' => Rule::make('id')->setValue($real_estate_data['entity_ids'])
            );
            if (empty($order) || isset($order['date'])) {
                $dates = !empty($real_estate_data['dates']) ? $real_estate_data['dates'] : array();
                if (empty($order) || !empty($order['date'])) {
                    arsort($dates);
                } else {
                    asort($dates);
                }
                $rules['id']->setOrder(array_keys($dates));
            } else {
                foreach($order as $field => $direction) {
                    if (in_array($field, $this->favorites_order_fields)) {
                        switch($field) {
                            case 'title':
                                $rules['complex_title'] = Rule::make('complex_title')->setOrder(!$direction);
                                break;

                            case RealEstateConfig::KEY_APPART_FLOOR:
                                $rules['floor_floor_number'] = Rule::make('floor_floor_number')->setOrder(!$direction);
                                break;

                            default:
                                $rules[$field] = Rule::make($field)->setOrder(!$direction);
                        }
                    }
                }
            }
            $real_estate = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $this->segment['id'])
                ->setTypeId($apartments_category['id'])
                ->setRules($rules)
                ->searchItems()
                ->getSearch();
        }
        $ans->add('resale', !empty($resale) ? $resale : array())
            ->add('real_estate', !empty($real_estate) ? $real_estate : array());
    }

    public function favoritesPopup() {
        $ans = $this->setJsonAns();
        $total_count = $this->account->getFavoriteCount(CatalogConfig::CATALOG_KEY_REAL_ESTATE) + $this->account->getFavoriteCount(CatalogConfig::CATALOG_KEY_RESALE);
        $ans->addData('count', $total_count);
        if (empty($total_count)) {
            $ans->setEmptyContent();
        } else {
            $ans->add('count', $total_count);
        }
    }

    public function service() {}

    public function top16() {
        $real_estate_catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $complex_type = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_COMPLEX, $real_estate_catalog['id'], $this->segment['id']);
        $real_estate = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $this->segment['id'])
            ->setTypeId($complex_type['id'])
            ->setRules(array(
                Rule::make(RealEstateConfig::KEY_OBJECT_TOP)->setValue(1),
                Rule::make(RealEstateConfig::KEY_OBJECT_PRIORITY)->setOrder(true),
                Rule::make(RealEstateConfig::KEY_OBJECT_TITLE)->setOrder(false)
            ))
            ->searchItems(0, 8)
            ->getSearch();
        $resale = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESALE, $this->segment['id'])
            ->setRules(array(
                Rule::make(RealEstateConfig::KEY_APPART_TOP)->setValue(1),
                Rule::make(RealEstateConfig::KEY_APPART_PRIORITY)->setOrder(true),
                Rule::make(RealEstateConfig::KEY_APPART_TITLE)->setOrder(false)
            ))
            ->searchItems(0, 8)
            ->getSearch();
        $this->getAns()
            ->add('t16_real_estate', $real_estate)
            ->add('t16_resale', $resale);
    }

    public function selection() {
        $districts = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_DISTRICT, $this->segment['id'])
            ->setPublicOnly(false)
            ->setRules(array(Rule::make(CatalogConfig::KEY_ITEM_TITLE)->setOrder(false)))
            ->searchItems()
            ->getSearch();
        $feedback_catalog = TypeEntity::getByKey(CatalogConfig::FEEDBACK_KEY, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $selection_category = TypeEntity::getByKey(FeedbackConfig::TYPE_FLAT_SELECTION, $feedback_catalog['id'], $this->segment['id']);
        $bed_number_prop = PropertyFactory::getSingleByKey(FeedbackConfig::KEY_SELECTION_BED_NUMBER, $selection_category['id'], 'self', $this->segment['id']);
        $bed_number_vals = array();
        foreach($bed_number_prop['values'] as $val) {
            $bed_number_vals[$val['key']] = $val['id'];
        }
        $this->getAns()
            ->add('form_type', FeedbackConfig::TYPE_FLAT_SELECTION)
            ->add('bed_number_vals', $bed_number_vals)
            ->add('districts', $districts);
    }

    public function owner() {
        $feedback_catalog = TypeEntity::getByKey(CatalogConfig::FEEDBACK_KEY, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $owner_category = TypeEntity::getByKey(FeedbackConfig::TYPE_OWNER, $feedback_catalog['id'], $this->segment['id']);
        $estate_type_prop = PropertyFactory::getSingleByKey(FeedbackConfig::KEY_OWNER_ESTATE_TYPE, $owner_category['id'], 'self', $this->segment['id']);
        $estate_type_vals = array();
        foreach($estate_type_prop['values'] as $val) {
            $estate_type_vals[$val['key']] = $val['id'];
        }
        $this->getAns()
            ->add('estate_type_vals', $estate_type_vals)
            ->add('form_type', FeedbackConfig::TYPE_OWNER);
    }

    public function favorites_request() {
        $ids = $this->request->query->get('id');
        if (empty($ids)) {
            return $this->notFound();
        }
        $resale = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESALE, $this->segment['id'])
            ->setRules(array(Rule::make('id')->setValue($ids), Rule::make('title')->setOrder(false)))
            ->searchItems()
            ->getSearch();
        $real_estate_catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        $apartment_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_FLAT, $real_estate_catalog['id'], $this->segment['id']);
        $real_estate = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE, $this->segment['id'])
            ->setTypeId($apartment_category['id'])
            ->setRules(array(Rule::make('id')->setValue($ids), Rule::make('complex_title')->setOrder(false)))
            ->searchItems()
            ->getSearch();
        if (empty($resale) && empty($real_estate)) {
            return $this->notFound();
        }
        $this->getAns()
            ->add('form_type', FeedbackConfig::TYPE_APART_REQUEST)
            ->add('real_estate', $real_estate)
            ->add('resale', $resale);
    }

    public function checkAuth(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $user = $this->account->getUser();
        if (empty($user)){
            $ans->addData('auth', 'no');
        } else {
            $ans->addData('auth', 'yes');
        }
    }

    public function clearFavorites() {
        $this->getModule('Catalog\RealEstate')->clearFavorites();
        return $this->getModule('Catalog\Resale')->clearFavorites();
    }
    /**
     * ajax-ом забираем адрес
     */
    public function getAddress(){
        $this->setJsonAns()->setEmptyContent();
        $this->getAns()->addData(\App\Configs\ContactsConfig::KEY_OFFICE_ADDRESS, \App\Builder::getInstance()->getSiteConfig($this->segment['id'])->get(\App\Configs\ContactsConfig::KEY_OFFICE_ADDRESS, CatalogConfig::CONFIG_CONTACTS_KEY));
    }
}