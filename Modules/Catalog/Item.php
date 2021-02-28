<?php
/* *******************    Товары      *********************/
/**
 * Description of Item
 *
 * @author olga
 */
namespace Modules\Catalog;
use App\Configs\MetroConfig;
use App\Configs\RealEstateConfig;
use App\Configs\ReviewConfig;
use Models\CatalogManagement\Catalog;
use Models\CatalogManagement\CatalogPosition;
use Models\CatalogManagement\Properties\CatalogPosition as CatalogPositionProp;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Rules\RuleAggregator;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type AS TypeEntity;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\CatalogManagement\Item AS ItemEntity;
use Models\CatalogManagement\Variant;
use Models\ContentManagement\Post AS PostEntity;
use Models\CatalogManagement\Properties;
use Models\FilesManagement\File;
use Models\Validator;
use LPS\Components\FS;

class Item extends \LPS\AdminModule{
    const GROUP_PAGE_SIZE = 10;
	const UNGROUP_PAGE_SIZE = 10;
    const REVIEW_PAGE_SIZE = 20;
    const PAGE_SEARCH_SIZE = 10;
    const COLLECTION_TYPE = 'PropertyValue';
    const FILE_TYPE = 'property_value';
    const POST_TYPE = 'property_value';
    /**
     * разделитель id айтемов\вариантов при редактировании свойства-объекта
     */
    const ITEM_IDS_DELIMITER = ',';
    const OBJ_POST_COUNT = 100000;
    const OBJ_FILE_COUNT = 100000;
    const OBJ_GALLERY_COUNT = 100000;
    const OBJ_ITEMS_COUNT = 100000;
    const OBJ_VARIANTS_COUNT = 100000;
    const OBJ_METRO_COUNT = 100000;
    const OBJ_USERS_COUNT = 100000;
    const OBJ_REGIONS_COUNT = 100000;
    private static $object_counts = array(
        Properties\Post::TYPE_NAME => self::OBJ_POST_COUNT,
        Properties\Gallery::TYPE_NAME => self::OBJ_GALLERY_COUNT,
        Properties\File::TYPE_NAME => self::OBJ_FILE_COUNT,
        Properties\Item::TYPE_NAME => self::OBJ_ITEMS_COUNT,
        Properties\Variant::TYPE_NAME => self::OBJ_VARIANTS_COUNT,
        Properties\Metro::TYPE_NAME => self::OBJ_METRO_COUNT,
        Properties\User::TYPE_NAME => self::OBJ_USERS_COUNT,
        Properties\Region::TYPE_NAME => self::OBJ_REGIONS_COUNT
    );
    /**
     * Значение по умолчанию статуса товара
     */
    const DEFAULT_ITEM_STATUS = ItemEntity::S_HIDE;
    /**
     * Значение по умолчанию статуса варианта
     */
    const DEFAULT_VARIANT_STATUS = Variant::S_HIDE;
    public function index(){
        $type_id = $this->request->query->get('id');
        if (empty($type_id) || $type_id == TypeEntity::DEFAULT_TYPE_ID){
            return $this->notFound();
        }
		$type = TypeEntity::getById($type_id, $this->segment['id']);
		if (empty($type)){
			return $this->notFound();
		}
//		if ($type['allow_children']){
//			$children = $type->getChildren();
//			$first = reset($children);
//			return $this->redirect('/catalog-item/?id=' . $first['id']);
//		}
//        if (!$type['allow_children']){
            $_SESSION['type_back_url'] = urlencode($_SERVER['REQUEST_URI']);
            $this->listItems(1);
//        }
        $types_by_parents = TypeEntity::getAllowChildrenTypesByParents(array(TypeEntity::STATUS_VISIBLE, TypeEntity::STATUS_HIDDEN));
        $all_types_by_levels = TypeEntity::getTypesByLevel(array(TypeEntity::STATUS_HIDDEN, TypeEntity::STATUS_VISIBLE), $type->getCatalog()->getId());
        $this->getAns()
            ->add('types_by_parents', $types_by_parents)
            ->add('current_type', $type)
			->add('all_types_by_levels', $all_types_by_levels)
            ;
    }

    /**
     * Ссылка на объекты корневого каталога по ключу (catalog, orders etc.)
     * обрабатывает ссылку вида /catalog-item/catalogIndex/?key=<catalog_key>
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    public function catalogIndex(){
        $catalog_key = $this->request->query->get('key');
        $catalog = TypeEntity::getByKey($catalog_key);
        if (!empty($catalog)){
            return $this->redirect($this->getModuleUrl() . '?id='.$catalog['id']);
        }
        $this->notFound();
    }
    public function listItems($inner = false){
        if (!$inner){
            $this->setJsonAns();
        }
        $only_count = $this->request->query->get('only_count');
        $type_id = $this->request->query->get('id', TypeEntity::DEFAULT_TYPE_ID);
		$type = TypeEntity::getById($type_id, $this->segment['id']);
        $catalog = $type->getCatalog();
        $page_size = $this->request->query->get('page_size', self::GROUP_PAGE_SIZE);
        $page = $this->request->query->get('page', 1);
        //фильтрация параметров, по которым производится поиск
        if ($type->getCatalog()['key'] == CatalogConfig::REVIEWS_AND_QUESTIONS_KEY){
            $this->request->query->set('sort', array(\App\Configs\ReviewConfig::CREATION_DATE => 0)); // Нам нужна сортировка по position
        } else {
            $this->request->query->set('sort', array('position' => 1)); // Нам нужна сортировка по position
        }
        // Пока прикрываем поиск по проброшенным свойствам
//        if ($catalog['nested_in']) {
//            $types = $type->getNestedInFinalTypes();
//            if (count($types) == 1) {
//                /** @var TypeEntity $search_type */
//                $search_type = reset($types);
//                $search_type_id = $search_type['id'];
//            } else {
//                $search_type_id = $type_id;
//            }
//        } else {
            $search_type_id = $type_id;
//        }
        $search_rules = \App\CatalogMethods::getSearchableRules($this->request, $search_type_id, array('filter_visible' => CatalogConfig::FV_ADMIN),
            $this->segment['id'], $sort_params, $has_variant_prop, $empty_filter_rules);
//        if ($catalog['nested_in'] && !empty($search_type) && !$empty_filter_rules) {
//            $search = CatalogSearch::factory($catalog['key'], $this->segment['id'])
//                ->setTypeId($search_type_id)
//                ->setRules($search_rules)
//                ->setEnableCountByTypes(FALSE)
//                ->setPublicOnly(FALSE)
//                ->searchItemIds();
//            $item_ids = $search->getParentIdsByTypeId($type_id);
//            $items = !empty($item_ids) ? CatalogSearch::factory($catalog['key'], $this->segment['id'])
//                    ->setTypeId($type_id)
//                    ->setRules(array(
//                        Rule::make('id')->setValue($item_ids)
//                    ))
//                    ->setEnableCountByTypes(FALSE)
//                    ->setPublicOnly(FALSE)
//                    ->searchItems(($page-1)*$page_size, $page_size) : array();
//        } else {
            $items = CatalogSearch::factory($catalog['key'], $this->segment['id'])
                ->setTypeId($type_id)
                ->setRules($search_rules)
                ->setEnableCountByTypes(FALSE)
                ->setPublicOnly(FALSE)
                ->searchItems(($page-1)*$page_size, $page_size);
//        }
        if (!empty($only_count)){
            return !empty($items) ? $items->getTotalCount() : 0;
        }
        $editable_properties = PropertyFactory::search($type_id, PropertyFactory::P_NOT_VIEW | PropertyFactory::P_NOT_DEFAULT | PropertyFactory::P_NOT_ENTITY | PropertyFactory::P_NOT_HIDE);
		//список свойств для поиска со значениями
        $search_properties = $type->getSearchableProperties(CatalogConfig::FV_ADMIN, FALSE, FALSE, $this->segment['id']);
        $this->getAns()->add('search_properties', $search_properties)
            ->add('catalog_items', $items )
			->add('catalog_items_count', !empty($items) ? $items->getTotalCount() : 0)
			->add('find_variants', !empty($items) ? $items->getFoundVariants() : 0)
            ->add('editable_properties', $editable_properties)
            ->add('pageSize', $page_size)
            ->add('pageNum', $page)
			->add('current_type', $type)
            ->addFormValue('type_id', $type_id);
    }
    /**
     * @ajax
     */
    public function getIds(){
        $entity = $this->request->query->get('entity');
        if ($entity != 'variants' && $entity != 'items'){
            throw new \LogicException('Param "entity" must be "variants" or "items"');
        }
        $type_id = $this->request->query->get('id', TypeEntity::DEFAULT_TYPE_ID);
        $search_params = \App\CatalogMethods::getSearchableRules($this->request, $type_id, array(), $this->segment['id']);
        if ($entity == 'items'){
            $ids = array_keys(CatalogSearch::factory()->setTypeId($type_id)->setPublicOnly(FALSE)->setRules($search_params)->searchItemIds()->getSearch());
        }elseif($entity == 'variants'){
            $v_ids = CatalogSearch::factory()->setTypeId($type_id)->setPublicOnly(FALSE)->setRules($search_params)->searchVariantIds()->getSearch();
            $ids = !empty($v_ids) ? array_keys($v_ids) : array();
        }
        return json_encode($ids);
    }
    public function create(){
        return $this->run('edit');
    }
    /**
     * Открываем страницу редактирования/создания айтема
     * @return type
     */
    public function edit(){
        $edit_id = $this->request->request->get('id');
        $ajax = TRUE;
        if (empty($edit_id)){
            $edit_id = $this->request->query->get('id');
            $ajax = FALSE;
        }
        if (empty($edit_id)){//значит хотим создать
            $create_item = TRUE;
            $type_id = $this->request->request->get('type_id');
            $ajax = TRUE;
            if (empty($type_id)){
                $type_id = $this->request->query->get('type_id');
                $ajax = FALSE;
            }
            $type = TypeEntity::getById($type_id, $this->segment['id']);
            if (empty($type)){
                return $this->notFound();
            }
            $catalog = $type->getCatalog();
            if ($catalog['dynamic_for']){
                throw new \LogicException('Невозможно создать айтем в динамической категории');
            }
            $entity_class = CatalogConfig::getEntityClass($catalog['key'], 'item');
            $copy_item_id = $this->request->request->get('copy_item');
            if ($copy_item_id) {
                $copy_item = ItemEntity::getById($copy_item_id);
                if (empty($copy_item)){
                    return $this->notFound();
                }
                $item = $copy_item->copyItemToType($type_id, $errors, $entity_class::S_TMP);
                $edit_id = $item['id'];
            } else {
                $edit_id = $entity_class::create($type_id, $entity_class::S_TMP, array(), $errors, $this->segment['id'], $this->request->query->get('parent_id', $this->request->request->get('parent_id')));
            }
		}
        if ($ajax){
            $ans = $this->setJsonAns();
            if (!empty($errors)){
                $ans->setEmptyContent()->setErrors($errors);
                return;
            }
        }
        $item = ItemEntity::getById($edit_id, $this->segment['id']);
        if (empty($item) || $item['status'] == ItemEntity::S_DELETE){
            return $this->notFound();
        }
		$type = $item->getType();
		$item_properties = PropertyFactory::search($type['id'], PropertyFactory::P_NOT_VIEW|PropertyFactory::P_NOT_DEFAULT|PropertyFactory::P_NOT_RANGE|PropertyFactory::P_ITEMS, 'key', 'group', 'parents', array(), $this->segment['id']);
		$specials = array();//спец отметки нужны в спец массиве
		foreach($item_properties as $id => $data){
			if ($data['group']['group'] == '1'){
				$specials[$data['group_title']][$data['key']] = $data;
				unset($item_properties[$id]);
			}
        }
        $nested_types = TypeEntity::search(array('nested_in' => $type['id']));
		$this->getAns()
            ->add('catalog_item_edit', TRUE)
            ->add('catalog_item', $item)
            ->add('catalog_item_variants', count($item->getVariantIds()))
            ->add('nested_types', $nested_types)
			->add('errors', !empty($errors) ? $errors : NULL)
			->add('item_properties', $item_properties)
			->add('properties_available', $type->getPropertiesAvailable())
			->add('specials', $specials)
			->add('current_type', $type)
            ->add('catalogs', TypeEntity::getCatalogs())
        ;
//        $this->itemReviews($item);
//        $this->itemQuestions($item);
        $this->itemSeoMetaTags($item['id']);
        if (!$ajax){
            $this->request->query->set('id', $item['type_id']);
            return $this->getModule('Catalog\Type')->run('catalog');
        }else{
			$this->getAns()->addData('last_update', $item['last_update']);
            // Только для отзывов и вопросов, и только при создании айтема
            // Автоматическая привязка к айтему и установка источника отзыва (сайт/админка) на админку
            if (!empty($create_item) && $item instanceof \Models\CatalogManagement\Positions\Review){
                $prop_values = array();
                if ($type['key'] == ReviewConfig::REVIEWS_KEY){
                    $prop_source = PropertyFactory::search($type['id'], PropertyFactory::P_ALL, 'key', 'type_group', 'parents', array('key' => ReviewConfig::SOURCE));
                    $prop_source = $prop_source[ReviewConfig::SOURCE];
                    $val = NULL;
                    foreach($prop_source['values'] as $v){
                        if ($v['key'] == ReviewConfig::SOURCE_MANAGER) {
                            $val = $v['id'];
                            break;
                        }
                    }
                    $prop_values[ReviewConfig::SOURCE] = array(0 => array('val_id' => NULL, 'value' => $val));
                }
                $product_id = $this->request->request->get('product_id');
                if (!empty($product_id)){
                    $prop_values[ReviewConfig::PRODUCT] = array(0 => array('val_id' => NULL, 'value' => $product_id));
                }
                if (!empty($prop_values)){
                    $item->updateValues($prop_values, $errors);
                }
            }
        }
		//варианты
//		$this->request->request->set('id', $edit_id);
//		$this->itemVariants(true);
    }

    public function itemSeoMetaTags($item_id = NULL){
        $inner = !empty($item_id);
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        $item_id = !empty($item_id) ? $item_id : $this->request->query->get('item_id');
        $item = ItemEntity::getById($item_id);
        if (empty($item)) {
            if (!$inner) {
                $ans->setEmptyContent()->addErrorByKey('item_id', 'empty');
            } else throw new \ErrorException('item_id not found');
        } else {
            $segments = \App\Segment::getInstance()->getAll();
            $tag_form_data = array();
            $persister = \Models\Seo\PagePersister::getInstance();
            foreach($segments as $s_id => $s) {
                $page_uid = rtrim($item->getUrl($s_id), '/');
                $tag = $persister->search(array('pageUID' => $page_uid, 'enabled' => 'any'));
                $post_data = $this->request->request->get('meta_tag_data');
                if (!$inner && !empty($post_data[$s_id])){
                    $tag_data = array_map('trim', $post_data[$s_id]);
                    $tag_data['page_uid'] = $page_uid;
                    // автоматическое выключение пустого мета-тега
                    $tag_data['enabled'] = 0;
                    foreach(array('title', 'description', 'keywords', 'canonical', 'text') as $field){
                        if (!empty($tag_data[$field])){
                            $tag_data['enabled'] = 1;
                        } else {
                            $tag_data[$field] = '';
                        }
                    }
                    if (empty($tag)){
                        $persister->createRule($tag_data);
                    } else {
                        $persister->updateRule($tag['id'], $tag_data);
                    }
                    $tag = $persister->search(array('pageUID' => $page_uid));
                }
                $tag_form_data[$s_id] = $tag;
            }
            $ans->add('catalog_item', $item)
                ->add('item_meta_tag', $tag_form_data)
                ->setFormData(array('meta_tag_data' => $tag_form_data));
            if (!$inner){
                $ans->addData('meta_tag_status', isset($post_data['enabled']) ? $post_data['enabled'] : (!empty($tag['enabled']) ? 1 : 0));
            }
        }
    }

    /**
     * Вкладка отзывов о товаре
     * @param bool|FALSE $item
     * @throws \Exception
     */
    public function itemReviews($item = FALSE){
        $inner = !empty($item);
        if ($inner) {
            if (!CatalogConfig::ENABLE_REVIEWS) {
                return;
            }
            $ans = $this->getAns();
        } else {
            $ans = $this->setJsonAns();
            if (!CatalogConfig::ENABLE_REVIEWS) {
                $ans->setEmptyContent();
                return;
            }
            $item_id = $this->request->request->get('id');
            $item = !empty($item_id) ? ItemEntity::getById($item_id) : NULL;
            if (empty($item)) {
                $ans->setEmptyContent()->addErrorByKey('id', 'empty');
                return;
            }
        }
        $type = $item->getType();
        $catalog = $type->getCatalog();
        if ($catalog['key'] != CatalogConfig::PRODUCT_CATALOG_FOR_REVIEWS_AND_QUESTIONS){
            if (!$inner) {
                $ans->setEmptyContent();
            }
            return;
        }
        $review_catalog = TypeEntity::getByKey(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY);
        $review_type = TypeEntity::getByKey(ReviewConfig::REVIEWS_KEY, $review_catalog['id']);
        $reviews = CatalogSearch::factory(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY)
            ->setTypeId($review_type['id'])
            ->setRules(array(
                Rule::make(ReviewConfig::PRODUCT)->setValue($item['id']),
                Rule::make(ReviewConfig::CREATION_DATE)->setOrder(1),
//                Rule::make(ReviewConfig::STATUS)->setValue(ReviewConfig::STATUS_ACCEPT)->setSearchByEnumKey()
            ))
            ->searchItems();
        $new_reviews_count = CatalogSearch::factory(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY)
            ->setTypeId($review_type['id'])
            ->setRules(array(
                Rule::make(ReviewConfig::PRODUCT)->setValue($item['id']),
                Rule::make(ReviewConfig::STATUS)->setValue(ReviewConfig::STATUS_NEW)->setSearchByEnumKey()
            ))
            ->searchItemIds()
            ->count();
        $ans->add('item_reviews', $reviews)
            ->add('reviews_type', $review_type)
            ->add('new_reviews_count', $new_reviews_count)
            ->add('enable_reviews', TRUE);
    }

    /**
     * Вкладка вопросов о товаре
     * @param bool|FALSE $item
     * @throws \Exception
     */
    public function itemQuestions($item = FALSE){
        $inner = !empty($item);
        if ($inner) {
            if (!CatalogConfig::ENABLE_QUESTIONS) {
                return;
            }
            $ans = $this->getAns();
        } else {
            $ans = $this->setJsonAns();
            if (!CatalogConfig::ENABLE_QUESTIONS) {
                $ans->setEmptyContent();
                return;
            }
            $item_id = $this->request->request->get('id');
            $item = !empty($item_id) ? ItemEntity::getById($item_id) : NULL;
            if (empty($item)) {
                $ans->setEmptyContent()->addErrorByKey('id', 'empty');
                return;
            }
        }
        $type = $item->getType();
        $catalog = $type->getCatalog();
        if ($catalog['key'] != CatalogConfig::PRODUCT_CATALOG_FOR_REVIEWS_AND_QUESTIONS){
            if (!$inner) {
                $ans->setEmptyContent();
            }
            return;
        }
        $questions_catalog = TypeEntity::getByKey(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY);
        $questions_type = TypeEntity::getByKey(ReviewConfig::QUESTIONS_KEY, $questions_catalog['id']);
        $questions = CatalogSearch::factory(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY)
            ->setTypeId($questions_type['id'])
            ->setRules(array(
                Rule::make(ReviewConfig::PRODUCT)->setValue($item['id']),
                Rule::make(ReviewConfig::CREATION_DATE)->setOrder(1),
//                Rule::make(ReviewConfig::STATUS)->setValue(ReviewConfig::STATUS_ACCEPT)->setSearchByEnumKey()
            ))
            ->searchItems();
        $new_questions_count = CatalogSearch::factory(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY)
            ->setTypeId($questions_type['id'])
            ->setRules(array(
                Rule::make(ReviewConfig::PRODUCT)->setValue($item['id']),
                Rule::make(ReviewConfig::STATUS)->setValue(ReviewConfig::STATUS_NEW)->setSearchByEnumKey()
            ))
            ->searchItemIds()
            ->count();
        $ans->add('item_questions', $questions)
            ->add('questions_type', $questions_type)
            ->add('new_questions_count', $new_questions_count)
            ->add('enable_questions', TRUE);
    }

    /**
     * Установка статуса для отзывов и вопросов (опубликован/отклонен)
     * POST-data:
     * id - один или несколько айдишников отзывов
     * status – 1 опубликован, 0 — отклонен
     */
    public function setReviewStatus(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $id = $this->request->request->get('id');
        if (empty($id)){
            $ans
                ->addErrorByKey('id', 'empty');
        } else {
            $records = CatalogSearch::factory(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY)
                ->setRules(array(Rule::make('id')->setValue($id)))
                ->searchItems()
                ->getSearch();
            if (empty($records)){
                $ans
                    ->addErrorByKey('id', 'empty');
            } else {
                $catalog = TypeEntity::getByKey(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY);
                $property = PropertyFactory::getByKey(ReviewConfig::STATUS, $catalog['id']);
                $property = reset($property);
                $status = $this->request->request->get('status') ? ReviewConfig::STATUS_ACCEPT : ReviewConfig::STATUS_REJECT;
                $status_value = NULL;
                foreach($property['values'] as $v){
                    if ($v['key'] == $status){
                        $status_value = $v['id'];
                    }
                }
                $properties = array(
                    ReviewConfig::STATUS => array(0 => array('val_id' => NULL, 'value' => $status_value))
                );
                foreach($records as $i){
                    $i->updateValues($properties);
                }
                $ans->addData('ids', array_keys($records));
            }
        }
    }
    private function setItemsVisible($check, $vis){
        $ans = $this->setJsonAns();
        $vis = empty($vis) ? 0 : $vis;
        $statuses = [
            0 => ItemEntity::S_HIDE,
            1 => ItemEntity::S_PUBLIC,
            2 => ItemEntity::S_TEMPORARY_HIDE,
        ];
        $status = $statuses[$vis];
        $this->getAns()->addData('status', $status);
        if (!empty($check) && is_array($check)){
            $items = ItemEntity::factory($check);
            foreach ($items as $item){
                if ($vis){
                    $variants = $item->getVariants($statuses);
                    $type = $item->getType();
                    $catalog = $type->getCatalog();
                    /**
                     * @TODO пока проверяем only_items у каталога, нужно решить, что делать с only_items типа
                     * так и смотреть у каталога, или задавать конечным типам при создании от каталога
                     * что делать при смене only_items у каталога
                     */
                    if ($catalog['only_items'] || !empty($variants)){
                        $item->update(array('status' => $status));
                    }else{
                        $errors[$item['id']] = 'В данном айтеме нет вариантов, поэтому его нельзя показать';
                    }
                }else{
                    $item->update(array('status'=>ItemEntity::S_HIDE));
                }
            }
			if (!empty($errors)){
				$ans->setEmptyContent()
                    ->setErrors($errors);
			}
        }
        if ($this->request->request->get('getList')){
            $this->request->query->set('id', $this->request->request->get('type_id'));
            return $this->run('listItems');
        }else{
            $ans->setEmptyContent();
        }
    }
    /**
     * Сменить значения свойств у выбранных с помощью фильтра товаров (или вариантов, подходящих под фильтр)
     * @return JSON
     */
    public function changeFilteredItemsProp(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $errors = array();
        $type_id = $this->request->request->get('type_id');
        if (empty($type_id)){
            $errors['exception'] = 'empty type_id';
            $ans->setErrors($errors);
            return;
        }
        $type = TypeEntity::getById($type_id);
        if (empty($type)){
            $errors['exception'] = 'type not_found';
            $ans->setErrors($errors);
            return;
        }
        if ($type['id'] == TypeEntity::DEFAULT_TYPE_ID){
            $errors['exception'] = 'Нельзя менять свойства из корневого каталога';
            $ans->setErrors($errors);
            return;
        }
        $values = $this->request->request->get('values');
        if (empty($values)){
            $errors['exception'] = 'Не выбрано ни одно свойство';
            $ans->setErrors($errors);
            return;
        }
//        if (self::checkDelayUpdatePropertiesTask($count, $prop_keys, TypeEntity::getById($type_id))){
            $errors['message'] = 'Выполнение задачи требует много времени. Поэтому она поставлена в список задач. Отчет о выполнении будет отправлен на email.';
            \Models\CronTask::add(array(
                'type' => \Models\CronTasks\UpdateProps::getType(),
                'data' => array(
                    'catalog_id' => $type->getCatalog()->getId(),
                    'properties_values' => $values,
                    'type_id' => $type_id,
                    'type_title' => $type['title'],
                    'request_data' => $this->request->request->all()
                ),
                'user_id' => $this->account->getUser()->getId(),
                'segment_id' => $this->segment['id']
            ));
            //запарно на два фронта работать
//        }elseif (!empty($items_data)){//обновляем свойства
//            self::updateProperties($type_id, $this->request->request->all(), $values, $errors, $this->segment['id']);
//            if (empty($errors['unique']) && (!empty($errors['items']) || !empty($errors['variants']))){
//                $errors['unique'] = array();
//            }
//            if (!empty($errors['items'])){
//                foreach ($errors['items'] as $err){
//                    if (!empty($err['unique'])){
//                        $errors['unique'] = $errors['unique'] + $err['unique'];
//                    }
//                }
//                unset($errors['items']);
//            }
//            if(!empty($errors['variants'])){
//                foreach ($errors['variants'] as $err){
//                    if (!empty($err['unique'])){
//                        $errors['unique'] = $errors['unique'] + $err['unique'];
//                    }
//                }
//                unset($errors['variants']);
//            }
//        }
//        if (!empty($errors)){
//            $serviceErrorProperties = ItemEntity::getServiceErrorProperties($errors, $type_id, PropertyFactory::search($type_id, PropertyFactory::P_ALL));
//            $result['errors'] = $errors;
//            if (!empty($serviceErrorProperties)){
//                $result['service_error_properties'] = $serviceErrorProperties;
//            }
//            $ans->setErrors($errors);
//        }else{
            $ans->addData('status', 'ok');
//        }
    }
	/**
     * Сохранение свойств айтема
	 * @ajax
	 */

	public function save(){
		//print_r($_POST);
		//if(!empty($_POST['is_arda'])){
			//setArdState(1);
		//}else{
			//setArdState(0);
		//}

		$this->setJsonAns();
		$edit_id = $this->request->request->get('id');
        $item = ItemEntity::getById($edit_id);
        if (empty($item) || $item['status'] == ItemEntity::S_DELETE){
            $this->getAns()->addErrorByKey('exception', 'Не найден айтем c id ' . $edit_id)->setEmptyContent();
            return;
        }
//        $last_update = $this->request->request->get('last_update');
//        if ($last_update < $item['last_update']){
//            $this->getAns()->addErrorByKey('obj', 'already_changed')->setEmptyContent();
//            return;
//        }
		$item_properties = $item->getPropertyList('key');
		$errors = array();
		$type = TypeEntity::getById($item['type_id']);
		$data = $this->request->request->get('q');
        if (empty($data)){
            $this->getAns()->setEmptyContent()->addErrorByKey('exception', 'Отсутствуют передаваемы данные. Параметр «q»');
            return;
        }
		//сохранение
        $json_data = json_decode($data, TRUE);
        if (!empty($json_data['properties'])){
            $user_send_properties = $json_data['properties'];
            unset($json_data['properties']);
        }else{
            $user_send_properties = array();
        }
        ksort($user_send_properties);
        //проверка значений
        foreach ($user_send_properties as $segment_id => $u_props){
            $item_properties_values[$segment_id] = self::filterJsonParams($item_properties, $u_props);
            if (!empty($segment_id) && !empty($item_properties_values[0])) {
                // Закидываем несегментированные значения в каждый сегмент
                $item_properties_values[$segment_id] = $item_properties_values[$segment_id] + $item_properties_values[0];
            }
        }
        if (!empty($item_properties_values)) {
            if (count($item_properties_values) > 1) {
                // и убираем пустой сегмент из сегментированных данных
                unset($item_properties_values[0]);
            }
            // Поскольку в одном сегменте может не быть ошибок, а в другом они есть, проверяем входные данные
            // по всем сегментам до попытки сохранения
            foreach ($item_properties_values as $segment_id => $u_props){
                ItemEntity::checkProperties($item['type_id'], $u_props, $errors, $segment_id, $item['id']);
            }
            if (!empty($errors)){//избавляемся от дублей (могут быть в нулевом сегменте)
                $errors = array_map('unserialize', array_unique(array_map('serialize', $errors)));
            }
            if (empty($errors)) {
                foreach ($item_properties_values as $segment_id => $u_props){
                    $item->updateValues($u_props, $errors, $segment_id);
                }
                if (!empty($errors)){//избавляемся от дублей (могут быть в нулевом сегменте)
                    $errors = array_map('unserialize', array_unique(array_map('serialize', $errors)));
                }
            }
        }
        if (empty($errors)){
            if ($item['status'] == ItemEntity::S_TMP){
                $catalog = $type->getCatalog();
                $status = CatalogConfig::getDefaultItemVisible($catalog['key']);
                if (is_null($status)){
                    $status = static::DEFAULT_ITEM_STATUS;
                }
                // Сделано, чтобы квартиры не скрывались из-за скрытого этажа или корпуса
                if ($catalog['key'] == CatalogConfig::CATALOG_KEY_REAL_ESTATE
                    && !$type['nested_in_final']
                    && $type['key'] != RealEstateConfig::CATEGORY_KEY_COMPLEX
                ) {
                    $status = ItemEntity::S_PUBLIC;
                }
                $json_data['status'] = $status;
            }
            $item->updateParams($json_data, $errors);//сохраняем
        }
        $item->save();
        $this->getAns()->addData('item_id', $edit_id);
		if(!empty($errors)){
			$serviceErrorProperties = array();
			foreach ($errors as $s_id => $err){
				$serviceErrorProperties += ItemEntity::getServiceErrorProperties(
					$err,
					$item['type_id'],
					PropertyFactory::search($type['id'], PropertyFactory::P_NOT_VIEW, 'key', 'position', 'parents', array(), NULL)
				);
			}
            $this->getAns()->setErrors($errors)->setEmptyContent();
			if (!empty($serviceErrorProperties)){
                $this->getAns()->addData('service_error_properties', $serviceErrorProperties);
			}
		}else{
            $this->getAns()->setStatus('ok');
		}
        $this->request->query->set('id', $type['id']);
        ItemEntity::clearCache($edit_id);
        $this->setContentData($item);
        $answer_action = $this->request->request->get('answer_action');
        return $this->run(in_array($answer_action, array('edit', 'listItems')) ? $answer_action : 'edit');
	}




	function setArdState($aState){
		echo '<pre>';
		print_r($_POST);
	}
    /**
     * Дополнительные данные в ответ
     * @return type
     */
    protected function setContentData(ItemEntity $item){
        return;
    }
    /**
     * фильтрация редактируемых свойств, приходящие от пользователя,
     * отдаются только те свойства, которые есть в $properties
     * @param \Models\CatalogManagement\Properties\Property[] $properties
     * @param array $values
     * @param int|null $segment_id
     * @return array
     * @throws \Exception
     * @TODO не уверена, что хак для добавления нового енума всё ещё нужен
     */
	private static function filterJsonParams($properties, $values, $segment_id = NULL){
        $params = array();
        if (empty($values)){
            return $params;
        }
        foreach ($values as $prop_key => $prop_val){
            if (empty($properties[$prop_key]) || $properties[$prop_key]['read_only'] == 1){
                continue;//убираем неизменяемые и несуществующие
            }
            /* @var $property \Models\CatalogManagement\Properties\Property */
            $property = $properties[$prop_key];
            $new_enum_add = FALSE;
//            $pos = 1;
            foreach ($prop_val as $v_num => $v){
                //очищаем переданные данные от мусора
                //нельзя, т.к. нам надо узнать, что множественные не заполнены
//                if (!empty($v['options']) && !empty($v['options']['delete']) && empty($v['val_id'])){
//                    continue;
//                }
                //хак для добавления нового енума
                if ($property['data_type'] == \Models\CatalogManagement\Properties\Enum::TYPE_NAME && $property['set'] != 1 && count($prop_val) == 2 && $v_num == 1){
                    if (!is_array($v['value'])){
                        $v['value'] = array($v['value']);
                    }
                    $enum_id = NULL;
                    foreach ($v['value'] as $s_id => $title){
                        if (!empty($title)){
                            /* @var $property \Models\CatalogManagement\Properties\Enum */
                            $e = '';
                            if (is_null($enum_id)){
                                $enum_id = $property->addEnumValue($title, NULL, NULL, $e);
                                $new_enum_add = TRUE;
                            }
                        }
                    }
                    if (!is_null($enum_id)){
                        $v['value'] = $enum_id;
                    }
                }
                $v['value'] = trim($v['value']);
                $params[$prop_key][$v_num] = $v;
                $params[$prop_key][$v_num]['value'] = (($v['value'] == '' && $property['data_type'] != \Models\CatalogManagement\Properties\String::TYPE_NAME && $property['data_type'] != \Models\CatalogManagement\Properties\Text::TYPE_NAME) ? NULL : $v['value']);
//                if (!isset($v['options']['position'])){
//                    $params[$prop_key][$v_num]['options']['position'] = $pos++;
//                }
            }
            if ($new_enum_add){//в такой ситуации это хак для добавления нового енума
                unset($params[$prop_key][0]);
            }
        }
        return $params;
	}
	protected function getRequestParam($v){
		return $this->request->request->get($v, $this->request->query->get($v));
	}
    /**
     * Попап для редактирования свойств-объектов
     * универсальный для всех, уникальные обработчики для ыымыразных типов объектов разнесены в отдельные функции
     */
    public function editObjPropertyPage(){
        $this->setJsonAns();
        $errors = array();
        $entity_id = $this->request->request->get('entity_id');
        $property_id = $this->request->request->get('property_id');
        $segment_id = $this->request->request->get('segment_id');
        $object_id = $this->request->request->get('object_id');
        $action = $this->request->request->get('action', 'edit');
        if (empty($entity_id) || empty($property_id)){
            $errors['exception'] = 'empty ' . (empty($entity_id) ? 'entity_id' : 'property_id');
        }
        if (empty($errors)){
            $property = PropertyFactory::getById($property_id, $this->segment['id']);
            if (empty($property)){
                $errors['exception'] = 'property not_found';
            } else {
                $entity = $property['multiple'] ? Variant::getById($entity_id, $this->segment['id']) : ItemEntity::getById($entity_id, $this->segment['id']);
                if (empty($entity) || empty($entity['properties'][$property['key']])){
                    $errors['exception'] = 'entity not_found';
                } else {
                    $result = $this->routeObjPropertyTypes($entity, $property, $object_id, $segment_id, $errors, $action);
                    if (!empty($errors)){
                        $this->getAns()->setEmptyContent()->setErrors($errors);
                        return;
                    }elseif($result){
                        $this->getAns()->setStatus('ok');
                        $this->routeObjPropertyTypes(
                            $entity,
                            $property,
                            $object_id,
                            $segment_id,
                            $errors,
                            in_array($property['data_type'], array(
                                Properties\File::TYPE_NAME,
                                Properties\Post::TYPE_NAME
                            )) && $property['set'] ? 'viewrow' : 'view');
                    }
                }
            }
        }
        if (!empty($errors)){
            $this->getAns()->setEmptyContent()->setErrors($errors);
        } else {
				$entity->save();
				if ($property['multiple']){
					Variant::clearCache($entity_id);
					$entity = Variant::getById($entity_id, $this->segment['id']);
				} else {
					ItemEntity::clearCache($entity_id);
					$entity = ItemEntity::getById($entity_id, $this->segment['id']);
				}
            $this->getAns()->add('property', $property)
                ->add('entity', $entity)
                ->add('segment_id', $segment_id)
                ->add('catalogs', TypeEntity::getCatalogs())
                ->addFormValue('entity_id', $entity_id)
                ->addFormValue('property_id', $property_id)
                ->addFormValue('segment_id', $segment_id)
                ->addFormValue('object_id', $object_id)
                ->add('only_list', $this->request->request->get('only_list'))
                ->addData('last_update', !empty($entity) ? $entity['last_update'] : NULL);
        }
    }
    /**
     * Определяем, куда направить запрос
     * @param CatalogPosition $entity
     * @param Property $property
     * @param int $object_id для просмотра не требуется
     * @param int $segment_id
     * @param array $errors
     * @param string $action виды запросов - редактирование, просмотр, просмотр одной строки множественного (edit|view|viewrow)
     * @return type
     */
    private function routeObjPropertyTypes(CatalogPosition $entity, Property $property, &$object_id, $segment_id, &$errors, $action = 'edit'){
        if (!in_array($action, array('edit', 'view', 'viewrow'))){
            $this->getAns()->addErrorByKey('exception', 'неверно задан параметр «action» ' . $action);
            return;
        }
        $method_name = $action . 'CatalogPosition' . ($action != 'view' ? ucfirst($property['data_type']) : 'Entity');
        if ($action == 'view' && method_exists($this, 'viewCatalogPosition' . ucfirst($property['data_type']))) {
            $method_name = 'viewCatalogPosition' . ucfirst($property['data_type']);
        }
        $this->getAns()->setTemplate('Modules/Catalog/Item/ObjProps'.ucfirst($action).'/' . $property['data_type'] . '.tpl');
        if (!method_exists($this, $method_name)){
            $errors['property'] = 'wrong_data_type';
            return;
        }
        return $this->$method_name($entity, $property, $object_id, $segment_id, $errors);
    }
    /**
     * Редактирование свойства-статьи
     * @param CatalogPosition $entity
     * @param Property $property
     * @param int $object_id
     * @param int $segment_id
     * @param array $errors
     * @throws \Models\ContentManagement\Exception
     */
    private function editCatalogPositionPost(CatalogPosition $entity, Property $property, &$object_id, $segment_id, &$errors){
        if ($this->request->query->has('second_segment') && empty($object_id)) {
            throw new \ErrorException('Не передан object_id для нового поста свойства #' . $property['key'] . ' сегмента #' . $segment_id);
        }
        $save = $this->request->request->get('save');
        $post = !empty($object_id) ? PostEntity::getById($object_id, $this->segment['id']) : NULL;
        if (empty($post)){
            $object_id = PostEntity::create(self::POST_TYPE);
            $post = PostEntity::getById($object_id, $this->segment['id']);
            $entity->updateValue($property['id'], $object_id, $errors, $segment_id);
            if ($property['segment']){//если свойство сегментированное, то создаем пустые посты сразу во всех сегментах
                $segments = \App\Segment::getInstance()->getAll();
                $obj_ids = array();
                foreach ($segments as $s_id => $s){
                    if ($s_id != $segment_id){
                        $obj_id = PostEntity::create(self::POST_TYPE);
                        $obj_ids[$s_id] = $obj_id;
                        $entity->updateValue($property['id'], $obj_id, $errors, $s_id);
                    }
                }
                $this->getAns()
                    ->addData('object_ids', $obj_ids);
            }
            if (!empty($errors)){
                $this->getAns()->addErrorByKey('prop', $errors);
                return;
            }else{
                $this->getAns()->add('create', 1);
            }
        }
        $status_list = PostEntity::getPostStatusList();
        //Внутри функции статус опубликован меняется на статус закрыт (а названия для удобства интерфейса меняются в обратную сторону), т.к. для статических страниц не предусмотренно комментирование
        $status_list[PostEntity::STATUS_CLOSE] = $status_list[PostEntity::STATUS_PUBLIC];
        unset($status_list[PostEntity::STATUS_PUBLIC]);
        $this->getAns()
            ->add('status_list', $status_list)
            ->add('object', $post)
            ->setFormData($post->asArray());
        if (!$save){
            return FALSE;
        }
        $params = Validator::getInstance($this->request)->checkFewResponseValues(array(
            'title' => array('type' => 'checkString', 'options' => array('empty' => true)),
            'annotation' => array('type' => 'checkString', 'options' => array('empty' => true)),
            'text' => array('type' => 'checkString', 'options' => array('empty' => true)),
        ), $errors);
        if (!empty($params['title']) || !empty($params['annotation']) || !empty($params['text'])){
            $status = $this->request->request->get('status');
            if (!empty($status) && !empty($status_list[$status])) {
                $params['status'] = $status;
            }
            $params['segment_id'] = !empty($segment_id) ? $segment_id : NULL;
            if ($post->edit($params, $errors) && \LPS\Config::ENABLE_LOGS && class_exists('\Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValuesLogger')){
                \Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValuesLogger::factory()->onPropertyEntityUpdate($entity, $property, $object_id, $post['title'], $segment_id);
            }
        }
        return TRUE;
    }
    /**
     * Редактирование свойства-объекта типа "файл"
     * @param CatalogPosition $entity
     * @param Property $property
     * @param int $object_id
     * @param int $segment_id
     * @return NULL
     */
    private function editCatalogPositionFile(CatalogPosition $entity, Property $property, &$object_id, $segment_id, &$errors){
        if (!empty($object_id)){
            $file = File::getById($object_id);
            $this->getAns()->add('object', $file)->setFormData($file->asArray());
        }
		$save = $this->request->request->get('save');
        if (!$save){
            return FALSE;
        }
        /* @var $ufile \Symfony\Component\HttpFoundation\File\UploadedFile */
        $ufile = $this->request->files->get('file');
        $file_path = $this->request->request->get('file_path');
        if (empty($ufile) && !empty($file_path)){
            if (!file_exists($file_path)){
                $this->getAns()->addErrorByKey('file', 'Этот файл уже загружался, надо выбрать другой');
                return;
            }
            $ufile = new \Symfony\Component\HttpFoundation\File\UploadedFile($file_path, $this->request->request->get('file_name'));
        }
        $title = $this->request->request->get('title');
        $title = !empty($title) ? $title : (!empty($ufile) ? $ufile->getClientOriginalName() : NULL);
        $changed = FALSE;
        // Валидация типа и размера файла
        if (!empty($ufile) && !empty($property['values'])){
            if (!empty($property['values']['format'])){
                $original_file_name = $ufile->getClientOriginalName();
                if (!preg_match('~\.('.implode('|', array_map('trim', explode(',', $property['values']['format']))).')$~i', $original_file_name, $regs)){
                    $this->getAns()->addErrorByKey('file', 'Неверный формат файла, допустимые форматы - ' . implode(', ', array_map('trim', explode(',', $property['values']['format']))));
                    return;
                }
            }
            if (!empty($property['values']['max'])){
                if (preg_match('~(\d+)([MmМмKkКк])?~u', $property['values']['max'], $matches)){
                    $max_file_size = $matches[1] * (!empty($matches[2]) ? (strpos('MmМм', $matches[2]) !== false ? 1048576 : 1024) : 1);
                    if ($ufile->getSize() > $max_file_size){
                        $this->getAns()->addErrorByKey('file', 'Слишком большой размер файла.');
                        return;
                    }
                }
            }
        }
		$directory = $entity::CATALOG_IDENTITY_KEY . $entity['id'];
		if (!empty($property['values']['swfzip']) && !empty($ufile)){
			$original_file_ext = $ufile->getClientOriginalExtension();
			if ($original_file_ext != 'zip'){
				throw new \Exception('Неверный формат файла, допустимые форматы - zip');
			}
			//теперь разархивируем
			$directory = $entity['id'] . '_' . $property['id'];
			$to_path = File::getFilePath() . $directory . '/';
			if (!file_exists($to_path)){
				FS::makeDirs($to_path);
			}
            $to_path = realpath($to_path);
			$zip = new \ZipArchive();
			$result = $zip->open($ufile->getRealPath());
			if ($result !== TRUE){
				$zip->close();
                unlink($ufile->getRealPath());//zip-ом воспользовались, файл можно удалять
				$this->getAns()->addErrorByKey('file', 'Невозможно прочесть файл, код ошибки: ' . $result);
				return;
			}
            $tmp_path = preg_replace('~\/$~', '', $to_path) . 'tmp/';
            if (!file_exists($tmp_path)){
                FS::makeDirs($tmp_path);
            }
            $tmp_path = realpath($tmp_path);
			if (!$zip->extractTo($tmp_path)){
				$zip->close();
                unlink($ufile->getRealPath());//zip-ом воспользовались, файл можно удалять
				$this->getAns()->addErrorByKey('file', 'Невозможно распаковать архив');
				return;
			}
			$zip->close();
            unlink($ufile->getRealPath());//zip-ом воспользовались, файл можно удалять
			$files = glob($tmp_path . DIRECTORY_SEPARATOR . '*.xml');
			if (empty($files)){
				FS::removeDirectory($tmp_path);
				$this->getAns()->addErrorByKey('file', 'Не найден обязательный файл в архиве *.xml');
				return;
			}
			if (count($files) !== 1){
				FS::removeDirectory($tmp_path);
				$this->getAns()->addErrorByKey('file', 'В архиве должен быть один файл с расширением *.xml');
				return;
			}
            //если нет ошибок, удаляем старую папку, заменяем на новую
            FS::removeDirectory($to_path);
            rename($tmp_path, $to_path);
            $files = glob($to_path . DIRECTORY_SEPARATOR . '*.xml');
			$file_xml = reset($files);
			$ufile = new \Symfony\Component\HttpFoundation\File\UploadedFile($file_xml, basename($file_xml));
		}
        if (empty($object_id)){
            if (empty($ufile)){
                $this->getAns()->addErrorByKey('file', \Models\Validator::ERR_MSG_EMPTY);
                return;
            }
            $object_id = File::add($title, $ufile, $error, $directory, TRUE, !empty($property['values']['swfzip']) ? NULL : TRUE);
            if (!empty($error)){
                $errors['file'] = $error;
                $this->getAns()->addErrorByKey('file', $error);
                return;
            }
            $file = File::getById($object_id);
            $params['type'] = self::FILE_TYPE;
            if (empty($file)) {
                $this->getAns()->addErrorByKey('exception', 'Не найден файл с id: ' . $object_id);
                return;
            }
            $entity->updateValue($property['id'], $object_id, $errors, $segment_id);
            if (!empty($errors)){
                $this->getAns()->addErrorByKey('prop', $errors);
                return;
            }else{
                $this->getAns()->add('create', 1);
            }
        }elseif (!empty($ufile)){
            $result = $file->reload($ufile, TRUE, !empty($property['values']['swfzip']) ? NULL : TRUE);
            if ($result !== true){
                $this->getAns()->addErrorByKey('file', $result);
                return;
            }
            $changed = TRUE;
        }
        if (!empty($title)) {
            $params['title'] = $title;
        }
        if (!empty($params) && $file->edit($params)){
            $changed = TRUE;
        }
        if ($changed && \LPS\Config::ENABLE_LOGS && class_exists('\Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValuesLogger')){
            \Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValuesLogger::factory()->onPropertyEntityUpdate($entity, $property, $object_id, $file['title'], $segment_id);
        }
        $this->getAns()->addData('file_id', $object_id);
        return TRUE;
    }
    private function editCatalogPositionImage(CatalogPosition $entity, Property $property, &$object_id, $segment_id, &$errors){
        $save = $this->request->request->get('save');
        $image = !empty($object_id) ? \Models\ImageManagement\Image::getById($object_id) : NULL;
        if (!empty($image)){
            $this->getAns()->add('object', $image)->setFormData($image->asArray());
        }
        if (!$save){
            return FALSE;
        }
        /* @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
        $file = $this->request->files->get('image');
        $height = $this->request->request->get('height');
        $width = $this->request->request->get('width');
        $title = $this->request->request->get('title');
        $text = $this->request->request->get('text');
        $changed = FALSE;
        if (!empty($file)){
            if (empty($image)) {
                $image = \Models\ImageManagement\Image::add($file, '', FALSE, $error);
                if (!empty($errors)){
                    $this->getAns()->addErrorByKey('prop', $errors);
                }else{
                    $object_id = $image['id'];
                    $entity->updateValue($property['id'], $image['id'], $errors, $segment_id);
                    $this->getAns()->add('create', 1);
                }
            } else {
                $error = $image->reload($file);
                if (empty($error)){
                    $changed = TRUE;
                }
            }
            if (!empty($error)){
                $errors['save'] = $error;
            }
            if (!is_null($image)){
                $this->getAns()->addData('image_url', $image->getUrl($width, $height));
            }
        }elseif (empty($image)) {
            $errors['image'] = Validator::ERR_MSG_EMPTY;
            return false;
        }
        if (empty($image)){
            throw new \Exception('Не получилось найти изображение');
        }
        if ($changed || $image->update(array('title' => !empty($title) ? $title : $file->getClientOriginalName(), 'text' => $text))){
            if (\LPS\Config::ENABLE_LOGS && class_exists('\Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValuesLogger')){
                \Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValuesLogger::factory()->onPropertyEntityUpdate($entity, $property, $object_id, '', $segment_id);
            }
        }
        return TRUE;
    }
    private function editCatalogPositionGallery(CatalogPosition $entity, Property $property, &$object_id, $segment_id, &$errors){
        if (empty($object_id)){
            $object_id = \Models\ImageManagement\Collection::create(self::COLLECTION_TYPE);
            $entity->updateValue($property['id'], $object_id, $errors, $segment_id);
            if (!empty($errors)){
                $this->getAns()->addErrorByKey('prop', $errors);
            }else{
                $this->getAns()->add('create', 1);
            }
        }
        if ($this->request->request->get('changed') && \LPS\Config::ENABLE_LOGS && class_exists('\Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValuesLogger')){
            \Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValuesLogger::factory()->onPropertyEntityUpdate($entity, $property, $object_id, '', $segment_id);
        }
        $collection = \Models\ImageManagement\Collection::getById($object_id);
        $this->getAns()->add('object', $collection);
        if ($this->request->request->get('save')){
            return TRUE;
        }
        return FALSE;
    }
    /**
     * Эта функция будет использоваться, когда будем делать добавление элементов каталогов через фильтр
     * @param CatalogPosition $entity
     * @param Property $property
     * @param int|null $object_id
     * @param int|null $segment_id
     * @param array $errors
     */
    private function editCatalogPositionItem(CatalogPosition $entity, Property $property, &$object_id, $segment_id, &$errors){
        if ($property['values']['edit_mode'] == \Models\CatalogManagement\Properties\CatalogPosition::SELECT_MODE_EDIT_POPUP) {
            $save = $this->request->request->get('save');
            if (empty($object_id)) {
                $catalog = TypeEntity::getById($property['values']['catalog_id'], $segment_id);
                $entity_class = CatalogConfig::getEntityClass($catalog['key'], 'item');
                $object_id = $entity_class::create($catalog['id'], $entity_class::S_TMP, array(), $errors, $segment_id);
                if (empty($errors)) {
                    $entity->updateValue($property['id'], $object_id, $errors, $segment_id);
                }
            }
            $item = !empty($object_id) ? ItemEntity::getById($object_id, $segment_id) : null;
            if (empty($item)) {
                throw new \ErrorException('Не удалось создать объект');
            }
            $item_properties = PropertyFactory::search($catalog['id'], PropertyFactory::P_NOT_VIEW|PropertyFactory::P_NOT_DEFAULT|PropertyFactory::P_NOT_RANGE|PropertyFactory::P_ITEMS, 'key', 'group', 'parents', array(), $segment_id);
            $this->getAns()
                ->add('object', $item)
                ->add('catalog_item_edit', TRUE)
                ->add('catalog_item', $item)
                ->add('catalog_item_variants', count($item->getVariantIds()))
                ->add('item_properties', $item_properties)
                ->add('properties_available', $catalog->getPropertiesAvailable())
                ->add('current_type', $catalog)
                ->add('catalogs', TypeEntity::getCatalogs());
            if (!$save){
                return FALSE;
            }
            $item_properties = $item->getPropertyList('key');
            $errors = array();
            $type = $item->getType();
            $data = $this->request->request->get('q');
            if (empty($data)){
                $this->getAns()->setEmptyContent()->addErrorByKey('exception', 'Отсутствуют передаваемы данные. Параметр «q»');
                return;
            }
            //сохранение
            $json_data = json_decode($data, TRUE);
            if (!empty($json_data['properties'])){
                $user_send_properties = $json_data['properties'];
                unset($json_data['properties']);
            }else{
                $user_send_properties = array();
            }
            ksort($user_send_properties);
            //проверка значений
            foreach ($user_send_properties as $segment_id => $u_props){
                $item_properties_values[$segment_id] = self::filterJsonParams($item_properties, $u_props);
                if (!empty($segment_id) && !empty($item_properties_values[0])) {
                    // Закидываем несегментированные значения в каждый сегмент
                    $item_properties_values[$segment_id] = $item_properties_values[$segment_id] + $item_properties_values[0];
                }
            }
            if (count($item_properties_values) > 1) {
                // и убираем пустой сегмент из сегментированных данных
                unset($item_properties_values[0]);
            }
            // Поскольку в одном сегменте может не быть ошибок, а в другом они есть, проверяем входные данные
            // по всем сегментам до попытки сохранения
            foreach ($item_properties_values as $segment_id => $u_props){
                ItemEntity::checkProperties($item['type_id'], $u_props, $errors, $segment_id, $item['id']);
            }
            if (empty($errors)) {
                foreach ($item_properties_values as $segment_id => $u_props){
                    $item->updateValues($u_props, $errors, $segment_id);
                    if (empty($errors[$segment_id])){
                        unset($errors[$segment_id]);
                    }
                }
            }
            if (empty($errors)){
                if ($item['status'] == ItemEntity::S_TMP){
                    if ($type['only_items']){
                        $status = ItemEntity::S_PUBLIC;
                    }else{
                        $status = static::DEFAULT_ITEM_STATUS;
                    }
                    $json_data['status'] = $status;
                }
                $item->updateParams($json_data, $errors);//сохраняем
            }
        }
//        $values = explode(self::ITEM_IDS_DELIMETER, preg_replace('~\s\r\t\n~', '', $this->request->request->get('values')));
//        if (!empty($object_id)){//значит хотим поменять один айтем на другой
//            $entity->updateValue($property['id'], $values, $errors, $segment_id);
//        }else{//значит добавляем толпу айтемов
//            $properties = $entity->getSegmentProperties($segment_id);
//            $prop_vals = $properties[$property['key']];
//            $update_data = array();
//            foreach ($prop_vals['value'] as $num => $val){//@TODO проверить что есть, чего нет
//                $update_data['val_id'] = $prop_vals['val_id'][$num];
//                $update_data['value'] = $val;
//            }
//        }
    }

    /**
     * временная функция, пока не сделали добавления по фильтру
     * Отдаем в шаблон список объектов (айтемы или варианты)
     * @ajax
     */
    public function getEntities(){
        $this->setJsonAns();
        $entity_id = $this->request->request->get('entity_id');
        $property_id = $this->request->request->get('property_id');
        $segment_id = $this->request->request->get('segment_id');
        if (empty($entity_id) || empty($property_id)){
            $errors['exception'] = 'empty ' . (empty($entity_id) ? 'entity_id' : 'property_id');
        }
        if (empty($errors)){
            $property = PropertyFactory::getById($property_id, $segment_id);
            if (empty($property)){
                $errors['exception'] = 'property not_found';
            } else {
                $entity = $property['multiple'] ? Variant::getById($entity_id, $segment_id) : ItemEntity::getById($entity_id, $segment_id);
                if (empty($entity) || empty($entity['properties'][$property['key']])){
                    $errors['exception'] = 'entity not_found';
                } else {
                    $values = explode(self::ITEM_IDS_DELIMITER, preg_replace('~\s\r\t\n~', '', $this->request->request->get('values')));
                    switch ($property['data_type']){
                        case Properties\Variant::TYPE_NAME:
                            $entities = Variant::factory($values, $segment_id);
                            break;
                        case Properties\Item::TYPE_NAME:
                            $entities = ItemEntity::factory($values, $segment_id);
                            break;
                        case Properties\User::TYPE_NAME:
                            $entities = \App\Auth\Users\Factory::getInstance()->getUsers(array('ids' => $values));
                            break;
                        default:
                            throw new \Exception('неизвестный тип данных');
                    }
                    foreach ($entities as $e_id => $ent){
                        if (is_null($ent) || $ent instanceof \Models\CatalogManagement\CatalogPosition && $ent->getType()->getCatalog()->getId() != $property['values']['catalog_id']){
                            unset($entities[$e_id]);
                        }
                    }
                    if ($property['set'] == 1){
                        $this->getAns()->add('objects', $entities)
                            ->addData('ids', array_keys($entities));
                    }else{
                        $ent = reset($entities);
                        $this->getAns()->add('object', $ent)
                            ->addData('id', $ent['id']);
                    }
                }
            }
        }
        if (!empty($errors)){
            $this->getAns()->setErrors($errors)->setEmptyContent();
            return;
        }
        $this->getAns()->setTemplate('Modules/Catalog/Item/ObjPropsView/' . $property['data_type'] . '.tpl')
            ->add('only_list', 1)
            ->add('property', $property)
            ->add('entity', $entity)
            ->add('segment_id', $segment_id)
            ->add('catalogs', TypeEntity::getCatalogs());
    }
    private function viewCatalogPositionMetro(CatalogPosition $entity, Property $property, $object_id, $segment_id, &$errors){
        $stations = CatalogSearch::factory(CatalogConfig::CATALOG_KEY_METRO, $this->segment['id'])
            ->setRules(array(Rule::make(MetroConfig::KEY_STATION_TITLE)->setOrder(false)))
			->setPublicOnly(FALSE)
            ->searchVariants()
            ->getSearch();
        /** @var \Models\CatalogManagement\Properties\Metro $property */
        $add_props = $property->getAddProperties();
        $page_size = self::$object_counts[$property['data_type']];
        $page = $this->request->request->get('page', 1);
        if (empty($page)){
            $page = 1;
        }
        $count = count($entity['properties'][$property['key']]['value']);
        if (ceil($count / $page_size) == $page){
            $this->getAns()->add('last_page', TRUE);
        }
        $this->getAns()
            ->add('objects', array_slice($entity[$property['key']], ($page-1)*$page_size, $page_size, TRUE))
            ->add('count', $count)
            ->add('pageNum', $page)
			->add('entity', $entity)
            ->add('pageSize', $page_size)
            ->add('stations', $stations)
            ->add('metro_prop', $property)
            ->add('walk_prop', $add_props['walk'])
            ->add('drive_prop', $add_props['drive']);
    }
    private function viewCatalogPositionEntity(CatalogPosition $entity, Property $property, $object_id, $segment_id, &$errors){
        if (!empty($segment_id)){
            $entity->setSegment($segment_id);
        }
        if ($property['set'] == 1){//галерея и картинка не должны сюда попадать
            $page_size = self::$object_counts[$property['data_type']];
            if (in_array($property['data_type'], array(Properties\Gallery::TYPE_NAME, Properties\Image::TYPE_NAME))){
                throw new \Exception('Неверно задан тип свойства + множественность ' . $property['data_type']);
            }
            $page = $this->request->request->get('page', 1);
            if (empty($page)){
                $page = 1;
            }
            $count = count($entity['properties'][$property['key']]['value']);
//            $search_ids = array();
//            if (($page+1)*$page_size <= $count){
//                $search_ids = array_slice($ids, ($page-1)*$page_size, $page_size);
//            }
            if (ceil($count / $page_size) == $page){
                $this->getAns()->add('last_page', TRUE);
            }
//            $method_name = 'getCatalogPosition' . ucfirst($property['data_type']);
            if ($property['segment']){
                $segments = \App\Segment::getInstance()->getAll();
                $objects = array();
                foreach ($segments as $s_id => $s){
                    $entity->setSegment($s_id);
                    $objects[$s_id] = array_slice($entity[$property['key']], ($page-1)*$page_size, $page_size, TRUE);
                    $objects[$s_id] = array_filter($objects[$s_id]);
                }
                $entity->setSegment($segment_id);
            }else{
                $objects = array_slice($entity[$property['key']], ($page-1)*$page_size, $page_size, TRUE);
            }
            $this->getAns()->add('objects', array_filter($objects))
                ->add('count', $count)
                ->add('pageNum', $page)
                ->add('pageSize', $page_size);
        }else{
            if ($property['segment']){
                $segments = \App\Segment::getInstance()->getAll();
                $objects = array();
                foreach ($segments as $s_id => $s){
                    $entity->setSegment($s_id);
                    $objects[$s_id] = $entity[$property['key']];
                }
                $objects = array_filter($objects);
                $this->getAns()->add('object', $objects);
                $entity->setSegment($segment_id);
            }else{
                $this->getAns()->add('object', $entity[$property['key']]);
            }
        }
        if ($property instanceof CatalogPositionProp || $property instanceof \Models\CatalogManagement\Properties\Region || $property instanceof \Models\CatalogManagement\Properties\Post) {
            $this->getAns()
                ->add('entities_list', $property->getEntitiesList($segment_id, false));
        }
    }
    private function viewrowCatalogPositionPost(CatalogPosition $entity, Property $property, $object_id, $segment_id, &$errors){
        $post = !empty($object_id) ? PostEntity::getById($object_id, $this->segment['id']) : NULL;
        if (empty($post)){
            $this->getAns()->addErrorByKey('exception', 'передан неверный object_id ')->setEmptyContent();
            return;
        }
        if ($property['segment']){
            $propBySegment = $entity->getPropertyBySegments($property['key']);
            $object = array();
            $val_id = array();
            $search_val_num = NULL;
            if ($property['set'] && !empty($propBySegment[$segment_id]['value'])){
                $search_val_num = array_search($object_id, $propBySegment[$segment_id]['value']);
                $search_val_pos = !empty($propBySegment[$segment_id]['position'][$search_val_num]) ? $propBySegment[$segment_id]['position'][$search_val_num] : null;
            }
            foreach ($propBySegment as $s_id => $pr_vals){
                if ($property['set']){
                    if (isset($search_val_pos) && !empty($pr_vals)){
                        $val_index = !empty($pr_vals['position']) ? array_search($search_val_pos, $pr_vals['position']) : null;
                        $object[$s_id] = !empty($val_index) && !empty($pr_vals['value'][$val_index]) ? PostEntity::getById($pr_vals['value'][$val_index], $s_id) : NULL;
                        $val_id[$s_id] = !empty($val_index) && !empty($pr_vals['val_id'][$val_index]) ? $pr_vals['val_id'][$val_index] : NULL;
                    }
                }else{
                    $object[$s_id] = !empty($pr_vals['value']) ? PostEntity::getById($pr_vals['value'], $s_id) : NULL;
                    $val_id[$s_id] = $pr_vals['val_id'];
                }
            }
            $val_id = array_filter($val_id);
            $object = array_filter($object);
        }else{
            $object = $post;
            if ($property['set']){
                $val_id = $entity['properties'][$property['key']]['val_id'][array_search($object_id, $entity['properties'][$property['key']]['value'])];
            }else{
                $val_id = $entity['properties'][$property['key']]['val_id'];
            }
        }
        $this->getAns()->add('object', $object)
            ->add('val_id', $val_id);
    }
    private function viewrowCatalogPositionFile(CatalogPosition $entity, Property $property, $object_id, $segment_id, &$errors){
        $file = File::getById($object_id, $this->segment['id']);
        if (empty($file)){
            $this->getAns()->addErrorByKey('exception', 'передан неверный object_id ')->setEmptyContent();
            return;
        }
        if ($property['segment']){
            $propBySegment = $entity->getPropertyBySegments($property['key']);
            $object = array();
            $val_id = array();
            $search_val_num = NULL;
            if ($property['set'] && !empty($propBySegment[$segment_id]['value'])){
                $search_val_num = array_search($object_id, $propBySegment[$segment_id]['value']);
                $search_val_pos = !empty($propBySegment[$segment_id]['position'][$search_val_num]) ? $propBySegment[$segment_id]['position'][$search_val_num] : null;
            }
            foreach ($propBySegment as $s_id => $pr_vals){
                if ($property['set']){
                    if (isset($search_val_pos) && !empty($pr_vals)){
                        $val_index = !empty($pr_vals['position']) ? array_search($search_val_pos, $pr_vals['position']) : null;
                        $object[$s_id] = !empty($val_index) && !empty($pr_vals['value'][$val_index]) ? PostEntity::getById($pr_vals['value'][$val_index], $s_id) : NULL;
                        $val_id[$s_id] = !empty($val_index) && !empty($pr_vals['val_id'][$val_index]) ? $pr_vals['val_id'][$val_index] : NULL;
                    }
                }else{
                    $object[$s_id] = !empty($pr_vals['value']) ? File::getById($pr_vals['value'], $s_id) : NULL;
                    $val_id[$s_id] = $pr_vals['val_id'];
                }
            }
            $val_id = array_filter($val_id);
            $object = array_filter($object);
        }else{
            $object = $file;
            if ($property['set']){
                $val_id = $entity['properties'][$property['key']]['val_id'][array_search($object_id, $entity['properties'][$property['key']]['value'])];
            }else{
                $val_id = $entity['properties'][$property['key']]['val_id'];
            }
        }
        $this->getAns()->add('object', $file)
            ->add('val_id', $val_id);
    }
	/**
	 * @ajax
	 * изменение видимости (статуса) из основной таблицы
	 */
	public function changeVisible(){
		$entity_type = $this->request->request->get('entity');
		$id = $this->request->request->get('id');
        $check = $this->request->request->get('check');
        $value = $this->request->request->get('value');
        if (empty($check)){
            $check = array($id);
        }
		if ($entity_type == 'item'){
            $items = ItemEntity::factory($check);
            $item = reset($items);
            $this->request->request->set('type_id', $item['type_id']);
            return $this->setItemsVisible($check, $value);
		}elseif($entity_type == 'variant'){
            $variants = Variant::factory($check,  $this->segment['id']);
            $variant = reset($variants);
			$this->request->request->set('v', $id);
			$this->request->request->set('id', $variant['item_id']);
            return $this->setVariantsVisible($check, $value);
		}else{
            $this->setJsonAns()
                ->setEmptyContent()
                ->addErrorByKey('exception', 'Неверный тип сущности');
		}
	}
    private function setVariantsVisible($check, $vis){
        if (empty($check) || !is_array($check)){
            return;
        }
        $vis = empty($vis) ? 0 : $vis;
        $statuses = [
            0 => ItemEntity::S_HIDE,
            1 => ItemEntity::S_PUBLIC,
            2 => ItemEntity::S_TEMPORARY_HIDE,
        ];
        $status = $statuses[$vis];
        $variants = Variant::factory($check);
        foreach ($variants as $v){
            $v->update(array('status' => $status));
        }
        $this->setJsonAns()
            ->setEmptyContent()
            ->addData('status', $status);
    }
    /**
     * Список вариантов товара с возможностью выбора одного $_POST или $_GET: 'v'
     * @ajax
     * @param type $inner
     */
    public function itemVariants($inner=false){
        if (!$inner){
            $this->setJsonAns()
                ->add('segments', \App\Segment::getInstance()->getAll())
                ->add('request_segment', $this->segment['id']);
        }
        $variant = NULL;
        $id = $this->request->request->get('id', $this->request->query->get('id'));
        $show_variant_id = $this->request->request->get('v', $this->request->query->get('v'));
        if (!empty($show_variant_id)){
            $variant = Variant::getById($show_variant_id);
            if (empty($variant)) {
                if ($inner) {
                    $uri = $this->request->server->get('REQUEST_URI');
                    $uri = preg_replace('~\&?v=\d+~', '', $uri);
                    return $this->redirect($uri);
                } else {
                    $show_variant_id = null;
                    $item = ItemEntity::getById($id);
                }
            } else {
                $item = $variant->getItem();
                if ($item['id'] != $id) {
                    if ($inner) {
                        $uri = $this->request->server->get('REQUEST_URI');
                        $uri = preg_replace('~\&?v=\d+~', '', $uri);
                        return $this->redirect($uri);
                    } else {
                        $item = ItemEntity::getById($id);
                        $variant = null;
                        $show_variant_id = null;
                    }
                }
            }
        }else{
            $item = ItemEntity::getById($id);
        }
        if (empty($item)){
            $this->setJsonAns()->setEmptyContent()->addErrorByKey('exception', 'Не найден айтем с id ' . $id);
            return;
        }
        $type = $item->getType();
        $variants = $item->getVariants(array(Variant::S_HIDE, Variant::S_PUBLIC));
		$item_variants_properties = PropertyFactory::search($item['type_id'], PropertyFactory::P_VARIANTS | PropertyFactory::P_NOT_VIEW | PropertyFactory::P_NOT_DEFAULT | PropertyFactory::P_NOT_RANGE, 'key', 'group', 'parents', array(), $this->segment['id']);
        $this->getAns()->add('catalog_item_variants', $variants)
            ->add('catalog_variant', $variant)
			->add('variant_create', $this->request->request->get('variant_create'))
            ->add('item_variants_properties', $item_variants_properties)
            ->add('show_variant_id', $show_variant_id)
            ->add('current_type', $type)
			->add('properties_available', $type->getPropertiesAvailable())
			->add('variant_list', 1)
			->add('astate', 1)
            ;
        if (!$inner){
            $this->getAns()->add('catalog_item', $item)
                ->addData('last_update', $variant['last_update']);
        }
        $this->request->request->set('id', $show_variant_id);
    }
    /**
     * Сохранение варианта
     * принимает json массив свойств для сохранения
     * $this->request->request->get('q') = array('properties' => array('{segment_id}' => array('{property_key}' => '{property_value}')))
     * @ajax
     * @throws \LogicException
     */
	public function saveVariant(){
		$this->setJsonAns();
        $id = $this->request->request->get('id');
        $variant_id = $this->request->request->get('variant_id');
        $variant_create = false;//флаг о том, создаем ли вариант или редактируем
        if (empty($id)){
            throw new \LogicException('Необходим id айтема для создания\редактирования варианта');
        }
        $item = ItemEntity::getById($id, $this->segment['id']);
        if (empty($item)){
            throw new \LogicException('Товар с id #' . $id . ' не найден');
        }
        $type = $item->getType();
        $variants = $item->getVariants();
        $this->request->query->set('show_variant_id', $variant_id);
		$json_data = json_decode($this->request->request->get('q'), 1);
		if (!empty($json_data['properties'])){
			$properties_params = $json_data['properties'];
			unset($json_data['properties']);
		}else{
			$properties_params = array();
		}
        $errors = NULL;
        if (empty($variant_id)){//если нет id варианта, значит создаем
            $copy_variant_id = $this->request->request->get('copy_variant');
            if (!empty($copy_variant_id)){
                $exists_variants = $item->getVariants(array(Variant::S_PUBLIC, Variant::S_HIDE));
                if (empty($exists_variants[$copy_variant_id])){
                    throw new \LogicException('Неизвестный variant_id');
                }
                $variant_id = $exists_variants[$copy_variant_id]->copy($errors);
                $variant_create = true;
            } else {
                $variant_id = $item->createVariant(Variant::S_TMP, array(), $errors);
                $variant_create = true;
            }
        }
		$variant = Variant::getById($variant_id);
		$variant_properties = $variant->getPropertyList('key');
//        $last_update = $this->request->request->get('last_update');
//        if (!empty($variant) && $last_update < $variant['last_update']){
//            $errors['obj'] = 'already_changed';
//        }
        if (empty($errors) && !$variant_create){
            //сначала проверим на ошибки
            ksort($properties_params);
            foreach ($properties_params as $s_id => $prop_val_params){
                //очищаем переданные данные от мусора
                foreach ($prop_val_params as $p_id => $vals){
                    foreach ($vals as $n => $d){
                        if (!empty($d['options']) && !empty($d['options']['delete']) && empty($d['val_id'])){
                            unset($prop_val_params[$p_id][$n]);
                        }
                    }
                }
				$checked_values[$s_id] = self::filterJsonParams($variant_properties, $prop_val_params);
                if (!empty($s_id) && !empty($checked_values[0])) {
                    // Закидываем несегментированные значения в каждый сегмент
                    $checked_values[$s_id] = $checked_values[$s_id] + $checked_values[0];
                }
            }
            if (!empty($checked_values)) {
                if (count($checked_values) > 1) {
                    // и убираем пустой сегмент из сегментированных данных
                    unset($checked_values[0]);
                }
                // Поскольку в одном сегменте может не быть ошибок, а в другом они есть, проверяем входные данные
                // по всем сегментам до попытки сохранения
                foreach ($checked_values as $s_id => $prop_val){
                    Variant::checkProperties($variant->getType()['id'], $prop_val, $errors, $s_id, $variant['id'], $variant['item_id']);
                }
                if (!empty($errors)){//избавляемся от дублей (могут быть в нулевом сегменте)
                    $errors = array_map('unserialize', array_unique(array_map('serialize', $errors)));
                }
                //теперь сохраняем
                if (empty($errors)) {
                    foreach ($checked_values as $s_id => $prop_val){
                        $variant->updateValues($prop_val, $errors[$s_id], $s_id);
                    }
                    if (!empty($errors)){//избавляемся от дублей (могут быть в нулевом сегменте)
                        $errors = array_map('unserialize', array_unique(array_map('serialize', $errors)));
                    }
                }
            }
            if (empty($errors)){
                //обновляем статус у варианта, если небыло ошибок и статус был tmp
                $params = $json_data;
                if (!$variant_create && $variant['status'] == Variant::S_TMP){
                    $params['status'] = self::DEFAULT_VARIANT_STATUS;
                }
                if (!empty($params)){
                    //тут возможно будут лишние параметры для сохранения, надо удалять.
                    $variant->update($params, array(), $errors, $s_id);
                }
            }
            $variant->save();
        }
        $serviceErrorProperties = array();
        if (!empty($errors)){
            foreach ($errors as $s_id => $err){
                $serviceErrorProperties += Variant::getServiceErrorProperties($err, $item['type_id'],  $variant->getPropertyList());
            }
        }
        $item_variant_properties = PropertyFactory::search($item['type_id'], PropertyFactory::P_VARIANTS | PropertyFactory::P_NOT_VIEW | PropertyFactory::P_NOT_DEFAULT | PropertyFactory::P_NOT_RANGE, 'key', 'group', 'parents', array(), $this->segment['id']);
        $this->getAns()
            ->add('errors', $errors)
            ->add('variant_list', true)
			->add('astate', 1)
            ->add('variant_create', $variant_create)
            ->add('editing_variant_id', $variant_id)
            ->add('catalog_item_variants', $variants)
            ->add('catalog_variant', $variant)
            ->add('catalog_item', $item)
            ->add('item_variants_properties', $item_variant_properties)
			->add('properties_available', $item->getType()->getPropertiesAvailable())
            ->add('service_error_properties', $serviceErrorProperties)
            ->add('segments', \App\Segment::getInstance()->getAll())
			->add('current_type', $type)
            ->setTemplate('Modules/Catalog/Item/itemVariants.tpl')
        ;
		$this->getAns()->addData('id', $variant_id);
		if(!empty($errors)){
			$this->getAns()->setErrors($errors)
					->addData('service_error_properties', $serviceErrorProperties);
		}else{
            Variant::clearCache($variant_id);
            $variant = Variant::getById($variant_id);
			$this->getAns()->setStatus('ok')
                ->addData('last_update', $variant['last_update']);

		}
    }
    /**
     * @ajax
     * шаблон редактирования значения одного свойства
     */
    public function editPropertyFace(){
        $this->setAjaxResponse();
        $prop_id = $this->request->request->get('id');
        if (empty($prop_id)){
            throw new \LogicException('Надо задать id свойства');
        }
        $property = PropertyFactory::getById($prop_id);
        if (empty($property)){
            throw new \LogicException('Свойство с id #' . $prop_id . ' не найдено');
        }
        $this->getAns()->add('property', $property);
    }
    /**
     * @deprecated
     */
    public function listVariants(){
        $ans = $this->setJsonAns();
        $item = ItemEntity::getById($this->request->query->get('id'));
        if (!empty($item)){
            $variants = $item->getVariants(array(Variant::S_PUBLIC, Variant::S_TEMPORARY_HIDE, Variant::S_HIDE));
            $ans->add('catalog_item_variants', $variants)
                ->add('current_type', $item->getType())
                ->add('catalog_item', $item);
        } else {
            $ans->setEmptyContent()->addErrorByKey('id', 'empty');
        }
    }
    /**
     * @ajax
     */
    public function moveVariant(){
        $position = $this->request->request->get('position');
        $variant = Variant::getById($this->request->request->get('variant_id'));
        if (!empty($position) && !empty($variant) && $variant->move($position)){
            $variant->save();
            $this->request->query->set('id', $variant['item_id']);
            return $this->run('listVariants');
        } else {
            $ans = $this->setJsonAns()->setEmptyContent();
            if (empty($position)){
                $ans->addErrorByKey('position', 'empty');
            }
            if (empty($variant)){
                $ans->addErrorByKey('variant', 'empty');
            }
        }
    }
    /**
     * @ajax
     */
    public function deleteVariant(){
        $id = $this->request->request->get('id');
        $variant_id = $this->request->request->get('variant_id');
        if (!empty($id) && !empty($variant_id)){
            $variant = Variant::getById($variant_id);
            $item = ItemEntity::getById($variant['item_id']);
            $variant->_delete($errors, \LPS\Config::DELETE_ENTITIES_FROM_DB);
            $variants = $item->getVariants(array(Variant::S_PUBLIC, Variant::S_HIDE));
            if (empty($variants)){
                $item->update(array('status' => ItemEntity::S_HIDE));
            }
            return '';
        }else{
            return 'error';
        }
    }
    /**
     * @ajax
     */
    public function checkValueOnUnique(){
        if (!empty($_POST['item_id']) && !empty($_POST['prop_key']) && !empty($_POST['value'])){
            if (!ItemEntity::checkUniqueValue($_POST['prop_key'], $_POST['value'], NULL, $_POST['item_id'])){
                return 1;
            }
        }
        return 0;
    }
    public function sortListCollection(){
        $this->setAjaxResponse();
        Images::factory();
        $item = ItemEntity::getById($this->request->request->get('item_id'));
        $this->getAns()->add('catalog_item', $item);
    }
    /**
     * @ajax
     */
    public function move(){
        $position = $this->request->request->get('position');
        $item = ItemEntity::getById($this->request->request->get('item_id'));
        if (!empty($position) && !empty($item) && $item->move($position, $errors)){
            $this->request->query->set('id', $item['type_id']);
            return $this->run('listItems');
        }
        $ans = $this->setJsonAns()->setEmptyContent();
        if (empty($position)){
            $ans->addErrorByKey('position', 'empty');
        }
        if (empty($item)){
            $ans->addErrorByKey('item_id', 'empty');
        }
    }

    public function delete(){
        $ans = $this->setJsonAns();
        $id = $this->request->request->get('id');
        if (empty($id)){
            $ans->addErrorByKey('exception', 'не передан id айтема')->setEmptyContent();
            return;
        }
        $item = ItemEntity::getById($id);
        if (empty($item)){
            $ans->addErrorByKey('exception', 'айтем с id ' . $id . ' не найден')->setEmptyContent();
            return;
        }
        $type_id = $item['type_id'];
        $item->delete($errors, \LPS\Config::DELETE_ENTITIES_FROM_DB);
        if (empty($errors)){
            // listItems получает id типа через get-параметр id, без этого не сможем вернуть список айтемов типа
            $this->request->query->set('id', $type_id);
            $ans->setStatus('ok');
            return $this->run('listItems');
        } else {
            $ans->setEmptyContent()
                ->setErrors($errors);
        }
    }

    public function delItems(){
        $check = $this->request->request->get('check');
        if (empty($check)){
            return $this->run('listItems');
        }
        $items = ItemEntity::factory($check);
        $errors = array();
        $item_type_id = NULL;
        foreach($items as $item){
            if (is_null($item)){
                return;
            }
            $item_type_id = $item['type_id'];
            $err = array();
            $item->delete($err, \LPS\Config::DELETE_ENTITIES_FROM_DB);
            if (!empty($err)) {
                foreach($err as $e) {
                    $e['id'] = $item['id'];
                    $e['title'] = !empty($item['title']) ? $item['title'] : NULL;
                    $errors[] = $e;
                }
            }
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            $url = urldecode($_SERVER['HTTP_REFERER']);
            $type_id = $this->request->request->get('type_id');
            if (preg_match('~/catalog\-item/edit/\?id=(\d+)~', $url, $matches)){
                $this->request->query->set('parent_id', $matches[1]);
                $this->request->query->set('id', $item_type_id);//возьмется последний из списка, и это хорошо
                $this->request->query->set('page_size', 100000);
            } elseif (preg_match('~[?&]id=(\d+)~', $url, $matches)){
                $this->request->query->set('id', $matches[1]);
            } elseif (!empty($type_id)) {
                $this->request->query->set('id', $type_id);
            } else {
                $this->setJsonAns()->setEmptyContent()->addErrorByKey('type_id', 'empty');
            }
            if (preg_match('~[?&]page=(\d+)~', $url, $matches)){
                $this->request->query->set('page', $matches[1]);
            }
            return $this->run('listItems');
        }
    }
    /**
     * Проверка, существует ли айтем
     * @ajax
     * @return type
     */
    public function checkIssetItemById(){
        $id = $this->request->request->get('id', $this->request->query->get('id'));
        $item = ItemEntity::getById($id);
        if (!empty($item)){
            return json_encode(array('id' => $item['id'], 'title' => $item[CatalogConfig::KEY_ITEM_TITLE]));
        }else{
            return json_encode(array('error' => 'not found'));
        }
    }
    /**
     * Чистка базы по запросу
     * @return type
     */
    public function cleanDB(){
        Catalog::factory()->cleanup(); /** @TODO пропущен параметр $type_key */
        return $this->redirect($this->request->server->get('HTTP_REFERER'));
    }

    public function transferItem(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $errors = array();
        $item_id = $this->request->request->get('item_id');
        $type_id = $this->request->request->get('type_id');
        $item = ItemEntity::getById($item_id);
        $type = TypeEntity::getById($type_id);
        if (empty($item)){
            $errors['item_id'] = 'empty';
        }
        if (empty($type)){
            $errors['type_id'] = 'empty';
        }
        if (empty($errors)){
            $item->changeType($type_id, $errors);
        }
        if (!empty($errors)){
            $ans->setErrors($errors);
        } else {
            $ans->setStatus('ok');
        }
    }

    public function transferVariant(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $errors = array();
        $item_id = $this->request->request->get('item_id');
        $variant_id = $this->request->request->get('variant_id');
        $type_id = $this->request->request->get('type_id');
        $type = TypeEntity::getById($type_id);
        $variant = Variant::getById($variant_id);
        if (empty($variant)){
            $errors['variant_id'] = 'empty';
        } else {
            if (empty($item_id) && !empty($type)){
                $old_item = $variant->getItem();
                $item = $old_item->copyItemToType($type_id, $errors);
                $item_id = !empty($item) ? $item['id'] : NULL;
            } elseif (!empty($item_id)) {
                $item = ItemEntity::getById($item_id);
                if (empty($item)){
                    $errors['item_id'] = 'not_found';
                }
            } else {
                if (empty($item_id)){
                    $errors['item_id'] = 'empty';
                }
                if (empty($type_id)){
                    $errors['type_id'] = 'empty';
                }
            }
            if (empty($errors)){
                $variant->changeItem($item, $e);
                if ($e) {
                    $errors['err'] = $e;
                } else {
                    $ans->addData('url', '/catalog-item/edit/?id='.$item_id.'&tab=variants&v='.$variant_id);
                }
            }
        }
        if (!empty($errors)){
            $ans->setErrors($errors);
        } else {
            $ans->setStatus('ok');
        }
    }
}
