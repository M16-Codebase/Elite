<?php
/**
 * Description of Viewer
 *
 * @author olga
 */
namespace Modules\Catalog;
use App\Configs\RealEstateConfig;
use Models\CatalogManagement\Catalog;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type AS TypeEntity;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\CatalogManagement\Item AS ItemEntity;
use Models\CatalogManagement\Variant;
use App\Configs\CatalogConfig;
use App\Auth\Account\Admin AS AccountAdmin;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Rules\RuleAggregator;
class Viewer extends \LPS\AdminModule{
    const PAGE_SEARCH_SIZE = 30;
    const LENGTH_ASSOC_ITEMS_BLOCK = 1000000;
    const LENGHT_CONCURRENT_ITEMS_BLOCK = 1000000;
    public function init(){
        $this->getAns()->add('admin_page', 1);
    }
    /**
     * Проверка прав
     * @param string $action
     * @return boolean
     */
    public function isPermission($action){
        if ($this->account instanceof \App\Auth\Account\Viewer || $this->account instanceof \App\Auth\Account\Admin){
            return true;
        }
        return false;
    }
    public function viewDocument(){
    }
    public function index(){
        $id = $this->request->query->get('id');
        $request_variant_id = $this->request->query->get('v');
        $request_tab = $this->request->query->get('tab');
		$default_segment = \App\Segment::getInstance()->getDefault();
        $item = ItemEntity::getById($id, $default_segment['id']);
        $catalog = $item->getType()->getCatalog();
        if (empty($item) || $item['status'] == ItemEntity::S_DELETE || $item['status'] == ItemEntity::S_TMP || !$catalog['enable_view_mode']){
            return $this->notFound();
		}
        $type = TypeEntity::getById($item['type_id']);
        //похожие товары
        $item_statuses = array(ItemEntity::S_PUBLIC, ItemEntity::S_HIDE);
        $concurrent_items = Catalog::factory($type->getCatalog()->getKey())->getConcurrents($item['type_id'], $item, self::LENGHT_CONCURRENT_ITEMS_BLOCK, array('status' => $item_statuses), Catalog::S_ITEM, false);
        //Прикрепленные сущности:
//        $internal_links = ILM::getInstance()->search(array(ILM::TARGET_TYPE_ITEM=>$id, ILM::TARGET_TYPE_TYPE=>$type['id']), array(ILM::OBJECT_TYPE_ITEM, ILM::OBJECT_TYPE_TYPE));
        //файлы
//        $files = (!empty($internal_links) && !empty($internal_links[ILM::OBJECT_TYPE_FILE])) ? File::factory(array_keys($internal_links[ILM::OBJECT_TYPE_FILE])) : array();
        //сопутствующие товары вычисляются из прикрепленных к типу товара типов.
        $assoc_items = array();
//        if (!empty($internal_links) && !empty($internal_links[ILM::OBJECT_TYPE_ITEM])){
//            $assoc_items = ItemEntity::factory(array_keys($internal_links[ILM::OBJECT_TYPE_ITEM]));
//        }
        //варианты
        $variant_statuses = array(Variant::S_PUBLIC, Variant::S_HIDE);
        $variants = $item->getVariants($variant_statuses);
//        $files = \Models\FilesManagement\ItemFile::search(array('variants' => array_keys($variants), 'start' => 0, 'limit' => 100000000), $count, true);
		$need_variant_properties = array();//CatalogConfig::ITEM_TYPE_IP_ID, CatalogConfig::ITEM_TYPE_HOUSE_ID);
        $type_properties = PropertyFactory::search($item['type_id'], !in_array($item['type_id'], $need_variant_properties) ? PropertyFactory::P_ITEMS : PropertyFactory::P_ALL, 'key', 'group', 'parents', array('visible' => CatalogConfig::V_ADMIN));
        $special_groups = array();
//		\Models\CatalogManagement\Comment::search(array('item_id' => $item['id'], 'new' => $this->account->getUser()->getId()), $comments_new_count, 0, 1000000);
//		if (empty($comments_new_count)){
//			\Models\CatalogManagement\Comment::search(array('item_id' => $item['id']), $comments_count, 0, 1000000, TRUE);
//		}
        $nested_types = TypeEntity::search(array('nested_in' => $type['id']));
        $nested_items = array();
        foreach ($nested_types as $nt){
            $nested_items[$nt['id']] = CatalogSearch::factory($type->getCatalog()->getKey(), $default_segment['id'])->setTypeId($nt['id'])->setRules(array('parent_id' => $item['id']))
                ->setPublicOnly(FALSE)
                ->searchItems();
        }
		$this->getAns()
            ->add('nested_types', $nested_types)
            ->add('nested_items', $nested_items)
            ->add('catalog_item_files', !empty($files) ? $files : array())
            ->add('catalog_item', $item)
            ->add('current_type', $type)
            ->add('type_properties', !empty($type_properties) ? $type_properties : array())//Длинный список свойств
            ->add('variant_properties', PropertyFactory::search($item['type_id'], PropertyFactory::P_VARIANTS, 'key', 'group', 'parents', array('visible' => CatalogConfig::V_ADMIN)))
            ->add('concurrent_items', !empty($concurrent_items) ? $concurrent_items : array())
            ->add('assoc_items', !empty($assoc_items) ? $assoc_items : array())
            ->add('variants', !empty($variants) ? $variants : array())
            ->add('current_variant', !empty($request_variant_id) ? $request_variant_id : null)
            ->add('request_tab', $request_tab)
            ->add('special_groups', $special_groups)
//            ->add('store_inner_separator', CatalogConfig::STORE_INNER_SEPARATOR)
//            ->add('store_outer_separator', CatalogConfig::STORE_OUTER_SEPARATOR)
//            ->add('file_types', \Models\FilesManagement\ItemFile::getTypes())
//			->add('metro_stations', \Models\Metro::getStations())
//			->add('metro_lines', \Models\Metro::getLines())
//			->add('comments_new_count', !empty($comments_new_count) ? $comments_new_count : 0)
//			->add('comments_count', !empty($comments_count) ? $comments_count : 0)
		;
		$this->request->request->set('item_id', $id);
    }
    public function search()
	{
		$result = $this->searchList(true);
		if ($result !== null) {
			return $result;
		}
	}

	public function searchList($inner = false) {
		if (!$inner) {
			$this->setJsonAns();
		}
        $items = array();
        $search_string = trim($this->request->request->get('search', $this->request->query->get('search')));
        $page = $this->request->query->get('page', 1);
        if ($page < 1 || intval($page) != $page){
            return $this->redirect($this->getModuleUrl() . __FUNCTION__ . '/', array('search', $search_string), '302');
        }
		$catalog = TypeEntity::getByKey(CatalogConfig::MAIN_SEARCH_CATALOG_KEY);
		$real_estate_catalog = TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE);
		$complex_category = TypeEntity::getByKey(RealEstateConfig::CATEGORY_KEY_COMPLEX, $real_estate_catalog['id']);
        if ($search_string){
            $real_estate = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_REAL_ESTATE)->setTypeId($complex_category['id'])->setRules(array(
				RuleAggregator::make(RuleAggregator::LOGIC_OR, array(
					Rule::make('id')->setValue($search_string),
					Rule::make(RealEstateConfig::KEY_OBJECT_TITLE)->setValue($search_string, Rule::SEARCH_LIKE),
					Rule::make(RealEstateConfig::KEY_OBJECT_TITLE_SEARCH)->setValue($search_string, Rule::SEARCH_LIKE),
					Rule::make(RealEstateConfig::KEY_OBJECT_ADDRESS)->setValue($search_string, Rule::SEARCH_LIKE)
				))
			))
                ->setPublicOnly($this->account instanceof AccountAdmin ? false : 1)->setEnableCountByTypes(TRUE)
                ->searchItems();
			if ($real_estate->count()) {
				$item = $real_estate->getFirst();
				return $this->redirect($this->getModule('Catalog\Item')->getModuleUrl() . 'edit/?id=' . $item['id'] . '&tab=options');
			}
            $resale = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_RESALE)->setRules(array(
				RuleAggregator::make(RuleAggregator::LOGIC_OR, array(
					Rule::make('id')->setValue($search_string),
					Rule::make(RealEstateConfig::KEY_APPART_TITLE)->setValue($search_string, Rule::SEARCH_LIKE),
					Rule::make(RealEstateConfig::KEY_APPART_ADDRESS)->setValue($search_string, Rule::SEARCH_LIKE)
				))
			))
                ->setPublicOnly($this->account instanceof AccountAdmin ? false : 1)->setEnableCountByTypes(TRUE)
                ->searchItems(($page-1)*self::PAGE_SEARCH_SIZE, self::PAGE_SEARCH_SIZE);
			if ($resale->count()) {
				$item = $resale->getFirst();
				return $this->redirect($this->getModule('Catalog\Item')->getModuleUrl() . 'edit/?id=' . $item['id'] . '&tab=options');
			}
        }
        $this->getAns()
			->add('currentCatalog', $real_estate_catalog)
			->add('real_estate_catalog', $real_estate_catalog)
			->add('resale_catalog', TypeEntity::getByKey(CatalogConfig::CATALOG_KEY_RESALE))
			->add('real_estate_items', !empty($real_estate) ? $real_estate->getSearch() : array())
            ->add('resale_items', !empty($resale) ? $resale->getSearch() : array())
            ->add('real_estate_count', !empty($real_estate) ? $real_estate->getTotalCount() : 0)
            ->add('resale_count', !empty($resale) ? $resale->getTotalCount() : 0)
            ->add('pageSize', self::PAGE_SEARCH_SIZE)
            ->add('search_string', !empty($search_string) ? $search_string : '')
			->add('current_type', $catalog)
            ->add('pageNum', $page);
    }
	/**
	 * Список комментов
	 * @param type $inner
	 */
	public function commentsList($inner = FALSE){
		if (!$inner){
			$this->setJsonAns();
		}
		$item_id = $this->request->request->get('item_id');
		$item = ItemEntity::getById($item_id);
		if (empty($item) && !$inner){
			$this->getAns()->addErrorByKey('exception', 'Не найден объект с id: ' . $item_id)->setEmptyContent();
		}
		$comments = \Models\CatalogManagement\Comment::search(array('item_id' => array($item_id), 'order' => array('date' => 1)));
		$lastViewDate = \Models\CatalogManagement\Comment::getLastView($item_id, $this->account->getUser()->getId());
		$this->getAns()->add('item_comments', $comments)
				->add('last_view_date', strtotime($lastViewDate));
		\Models\CatalogManagement\Comment::setLastView($item_id, $this->account->getUser()->getId());
	}
	/**
	 * @ajax
	 * Добавить комментарий
	 * @return type
	 */
	public function addComment(){
        $item_id = $this->request->request->get('item_id');
        $params['text'] = $this->request->request->get('text');
		if ($this->account->isPermission('catalog-item', 'changeCommentImportant')){
			$params['important'] = $this->request->request->get('important', 0);
		}
        $user_id = $this->account->getUser()->getId();
        if (empty($item_id))
            $errors['item_id'] = \Models\Validator::ERR_MSG_EMPTY;
        if (empty($params['text']))
            $errors['text'] = \Models\Validator::ERR_MSG_EMPTY;
        if (empty($errors)){
            $id = \Models\CatalogManagement\Comment::create($item_id, $user_id, $params);
			if (empty($id)){
				$errors['main'] = 'Невозможно создать комментарий';
			}
        }
		if (!empty($errors)){
			$this->setJsonAns()->setErrors($errors)->setEmptyContent();
        }else{
			return $this->run('commentsList');
		}
    }
	/**
	 * @ajax
	 * Изменить важность коммента
	 */
	public function changeCommentImportant(){
		$comment_id = $this->request->request->get('id');
		$comment = \Models\CatalogManagement\Comment::getById($comment_id);
		if(empty($comment)){
			$this->getAns()->addErrorByKey('exception', 'Не найден комментарий с id: ' . $comment_id);
			return;
		}
		$important = $this->request->request->get('important', 0);
		$comment->update(array('important' => !empty($important) ? 1 : 0));
		$item = $comment->getItem();
		$this->request->request->set('item_id', $item['id']);
		return $this->run('commentsList');
	}
	/**
	 * @ajax
	 * Изменить статус комментария
	 */
	public function changeCommentStatus(){
		$comment_id = $this->request->request->get('id');
		$comment = \Models\CatalogManagement\Comment::getById($comment_id);
		if(empty($comment)){
			$this->getAns()->addErrorByKey('exception', 'Не найден комментарий с id: ' . $comment_id)->setEmptyContent();
			return;
		}
		$status = $this->request->request->get('status');
		$comment->setStatus($status);
		$item = $comment->getItem();
		$this->request->request->set('item_id', $item['id']);
		return $this->run('commentsList');
	}
	
	public function favorites(){
		$this->favoriteList(true);
	}
	/**
	 * @ajax
	 */
	public function favoriteList($inner = FALSE){
		if (!$inner){
			$this->setJsonAns();
		}
		$data = $this->account->getFavoriteData();
		if (!empty($data['comments'])){
			$this->getAns()->addFormValue('comments', $data['comments']);
		}
		$this->getAns()
			->add('request_segment', \App\Segment::getInstance()->getDefault());
	}
	
	public function printFavorites(){
		$this->setAjaxResponse();
		$segment = \App\Segment::getInstance()->getDefault();
		$favorites = $this->account->getFavorites();
		$variant_properties = array();
		foreach ($favorites['items'] as $f){
			if (empty($variant_properties[$f['item']['type_id']])){
				$variant_properties[$f['item']['type_id']] = PropertyFactory::search($f['item']['type_id'], PropertyFactory::P_VARIANTS, 'key', 'group', 'parents', array('visible' => CatalogConfig::V_ADMIN), $segment['id']);
			}
		}
		$this->getAns()->add('variant_properties_by_type', $variant_properties);
		$this->favoriteList(true);
	}
	
	public function printContacts(){
		$this->setAjaxResponse();
		$this->favoriteList(true);
	}
	
	public function addToFavorites(){
		$this->setJsonAns()->setEmptyContent();
		$variant_id = $this->request->request->get('variant_id');
		$variant_ids = $this->request->request->get('variants', $this->request->request->get('check'));
		$vs = array();
		if (!empty($variant_id)){
			$vs = array($variant_id);
		}elseif(!empty($variant_ids)){
			$vs = $variant_ids;
		}
		if (!empty($vs)){
			foreach ($vs as $v_id){
				$this->account->addFavorite($v_id);
				$this->getAns()->addData('status', 'ok')
				->addData('count', $this->account->getFavoriteCount());
			}
		}else{
			$this->getAns()->addErrorByKey('exception', 'Нет данных для добавления');
		}
	}
	
	public function removeFromFavorites(){
		$this->setJsonAns()->setEmptyContent();
		$variant_id = $this->request->request->get('variant_id');
		$variant_ids = $this->request->request->get('variants', $this->request->request->get('check'));
		$vs = array();
		if (!empty($variant_id)){
			$vs = array($variant_id);
		}elseif(!empty($variant_ids)){
			$vs = $variant_ids;
		}
		if (!empty($vs)){
			foreach ($vs as $v_id){
				$this->account->removeFavorite($v_id);
			}
			$this->getAns()->addData('status', 'ok');
		}else{
			$this->getAns()->addErrorByKey('exception', 'Нет данных для удаления');
		}
	}
	
	public function saveFavoriteComment(){
		$this->setJsonAns()->setEmptyContent();
		$comment_type = $this->request->request->get('type');
		$comment = $this->request->request->get('value', $this->request->request->get('comment'));
		if ($comment_type == 'item'){
			$item_id = $this->request->request->get('id', $this->request->request->get('item_id'));
			if (!empty($item_id)){
				$this->account->saveFavoriteComment($comment_type, $comment, $item_id);
			}else{
				$error = 'Не передан id объекта, к которому написан коментарий';
			}
		}elseif($comment_type == 'title' || $comment_type == 'text'){
			$this->account->saveFavoriteComment($comment_type, $comment, NULL);
		}else{
			$error = 'Неверно задан тип комментария';
		}
		if (!empty($error)){
			$this->getAns()->addErrorByKey('exception', $error);
		}else{
			$this->getAns()->addData('status', 'ok');
		}
	}
	
	public function getFavoritePDF(){
		$segment_id = $this->request->request->get('segment_id');
		$segment = empty($segment_id) ? \App\Segment::getInstance()->getDefault(true) : \App\Segment::getInstance()->getById($segment_id);
		$comments = $this->request->request->get('comments', $this->request->query->get('comments'));
		$this->account->setFavoriteComments($comments);
		$content = $this->getAns()->setTemplate('Modules/Catalog/Viewer/getFavoritePDF.tpl')->getContent();
		require 'vendor/dompdf/dompdf/dompdf_config.inc.php';
		$dompdf = new \DOMPDF();// Создаем обьект
		$dompdf->load_html($content); // Загружаем в него наш html код
		$dompdf->render(); // Создаем из HTML PDF
		$dompdf->stream('presentation.pdf'); // Выводим результат (скачивание)
		exit;
	}
}
?>