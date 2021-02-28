<?php
/**
 * Description of Main
 *
 * @author olga
 */
namespace Modules\Catalog;
use App\Configs\CatalogConfig;
use App\Configs\ReviewConfig;
use App\Configs\SphinxConfig;
use Models\CatalogManagement\Catalog;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type AS TypeEntity;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\CatalogManagement\Item AS ItemEntity;
use Models\CatalogManagement\Variant;
use App\Auth\Account\Admin AS AccountAdmin;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Rules\RuleAggregator;
use Models\SphinxManagement\SphinxSearch;

class Main extends CatalogPublic{
    const DEFAULT_CATALOG_KEY = CatalogConfig::CATALOG_KEY;
    const MAP_PAGE_SIZE = 10;
    const PAGE_SIZE = 10;
    const PAGE_SEARCH_SIZE = 12;
    const REVIEW_PAGE_SIZE = 10;
    const AUTOCOMPLETE_LIST_SIZE = 15;
    const LENGTH_CONCURRENT_ITEMS_BLOCK = 4;
    const LENGTH_ASSOC_ITEMS_BLOCK = 5;
    const ITEMS_VIEWER_SIZE = 8;
	const GROUP_PAGE_SIZE = 10;
	const UNGROUP_PAGE_SIZE = 10;

    const REVIEW_MAILS_LIST = 'review';
    const QUESTIONS_MAILS_LIST = 'question';

    /**
     * Составляем "хлебные крошки"
     * @param int $type_id
     * @param int $segment_id
     * @return \Models\CatalogManagement\Type
     * @TODO избавиться
     */
    public static function getPath($type_id = null, $segment_id = NULL){
        return \App\Blocks::getInstance()->get('path', array('type_id' => $type_id), $segment_id);
    }
	protected $endRouterTail = NULL;
    
    /**
     * Возвращает рандомные товары со значением = 1 заданного свойства (требуется для определения новинок и хитов продаж)
     * public для доступа из других модулей, или для аякса
     * @param string|null $key
     * @param bool $inner
     * @param int $type_id
     * @return Item[]
     * @throws \LogicException
     */
    public function randItems($key = null, $inner = false, $type_id = 1, $limit = 0){
//        if (empty($key) && $inner){
//            throw new \LogicException('Надо задать ключ, по которому будет производиться поиск');
//        }
        if(!$inner){
            $key = $this->request->request->get('key');
//            if (empty($key)){
//                return '';
//            }
            $type_id = $this->request->request->get('type_id');
            if (empty($type_id)){
                return '';
            }
        }
        if (empty($limit)){
            $limit = $this->request->request->get('limit', self::ITEMS_VIEWER_SIZE);
        }
        $rules = array();
        if (!empty($key)){
            if ($key == 'sale'){
                $rule = Rule::make(CatalogConfig::KEY_ITEM_PRICE);
                $rule->setExists();
            }else{
                $rule = Rule::make($key);
                $rule->setValue(1);
            }
            $rules[] = $rule;
        }
        $rules[] = Rule::make('status')->setValue(array(ItemEntity::S_PUBLIC));
        $rules[] = Rule::make(CatalogConfig::KEY_ITEM_VISIBLE)->setValue(array(CatalogConfig::VALUE_VISIBLE_ANY, CatalogConfig::VALUE_VISIBLE_SITE))->setSearchByEnumValue();
        $segment = \App\Segment::getInstance()->getDefault(true);
        $items = CatalogSearch::factory(self::DEFAULT_CATALOG_KEY, $segment['id'])->setTypeId($type_id)->setRules($rules)
            ->setSortMode(CatalogSearch::SORT_RANDOM)->setPublicOnly($this->account instanceof AccountAdmin ? 0 : 1)
            ->setEnableCountByTypes(FALSE)->searchItems(0, $limit);
        if (!$inner){
            $this->setAjaxResponse();
            $this->getAns()->add('items', $items)->add('from_ajax', true);
        }else{
            return $items;
        }
    }

    public function index(){
        if (!empty($this->routeTail)) {
            $type = TypeEntity::getById($this->routeTail, $this->segment['id']);
        } else {
            $type = TypeEntity::getByKey(static::DEFAULT_CATALOG_KEY, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        }
        if (empty($type)) {
            return $this->notFound();
        }
        $types = $type->getChildren();
//		if (!empty($types)){
//			$t = reset($types);
//			return $this->redirect($t->getUrl());
//		}
        $countByTypes = array();
        $this->getAns()->add('types_children', $types)
            ->add('count_by_type', $countByTypes)
            ->add('current_type', $type)
            ->add('product_menu_list', self::getPath($type['id']));
    }

    public function items(){
        if ($this->request->query->has('print')){
            $this->setAjaxResponse('Modules/Catalog/Main/printItems.tpl');
        }
		$only_count = $this->request->query->get('only_count');
        $ajax = $this->request->query->get('ajax');
        if ($ajax){
            $ajax_returned_errors = array();
        }
        $type_id = $this->routeTail;
        if ($type_id == TypeEntity::DEFAULT_TYPE_ID){
            return $this->redirect($this->getModuleUrl());
        }
        \Models\CatalogManagement\CatalogHelpers\Type\AdditionalFields::factory();
        $type = TypeEntity::getById($type_id, $this->segment['id']);
        if (empty($type)){
            if ($ajax){
                $ajax_returned_errors = 'Не найден тип товара. ID ' . $type_id;
            }else{
                return $this->notFound();
            }
        }
        $segment = \App\Segment::getInstance()->getDefault(true);
        if (trim($this->getModuleUrl() . $this->route, '/') != trim($type->getUrl(), '/') && trim($this->getModuleUrl() . $this->route, '/') != trim($type->getUrl() . $this->endRouterTail, '/') ){
//            return $this->redirect($type->getUrl());//если урл неправильный
        }
        $page = $this->request->query->get('page', 1);
        $params = $this->request->query->all();
        if ($page < 1 || !is_numeric($page)){
            $params['page'] = 1;
            if ($ajax){
                $ajax_returned_errors = 'Неверно задан номер страницы: ' . $page;
            }else{
                return $this->redirect($this->getModuleUrl() . $type['id'] . '/', $params);
            }
        }
		$sort_user_param = $this->request->query->get('sort');
		if (empty($sort_user_param)){
            /* @todo после решения проблем с сортировкой вернуть */
			$this->request->query->set('sort', array(/*CatalogConfig::KEY_ITEM_AVAILABLE_STATUS => 1, */CatalogConfig::KEY_ITEM_TITLE => 1));
		}
        //фильтрация параметров, по которым производится поиск
        $search_params = \App\CatalogMethods::getSearchableRules($this->request, $type_id, array(), $this->segment['id']);
        $count = 0;
        $catalog = Catalog::factory(self::DEFAULT_CATALOG_KEY, $segment['id']);
        $limit = self::PAGE_SIZE;
        $start = ($page-1)*$limit;
		if (!empty($only_count)){
            $result = CatalogSearch::factory(CatalogConfig::CATALOG_KEY, $segment['id'])
                ->setTypeId($type_id)
                ->setRules($search_params)
                ->searchItemIds($start, $limit);
            return $result['total_count'];
		}
		$this->request->query->set('type_id', $type_id);
        $searchable_properties = $catalog->getSearchableProperties($type['id'], 'public', null, array('filter_visible' => CatalogConfig::FV_PUBLIC), array(), 'type_group', TRUE);
		$this->itemsList(TRUE, self::PAGE_SIZE);
        $this->getAns()
			->add('current_type', $type)
			->add('search_params', $search_params)
			->add('search_properties', $searchable_properties)
            ->add('foreign_price', \App\CatalogMethods::getForeignPrice($searchable_properties))
		;
		if ($ajax){
			$this->getAns()->setTemplate('Modules/Catalog/Main/itemsList.tpl');
		}
    }
	
	public function printItems(){
		$this->setAjaxResponse();
	}
    
    public function favorites(){
        $segment = \App\Segment::getInstance()->getDefault(true);
        $today = strtotime(date('Y-m-d'));
        $viewed_list = \Models\CatalogManagement\ViewedItems::getInstance()->getList();
        $viewed_items = array();
        $item_ids = array();
        foreach($viewed_list as $item){
            $item_ids[] = $item['id'];
        }
        $items = ItemEntity::factory($item_ids, $segment['id']);
        foreach($viewed_list as $item){
            $days = (int)ceil( ($today - $item['time']) / 86400 );
            $viewed_items[] = array(
                'days' => $days,
                'item' => $items[$item['id']]
            );
        }
        $this->getAns()
            ->add('viewed_items', $viewed_items);
    }
	
	public function cleanFavorites(){
		$this->setJsonAns()->setEmptyContent();
		$this->account->cleanFavorites(); /** @TODO это работает? если нет - починить. */
		$this->getAns()->addData('status', 'ok');
	}
    
    public function sendmail(){
        $variant_ids = $this->request->request->get('variants', $this->request->request->get('check'));
		$vs = (!empty($variant_ids)) ? $variant_ids : array();
        $items = array();
        $favorites = $this->account->getFavorites();
        foreach ($vs as $variant_id){
            $variant = Variant::getById($variant_id);
            $item_id = $variant['item_id'];
            $items[$item_id] = $item_id;
        }
        $mail = new \LPS\Container\WebContentContainer('mails/offersRequest.tpl');
        $mail->add('variants', $vs)->add('items', $items)->add('favorites', $favorites);
        \Models\Email::send($mail, array('hokum.gru@gmail.com'=>'Charles Manson'));
        
        return '{errors: false}';
    }
    /**
     * Просмотр айтема
     * @return type
     */
    public function viewItem(){
        $favorites = $this->account->getFavoriteData(static::DEFAULT_CATALOG_KEY);
        $session = \App\Builder::getInstance()->getCurrentSession();
        $segment = \App\Segment::getInstance()->getDefault(true);
        $rout = explode('/', $this->routeTail);
        $id = !empty($rout[0]) ? str_replace('i', '', $rout[0]) : null;
        $request_variant_id = null;
        $request_tab = 'view';
        if (!empty($rout[1]) && preg_match('~^v([0-9]*)$~', $rout[1], $route_data)){
            $request_variant_id = $route_data[1];
        }elseif(!empty($rout[1]) && empty($rout[2])){
            $request_tab = $rout[1];
        }
        $item = ItemEntity::getById($id, $segment['id']);
        if (empty($item)){
            return $this->notFound();
		}
//        if (!in_array($item['status'], array(ItemEntity::S_PUBLIC, ItemEntity::S_TEMPORARY_HIDE))) {
//            return $this->notFound();
//        }
        \Models\CatalogManagement\ViewedItems::getInstance()->addItem($id);
        $type = TypeEntity::getById($item['type_id'], $segment['id']);
        if (empty($type)){
            return $this->notFound();
        }
        //если всё ок, запихиваем id в POST данные для использования в методах вкладок
        $this->request->request->set('item_id', $id);
        $this->request->request->set('tab', $request_tab);
        $this->request->request->set('variant_id', $request_variant_id);
		$this->getAns()
            ->add('catalog_item', $item)
            ->add('current_type', $type)
            ->add('product_menu_list', self::getPath($item['type_id']))
            ->add('compare_vars', $session->get('compare'))
            ->add('favorites', $favorites['entity_ids']);
		$result = $this->inset(TRUE);
		$allGetParams = $this->request->query->all('print');
		if (isset($allGetParams['print'])){
			return $this->run('printItem');
		}
        return $result;
    }
    /**
     * недавно просмотренные айтемы
     */
    public function viewedItems(){
        $segment = \App\Segment::getInstance()->getDefault(true);
        $today = strtotime(date('Y-m-d'));
        $viewed_list = \Models\CatalogManagement\ViewedItems::getInstance()->getList();
        $viewed_items = array();
        $item_ids = array();
        foreach($viewed_list as $item){
            $item_ids[] = $item['id'];
        }
        $items = ItemEntity::factory($item_ids, $segment['id']);
        foreach($viewed_list as $item){
            $days = (int)ceil( ($today - $item['time']) / 86400 );
            $viewed_items[] = array(
                'days' => $days,
                'item' => $items[$item['id']]
            );
        }
        $this->getAns()
            ->add('viewed_items', $viewed_items)
            ->add('favorites', $this->account->getFavorites($segment['id']));
    }
    /************** Вкладки  **********/

    public function inset($inner = FALSE){
        if (!$inner){
            $this->setAjaxResponse();
        }
        $insetName = $this->request->request->get('tab');
        $method = 'inset' . ucfirst($insetName);
        if (!method_exists($this, $method)){
            return $this->notFound();
        }
        $return = $this->$method($inner);
        return $return;
    }
    /**
     * Вкладка просмотра айтема
     * @param type $inner
     * @return type
     * @throws \LogicException
     */
    private function insetView($inner = FALSE){
        if (!$inner){
            $this->getAns()->setTemplate('Modules/Catalog/Main/inset/view.tpl');
        }
		$segment = \App\Segment::getInstance()->getDefault(true);
        $item_id = $this->request->request->get('item_id');
        $item = ItemEntity::getById($item_id, $segment['id']);
        if (empty($item)){
            throw new \LogicException('Не найден товар с id ' . $item_id);
        }
        $item_properties = PropertyFactory::search($item['type_id'], PropertyFactory::P_ITEMS, 'key', 'group', 'parents', array('visible' => CatalogConfig::V_PUBLIC_FULL), $segment['id']);
        $special_groups = array();
//        foreach ($item_properties as $prop){
//            if (in_array($prop['group_key'], CatalogConfig::$special_groups)){
//                if (isset($item['properties'][$prop['key']]) && $item['properties'][$prop['key']]['value'] == 1){
//                    $special_groups[$prop['group_key']][$prop['key']] = $prop;
//                }
//                unset($item_properties[$prop['key']]);
//            }
//        }
		$sp = $item->getSegmentProperties($segment['id']);
        //проверяем верность запрашиваемых данных
        $variants = $item->getVariants();
        $request_variant_id = $this->request->request->get('variant_id');
        if (empty($variants) || (!empty($request_variant_id) && empty($variants[$request_variant_id]))){
            return $this->notFound();
        }
        //похожие товары
        $item_statuses = array(ItemEntity::S_PUBLIC);
        if ($this->account->isPermission('catalog-item', 'changeHidden')){
            $item_statuses[] = ItemEntity::S_HIDE;
        }
		$rules = array();
		$rules['status'] = Rule::make('status')->setValue($item_statuses);
        $concurrent_items = Catalog::factory(self::DEFAULT_CATALOG_KEY, $segment['id'])->getConcurrents(
			$item['type_id'], 
			$item, 
			self::LENGTH_CONCURRENT_ITEMS_BLOCK,
			$rules,
			Catalog::S_ITEM,
			TRUE,
			$major_params);
		$query_string = $this->request->request->get('query_string');
		$around = array('prev' => NULL, 'next' => NULL);
		if (!empty($query_string)){
			parse_str($query_string, $query_params);
			if (isset($query_params['from_filter'])){
				$this->request->query->add($query_params);
                $search_params = \App\CatalogMethods::getSearchableRules($this->request, $item['type_id'], array(), $segment['id']);
                $around = CatalogSearch::factory(self::DEFAULT_CATALOG_KEY, $segment['id'])->setPublicOnly(TRUE)->setRules($search_params)->getAround($item);
			}
		}
		$major_filter_url = $this->propertiesToUrl(
			$item->getType(),
			PropertyFactory::search($item['type_id'], PropertyFactory::P_MAJOR, 'key', 'position', 'parents', array(), $segment['id']), 
			$major_params);
        $this->getAns()
			->add('around', $around)
			->add('query_string', $query_string)
			->add('concurrent_url', $major_filter_url)
			->add('variant_id', $request_variant_id)
            ->add('type_properties', !empty($item_properties) ? $item_properties : array())
            ->add('variant_properties_full', PropertyFactory::search($item['type_id'], PropertyFactory::P_VARIANTS, 'key', 'group', 'parents', array('visible' => CatalogConfig::V_PUBLIC_VARIANT), $segment['id']))
            ->add('variant_properties_short', PropertyFactory::search($item['type_id'], PropertyFactory::P_VARIANTS, 'key', 'group', 'parents', array('visible' => CatalogConfig::V_PUBLIC_VARIANT), $segment['id']))
            ->add('special_groups', $special_groups)
            ->add('variants', !empty($variants) ? $variants : array())
            ->add('concurrent_items', !empty($concurrent_items) ? $concurrent_items : array())
            ->add('first_variant', reset($variants))
//            ->add('discounts', \Models\CatalogManagement\Discount::search(array('entity_ids' => $item_id)))
            ;
    }
	
	public function printItem(){
		$this->setAjaxResponse();
	}
    /******************************************/
    /**
     * Страница поиска по каталогу
     * @return type
     */
    public function search(){
        $search_string = trim($this->request->request->get('search', $this->request->query->get('search')));
        $search_manager = \Models\Search::getInstance();
		$phrase_url = $search_manager->getUrl($search_string);//ищем по предустановленным значениям
		if (!empty($phrase_url)){
            $search_manager->log($search_string);
			return $this->redirect($phrase_url);
		}
        $page = $this->request->query->get('page', 1);
        if ($page < 1 || intval($page) != $page){
            return $this->redirect($this->getModuleUrl() . __FUNCTION__ . '/', array('search', $search_string), '302');
        }
        $catalog = TypeEntity::getByKey(self::DEFAULT_CATALOG_KEY);
		$type_id = $this->request->query->get('type_id', $catalog['id']);
		$defaultSegment = \App\Segment::getInstance()->getDefault(1);
        if ($search_string){
            if (preg_match('~[0-9]{5}~', $search_string)){
				$codeRule = RuleAggregator::make(RuleAggregator::LOGIC_AND, array(Rule::make(CatalogConfig::KEY_ITEM_CODE)->setValue(intval($search_string))));
                $item = CatalogSearch::factory(self::DEFAULT_CATALOG_KEY, $defaultSegment['id'])->setRules($codeRule)->searchItems()->getFirst();
                if (!empty($item)){
                    return $this->redirect($item->getUrl());
				}
            }
            //в шаблоне слева нам нужны все типы, которые найдены у поисковой строки
            $search_params = RuleAggregator::make(RuleAggregator::LOGIC_OR, array(
                CatalogConfig::KEY_ITEM_TITLE => Rule::make(CatalogConfig::KEY_ITEM_TITLE)
                    ->setValue($search_string)
                    ->setSearchType(Rule::SEARCH_LIKE),
                CatalogConfig::KEY_VARIANT_TITLE => Rule::make(CatalogConfig::KEY_VARIANT_TITLE)
                    ->setValue($search_string)
                    ->setSearchType(Rule::SEARCH_LIKE)
            ));
            $searches = CatalogSearch::factory(self::DEFAULT_CATALOG_KEY, $defaultSegment['id'])
                ->setTypeId($type_id)
                ->setEnableCountByTypes(TRUE)
                ->setRules($search_params)
                ->setPublicOnly($this->account->isPermission('catalog-item', 'changeHidden') ? false : 1)
                ->searchItems(($page-1)*self::PAGE_SEARCH_SIZE, self::PAGE_SEARCH_SIZE);
            $search_manager->log($search_string);
        }
        $catalog = TypeEntity::getByKey(self::DEFAULT_CATALOG_KEY);
        $this->getAns()->add('type_menu', !empty($searches['count_by_type']) ? TypeEntity::search(array('ids' => array_keys($searches['count_by_type']), 'allow_children' => 0)) : array())
            ->add('count_by_types', !empty($searches) ? $searches['count_by_type'] : array())
            ->add('search_string', !empty($search_string) ? $search_string : '')
			->add('searched_type', $type_id)
//            ->add('main_types', self::getTypeChildren($sv, $catalog['id']))
            /*->add('news', \Modules\Main\View::getNews())*/
            ;
        $this->itemsList(true);//, $searches);
    }
    /**
     * Преобразовать свойства в урл для фильтра
     * @param TypeEntity $type
     * @param array $properties
     * @param array $params
     * @throws \Exception
     * @return string
     */
	private function propertiesToUrl(TypeEntity $type, $properties, $params){
		if (empty($params)){
			return '';
		}
		$url = $type->getUrl() . '?';
		foreach ($params as $p_key => $p){
			if (empty($properties[$p_key])){
				throw new \Exception('Переданы не все свойства: "' . $p_key . '"');
			}
			$prop = $properties[$p_key];
			switch ($prop['search_type']){
				case PropertyFactory::SEARCH_CHECK:
					$search_part = $p_key . '[]=' . $p['value'];
					break;
				case PropertyFactory::SEARCH_BETWEEN:
					$search_part = (isset($p['min']) ? ($p_key . '[min]=' . $p['min']) : '') . (isset($p['max']) ? ($p_key . '[max]=' . $p['max']) : '');
					break;
				default:
					$search_part = $p_key . '=' . $p['value'];
			}
			$url .= '&' . $search_part;
		}
		$url = str_replace('?&', '?', $url);
		return $url;
	}

    /**
     * список найденных позиций каталога
     * @ajax
     * @param bool $inner
     * @param int $pageSize
     * @throws \Exception
     */
    public function itemsList($inner = FALSE, $pageSize = self::PAGE_SEARCH_SIZE){
        if (!$inner){
            $this->setAjaxResponse();
			$pageSize = $this->request->request->get('page_size', $this->request->query->get('page_size', $pageSize));
        }
        $catalog = TypeEntity::getByKey(self::DEFAULT_CATALOG_KEY);
        $type_id = $this->request->query->get('type_id', $this->request->request->get('type_id', $catalog['id']));
        $search_string = trim($this->request->request->get('search', $this->request->query->get('search')));
        $page = $this->request->query->get('page', 1);
        $count = 0;
        $item_properties = PropertyFactory::search($type_id, PropertyFactory::P_ALL, 'key', 'group', 'parents', array('visible' => CatalogConfig::V_PUBLIC_ITEM));
        $searches = $this->searchMethod($type_id, $count, $page, FALSE, $pageSize);
        $this->getAns()
            ->add('catalog_items', $searches['items'])
            ->add('count', $count)
            ->add('found_types', TypeEntity::factory(array_keys($searches['count_by_type'])))
            ->add('count_by_types', $searches['count_by_type'])
            ->add('pageSize', $pageSize)
            ->add('search_string', !empty($search_string) ? $search_string : '')
            ->add('pageNum', $page)
			->add('searched_type', $type_id)
			->add('item_properties', $item_properties)
		;
    }

    /**
     * Метод поиска по каталогу
     * @param int $type_id
     * @param $count
     * @param $page
     * @param bool $countByTypeOnly
     * @param int $pageSize
     * @throws \Exception
     * @internal param string $search_string
     * @return array (Item[] или count_by_type зависит от $countByTypeOnly)
     */
    public function searchMethod($type_id, &$count, $page, $countByTypeOnly = FALSE, $pageSize = self::PAGE_SEARCH_SIZE){
        $default_segment = \App\Segment::getInstance()->getDefault(true);
        //фильтрация параметров, по которым производится поиск
        $search_params = \App\CatalogMethods::getSearchableRules($this->request, $type_id, array(), $this->segment['id']);
        $catalog_search = CatalogSearch::factory(self::DEFAULT_CATALOG_KEY, $default_segment['id'])
            ->setTypeId($type_id)->setRules($search_params)
            ->setPublicOnly($this->account->isPermission('catalog-item', 'changeHidden') ? false : 1)
            ->setEnableCountByTypes($countByTypeOnly);
        $search_result = $catalog_search->searchItems(($page - 1) * $pageSize, $pageSize);
        $count = $search_result['total_count'];
        return array('items' => $search_result->getSearch(), 'count_by_type' => $catalog_search->getItemsCountByTypes()->getSearch());
    }
    
    /**
     * Набор фильтруемых параметров
     * @ajax
     */
    public function itemsFilter($inner = FALSE, $type_id = NULL){
        $catalog = TypeEntity::getByKey(self::DEFAULT_CATALOG_KEY);
        if (!$inner){
            $type_id = $this->request->query->get('type_id', $this->request->request->get('type_id', $catalog['id']));
            $this->setAjaxResponse();
        }else{
            if (empty($type_id)){
                throw new \LogicException('type_id is null');
            }
        }
		$filter_visible = $this->request->request->get('filter_visible', CatalogConfig::FV_MAIN);
        $segment = \App\Segment::getInstance()->getDefault(true);
        $catalog = Catalog::factory(self::DEFAULT_CATALOG_KEY, $segment['id']);
        $search_properties = $catalog->getSearchableProperties($type_id, $this->account->isPermission('catalog-item', 'changeHidden') ? 'exist' : 'public', NULL, array('filter_visible' => $filter_visible), 'position', TRUE);
        $this->getAns()->add('search_properties', $search_properties)
            ->add('foreign_price', \App\CatalogMethods::getForeignPrice($search_properties))
            ->add('request_region', \App\Segment::getInstance()->getDefault(true));
    }
    /*************************************/
    /**
     * возвращает список значений для автокомплита по id свойства
     * @param int $prop_id
     * @return
     */
    public function getTypePropertyValues(){
        $prop_id = $this->request->query->get('id');
        $ans = '';
        if (!empty($prop_id)){
			$def_segment = \App\Segment::getInstance()->getDefault(true);
            $property = PropertyFactory::getById($prop_id, $def_segment['id']);
            if (!empty($property)){
                $values = $property->getDistinctValues($this->request->query->get('q'));
                foreach ($values as $key => $val){
                    $ans .= "$key|$val\n";
                }
            }
        }
        return $ans;
    }

    /**
     * Возвращает рулы поиска по заголовку с инверсией клавиатуры (защита от неверной раскладки)
     * @param $search_string
     * @return \Models\CatalogManagement\Rule|null
     */
    private function getSearchStringRule($search_string, $field = null){
        if (!empty($search_string)){
            $checked_string = \LPS\Components\FormatString::keyboardLayout($search_string, 2);
            if ($checked_string != $search_string){
                $search_string = array(
                    $search_string,
                    $checked_string
                );
                $this->getAns()->add('checked_string', $checked_string);
            }
            return Rule::make(!empty($field) ? $field : CatalogConfig::KEY_VARIANT_TITLE_SEARCH)
                ->setValue($search_string)
                ->setSearchType(Rule::SEARCH_LIKE);
        } else {
            return NULL;
        }
    }

    /**
     * возвращает список значений для автокомплита по key свойства
     */
    public function getPropertyValuesByKey(){
        $ans = array();
        $prop_key = $this->request->query->get('key');
        $type_id = $this->request->query->get('type_id');
        $ifVariant = $this->request->query->get('ifVariant');
        $extendedResult = $this->request->query->has('extended');
        $limit = $this->request->query->get('count', self::AUTOCOMPLETE_LIST_SIZE);
        $segment = \App\Segment::getInstance()->getDefault(TRUE);
		if (empty($prop_key)){
			throw new \LogicException('Не задан ключ свойства');
		}
        $params = array();
        $term = trim($this->request->query->get('term'));

        // поиск через сфинкс
        if (SphinxConfig::ENABLE_SPHINX && SphinxConfig::ENABLE_AUTOCOMPLETE){
            if (!empty($term)) {
                $ids = $this->sphinxSearchItemIds($term, $type_id, $ifVariant);
            }
            if (!empty($ids) && !empty($term)){
                $string_rule = $ifVariant ? Rule::make('variant.id')->setValue($ids)->setOrder($ids) : Rule::make('id')->setValue($ids)->setOrder($ids);
            }
        }
        // в бд через лайк
        if (empty($string_rule)) {
            if ($prop_key == 'title'){
                $string_rule = $this->getSearchStringRule($term, $prop_key);
            }else{
                $string_rule = Rule::make($prop_key)->setValue($term, Rule::SEARCH_LIKE)->setOrder(FALSE);
            }
        }

        if (!empty($string_rule)){
            $params[] = $string_rule;
        }
        if (!empty($type_id)) {
            $type = TypeEntity::getById($type_id, $this->segment['id']);
            $catalog = $type->getCatalog();
        } else {
            $catalog = TypeEntity::getByKey(self::DEFAULT_CATALOG_KEY, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        }
        $catalog_search = CatalogSearch::factory($catalog['key'], $segment['id'])->setTypeId($type_id)->setPublicOnly( $this->account->isPermission('catalog-item', 'changeHidden') ? false : true)->setRules($params);
        $values = $ifVariant ? $catalog_search->searchVariants(0, $limit) : $catalog_search->searchItems(0, $limit);
        $i = 0;
        $distinct_values = array();
        foreach ($values as $val){
            $prop_value = !empty($val['properties'][$prop_key]) ? $val['properties'][$prop_key]['real_value'] : '';
            if (!in_array($prop_value, $distinct_values)){
                $ans[$i]['value'] = $prop_value;
                $ans[$i]['label'] = $val[$prop_key];
                $distinct_values[] = $prop_value;
                $i++;
            }
        }
        return json_encode($ans);
    }

    /**
     * @ajax
     * @return json
     */
    public function addReview(){
        $this->setAjaxResponse();
        $item_id = $this->request->request->get('item_id');
        $params['mark'] = $this->request->request->get('mark');
        $params['text'] = $this->request->request->get('text');
        $params['text_worth'] = $this->request->request->get('text_worth');
        $params['text_fault'] = $this->request->request->get('text_fault');
        $params['name'] = $this->request->request->get('name');
        if (empty($item_id))
            $errors['item_id'] = \Models\Validator::ERR_MSG_EMPTY;
        if (empty($params['mark']))
            $errors['mark'] = \Models\Validator::ERR_MSG_EMPTY;
        if (empty($params['text']) && empty($params['text_worth']) && empty($params['text_fault']))
            $errors['text'] = \Models\Validator::ERR_MSG_EMPTY;
        if (empty($params['name'])){
            if (!($this->account instanceof \App\Auth\Account\Guest)){
                $params['name'] = $this->account->getUser()->getName();
            }else{
                $errors['name'] = \Models\Validator::ERR_MSG_EMPTY;
            }
        }
        if (empty($errors)){
            $id = \Models\CatalogManagement\Review::create($item_id);
            $review = \Models\CatalogManagement\Review::getById($id);
            $user = $this->account->getUser();
            $params['user_id'] = !empty($user) ? $user->getId() : NULL;
            $review->update($params);
            if (!empty($review)){
                $message = 'success';
            }else{
                $message = 'error';
            }
            return json_encode(array('status' => $message));
        }else{
            return json_encode(array('errors' => $errors));
        }
    }
    /**
     * Сравнение товаров
     */
    public function compare(){
        \Models\CatalogManagement\CatalogHelpers\Variant\Bonus::factory();
        $set_variant_ids = $this->request->query->get('id');
        $s = \App\Builder::getInstance()->getCurrentSession();
        $compare = $s->get('compare');
        if (!empty($compare) && !empty($set_variant_ids)){
            foreach ($compare as $v_id){
                $this->delCompare($v_id);
            }
        }
        if (!empty($set_variant_ids)){
            foreach ($set_variant_ids as $s_id){
                $this->setCompare($s_id);
            }
        }
        $variant_ids = !empty($compare) ? $compare : array();
        if (empty($set_variant_ids) && !empty($compare)){
			$default_segment = \App\Segment::getInstance()->getDefault(true);
            $urlTail = 'compare/';
            $i = 0;
            foreach ($variant_ids as $v_id){
                $urlTail .= ($i===0 ? '?' : '&') . 'id[]=' . $v_id;
                $i++;
            }
            return $this->redirect('/' . $default_segment['key'] . $this->getModuleUrl() . $urlTail);
        }
        $properties = array();
        $variants = array();
        $items = array();
        if (!empty($variant_ids)){
            $variants = Variant::factory(str_replace('v', '', $variant_ids));
            if (!empty($variants)){
                foreach($variants as $id => $variant){
                    $item = $variant->getItem();
                    if (!array_key_exists($item['id'], $items)) {
                        $items[$item['id']] = array(
                            'item' => $item,
                            'var_ids' => array()
                        );
                    }
                    $items[$item['id']]['var_ids'][] = $variant['id'];
                    $types_props[$variant['type_id']] = $variant['type_id'];
                }
                $properties = PropertyFactory::search($types_props, PropertyFactory::P_ALL, 'key', 'group', 'parents', array('visible' => CatalogConfig::V_PUBLIC_VARIANT_FULL + CatalogConfig::V_PUBLIC_FULL));
            }
        }
      //  $sv = SegmentVisible::factory();
        $catalog = TypeEntity::getByKey(self::DEFAULT_CATALOG_KEY);
        $this->getAns()
            ->add('properties', $properties)
            ->add('variants', $variants)
            ->add('items', $items)
            ->add('current_type', !empty($variant) ? $variant->getType() : NULL)
      //      ->add('main_types', self::getTypeChildren($sv, $catalog['id']))
      //      ->add('post_bonus', Post::getById(CatalogConfig::POST_BONUS_ID))
                ;
    }

    /**
     * @ajax смотрим, какие итемы в сравнении
     * @param bool $inner
     */
    public function comparePanel($inner = false){
        if (!$inner){
            $this->setAjaxResponse();
        }
        $s = \App\Builder::getInstance()->getCurrentSession();
        $variant_ids = $s->get('compare');
        $variants = array();
        if (!empty($variant_ids)){
            $variants = Variant::factory($variant_ids);
        }
        $this->getAns()->add('compare_variants', $variants);
    }
    public function setCompare($variant_id = null){
		$inner = TRUE;
        if (empty($variant_id)){
            $this->setJsonAns();
            $variant_id = $this->request->request->get('id');
			$inner = FALSE;
        }
        $clear = $this->request->request->get('clear');
        $s = \App\Builder::getInstance()->getCurrentSession();
        $variant_ids = $s->get('compare');
        if (!empty($variant_id) && (empty($variant_ids) || count($variant_ids) < 4)){
            if (!empty($variant_ids)){
                $variants = Variant::factory(array_merge($variant_ids, array($variant_id)));
                $type_tmp = NULL;
                $error = FALSE;
                foreach ($variants as $v){
                    if (!is_null($type_tmp) && $type_tmp != $v['type_id']){
                        if ($v['id'] == $variant_id){
                            $old_type = TypeEntity::getById($type_tmp);
                            $new_type = $variants[$variant_id]->getType();
                        }else{
                            $old_type = $variants[$variant_id]->getType();
                            $new_type = TypeEntity::getById($type_tmp);
                        }
                        $this->getAns()->addData('old', $old_type['title'])
                                ->addData('new', $new_type['title']);
                        $error = TRUE;
                        break;
                    }elseif(is_null($type_tmp)){
                        $type_tmp = $v['type_id'];
                    }
                }
            }
            if (empty($error) || !empty($clear)){
                if (empty($clear)){
                    $variant_ids[$variant_id] = $variant_id;
                }else{
                    $variant_ids = array($variant_id => $variant_id);
                }
                $s->set('compare', $variant_ids);
            }elseif (!empty($error)){
                $this->getAns()->addErrorByKey('main', 'type');
            }
        }else{
            $this->getAns()->addErrorByKey('main', 'full');
        }
        return !$inner ? $this->run('comparePanel') : NULL;
    }
    public function delCompare($variant_id = null){
		$inner = TRUE;
        if (empty($variant_id)){
            $variant_id = $this->request->request->get('id');
			$inner = FALSE;
        }
		$s = \App\Builder::getInstance()->getCurrentSession();
        $variant_ids = $s->get('compare');
        if (!empty($variant_id) && !empty($variant_ids[$variant_id])){
            unset($variant_ids[$variant_id]);
			$s->set('compare', $variant_ids);
        }
        return !$inner ? $this->run('comparePanel') : NULL;;
    }
    /**
     * Удаление из списка для сравнения всех предложений, соответствующих объекту
     */
    public function delCompareItem(){
        $item_id = $this->request->request->get('id');
        $s = \App\Builder::getInstance()->getCurrentSession();
        $variant_ids = $s->get('compare');
        if (empty($variant_ids)){
            $variant_ids = array();
        }
        $item = ItemEntity::getById($item_id);
        if (!empty($item)){
            $variants = $item->getVariants();
            $remove_ids = array_keys($variants);
            foreach($remove_ids as $id){
                if (!empty($variant_ids[$id])){
                    unset($variant_ids[$id]);
                }
            }
            $s->set('compare', $variant_ids);
        }
        return $this->run('comparePanel');
    }
    
    public function clearCompare(){
        \App\Builder::getInstance()->getCurrentSession()->set('compare', array());
        return $this->run('comparePanel');
    }
	
	public function getPdf(){
        $segment = \App\Segment::getInstance()->getDefault(true);
        $id = $this->request->query->get('id');
        $request_variant_id = null;
        $item = ItemEntity::getById($id, $segment['id']);
        if (empty($item)){
            return $this->notFound();
		}
        $type = TypeEntity::getById($item['type_id'], $segment['id']);
        if (empty($type)){
            return $this->notFound();
        }
        //если всё ок, запихиваем id в POST данные для использования в методах вкладок
        $this->request->request->set('item_id', $id);
        $this->request->request->set('tab', 'view');
        $this->request->request->set('variant_id', $request_variant_id);
        $catalog = TypeEntity::getByKey(self::DEFAULT_CATALOG_KEY);
		$this->getAns()
            ->add('catalog_item', $item)
            ->add('current_type', $type)
//            ->add('main_types', self::getTypesForLeftMenu($catalog['id']))
            ->add('product_menu_list', self::getPath($item['type_id']))
            ->add('request_tab', 'view')
            ;
		$request_segment = \App\Segment::getInstance()->getDefault(true);
        $this->getAns()->add('request_segment', $request_segment)
				->add('site_config', \App\Builder::getInstance()->getSiteConfig())
				->add('constants', array(
			'key_variant_status' => \App\Configs\CatalogConfig::KEY_VARIANT_STATUS,
			'variant_status_special_value' => \App\Configs\CatalogConfig::STATUS_VARIANT_NO_KP,
//			'city_saint_petersburg_id' => \App\Configs\CatalogConfig::CITY_SAINT_PETERSBURG_ENUM_ID
		));
        $this->inset(TRUE);
		if (!file_exists(\LPS\Config::getRealDocumentRoot() . 'data/pdf/fonts/')){
			\LPS\Components\FS::makeDirs(\LPS\Config::getRealDocumentRoot() . 'data/pdf/fonts/');
		}
		require 'includes/dompdf_config.inc.php';
//		$content = $this->getAns()->setTemplate('Modules/Catalog/Main/getPdf.tpl')->getContent();
		$content = str_replace('²', '<span class="sup">2</span>', $this->getAns()->setTemplate('Modules/Catalog/Main/getPdf.tpl')->getContent());
//		$content = '<HTML><HEAD><meta http-equiv="content-type" content="text/html; charset=utf-8" /></HEAD><BODY>Привет</BODY></HTML>';
		$dompdf = new \DOMPDF();// Создаем обьект
		$dompdf->load_html($content); // Загружаем в него наш html код
		$dompdf->render(); // Создаем из HTML PDF
		$dompdf->stream($item[CatalogConfig::KEY_ITEM_TITLE] . '.pdf'); // Выводим результат (скачивание)
		exit;
	}

    /** ****************************** Отзывы и вопросы ****************************** */
    /**
     * Форма отправки отзыва о товаре
     * @throws \Exception
     */
    public function sendReview(){
        $product_id = $this->request->query->get('product_id');
        $product = !empty($product_id) ? ItemEntity::getById($product_id) : NULL;
        if (empty($product) || $product->getType()->getCatalog()['key'] != CatalogConfig::CATALOG_KEY) {
            return $this->notFound();
        }
        $reviews_catalog = TypeEntity::getByKey(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY);
        $review_type = TypeEntity::getByKey(ReviewConfig::REVIEWS_KEY, $reviews_catalog['id']);
        $this->getAns()
            ->add('product', $product)
            ->add('reviews_props' , PropertyFactory::search($review_type['id'], PropertyFactory::P_ALL, 'key'));
    }
    /**
     * Форма отправки отзыва о товаре
     * @throws \Exception
     */
    public function sendQuestion(){
        $product_id = $this->request->query->get('product_id');
        $product = !empty($product_id) ? ItemEntity::getById($product_id) : NULL;
        if (empty($product) || $product->getType()->getCatalog()['key'] != CatalogConfig::CATALOG_KEY) {
            return $this->notFound();
        }
        $reviews_catalog = TypeEntity::getByKey(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY);
        $review_type = TypeEntity::getByKey(ReviewConfig::QUESTIONS_KEY, $reviews_catalog['id']);
        $this->getAns()
            ->add('product', $product)
            ->add('reviews_props' , PropertyFactory::search($review_type['id'], PropertyFactory::P_ALL, 'key'));
    }

    /**
     * Обработка формы отзыва о товаре
     * @throws \Exception
     */
    public function makeReview(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $errors = array();

        $review = $this->processReveiwQuestion(array(
            'title',
            'text',
            'recommendation',
            'author',
            'gender',
            'age_group',
            'duration',
            'city'
        ), ReviewConfig::REVIEWS_KEY, $errors);

        if (!empty($errors)) {
            $ans->setErrors($errors);
            if (!empty($review)) {
                $ans->addData('id', $review['id']);
            }
        } else {
            $mail = new \LPS\Container\WebContentContainer('mails/feedbackReview.tpl');
            $mail->add('review', $review);
            $emails = \Models\SiteConfigManager::getInstance()->get(self::REVIEW_MAILS_LIST, \App\Configs\CatalogConfig::CONFIG_NOTIFICATION_KEY);
            \Models\Email::send($mail, !empty($emails) ? $emails : \LPS\Config::getParametr('email', 'to'));
            $ans->setStatus('OK');
        }
    }

    /**
     * Обработка формы вопроса о товаре
     * @throws \Exception
     */
    public function makeQuestion(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $errors = array();

        $question = $this->processReveiwQuestion(array(
            'title',
            'text',
            'author',
            'email',
            'gender',
            'age',
            'city'
        ), ReviewConfig::QUESTIONS_KEY, $errors);

        if (!empty($errors)) {
            $ans->setErrors($errors);
            if (!empty($question)) {
                $ans->addData('id', $question['id']);
            }
        } else {
            $mail = new \LPS\Container\WebContentContainer('mails/feedbackQuestion.tpl');
            $mail->add('question', $question);
            $emails = \Models\SiteConfigManager::getInstance()->get(self::QUESTIONS_MAILS_LIST, \App\Configs\CatalogConfig::CONFIG_NOTIFICATION_KEY);
            \Models\Email::send($mail, !empty($emails) ? $emails : \LPS\Config::getParametr('email', 'to'));
            $ans->setStatus('OK');
        }
    }

    /**
     * Обработчик форм отзывов и вопросов о товаре
     * @param array $params — список post-параметров формы
     * @param string $type_key — ключ категории (отзыв/вопрос)
     * @param array $errors
     * @return \Models\CatalogManagement\Positions\Review|null
     * @throws \Exception
     */
    private function processReveiwQuestion($params, $type_key, &$errors){
        $entity_id = $this->request->request->get('id');
        $product_id = $this->request->request->get('product_id');
        $product = !empty($product_id) ? ItemEntity::getById($product_id) : NULL;
        if (empty($product) || $product->getType()->getCatalog()['key'] != CatalogConfig::CATALOG_KEY) {
            $errors['product'] = 'empty';
        } else {
            $reviews_catalog = TypeEntity::getByKey(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY);
            $review_type = TypeEntity::getByKey($type_key, $reviews_catalog['id']);
//            $review = !empty($review_id)
            $entity_id = !empty($entity_id) ? $entity_id : ItemEntity::create($review_type['id']);
            $entity = !empty($entity_id) ? ItemEntity::getById($entity_id) : NULL;
            if (empty($entity) || $entity->getType()['key'] != $type_key) {
                $errors[$type_key] = 'cant_create';
            } else {
                $prop_params = $this->makeReviewParams($params, $product);
                if ($type_key == ReviewConfig::REVIEWS_KEY){
                    $prop_source = PropertyFactory::search($review_type['id'], PropertyFactory::P_ALL, 'key', 'type_group', 'parents', array('key' => ReviewConfig::SOURCE));
                    $prop_source = $prop_source[ReviewConfig::SOURCE];
                    $source_val = NULL;
                    foreach($prop_source['values'] as $val){
                        if ($val['key'] == ReviewConfig::SOURCE_SITE){
                            $source_val = $val['id'];
                            break;
                        }
                    }
                    $prop_params[ReviewConfig::SOURCE] = array(0 => array('val_id' => NULL, 'value' => $source_val));
                }
                $entity->update(array('status' => ItemEntity::S_PUBLIC), $prop_params, $errors);
            }
        }
        return !empty($entity) ? $entity : NULL;
    }

    /**
     * Формируем массив значений пропертей для CatalogPosition::update из данных обычной формы
     * @param array $fields список ячеек, забираемых из post-запроса
     * @param ItemEntity $product
     * @return array
     */
    private function makeReviewParams(Array $fields, ItemEntity $product) {
        $result = array(
            ReviewConfig::PRODUCT => array(0 => array('val_id' => NULL, 'value' => $product['id']))
        );
        foreach($fields as $field){
            $result[$field] = array(0 => array('val_id' => NULL, 'value' => $this->request->request->get($field)));
        }
        return $result;
    }
}