<?php
/* ***************** Типы  *************************/
/**
 * Description of Type
 *
 * @author olga
 */
namespace Modules\Catalog;
use App\Configs\CatalogConfig;
use App\Configs\SeoConfig;
use App\Configs\SphinxConfig;
use Models\CatalogManagement\CatalogHelpers\Type\AdditionalFields;
use Models\CatalogManagement\CatalogHelpers\Type\DynamicCategory;
use Models\CatalogManagement\Properties;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\RulesConstructor;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type AS TypeEntity;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\ContentManagement\Post;
use Models\ContentManagement\PostHelpers\Images AS PostImages;
use \App\Auth\Account\SuperAdmin;
//use Models\CatalogManagement\Tags;
use Models\InternalLinkManager AS ILM;
use Models\FilesManagement\File;
use Models\CatalogManagement\Properties\Enum;
use Models\Validator;

class Type extends \LPS\AdminModule{
    private static $typeEditFields = array(
        'parent_id' => array('type' => 'checkInt'),
//        'title' => array('type' => 'checkString'),
        'allow_children' => array('type' => 'checkFlag'),
        'key' => array('type' => 'checkKey', 'options' => array('empty' => TRUE)),
        'nested_in' => array('type' => 'checkInt', 'options' => array('empty' => TRUE)),
        'allow_item_property' => array('type' => 'checkFlag'),
        'allow_variant_property' => array('type' => 'checkFlag'),
        'only_items' => array('type' => 'checkFlag'),
        'show_props_tab' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'show_groups_tab' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'show_text_tab' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'show_cover_tab' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'show_banner_tab' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'enable_view_mode' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE))
    );
    private static $catalogEditFields = array(
//        'title' => array('type' => 'checkString'),
        'allow_children' => array('type' => 'checkInt', 'option' => array('empty' => true)),
        'key' => array('type' => 'checkKey', 'options' => array('empty' => TRUE)),
        'nested_in' => array('type' => 'checkFlag'),
        'dynamic_for' => array('type' => 'checkString'),
        'allow_item_property' => array('type' => 'checkFlag'),
        'allow_variant_property' => array('type' => 'checkFlag'),
        'only_items' => array('type' => 'checkFlag'),
        'show_props_tab' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'show_groups_tab' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'show_metatags_tab' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'show_text_tab' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'show_cover_tab' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'show_banner_tab' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'enable_view_mode' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'allow_item_url' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'allow_segment_properties' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE)),
        'item_cover_name' => array('type' => 'checkString', 'options' => array('empty' => TRUE)),
        'search_by_sphinx' => array('type' => 'checkFlag', 'options' => array('empty' => TRUE))
    );
    /**
     * Просмотр каталогов
     */
    public function index(){
        $this->request->query->set('id', TypeEntity::DEFAULT_TYPE_ID);
        if ($this->account->getRole() !== \App\Configs\AccessConfig::ROLE_SUPER_ADMIN) {
            return $this->deny();
        }
        $this->catalog(TRUE);
    }
    /**
     * Просмотр категорий в каталоге
     * @return type
     * @throws \Exception
     */
    public function catalog($inner = FALSE){
        $parent_id = $this->request->query->get('id');
        if (empty($parent_id) || ($parent_id == TypeEntity::DEFAULT_TYPE_ID && !$inner)){
            return $this->notFound();
        }
        $type = TypeEntity::getById($parent_id, $this->segment['id']);
        if (empty($type)){
            return $this->notFound();
        }
        if ($type['allow_children']){
            $children = $type->getChildren(array(TypeEntity::STATUS_VISIBLE, TypeEntity::STATUS_HIDDEN));
        } elseif ($type->getCatalog()['key'] == CatalogConfig::CONFIG_KEY) {
            // Автосоздание айтема конфига
            \Models\CatalogManagement\Positions\Settings::getConfigByKey($type['key']);
        }
        $form_data = array();
        $type_post = $type['post'];
        if (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_LANGUAGE) {
            $form_data['text'] = !empty($type_post) ? $type_post['text'] : '';
            $form_data['title'] = !empty($type_post) ? $type_post['title'] : '';
            $form_data['annotation'] = !empty($type_post) ? $type_post['annotation'] : '';
        }
        $types_by_parents = TypeEntity::getAllowChildrenTypesByParents(array(TypeEntity::STATUS_VISIBLE, TypeEntity::STATUS_HIDDEN));
        if ($type['id'] != TypeEntity::DEFAULT_TYPE_ID && !$type->getCatalog()['dynamic_for']){
            $this->getModule('Catalog\Item')->listItems(true);
        }
        $all_types_by_levels = $type['id'] != TypeEntity::DEFAULT_TYPE_ID ? TypeEntity::getTypesByLevel(array(TypeEntity::STATUS_HIDDEN, TypeEntity::STATUS_VISIBLE), $type->getCatalog()->getId()) : array();
        $this->getAns()
            ->add('all_types_by_levels', $all_types_by_levels)
            ->add('current_type', $type)
            ->add('types', !empty($children) ? $children : array())
            ->add('properties_key', \App\Configs\CatalogConfig::getPropertiesKeys())
            ->add('prop_groups', $type->getGroups())
            ->add('types_by_parents', $types_by_parents)
            ->add('prop_data_type_separator', \App\Configs\CatalogConfig::PROP_DATA_TYPE_SEPARATOR)
            ->setFormData($form_data);
        $this->getTypeMetaTags($type);
        $this->request->request->set('url', $type['url']);
        $this->getModule('Site\Banner')->banners(true);
        if (!$type['allow_children'] && $type->getCatalog()['dynamic_for']) {
            // Вкладка правил динамической категории
            $this->dynamicRulesList($parent_id);
            $this->dynamicItemsList($parent_id);
        }
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
            $type_key = $this->request->query->get('type');
            if (empty($type_key)){
                return $this->redirect($this->getModuleUrl() . 'catalog/?id='.$catalog['id']);
            } else {
                $type = TypeEntity::getByKey($type_key, $catalog['id']);
                if (!empty($type)){
                    return $this->redirect($this->getModuleUrl() . 'catalog/?id='.$type['id']);
                }
            }
        }
        $this->notFound();
    }

    /**
     * Ссылка на конфиг
     * обрабатывает ссылку вида /catalog-type/settingsIndex/?key=<config_key>
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    public function settingsIndex(){
        $config_key = $this->request->query->get('key');
        $config_catalog = TypeEntity::getByKey(CatalogConfig::CONFIG_KEY);
        $type = TypeEntity::getByKey($config_key, $config_catalog['id']);
        if (!empty($type)){
            return $this->redirect($this->getModuleUrl() . 'catalog/?id='.$type['id']);
        }
        $this->notFound();
    }

    /**
     * Загружает соответствующие типу правила конструктора метатегов
     * и доступные переменные для конструктора метатегов
     * @param TypeEntity $current_type
     */
    private function getTypeMetaTags(TypeEntity $current_type){
        $type_vars = array_merge(SeoConfig::getMetaTagVariables(), array(
            '{$current_type.title}' => 'Заголовок типа'
        ));
        $prop_list = PropertyFactory::search($current_type['id'], PropertyFactory::P_ITEMS | PropertyFactory::P_NOT_ENTITY);
        $catalog = $current_type->getCatalog();
        $item_name = \LPS\Components\FormatString::ucfirstUtf8($catalog['word_cases']['i'][1]['i']);
        $item_vars = $type_vars;
        foreach($prop_list as $prop){
            $key = $prop['default_prop'] ? substr($prop['key'], 0, -strlen(Property::DEFAULT_PROP_SUFFIX)) : $prop['key'];
            $item_vars['{$catalog_item.' . $key . '}'] = $item_name.'.' . $prop['title'];
        }
        $type_url = $current_type->getUrl();
        if (!empty($catalog)){
            $this->getAns()
                ->add('type_seo_tags', $this->getMetaTag($type_url, 'items', $catalog['key']))
                ->add('type_children_seo_tags', $this->getMetaTag($type_url.'*', 'items', $catalog['key']))
                ->add('type_items_seo_tags', $this->getMetaTag($type_url.'*', 'viewItem', $catalog['key']))
                ->add('meta_tags_type_vars', $type_vars)
                ->add('meta_tags_item_vars', $item_vars)
            ;
        }

    }

    /**
     * Создание/редактирование метатега к категории
     * Post-data:
     * type_id – id типа, к которому привязываются мета-теги
     * meta_tag_type — Тип метатега
     *                      current — только для текущей категории
     *                      children — для дочерних категорий
     *                      items — для объектов каталога, входящих в текущую категорию
     * Стандартные свойства метатегов:
     *      title
     *      description
     *      text
     * Статус (вкл/выкл), и остальные служебные поля заполняются автоматически
     * @throws \Exception
     */
    public function saveMetaTags(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $type_id = $this->request->request->get('type_id');
        $type = TypeEntity::getById($type_id);
        if (empty($type)){
            $ans->addErrorByKey('type_id', \Models\Validator::ERR_MSG_EMPTY);
            return;
        }
        $meta_tag_type = $this->request->request->get('meta_tag_type');
        if (!in_array($meta_tag_type, array('current', 'children', 'items'))){
            $ans->addErrorByKey('meta_tag_type', \Models\Validator::ERR_MSG_INCORRECT);
            return;
        }
        $page_uid = rtrim($type['url'], '/');
        if (in_array($meta_tag_type, array('children', 'items'))){
            $page_uid .= '/*';
        }
        // canonical и keywords для универсальных правил делать бессмысленно, собираем только тайтол, текст и дескрипшон
        $params = Validator::getInstance($this->request)->checkFewResponseValues(array(
            'title' => array('type' => 'checkString', 'options' => array('empty' => true)),
            'description' => array('type' => 'checkString', 'options' => array('empty' => true)),
            'text' => array('type' => 'checkString', 'options' => array('empty' => true))
        ), $errors);
        $params = array_map('trim', $params);
        // Автоматический выключатель для пустых метатегов. Непустые автоматом включаем
        $enabled = 0;
        foreach($params as $v){
            if (!empty($v)){
                $enabled = 1;
            }
        }
        $params['enabled'] = $enabled;
        $params['page_uid'] = $page_uid;
        // Жестко привязываем правило к модулю и экшену
        $params['moduleUrl'] = $type->getCatalog()['key'];
        $params['action'] = $meta_tag_type == 'items' ? 'viewItem' : 'items';

        $seo_tags_constructor = \Models\Seo\PagePersister::getInstance();
        $rule = $seo_tags_constructor->search(array('page_uid' => $page_uid, 'enabled' => 'any', 'moduleUrl' => $params['moduleUrl'], 'action' => $params['action']));
        if (empty($rule)){
            $rule_id = $seo_tags_constructor->createRule($params);
        } else {
            $rule_id = $rule['id'];
            $seo_tags_constructor->updateRule($rule_id, $params);
        }

        if (!empty($errors)){
            $ans->setErrors($errors);
        } else {
            $ans->addData('id', $rule_id);
        }
    }

    private function getMetaTag($pageUID, $action, $catalog_key){
        $rule = \Models\Seo\PagePersister::getInstance()->search(array('pageUID' => $pageUID, 'moduleUrl' => $catalog_key, 'action' => $action, 'enabled' => 'any'), TRUE);
        if (!empty($rule)){
            $rule['parent_type'] = TypeEntity::getByUrl(rtrim($rule['page_uid'], '*'));
        }
        return $rule;
    }

    /**
     * Карта метатегов каталога (отображаем дерево категорий с указанием определенных в них метатегов
     * 4 типа: Теги категории, теги дочерних категорий, теги дочерних айтемов и айтемов с переопределенными тегами
     * @param null $type
     * @throws \Exception
     */
    public function catalogMetaTagsList($type = NULL)
    {
        $inner = !empty($type);
        if (!$inner) {
            $ans = $this->setJsonAns();
            $type_id = $this->request->request->get('id', $this->request->query->get('id'));
            $type = TypeEntity::getById($type_id);
            if (empty($type)) {
                $ans->setEmptyContent()->addErrorByKey('id', 'emtpy');
                return;
            }
        } else {
            $ans = $this->getAns();
        }
        $catalog = $type->getCatalog();
        $page_uid = rtrim($type['url'], '/');
        $category_rules = \Models\Seo\PagePersister::getInstance()->search(array('pageUID_like' => $page_uid . '%', 'moduleUrl' => $catalog['key'], 'action' => 'items'), false);
        $item_common_rules = \Models\Seo\PagePersister::getInstance()->search(array('pageUID_like' => $page_uid . '%', 'moduleUrl' => $catalog['key'], 'action' => 'viewItem'), false);
        $item_rules = \Models\Seo\PagePersister::getInstance()->search(array('pageUID_like' => $page_uid . '%'), false);
        $url_list = array($type['url'] => $type['id']);
        $child_types = $type->getAllChildren(array(TypeEntity::STATUS_VISIBLE, TypeEntity::STATUS_HIDDEN));
        foreach($child_types as $p_id => $types){
            foreach($types as $id => $t){
                $url_list[$t['url']] = $id;
            }
        }
        $category_rules_by_id = array();
        foreach($category_rules as $rule){
            $url = $rule['page_uid'];
            if (substr($url, -1) != '/'){
                $url .= '/';
            }
            if (!empty($url_list[$url])){
                $category_rules_by_id[$url_list[$url]] = $rule;
            }
        }
        // Собираем список мета-тегов категорий
        $category_rules_by_id = array();
        // и метатегов дочерних категорий
        $category_child_rules_by_id = array();
        foreach($category_rules as $rule){
            $url = $rule['page_uid'];
            if (substr($url, -1) != '/'){
                $url .= '/';
            }
            if (!empty($url_list[$url])){
                $category_rules_by_id[$url_list[$url]] = $rule;
            }elseif (substr($url, -1) == '*' && !empty($url_list[substr($url, 0, -1)])){
                $category_child_rules_by_id[$url_list[substr($url, 0, -1)]] = $rule;
            }
        }
        // метатеги дочерних айтемов категорий
        $item_common_rules_by_id = array();
        foreach($item_common_rules as $rule){
            $url = substr($rule['page_uid'], 0, -1);
            if (!empty($url_list[$url])){
                $item_common_rules_by_id[$url_list[$url]] = $rule;
            }
        }
        // и, для конечных категорий, список метатегов, привязанных к конктретным айтемам
        $item_prefix = $catalog['item_prefix'];
        $item_rules_by_id = array();
        foreach($item_rules as $rule){
            $url = substr($rule['page_uid'], 0, strpos($rule['page_uid'], $item_prefix));
            if (!empty($url_list[$url])){
                $item_rules_by_id[$url_list[$url]][$rule['id']] = $rule;
            }
        }
        $ans->add('catalog_type', $type)
            ->add('category_children', $child_types)
            ->add('category_rules_by_id', $category_rules_by_id)
            ->add('category_child_rules_by_id', $category_child_rules_by_id)
            ->add('item_common_rules_by_id', $item_common_rules_by_id)
            ->add('item_rules_by_id', $item_rules_by_id);
    }

    /**
     * Список айтемов конечного типа с переопределенными метатегами
     * @throws \Exception
     */
    public function categoryItemsWithTagsList(){
        $ans = $this->setJsonAns();
        $type_id = $this->request->request->get('id', $this->request->query->get('id'));
        $type = TypeEntity::getById($type_id);
        if (empty($type)) {
            $ans->setEmptyContent()->addErrorByKey('id', 'emtpy');
        } elseif ($type['allow_children']) {
            $ans->setEmptyContent()->addErrorByKey('id', 'allow_children');
        } else {
            $item_rules = \Models\Seo\PagePersister::getInstance()->search(array('pageUID_like' => $type['url'] . '%'), false);
            $item_keys = array();
            $catalog = $type->getCatalog();
            $item_prefix = $catalog['item_prefix'];
            foreach($item_rules as $rule){
                $prefix_pos = strpos($rule['page_uid'], $item_prefix);
                if ($prefix_pos){
                    $item_key = substr($rule['page_uid'], $prefix_pos+strlen($item_prefix));
                    $item_key = trim($item_key, '/');
                    if (!empty($item_key)){
                        $item_keys[] = $item_key;
                    }
                }
            }
            if (!empty($item_keys)){
                $items = CatalogSearch::factory($catalog['key'])->setRules(array(Rule::make('key')->setValue($item_keys)))->searchItems()->getSearch();
            } else {
                $items = array();
            }
            $ans->add('catalog_type', $type)
                ->add('items', $items);
        }

    }
    /**
     * Создание каталога
     */
    public function createCatalog(){
        if ($this->account->getRole() !== \App\Configs\AccessConfig::ROLE_SUPER_ADMIN) {
            throw new \Exception('Только разработчики могут создавать каталоги');
        }
        $this->request->request->set('parent_id', TypeEntity::DEFAULT_TYPE_ID);
//        $this->request->request->set('allow_children', 1);
        return $this->create(TRUE);
    }

    /**
     * @ajax
     * Создать тип
     * @param bool $create_catalog 
     * @throws \Exception
     * @return NULL
     */
    public function create($create_catalog = FALSE){
//        if ($create_catalog){
//            $max_children = $this->request->request->get('max_children');
//            $this->request->request->set('allow_children', !empty($max_children) ? 1 : 0);
//        }
        $defaultSegment = \App\Segment::getInstance()->getDefault();
        $validator = \Models\Validator::getInstance($this->request);
        $params = $validator->checkFewResponseValues($create_catalog ? self::$catalogEditFields : self::$typeEditFields, $errors);
        // Заголовок может быть сегментированным, поэтому проверяем отдельно
        $title = $this->request->request->get('title');
        if (is_array($title)) {
            $segments = \App\Segment::getInstance()->getAll();
            foreach($segments as $seg) {
                $err = null;
                $t = !empty($title[$seg['id']]) ? $title[$seg['id']] : null;
                $params['title'][$seg['id']] = $validator->checkValue($t, 'checkString', $err);
                if (!empty($err)) {
                    $errors['title['.$seg['id'].']'] = $err;
                }
            }
        } else {
            $params['title'] = $title;
            if (empty($title)) {
                $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
            }
        }
        if (!$create_catalog && (empty($params['parent_id']) || $params['parent_id'] == TypeEntity::DEFAULT_TYPE_ID)){
            throw new \Exception('Wrong method for create catalog');
        }
        if (empty($params['nested_in'])){
            $params['nested_in'] = NULL;
        }
        if (empty($params['dynamic_for'])){
            $params['dynamic_for'] = NULL;
        }
        if (empty($params['key']) && !empty($params['title'])){
            $params['key'] = \LPS\Components\Translit::UrlTranslit(is_array($params['title']) ? reset($params['title']) : $params['title']);//т.к. ключ нужен будет в урле, то и транслит нужен урловый
        }
        if ($create_catalog){
            $params['parent_id'] = TypeEntity::DEFAULT_TYPE_ID;
            if ($params['nested_in']){
                $params['allow_children'] = 1;
                $params['item_prefix'] = '';
                $params['only_items'] = 1;
                $params['allow_variant_property'] = null;
            } else {
                $item_prefix = $this->request->request->get('item_prefix');
                if ($params['allow_children'] > 1 && empty($item_prefix)){
                    $errors['item_prefix'] = \Models\Validator::ERR_MSG_EMPTY;
                } else {
                    if (!empty($item_prefix) && strpos('_-', substr($item_prefix, -1)) === FALSE){
                        $item_prefix .= '_';
                    }
                    $params['item_prefix'] = !empty($item_prefix) ? $item_prefix : '';
                }
                if (!empty($params['only_items'])) {
                    $params['allow_variant_property'] = null;
                }
            }
            if (!$params['nested_in'] && $this->request->request->get('dynamic_category')) {
                if (empty($params['dynamic_for'])){
                    $errors['dynamic_for'] = \Models\Validator::ERR_MSG_EMPTY;
                } else {
                    $catalogs = TypeEntity::getById(TypeEntity::DEFAULT_TYPE_ID)->getChildren();
                    $catalog_found = FALSE;
                    foreach($catalogs as $cat){
                        if ($cat['key'] != CatalogConfig::CONFIG_KEY && $cat['key'] == $params['dynamic_for']){
                            $catalog_found = TRUE;
                            break;
                        }
                    }
                    if (!$catalog_found){
                        $errors['dynamic_for'] = \Models\Validator::ERR_MSG_INCORRECT;
                    } else {
                        $params['item_prefix'] = 'i_';
                        unset($errors['item_prefix']);
                    }
                }
            } else {
                $params['dynamic_for'] = NULL;
            }
        } else{
            $parent_type = TypeEntity::getById($params['parent_id'], $defaultSegment['id']);
            $catalog = $parent_type->getCatalog();
            if ($catalog['nested_in']){
                if ($this->account->getRole() !== \App\Configs\AccessConfig::ROLE_SUPER_ADMIN) {
                    throw new \Exception('Только разработчики могут создавать категории в каталоге с наследуемыми айтемами');
                }
                $params['allow_children'] = 0;
                $params['only_items'] = 1;
            }
        }
        if (empty($errors)){
            $type = TypeEntity::create(
                $params,// 'user_id' => $user_id),
                array(),
                $errors,
                $defaultSegment['id']
            );
            $this->request->request->set('parent_id', $type['parent_id']);
        }
		if (!empty($errors)){
            $this->request->request->set('errors', $errors);
		}
        TypeEntity::clearCache($params['parent_id']);
        return $this->run('typesList');
    }
    /**
     * @ajax
     */
    public function editPopup(){
        $this->setAjaxResponse();
        $type_id = $this->request->request->get('id');
		$defaultSegment = \App\Segment::getInstance()->getDefault();
        if (empty($type_id)){
            $parent_id = $this->request->request->get('parent_id');
            $parent = TypeEntity::getById($parent_id);
            $this->getAns()
                ->add('create', 1)
                ->add('current_type', $parent);
        }else{
            $type = TypeEntity::getById($type_id, $defaultSegment['id']);
            $form_data = $type->asArray();
            if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE && $segment_data = $type['segment_data']){
                foreach ($segment_data as $field => $data){
                    if (!empty($data) && count($data) == 1 && isset($data[0])){
                        $segment_data[$field] = $data[0];
                    }
                }
                if (!empty($segment_data)){
                    $form_data = $segment_data + $form_data;
                }
            }
            if (!empty($type['dynamic_for'])){
                $form_data['dynamic_category'] = 1;
            }
            $parent = $type->getParent();
            $this->getAns()
                ->add('type', $type)
                ->add('current_type', $parent)
                ->setFormData($form_data);
        }
        $current_catalog = $parent->getCatalog();
        $typesByLevels = NULL;
        if ($current_catalog['nested_in'] && $parent['id'] != TypeEntity::DEFAULT_TYPE_ID){
            $typesByLevels = TypeEntity::getTypesByLevel(array(TypeEntity::STATUS_HIDDEN, TypeEntity::STATUS_VISIBLE), $current_catalog['id'], $defaultSegment['id']);
        }
		$this->getAns()->add('groups', array())
            ->add('field_list', CatalogConfig::getFields('type'))
            ->add('typesByLevels', $typesByLevels)
            ->add('item_covers_list', \Models\CatalogManagement\CatalogHelpers\Type\AdditionalFields::factory()->getItemCoversList())
			->add('segments', \App\Segment::getInstance()->getAll());
    }
    /**
     * редактирование каталога
     * @ajax
     */
    public function updateCatalog(){
        if ($this->account->getRole() !== \App\Configs\AccessConfig::ROLE_SUPER_ADMIN) {
            throw new \Exception('Только разработчики могут редактировать каталоги');
        }
//        $this->request->request->set('allow_children', 1);
        return $this->update(TRUE);
    }
    /**
     * Обновить данные о типе
     * @ajax
     */
    public function update($update_catalog = FALSE){
//        if ($update_catalog){
//            $max_children = $this->request->request->get('max_children');
//            $this->request->request->set('allow_children', !empty($max_children) ? 1 : 0);
//        }
        $defaultSegment = \App\Segment::getInstance()->getDefault();
        $id = $this->request->request->get('id');
        if (!empty($id)){
            $type = TypeEntity::getById($id, $defaultSegment['id']);
            $validator = \Models\Validator::getInstance($this->request);
            $params = $validator->checkFewResponseValues($update_catalog ? self::$catalogEditFields : self::$typeEditFields, $errors);
            // Заголовок может быть сегментированным, поэтому проверяем отдельно
            $title = $this->request->request->get('title');
            if (is_array($title)) {
                $segments = \App\Segment::getInstance()->getAll();
                foreach($segments as $seg) {
                    $err = null;
                    $t = !empty($title[$seg['id']]) ? $title[$seg['id']] : null;
                    $params['title'][$seg['id']] = $validator->checkValue($t, 'checkString', $err);
                    if (!empty($err)) {
                        $errors['title['.$seg['id'].']'] = $err;
                    }
                }
            } else {
                $params['title'] = $title;
                if (empty($title)) {
                    $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
                }
            }
            if (empty($params['nested_in'])){
                $params['nested_in'] = NULL;
            }
            if (empty($params['key']) && !empty($params['title'])){
                $params['key'] = \LPS\Components\Translit::UrlTranslit($params['title']);//т.к. ключ нужен будет в урле, то и транслит нужен урловый
            }
            $params['word_cases'] = $this->request->request->get('word_cases');
            if ($update_catalog){
                $params['parent_id'] = TypeEntity::DEFAULT_TYPE_ID;
                if ($params['nested_in']){
                    $params['allow_children'] = 1;
                    $params['item_prefix'] = '';
                    $params['only_items'] = 1;
                    $params['allow_variant_property'] = null;
//                    $params['max_children'] = 1;
                } else {
                    $item_prefix = $this->request->request->get('item_prefix');
                    if ($params['allow_children']){
                        if ($params['allow_children'] > 1 && empty($item_prefix)){
                            $errors['item_prefix'] = \Models\Validator::ERR_MSG_EMPTY;
                        } else {
                            if (!empty($item_prefix) && strpos('_-', substr($item_prefix, -1)) === FALSE){
                                $item_prefix .= '_';
                            }
                            $params['item_prefix'] = !empty($item_prefix) ? $item_prefix : '';
                        }
                        if ($params['allow_children'] < 0 || $params['allow_children'] > 5 || $params['allow_children'] < $type->getCatalogMaxChildrenLevel()){
                            $errors['allow_children'] = \Models\Validator::ERR_MSG_INCORRECT_FORMAT;
                        }
                    } else {
                        $params['allow_children'] = 0;
                        $params['item_prefix'] = '';
                    }
                    // Невозможно использовать для пропертей-сущностей варианты из каталога с отключенными вариантам
                    if (!empty($params['only_items'])) {
                        $params['allow_variant_property'] = null;
                    }
                }
                if (!$params['nested_in'] && $this->request->request->get('dynamic_category')) {
                    if (empty($params['dynamic_for'])){
                        $errors['dynamic_for'] = \Models\Validator::ERR_MSG_EMPTY;
                    } else {
                        $catalogs = TypeEntity::getById(TypeEntity::DEFAULT_TYPE_ID)->getChildren();
                        $catalog_found = FALSE;
                        foreach($catalogs as $cat){
                            if ($cat['key'] != CatalogConfig::CONFIG_KEY && $cat['key'] == $params['dynamic_for']){
                                $catalog_found = TRUE;
                                break;
                            }
                        }
                        if (!$catalog_found){
                            $errors['dynamic_for'] = \Models\Validator::ERR_MSG_INCORRECT;
                        } else {
                            $params['item_prefix'] = 'i_';
                            unset($errors['item_prefix']);
                        }
                    }
                } else {
                    $params['dynamic_for'] = NULL;
                }
            } else {
                $catalog = $type->getCatalog();
                if ($catalog['nested_in']) {
                    $params['allow_children'] = 0;
                    $params['only_items'] = 1;
                } else {
                    if ($params['allow_children']){
                        if (!$type->isCanHasChildren()){
                            $errors['allow_children'] = \Models\Validator::ERR_MSG_INCORRECT_FORMAT;
                        }
                    }
                }
                if ($catalog['key'] == CatalogConfig::FEEDBACK_KEY){
                    $params['number_prefix'] = $this->request->request->get('number_prefix');
                }
                unset($params['max_children']);
            }
            $this->request->request->set('parent_id', $type['parent_id']);
            if (!$update_catalog && $type->isCatalog()){
                throw new \Exception('Wrong method for update catalog');
            }
            if (!$type['fixed'] || $this->account instanceof SuperAdmin){
				if (empty($errors)){
                    if ($update_catalog && ($type['allow_item_property'] && empty($params['allow_item_property']))){
                        //надо удалить все свойства и значения таких свойств
                        $props = PropertyFactory::searchIds(TypeEntity::DEFAULT_TYPE_ID, PropertyFactory::P_ALL, 'position', 'children', array('data_type' => Properties\Item::TYPE_NAME, `values` => $type['id']), $defaultSegment['id']);
                        if (!empty($props)){
                            foreach ($props as $p_id){
                                Property::delete($p_id);
                            }
                        }
                    }
                    if ($update_catalog && $type['allow_variant_property'] && empty($params['allow_variant_property'])){
                        //надо удалить все свойства и значения таких свойств
                        $props = PropertyFactory::searchIds(TypeEntity::DEFAULT_TYPE_ID, PropertyFactory::P_ALL, 'position', 'children', array('data_type' => Properties\Variant::TYPE_NAME, `values` => $type['id']), $defaultSegment['id']);
                        if (!empty($props)){
                            foreach ($props as $p_id){
                                Property::delete($p_id);
                            }
                        }
                    }
                    $type->update($params, $errors);
				}
            }
        }else{
            $errors['id'] = Validator::ERR_MSG_EMPTY;
        }
		if (!empty($errors)){
            $this->request->request->set('errors', $errors);
		}
        return $this->run('typesList');
    }
    /**
     * @deprecated ?
     * @ajax
     * @return int
     */
    public function checkAllowChildren(){
        $id = $this->request->request->get('id');
        $type = TypeEntity::getById($id);
        $allow_children = $this->request->request->get('allow_children', 0);
        $result = $type->checkChangingAllowChildren($allow_children ? 1 : 0);
        return $result ? 1 : 0;
    }
    /**
     * Поменять статусы: видимый, невидимый
     * @ajax
     */
    public function updateHidden(){
        $defaultSegment = \App\Segment::getInstance()->getDefault();
        $id = $this->request->request->get('id');
        if (!empty($id)){
            $type = TypeEntity::getById($id, $defaultSegment['id']);
            if (!$type['fixed'] || $this->account instanceof SuperAdmin){
                $hidden = $this->request->request->get('hidden');
                $result = $type->setStatus($hidden ? TypeEntity::STATUS_HIDDEN : TypeEntity::STATUS_VISIBLE);
                if (!$result){
                    throw new \LogicException('Cant update type');
                }
            }
        }
        return $this->run('typesList');
    }
//    public function changeRegionVisible(){
//        $id = $this->request->request->get('id');
//        $ids = $this->request->request->get('check');
//        $reg_id = $this->request->request->get('reg_id');
//        $visible = $this->request->request->get('visible');
//        if (empty($ids) && !empty($id)){
//            $ids = array($id => $id);
//        }
//        $segment_helper = \Models\CatalogManagement\CatalogHelpers\Type\SegmentVisible::factory();
//        if (!empty($reg_id) && $this->account->isPermission('profile', 'changeRegion')){
//            foreach ($ids as $id => $on){
//                $type = TypeEntity::getById($id);
//                if (empty($type)){
//                    $error = 'Тип не найден';
//                }elseif (!$segment_helper->changeSegmentVisible($type['id'], $reg_id, $visible)){
//                    $error = 'System Error. Невозможно поменять видимость';
//                }
//            }
//        }elseif(!empty($reg_id)){
//            $error = 'У Вас нет прав менять видимость в данном регионе';
//        }else{
//            $reg = \App\Segment::getInstance()->getDefault();
//            foreach ($ids as $id => $on){
//                $type = TypeEntity::getById($id);
//                if (empty($type)){
//                    $error = 'Тип не найден';
//                }elseif (!$segment_helper->changeSegmentVisible($type['id'], $reg['id'], $visible)){
//                    $error = 'System Error. Невозможно поменять видимость';
//                }
//            }
//        }
//        return json_encode(!empty($error) ? array('error' => $error) : array('status' => 'ok'));
//    }
    /**
     * Передвинуть тип на позицию
     * @ajax
     */
    public function move(){
        $defaultSegment = \App\Segment::getInstance()->getDefault();
        $type_id = $this->request->request->get('type_id');
        $position = $this->request->request->get('position');
        if (!empty($type_id) && !empty($position)){
            TypeEntity::getById($type_id)->move($position, $defaultSegment['id']);
        }
        return $this->run('typesList');
    }
    /**
     * Список типов, который запрашивается при ajax запросах
     *  @ajax
     */
    public function typesList(){
        $defaultSegment = \App\Segment::getInstance()->getDefault();
        $errors = $this->request->request->get('errors');
        $ans = $this->setJsonAns();
        if (!empty($errors)){
            $ans->setErrors($errors)->setEmptyContent();
            return;
        }
        $parent_id = $this->request->request->get('parent_id');
        if (empty($parent_id)){
            $ans->addErrorByKey('parent_id', Validator::ERR_MSG_EMPTY)->setEmptyContent();
            return;
        }
		$type = TypeEntity::getById($parent_id, $defaultSegment['id']);
        $children = $type->getChildren(array(TypeEntity::STATUS_VISIBLE, TypeEntity::STATUS_HIDDEN));
        $ans->add('types', $children)->add('current_type', $type);
    }
    /**
     * Сделать невидимыми один или несколько типов
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setTypesHidden(){
        $check = $this->request->request->get('check');
        foreach($check as $type_id => $val){
            $type = TypeEntity::getById($type_id);
            if (!$type['fixed'] || $this->account instanceof SuperAdmin){
                $type->setStatus(TypeEntity::STATUS_HIDDEN);
            }
        }
        return $this->run('typesList');
    }
    /**
     * Сделать видимыми один или несколько типов
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setTypesVisible(){
        $check = $this->request->request->get('check');
        foreach($check as $type_id => $val){
            $type = TypeEntity::getById($type_id);
            if (!$type['fixed'] || $this->account instanceof SuperAdmin){
                $type->setStatus(TypeEntity::STATUS_VISIBLE);
            }
        }
        return $this->run('typesList');
    }
    /**
     * Удалить один или несколько типов
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(){
        $check = $this->request->request->get('check');
        $types = TypeEntity::factory(array_keys($check));
        if ($this->account->getRole() !== \App\Configs\AccessConfig::ROLE_SUPER_ADMIN && !empty($types)) {
            foreach($types as $type) {
                if ($type->getCatalog()['nested_in']) {
                    throw new \Exception('Только разработчики могут удалять категории в каталоге с наследуемыми айтемами');
                } elseif ($type->isCatalog()) {
                    throw new \Exception('Только разработчики могут удалять каталоги');
                }
            }
        }
        $errors = array();
        foreach($types as $type_id => $type){
            if (!empty($type) && (!$type['fixed'] || $this->account instanceof SuperAdmin)){
                $type->delete($e);
                if (!empty($e)){
                    $errors[$type_id] = $e;
                }
            }
        }

        if (!empty($errors)){
            $this->request->request->set('errors', $errors);
        }
        return $this->run('typesList');
    }
    /**
     * обложка типа
     * @ajax
     */
    public function addCover(){
        $this->setJsonAns();
        $type_id = $this->request->request->get('type_id');
        $type = TypeEntity::getById($type_id);
        $files = $this->request->files->get('cover', null);
        $result = \Models\CatalogManagement\CatalogHelpers\Type\AdditionalFields::factory()->uploadCover($type, $files, $e);
        if (empty($e)){
            if ($result){
                $this->getAns()->add('type_cover', $result)
                        ->add('cover_status', 'new');
            }else{
                $this->getAns()->add('cover_status', 'delete');
            }
        }else{
            $this->getAns()->addErrorByKey('cover', $e);
        }
    }
    /**
     * дефолтная картинка для товаров типа
     * @ajax
     */
    public function addDefault(){
        $this->setJsonAns()->setTemplate('Modules/Catalog/Type/addCover.tpl');
        $type_id = $this->request->request->get('type_id');
        $type = TypeEntity::getById($type_id);
        $files = $this->request->files->get('default', null);
        $result = \Models\CatalogManagement\CatalogHelpers\Type\AdditionalFields::factory()->uploadDefault($type, $files, $e);
        if (empty($e)){
            if ($result){
                $this->getAns()->add('type_cover', $result)
                        ->add('cover_status', 'new');
            }else{
                $this->getAns()->add('cover_status', 'delete');
            }
        }else{
            $this->getAns()->addErrorByKey('default', $e);
        }
    }
    /**
     * @ajax
     * @return string
     */
    public function addDescription(){
        $type_post_helper = AdditionalFields::factory();
        $type_id = $this->request->request->get('type_id');
        $annotation = $this->request->request->get('annotation');
        $title = $this->request->request->get('title');
        $text = $this->request->request->get('text');
        $status = $this->request->request->get('status', Post::STATUS_NEW);
        $segment_id = $this->request->request->get('segment_id');
        if (empty($type_id)){
            throw new \LogicException('Empty type_id');
        }
        $type = TypeEntity::getById($type_id, $segment_id);
        $type_post_helper->editPost($type, array('text' => $text, 'status' => $status, 'title' => $title, 'annotation' => $annotation), $segment_id);
        return '';
    }

    public function addSearchRules(){

    }

//    public function addAssoc(){
//        $type_id = $this->request->request->get('id');
//        $assoc_id = $this->request->request->get('assoc_id');
//        $obj_type = constant('Models\InternalLinkManager::OBJECT_TYPE_' . strtoupper($this->request->request->get('type')));
//        if (!empty($type_id) && !empty($assoc_id) && !empty($obj_type)){
//            ILM::getInstance()->add(ILM::TARGET_TYPE_TYPE, $type_id, $obj_type, $assoc_id);
//        }
//        $this->request->request->set('obj_type', $obj_type);
//        return $this->run('assocList');
//    }
//    public function delAssoc(){
//        $type_id = $this->request->request->get('id');
//        $assoc_id = $this->request->request->get('assoc_id');
//        $obj_type = constant('Models\InternalLinkManager::OBJECT_TYPE_' . strtoupper($this->request->request->get('type')));
//        if (!empty($type_id) && !empty($assoc_id) && !empty($obj_type)){
//            ILM::getInstance()->delete(ILM::TARGET_TYPE_TYPE, $type_id, $obj_type, $assoc_id);
//        }
//        $this->request->request->set('obj_type', $obj_type);
//        return $this->run('assocList');
//    }
//    public function assocList($inner = false){
//        if (!$inner){
//            $this->setAjaxResponse();
//        }
//        $type_id = $this->request->request->get('id');
//        if (empty($type_id)){
//            throw new \LogicException('Type id not found');
//        }
//        $obj_type = $this->request->request->get('obj_type');
//        $attaches = ILM::getInstance()->search(array(ILM::TARGET_TYPE_TYPE => $type_id), $obj_type);
//        $entity = array();
//        if (!empty($attaches) && !empty($attaches[$obj_type])){
//            switch ($obj_type){
//                case ILM::OBJECT_TYPE_TYPE:
//                    $entity = TypeEntity::factory(array_keys($attaches[$obj_type]));
//                    break;
//                case ILM::OBJECT_TYPE_FILE:
//                    $entity = File::factory(array_keys($attaches[$obj_type]));
//                    break;
//                case ILM::OBJECT_TYPE_ARTICLE:
//                    $entity = Post::factory(array_keys($attaches[$obj_type]));
//                    break;
//            }
//        }
//        $this->getAns()->add('entity', $entity);
//    }
    /********** свойства **********/
    /**
     *  @ajax
     */
    public function properties(){
        $this->setJsonAns();
        $select_properties = $this->request->request->get('select_properties');
        $prop_id = $this->request->request->get('prop_id');
        if (!empty($select_properties)){
            $this->getAns()->add('select_properties', 1);
        }elseif (!empty($prop_id)){
            $property = PropertyFactory::getById($prop_id, $this->segment['id']);
            $position = $this->request->request->get('position');
            $del = $this->request->request->get('del');
            if (!empty($position) && !empty($prop_id)){
                $property->move($position);
            }elseif(!empty($del) && !empty($prop_id)){
                Property::delete($prop_id);
            }
        }
        $type = TypeEntity::getById($this->request->request->get('type_id'), $this->segment['id']);
        $this->getAns()->add('type_id', $type['id'])
            ->add('current_type', $type)
            ->add('current_type_unchangeable', $type['fixed'] && !($this->account instanceof SuperAdmin))
            ->add('properties', $type->getProperties())
            ->add('properties_key', \App\Configs\CatalogConfig::getPropertiesKeys())
            ->add('prop_data_type_separator', \App\Configs\CatalogConfig::PROP_DATA_TYPE_SEPARATOR)
            ->add('prop_groups', $type->getGroups());
    }
    /**
     * Страница редактирования свойства
     * @return type
     */
    public function editProp(){
        $this->setJsonAns();
        $property_id = $this->request->query->get('id', $this->request->request->get('id'));
        $type_id = $this->request->query->get('type_id', $this->request->request->get('type_id'));
        if (empty($type_id) && (empty($property_id) || !PropertyFactory::isPropertyExist($property_id))){
            return $this->notFound();
        }
        $property = !empty($property_id) ? PropertyFactory::getById($property_id, $this->segment['id']) : array(
            'id' => 0,
            'type_id' => $type_id, 
            'title' => '', 
            'description' => '', 
            'public_description' => '',
            'key' => '',
            'data_type' => Properties\String::TYPE_NAME,
            'major' => NULL,
            'search_type' => PropertyFactory::SEARCH_NONE,
            'visible' => array(1 => 1),
            'values' => NULL,
            'mask' => '',
            'filter_title' => '',
            'necessary' => NULL,
            'unique' => NULL,
            'multiple' => NULL,
            'group_id' => NULL,
            'read_only' => NULL,
            'set' => NULL,
            'filter_visible' => NULL,
            'fixed' => NULL,
            'filter_slide' => NULL,
            'segment' => NULL,
            'context' => '',
            'sort' => NULL,
            'external_key' => '',
            'default_prop' => NULL,
            'default_value' => NULL
        );
        $type = !empty($property['id']) ? $property->getType() : TypeEntity::getById($type_id, $this->segment['id']);
        if ((
                (!empty($property)  && ($property['fixed'] == 1 || $property['fixed'] == 2))
                || $type['fixed']
            )
            && !($this->account instanceof SuperAdmin)){
            return $this->deny();
        }
        $unchangeableParams = !empty($property) && !is_numeric($property['fixed']) ? explode('.', $property['fixed']) : array();
        if (!empty($property['id'])){
            $property_array = $property->asArray();
        }else{
            $property_array = $property;
        }
        if ($property_array['data_type'] == Properties\Item::TYPE_NAME || $property_array['data_type'] == Properties\Variant::TYPE_NAME){
            $property_array['data_type'] = $property_array['data_type'] . \App\Configs\CatalogConfig::PROP_DATA_TYPE_SEPARATOR . $property_array['values']['catalog_id'];
        }
        if ($property_array['data_type'] == Properties\Gallery::TYPE_NAME){
            $property_array['data_type'] = Properties\Image::TYPE_NAME;
            $property_array['set'] = 1;
        }
        if ($property['major'] !== null){
            $property_array['major'] = 1;
            $property_array['major_count'] = $property['major'];
        }
        $this->getAns()->setFormData($property_array);
        $view_properties = $this->getViewPropList($property);
        $catalogs = TypeEntity::getById(TypeEntity::DEFAULT_TYPE_ID)->getChildren();
        $range_properties = PropertyFactory::search(
            $property['type_id'],
            PropertyFactory::P_VARIANTS,
            'id',
            'group',
            'parents',
            array('data_type' => array(Properties\Int::TYPE_NAME, Properties\Float::TYPE_NAME))
        );
        $this->getAns()
            ->add('type_properties', $view_properties['type_properties'])
            ->add('range_properties', $range_properties)
            ->add('variants_properties', $view_properties['variant_properties'])
            ->add('property', $property)
            ->add('current_type', $type)
            ->add('field_list', CatalogConfig::getFields('property'))
            ->add('product_menu_list', Main::getPath($type['id']))
            ->add('prop_groups', $type->getGroups())
            ->add('unchangeableParamsByProps', $unchangeableParams)
            ->add('catalogs_list', $catalogs)
            ->add('prop_data_type_separator', \App\Configs\CatalogConfig::PROP_DATA_TYPE_SEPARATOR)
            ->add('properties_key', \App\Configs\CatalogConfig::getPropertiesKeys())
            ->add('allow_file_types', \Models\FilesManagement\File::getAllowExt())
            ->add('data_type_allows', PropertyFactory::getPropertyAllows())
            ->add('allow_property_posts', \App\Configs\PostConfig::getAllowPropertyPosts())
            ->add('string_validation_presets', \App\Configs\CatalogConfig::getPropertyFieldsData('string_prop_validation'));
    }

    /**
     * @param Properties\Property|array $property
     * @return Properties\Property[][]
     */
    private function getViewPropList($property){
        $filter = $property['segment'] == 0
            ? PropertyFactory::P_NOT_DEFAULT|PropertyFactory::P_NOT_SEGMENT|PropertyFactory::P_NOT_SET
            : PropertyFactory::P_NOT_DEFAULT|PropertyFactory::P_NOT_SET;
        $data_type = array(
            Properties\Int::TYPE_NAME,
            Properties\Float::TYPE_NAME,
            Properties\String::TYPE_NAME,
            Properties\Text::TYPE_NAME,
            Properties\Date::TYPE_NAME,
            Enum::TYPE_NAME,
            Properties\Range::TYPE_NAME
        );
        if (!in_array($property['key'], CatalogConfig::getPropKeysCanContainViews())) {
            $filter = $filter|PropertyFactory::P_NOT_VIEW;
        } else {
            $data_type[] = Properties\View::TYPE_NAME;
			$data_type[] = Properties\DiapasonInt::TYPE_NAME;
			$data_type[] = Properties\DiapasonFloat::TYPE_NAME;
            $data_type[] = Properties\Post::TYPE_NAME;
        }
        $type_properties = PropertyFactory::search(
            $property['type_id'],
            $filter,
            'id',
            'group',
            'parents',
            array(
                'data_type' => $data_type,
                'not_key' => CatalogConfig::getPropKeysCanContainViews()
            ));
        $filter = $property['segment'] == 0
            ? PropertyFactory::P_NOT_DEFAULT|PropertyFactory::P_NOT_SEGMENT
            : PropertyFactory::P_NOT_DEFAULT;
        $data_type = array(
            Properties\Int::TYPE_NAME,
            Properties\Float::TYPE_NAME,
            Properties\String::TYPE_NAME,
            Properties\Text::TYPE_NAME,
            Properties\Date::TYPE_NAME,
            Enum::TYPE_NAME
        );
        if (!in_array($property['key'], CatalogConfig::getPropKeysCanContainViews())) {
            $filter = $filter|PropertyFactory::P_NOT_VIEW;
        } else {
            $data_type[] = Properties\View::TYPE_NAME;
            $data_type[] = Properties\Post::TYPE_NAME;
        }
        $variant_properties = PropertyFactory::search(
            $property['type_id'],
            $filter,
            'id',
            'group',
            'parents',
            array(
                'data_type' => $data_type,
                'not_key' => CatalogConfig::getPropKeysCanContainViews()
            ));
        return array(
            'type_properties' => $type_properties,
            'variant_properties' => $variant_properties
        );
    }
    /**
     * Сохранение свойства
     * @ajax
     */
    public function saveProp(){
        $this->setJsonAns();
        $property_id = $this->request->query->get('id');
        $type_id = $this->request->request->get('type_id');
        $all_data = $this->request->request->all();
        if (empty($all_data)){
            $this->getAns()->addErrorByKey('exception', 'Не переданы данные для сохранения')->setEmptyContent();
            return;
        }
        if (!empty($property_id)){
            $property = PropertyFactory::getById($property_id, $this->segment['id']);
            $type = $property->getType();
            if (!empty($property['fixed']) && !($this->account instanceof SuperAdmin)){
                if ($property['fixed'] == 1 || $property['fixed'] == 2 || $type['fixed']){
                    return $this->deny();
                } else {
                    $attr_keys = explode('.', $property['fixed']);
                    foreach($attr_keys as $attr_key) {
                        $prop_attr_key = $attr_key == 'major_count' ? 'major' : $attr_key;
                        if (isset($property[$prop_attr_key]) && !in_array($attr_key, array('image'))) {
                            $all_data[$attr_key] = $property[$prop_attr_key];
                        } else {
                            unset($all_data[$attr_key]);
                        }
                    }
                }
            }
            if (!empty($unchangeableParams) && !($this->account instanceof SuperAdmin)){
                foreach ($unchangeableParams as $param){
                    $all_data[$param] = $property[$param];
                }
            }
        }
        $property_multiple = empty($all_data['multiple']) ? 0 : 1;
        if ($all_data['data_type'] == \Models\CatalogManagement\Properties\Image::TYPE_NAME && $all_data['set'] == 1){
            $all_data['data_type'] = \Models\CatalogManagement\Properties\Gallery::TYPE_NAME;
            $all_data['set'] = 0;
        }
        if (strpos($all_data['data_type'], \App\Configs\CatalogConfig::PROP_DATA_TYPE_SEPARATOR)){
            $d = explode(\App\Configs\CatalogConfig::PROP_DATA_TYPE_SEPARATOR, $all_data['data_type']);
            $all_data['values']['catalog_id'] = $d[1];
            $all_data['data_type'] = $d[0];
        }
        $all_data['major'] = empty($all_data['major']) ? NULL : (!empty($all_data['major_count']) ? $all_data['major_count'] : 0);
        unset($all_data['major_count']);
        // Не позволяем добавлять представления в свойства, которые не могут их содержать
        if ((!empty($property) || !empty($type_id))
            && $all_data['data_type'] == \Models\CatalogManagement\Properties\View::TYPE_NAME
            && !empty($all_data['values'])
            && !in_array($all_data['key'], CatalogConfig::getPropKeysCanContainViews())) {
            if (preg_match_all('~\{([^}]+)\}~i', $all_data['values'], $keys)) {
                if (!empty($keys[1])) {
                    $props = PropertyFactory::search(
                        !empty($type_id) ? $type_id : $property['type_id'],
                        PropertyFactory::P_VIEW,
                        'id',
                        'type_group',
                        'parents',
                        array('key' => $keys[1])
                    );
                    if (!empty($props)) {
                        $this->getAns()->setEmptyContent()->addErrorByKey('values', \Models\Validator::ERR_MSG_INCORRECT);
                        return;
                    }
                }
            }
        }
		//нельзя делать множественным 3d-тур, и формат может быть только zip
		if (!empty($all_data['values']['swfzip'])){
			$all_data['values']['set'] = 0;
			$all_data['values']['format'] = 'zip';
		}
        if (empty($property_id) && !empty($type_id)){
            $all_data['type_id'] = $type_id;
            $id = Property::create($all_data, $errors, $this->segment['id']);
            if ($id){
                $property = PropertyFactory::getById($id);
                $all_data['key'] = $property['key'];//автосоздается в создании свойства
                $result = TRUE;
            }else{
                $result = FALSE;
            }
        }
        if (!empty($property)){
            $result = $property->update($all_data, $errors);
        }
        if ($result !== FALSE){
            $property = $result;
            $type = $property->getType();
            if ($property['multiple'] != $property_multiple && !$type['allow_children'] && !empty($property_multiple)){//если есть хоть один расщепляемый параметр, надо убрать галку у этого типа, чтобы не создавать варианты автоматом
                $type->update(array('only_items'=> 0));
            }
            $this->getAns()->addData('url', $this->getModuleUrl() . ($property['type_id'] != TypeEntity::DEFAULT_TYPE_ID ? 'catalog/' : '') . '?id=' . $property['type_id'] . '&tab=properties');
        }else{
            $this->getAns()->setErrors($errors)->setEmptyContent();
        }
        return $this->run('properties');
    }
    /**
     * @ajax
	 * возвращает все уникальные знаяения одного свойства
     */
    public function getPropertyDistinctValues(){
       $property_id = $this->request->query->get('id');
       $property = PropertyFactory::getById($property_id);
       if (!empty($property)){
           return json_encode($property->getDistinctValues());
       }else{
           return '';
       }
    }

    public function delProps(){
        \Models\CatalogManagement\CatalogHelpers\Property\Image::factory();
        $props = PropertyFactory::get($this->request->request->get('check', array()));
        $errors = array();
        foreach($props as $prop){
            $prop_id = $prop['id'];
            if (!Property::delete($prop_id, $err)){
                $errors[$prop_id] = !empty($err) ? $err : 'not_deleted';
                $err = NULL;
            }
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('properties');
        }
//        $type_id = $this->request->request->get('type_id');
//        return $this->redirect($this->getModuleUrl() . ($type_id != TypeEntity::DEFAULT_TYPE_ID ? 'catalog/' : '') . '?id=' . $type_id . '&tab=properties');
    }
    /**
     * @ajax
     */
//    public function editEnumValue(){
//        if (!empty($_POST['prop_id']) && !empty($_POST['enum_value']) && !empty($_POST['enum_id'])){
//            $properties = PropertyFactory::get(array($_POST['prop_id']));
//            $property = $properties[$_POST['prop_id']];
//            $property->editEnumValue($_POST['enum_id'], $_POST['enum_value'], NULL, $this->request->request->get('enum_key'));
//            return 0;
//        }
//        return '';
//    }
//    /**
//     * @ajax
//     */
//    public function delEnumValue(){
//        if (!empty($_POST['prop_id']) && !empty($_POST['enum_value_id'])){
//            $properties = PropertyFactory::get(array($_POST['prop_id']));
//            $property = $properties[$_POST['prop_id']];
//            $property->deleteEnumValue($_POST['enum_value_id']);
//            return 0;
//        }
//        return '';
//    }
    public function addEnumValueToType(){
        $property_id = $this->request->request->get('prop_id');
        $value = $this->request->request->get('value');
        /** @var \Models\CatalogManagement\Properties\Enum $property */
        $property = PropertyFactory::getById($property_id);
        if (empty($property)){
            throw new \LogicException('Свойство не найдено');
        }
        $enum_id = $property->addEnumValue($value, 0, NULL, $error);
        if (empty($enum_id) && !empty($error)){
            return json_encode(array('error' => $error));
        }
        $type = TypeEntity::getById($property['type_id']);
        if (empty($type)){
            throw new \LogicException('Тип не найден');
        }
        $types = TypeEntity::search();
		foreach ($types as $t){
			$t->setSingleEnumUse($property_id, $enum_id, $t['id'] != $type['id'] ? false : true);
		}
        return json_encode(array('status' => 'ok', 'id' => $enum_id));
    }
    /**
     * @ajax
     * @return json
     */
    public function loadPropertyImage(){
        $property_id = $this->request->request->get('id');
        $image = $this->request->files->get('image');
        $height = $this->request->request->get('height');
        $width = $this->request->request->get('width');
        \Models\CatalogManagement\CatalogHelpers\Property\Image::factory();
        $property = PropertyFactory::getById($property_id);
        if (!empty($property)){
            $result = $property->uploadImage($image, $error);
            $returned_value = array();
            if (!is_null($result)){
                $returned_value['image_url'] = $result->getUrl($width, $height);
            }
            if (!empty($error)){
                $returned_value['error'] = $error;
            }
            return !empty($returned_value) ? json_encode($returned_value) : '';
        }else{
            throw new \LogicException('Property not found');
        }
    }

    /**
     * @ajax
     * возвращает список значений для автокомплита
     * @param int $prop_id
     * @return
     */
    public function getTypePropertyValues(){
        $property = PropertyFactory::getById($_GET['prop_id']);
        $values = $property->getDistinctValues($_GET['q']);
        foreach ($values as $key => $val){
            echo "$key|$val\n";
        }
        exit();
    }
	/**
	 * @ajax
	 * используемые значения enum свойства
	 */
	public function propertyAvailable(){
		$this->setAjaxResponse();
		$type_id = $this->request->request->get('type_id');
		$prop_id = $this->request->request->get('prop_id');
		if (empty($type_id) || empty($prop_id)){
			throw new \LogicException('Не найден type_id или prop_id');
		}
		$type = TypeEntity::getById($type_id);
		$property = PropertyFactory::getById($prop_id);
		if (empty($type) || empty($property)){
			throw new \LogicException('Не найден тип с id '.$type_id.' или свойство c id ' . $prop_id);
		}
		$form_data = $type->getPropertiesAvailable($prop_id);
		$this->getAns()->add('type', $type)
			->add('property', $property)
			->add('available_enums', $form_data)
			->add('request_segment', \App\Segment::getInstance()->getDefault())
			->setFormData($form_data);
	}
	/**
	 * @ajax
	 * выставить какие enum значения должны быть использованы
	 */
	public function setPropertyAvailable(){
		$type_id = $this->request->request->get('type_id');
		$prop_id = $this->request->request->get('prop_id');
		if (empty($type_id) || empty($prop_id)){
			throw new \LogicException('Не найден type_id или prop_id');
		}
		$type = TypeEntity::getById($type_id);
		$property = PropertyFactory::getById($prop_id);
		if (empty($type) || empty($property)){
			throw new \LogicException('Не найден тип с id '.$type_id.' или свойство c id ' . $prop_id);
		}
		$available = $this->request->request->get('available');
		$type->setPropertyAvailable($prop_id, $this->request->request->get('available'));
		$ids = $this->request->request->get('ids', array());
		if (empty($ids) && $property['data_type'] == Enum::TYPE_NAME && !empty($available)){
			return json_encode(array('error' => 'Должно использоваться хотя бы одно значение, либо само свойство не должно использоваться в данном типе.'));
		}
		$type->setEnumUsed($prop_id, $ids);
		return json_encode(array('status' => 'ok'));
	}
    /**
     * Проверка, есть ли неуникальные значения свойства у уже созданных товаров
     * @return type
     */
    public function checkItemValuesOnUnique(){
        $this->setJsonAns()->setEmptyContent();
        $property_id = $this->request->request->get('property_id', $this->request->query->get('property_id'));
        if (empty($property_id)){
            $result['error'] = 'Id свойства не задан';
        }
        $db = \App\Builder::getInstance()->getDB();
        $property = PropertyFactory::getById($property_id);
        $result = array();
        if (empty($property)){
            $result['error'] = 'Свойство не найдено';
        }else{
            $result['data'] = $db->query('SELECT `segment_id`, `value`, COUNT(`value`) AS `count`, GROUP_CONCAT(`'. ($property['multiple'] ? 'variant_id' : 'item_id') .'` SEPARATOR ", ") AS `items` FROM `'.$property['table'].'` WHERE `property_id` = ?d GROUP BY `segment_id`, `value` HAVING `count` > 1', $property_id)->select('segment_id', 'value');
            if (!empty($result['data'])){
                $this->getAns()->addData('items', $result['data']);
                $result['error'] = 'Вы не можете сделать это свойство уникальным, т.к. созданные товары имеют одинаковые значения данного свойства.';
            }
        }
        if (!empty($result['error'])){
            $this->getAns()->addErrorByKey('main', $result['error']);
        }else{
            $this->getAns()->setStatus('ok');
        }
    }
    /**
     * Проверяет, разные ли значения вариантов одного айтема
     * @return type
     */
    public function checkMultChange(){
        $prop_id = $this->request->request->get('id');
        $property = PropertyFactory::getById($prop_id);
        if (!$property['multiple']){
            return json_encode(array('status' => 'not_multiple'));
        }
        $result = $this->db->query('SELECT `v`.`item_id` FROM `'.\Models\CatalogManagement\Variant::TABLE.'` AS `v`'
            . 'INNER JOIN `'.$property['table'].'` AS `vv` '
            . 'ON (`vv`.`variant_id` = `v`.`id` AND `vv`.`property_id` = ?d) '
            . 'GROUP BY `v`.`item_id`, `vv`.`segment_id` '
            . 'HAVING COUNT(DISTINCT(`vv`.`value`)) > 1', 
            $property['id'])->getCol('item_id', 'item_id');
        return json_encode(array('status' => !empty($result) ? 'not' : 'ok', 'data' => array_keys($result)));
    }
    /**************** группы свойств ***************/

    public function propGroupFields() {
        $ans = $this->setJsonAns();
        $errors = array();
        $type_id = $this->request->request->get('type_id');
        $group_id = $this->request->request->get('group_id');
//        if (empty($group_id)) {
//            $errors['group_id'] = \Models\Validator::ERR_MSG_EMPTY;
//        }
        if (empty($type_id)) {
            $errors['type_id'] = \Models\Validator::ERR_MSG_EMPTY;
        }
        if (empty($errors)) {
            $group = !empty($group_id) ? \Models\CatalogManagement\Group::getById($type_id, $group_id) : NULL;
            if (!empty($group_id) && empty($group)) {
                $errors['group_id'] = \Models\Validator::ERR_MSG_EMPTY;
            } else {
                $form_data = !empty($group) ? $group->asArray() : array();
                $form_data['type_id'] = $type_id;
                $form_data['group_id'] = $group_id;
                $ans->setFormData($form_data)
                    ->add('group', $group);
            }
        }
        if (!empty($errors)) {
            $ans->setEmptyContent()
                ->setErrors($errors);
        }
    }
    /**
     * @ajax
     */
    public function addPropGroup(){
        $errors = array();
        $type_id = $this->request->request->get('type_id');
        $title = $this->request->request->get('title');
        $key = $this->request->request->get('key');
        $type = TypeEntity::getById($type_id);
        if (empty($type)){
            $errors['type_id'] = \Models\Validator::ERR_MSG_EMPTY;
        } else {
            $type->addGroup($title, $key, $errors);
        }
        if (empty($errors)){
            return $this->run('propGroups');
        } else {
            $this->setJsonAns()
                ->setEmptyContent()
                ->setErrors($errors);
        }
    }
    public function delPropGroup(){
        $group_id = $this->request->request->get('group_id');
        $type_id = $this->request->request->get('type_id');
        $type = TypeEntity::getById($type_id);
        $type->deleteGroup($group_id);
        return $this->run('propGroups');
    }
    public function editPropGroup(){
        $group_id = $this->request->request->get('group_id');
        $type_id = $this->request->request->get('type_id');
        $type = TypeEntity::getById($type_id);
        if (empty($type)){
            $errors['type_id'] = \Models\Validator::ERR_MSG_EMPTY;
        } else {
            $params = array(
                'title' => $this->request->request->get('title'),
                'key' => $this->request->request->get('key'),
                'group' => $this->request->request->get('group')
            );
            $type->updateGroup($group_id, $params, $errors);
        }
        if (empty($errors)){
            return $this->run('propGroups');
        } else {
            $this->setJsonAns()
                ->setEmptyContent()
                ->setErrors($errors);
        }
    }
    public function movePropGroup(){
        $type_id = $this->request->request->get('type_id');
        $group_id = $this->request->request->get('group_id');
        $move = $this->request->request->get('move');
        $type = TypeEntity::getById($type_id);
        $type->moveGroup($group_id, $move);
        return $this->run('propGroups');
    }
    public function propGroups(){
        $default_segment = \App\Segment::getInstance()->getDefault();
        $this->setJsonAns();
        $type_id = $this->request->request->get('type_id');
        $type = TypeEntity::getById($type_id, $default_segment['id']);
        $prop_groups = $type->getGroups();
        if ($type['fixed'] && !($this->account instanceof SuperAdmin)){
            $current_type_unchangeable = 1;
        }else{
            $current_type_unchangeable = 0;
        }
        $this->getAns()->add('prop_groups', $prop_groups)
			->add('current_type', $type)
            ->add('current_type_unchangeable', $current_type_unchangeable);
    }
	/**
	 * Обновить только некоторые параметры свойства
	 * @param array $params
	 * @return \Models\CatalogManagement\Properties\Property
	 */
//	private function editPropParams(\Models\CatalogManagement\Properties\Property $property, $data, &$errors = NULL){
//		if (empty($data)){
//			return;
//		}
//		$defaultSegment = \App\Segment::getInstance()->getDefault();
//		$sfH = \Models\CatalogManagement\CatalogHelpers\Property\SegmentFields::factory();
//		$params = $property->asArray();//ключ уже определен, а сохранять без ключа нельзя.
//		$params['segment_enum'] = $property['segment_enum'];
//		$editable = PropertyFactory::getEditableParams();
//		$segment_fields = $sfH->fieldsList();
//		$params = $data + $params;//новые данные останутся, старые затрутся
//		foreach ($params as $field => $value){
//			if (in_array($field, $segment_fields) && !is_array($value)){
//				$params[$field] = array($defaultSegment['id'] => $value);                        
//			}
//			if (!in_array($field, $editable)){
//				unset($params[$field]);
//			}
//		}
//		return $property->update($params, $errors);
//	}
    public function getWordCases(){
        $this->setJsonAns()->setEmptyContent();
        $data = \LPS\Components\FormatString::wordCases($this->request->request->get('word'), $error);
        if (!empty($error)){
            $this->getAns()->setErrors($error);
            return;
        }
        $this->getAns()->addData('result', $data);
    }

    /** ******************************  Работа с динамическими категориями  ******************************* */

    /**
     * @param int $type_id
     * @param array $errors
     * @param TypeEntity $type Динамическая категория
     * @param TypeEntity $catalog Опорный каталог
     * @return bool
     * @throws \Exception
     */
    private function dcGetType($type_id, &$errors, &$type, &$catalog){
        $errors = array();
        if (empty($type_id)){
            $errors['type_id'] = Validator::ERR_MSG_EMPTY;
        } else {
            $type = TypeEntity::getById($type_id);
            if (empty($type)) {
                $errors['type'] = 'not_found';
            } else {
                $dynamic_catalog = $type->getCatalog();
                if (empty($dynamic_catalog['dynamic_for'])) {
                    $errors['type'] = 'not_dynamic';
                } else {
                    $catalog = TypeEntity::getByKey($dynamic_catalog['dynamic_for']);
                    if (empty($catalog)){
                        $errors['base_catalog'] = 'not_found';
                    }
                }
            }
        }
        return empty($errors);
    }

    public function saveCategoryRules(){
        if ($this->dcGetType($this->request->request->get('type_id'), $errors, $type, $catalog)){
            $rules = $type['rules'];
            $request_rules = $this->request->request->get('rules');
            if (empty($request_rules)){
                $errors['rules'] = 'empty';
            } else {
                $ids2delete = array_keys($rules);
                $ids2delete = array_combine($ids2delete, $ids2delete);
                $helper = \Models\CatalogManagement\CatalogHelpers\Type\DynamicCategory::factory();
                foreach($request_rules as $rule_id => $rule){
                    $err = array();
                    if (!empty($rules[$rule_id])) {
                        unset($ids2delete[$rule_id]);
                        $helper->editDynamicRule($type, $rule_id, $rule, $err);
                    } else {
                        $rule_id = $helper->addDynamicRule($type, $rule, $err);
                    }
                    if (!empty($err)){
                        $errors['rules'][$rule_id] = $err;
                    }
                }
                if (empty($errors) && !empty($ids2delete)){
                    foreach($ids2delete as $rule_id){
                        $helper->deleteDynamicRule($type, $rule_id, $err);
                    }
                }
            }
        }
        if (!empty($errors)) {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('dynamicRulesList');
        }
    }

    public function dynamicItemsList($type_id = NULL){
        if (empty($type_id)){
            $type_id = $this->request->request->get('type_id');
            $inner = FALSE;
            $ans = $this->setJsonAns();
        } else {
            $inner = TRUE;
            $ans = $this->getAns();
        }
        if ($this->dcGetType($type_id, $errors, $type, $catalog)){
            $rules = $type['complete_rules'];
            $page_size = $this->request->query->get('page_size', Item::GROUP_PAGE_SIZE);
            $page = $this->request->query->get('page', 1);
            $search = \Models\CatalogManagement\Search\CatalogSearch::factory($rules['catalog'])->setRules(array($rules['rules']));
            $items = $search->searchItems(($page-1)*$page_size, $page_size);
            $this->getAns()
                ->add('catalog_items', $items )
                ->add('catalog_items_count', $items->getTotalCount())
                ->add('find_variants', $items->getFoundVariants())
                ->add('pageSize', $page_size)
                ->add('pageNum', $page);
        }
        if (!empty($errors)){
            if (!$inner) {
                $ans->setEmptyContent()->setErrors($errors);
            } else {
                throw new \LogicException('Недопустимое использование Modules\Catalog\Type::dynamicItemsList(), errors: ' . var_export($errors, true));
            }
        }
    }

    /**
     * Вкладка настройки
     * @param int $type_id
     */
    public function dynamicRulesList($type_id = NULL){
        if (empty($type_id)){
            $type_id = $this->request->request->get('type_id');
            $inner = FALSE;
            $ans = $this->setJsonAns();
        } else {
            $inner = TRUE;
            $ans = $this->getAns();
        }
        if ($this->dcGetType($type_id, $errors, $type, $catalog)){
            $rules = $type['rules'];
            $typesByLevels = TypeEntity::getTypesByLevel(array(TypeEntity::STATUS_HIDDEN, TypeEntity::STATUS_VISIBLE), $catalog['id']);
            $rules_data = RulesConstructor::getInstance()->getRulesData(!empty($rules) ? $rules : array(), $catalog);
            $ans->add('dc_rules', $rules)
                ->add('dc_catalog', $catalog)
                ->add('dc_types_by_level', $typesByLevels)
                ->add('dc_used_types', !empty($rules_data['types']) ? $rules_data['types'] : array())
                ->add('dc_used_props', RulesConstructor::getPropsList(!empty($rules_data['props']) ? $rules_data['props'] : array()))
                ->add('dc_not_found_props', !empty($rules_data['not_found_props']) ? $rules_data['not_found_props'] : array());
        }
        if (!empty($errors)){
            if (!$inner) {
                $ans->setEmptyContent()->setErrors($errors);
            } else {
                throw new \LogicException('Недопустимое использование Modules\Catalog\Type::dynamicRulesList(), errors: ' . var_export($errors, true));
            }
        }
    }

    /**
     * Редактирование отдельного правила
     * post-data:
     * type_id – обязательно, тип, к которому привязывается правило
     * rule_id – id правила, обязательно только для существующих правил, для нового — пустое значение
     */
    public function dynamicRuleFields(){
        $ans = $this->setJsonAns();
        if ($this->dcGetType($this->request->request->get('type_id'), $errors, $type, $catalog)){
            $rule_id = $this->request->request->get('rule_id');
            if (empty($rule_id)){
                // Новое правило
                $ans->add('dc_used_props', RulesConstructor::getPropsList(RulesConstructor::getInstance()->getAllowProperties($catalog['id'])));
            } else {
                $rules = $type['rules'];
                if (empty($rules[$rule_id])){
                    $errors['rule_id'] = Validator::ERR_MSG_EMPTY;
                } else {
                    $rules_data = RulesConstructor::getInstance()->getRulesData(array($rule_id => $rules[$rule_id]), $catalog);
                    $ans->add('dc_rule', $rules[$rule_id])
                        ->add('dc_used_types', !empty($rules_data['types']) ? $rules_data['types'] : array())
                        ->add('dc_used_props', RulesConstructor::getPropsList(!empty($rules_data['props']) ? $rules_data['props'] : array()))
                        ->add('dc_not_found_props', !empty($rules_data['not_found_props']) ? $rules_data['not_found_props'] : array());
                }
            }
        }
        if (!empty($errors)){
            $ans->setEmptyContent()->setErrors($errors);
        } else {
            $typesByLevels = TypeEntity::getTypesByLevel(array(TypeEntity::STATUS_HIDDEN, TypeEntity::STATUS_VISIBLE), $catalog['id']);
            $ans->add('dc_catalog', $catalog)
                ->add('dc_types_by_level', $typesByLevels);
        }
    }

    /**
     * Список пропертей правила динамической категории, уточняемый по мере зпаолнения типов
     * post-data:
     * type_id — тип, к которому привязывается правило
     * rule_type_ids – список типов, проперти из которых используются в правиле
     */
    public function dynamicRuleProps(){
        $ans = $this->setJsonAns();
        if ($this->dcGetType($this->request->request->get('type_id'), $errors, $type, $catalog)){
            $type_ids = $this->request->request->get('rule_type_ids');
            $type_ids = !empty($type_ids) ? $type_ids : array($catalog['id']);
            $ans->add('dc_used_props', RulesConstructor::getPropsList(RulesConstructor::getInstance()->getAllowProperties($type_ids)));
        }
        if (!empty($errors)){
            $ans->setEmptyContent()->setErrors($errors);
        }
    }

    /**
     * Шаблон выбора значений проперти
     * post_data:
     * type_id — тип, к которому привязывается правило
     * rule_type_ids – список типов, проперти из которых используются в правиле
     * rule_id
     * prop_key — ключ проперти, значения которой нас интересуют
     */
    public function dynamicRulePropFields(){
        $ans = $this->setJsonAns();
        if ($this->dcGetType($this->request->request->get('type_id'), $errors, $type, $catalog)){
            $prop_key = $this->request->request->get('prop_key');
            $type_ids = $this->request->request->get('rule_type_ids');
            $rule_id = $this->request->request->get('rule_id');
            if (empty($prop_key)){
                $errors['prop_key'] = Validator::ERR_MSG_EMPTY;
            } else {
                $rules = $type['rules'];
                $rule = !empty($rule_id) && !empty($rules[$rule_id]) ? $rules[$rule_id] : NULL;
                if (!empty($rule_id) && empty($rule)){
                    $errors['rule_id'] = Validator::ERR_MSG_EMPTY;
                } else {
                    if (!empty($type_ids)){
                        $props = RulesConstructor::getInstance()->getAllowProperties($type_ids);
                    } elseif (empty($rule)){
                        $props = RulesConstructor::getInstance()->getAllowProperties($catalog['id']);
                    } else {
                        $rules_data = RulesConstructor::getInstance()->getRulesData(array($rule_id => $rules[$rule_id]), $catalog);
                        $props = !empty($rules_data['props']) ? $rules_data['props'] : array();
                    }
                    if (empty($props[$prop_key])){
                        $errors['prop_key'] = Validator::ERR_MSG_EMPTY;
                    } else {
                        $props = $props[$prop_key];
                        $first = reset($props);
                        switch($first['data_type']){
                            case Properties\Int::TYPE_NAME:
                            case Properties\Float::TYPE_NAME:
                                $values_type = 'number';
                                break;
                            case Properties\Enum::TYPE_NAME:
                                $values_type = 'enum';
                                break;
                            case Properties\Flag::TYPE_NAME:
                                $values_type = 'flag';
                                break;
                            default:
                                $values_type = 'equal';
                        }
                        $ans->add('values_type', $values_type);
                        if ($values_type == 'enum'){
                            $enum_values = array();
                            foreach($props as $prop){
                                foreach($prop['values'] as $val){
                                    $enum_values[$val['id']] = $val['value'];
                                }
                            }
                            $ans->add('enum_values', $enum_values);
                        } elseif ($values_type == 'flag') {
                            $flag_values = array(1 => 'Да', 0 => 'Нет');
                            foreach($props as $prop){
                                foreach($prop['values'] as $key => $val){
                                    $flag_values[$key == 'yes' ? 1 : 0] = $val;
                                }
                            }
                            $ans->add('flag_values', $flag_values);
                        }
                    }
                }
            }
        }
        if (!empty($errors)){
            $ans->setEmptyContent()->setErrors($errors);
        }
    }

    public function saveDynamicRule(){
        if ($this->dcGetType($this->request->request->get('type_id'), $errors, $type, $catalog)){
            $rule_id = $this->request->request->get('rule_id');
            $rule = $this->request->request->get('rule');
            if (empty($rule)){
                $errors['rule'] = Validator::ERR_MSG_EMPTY;
            } else {
                $helper = DynamicCategory::factory();
                if (empty($rule_id)){
                    $helper->addDynamicRule($type, $rule, $errors);
                } else{
                    $helper->editDynamicRule($type, $rule_id, $rule, $errors);
                }
            }
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('dynamicRulesList');
        }
    }

    public function deleteDynamicRule(){
        if ($this->dcGetType($this->request->request->get('type_id'), $errors, $type, $catalog)){
            $rule_ids = $this->request->request->get('id');
            if (empty($rule_ids)){
                $errors['id'] = Validator::ERR_MSG_EMPTY;
            } else{
                $helper = DynamicCategory::factory();
                $rule_ids = is_array($rule_ids) ? $rule_ids : array($rule_ids);
                foreach($rule_ids as $rule_id){
                    $helper->deleteDynamicRule($type, $rule_id);
                }
            }
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('dynamicRulesList');
        }
    }

    public function configPropertiesList(){
        $ans = $this->setJsonAns();
        $errors = array();
        $type_id = $this->request->request->get('id', 23);
        $type = TypeEntity::getById($type_id);
        if (empty($type)) {
            $errors['id'] = 'empty';
        } elseif ($type['allow_children']) {
            $errors['type'] = 'allow_children';
        } else {
            $catalog = $type->getCatalog();
            if ($catalog['key'] != CatalogConfig::CONFIG_KEY){
                $errors['type'] = 'incorrect';
            }
        }
        if (empty($errors)){
            $config_item = \Models\CatalogManagement\Positions\Settings::getConfigByKey($type['key']);
            if (empty($config_item)) {
                $errors['config'] = 'cant_create';
            } else {
                $ans->add('current_type', $type)
                    ->add('catalog_item', $config_item);
            }
        }
        if (!empty($errors)) {
            $ans->setEmptyContent()->setErrors($errors);
        }
    }
}
