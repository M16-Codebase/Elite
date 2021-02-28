<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 08.04.15
 * Time: 12:42
 */

namespace Modules\Catalog;
use App\Configs\CatalogConfig;
use App\Configs\SphinxConfig;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type AS TypeEntity;
use Models\CatalogManagement\Item AS ItemEntity;
use Models\SphinxManagement\SphinxSearch;
use Models\CatalogManagement\Filter\FilterMap;


abstract class CatalogPublic extends \LPS\WebModule{
    const DEFAULT_CATALOG_KEY = 'undefined';

    protected $endRouterTail = NULL;

    public function init() {
        $this->protectedFunctionsList['sphinxsearchitemids'] = true;
        $this->protectedFunctionsList['sphinxsearchitems'] = true;
        parent::init();
    }

    private function dump($var, $route) {
        if ($route == 'complex/krestovskij-de-luxe/apartments') {
            dump($var);
        }
    }

    protected function route($route){
        $catalog = TypeEntity::getByKey(static::DEFAULT_CATALOG_KEY);
        $this->filterMap->setCatalog(static::DEFAULT_CATALOG_KEY);
        if ($catalog['nested_in']){
            // Урл для кустика
            $keys = explode('/', $route);
            $action = NULL;

            /**
             * Это для новостроя такой костыль
             * Иначе не работает (ЧПУ фильтров)
             */
            if ($this->filterMap->parseParams($route)) {
                $action = 'items';
                if (static::DEFAULT_CATALOG_KEY ===  CatalogConfig::CATALOG_KEY_REAL_ESTATE) {
                    $action = 'index';
                }
                return $action;
            }
            /*--------------------------*/

            if (!empty($keys)){
                $type_key = array_shift($keys);
                $type = TypeEntity::getByKey($type_key, $catalog['id'], $this->segment['id']);
                if (empty($type)) {
                    // пробуем найти такой айтем и по нему найти его тип
                    $type_id  = ItemEntity::getTypeIdByKey($type_key);//dump($keys);
                    $type = TypeEntity::getById($type_id);
                    array_unshift($keys, $type_key);
                }

                if (!empty($type)){
                    if (empty($keys)){
                        $action = 'items';
                        $this->routeTail = $type['id'];
                    } else {
                        $segment = \App\Segment::getInstance()->getDefault(true);
                        $item = NULL;
                        $item = $this->getItemByKeyAndTypeId(array_shift($keys), $type['id'], $segment['id']);
                        $next_item = $item;
                        // хранит значение предыдущего ключа в цикле, нужна для проверки
                        // $prev_key = null;
                        while (!empty($next_item) && in_array($item['status'], array(ItemEntity::S_PUBLIC, ItemEntity::S_TEMPORARY_HIDE)) && !empty($keys)){
                            $key = array_shift($keys);
                            $next_item = $this->getItemByKeyAndParentId($key, $item['id'], $segment['id']);
                            if (!empty($next_item)) {
                                $item = $next_item;
                            } else {
                                if (!empty($keys)) {
                                    if (in_array($key, FilterMap::allowedSectors())) {
                                        $action = 'viewItem';
                                        //dump($item['key'] . (!empty($key) ? '/'.$key : ''));
                                        $this->routeTail = $item['id'] . (!empty($key) ? '/'.$key : '');
                                        //if (!empty($keys)) {
                                        //    $this->routeTail .= '/' . implode('/', $keys);
                                        //}
                                    } else {
                                        $item = null;
                                    }
                                }
                            }
                        }
                        if (!empty($item)) {
                            if (!in_array($item['status'], array(ItemEntity::S_PUBLIC, ItemEntity::S_TEMPORARY_HIDE))) {
                                // Редиректы скрытых айтемов на родителя
                                $parent = $item->getParent();
                                $action = 'routeRedirect';
                                $this->routeTail = !empty($parent)
                                    ? $parent->getUrl($this->segment['id'])
                                    : $item->getType()->getCatalog()->getUrl($this->segment['id']);
                            } else {
                                $action = 'viewItem';
                                //dump($item['key'] . (!empty($key) ? '/'.$key : ''));
                                $this->routeTail = $item['id'] . (!empty($key) ? '/'.$key : '');
                                //if (!empty($keys)) {
                                //    $this->routeTail .= '/' . implode('/', $keys);
                                //}
                            }
                        }
                    }
                }
            }
        } else {
            // Урлы теперь собраны из ключей типов, айтемов и вариантов
            // чтобы различать ключи типов и айтемов, у айтемов к ключу подставляется префикс
            // айтема каталога (хранится в каталоге)
            // В экшенах механизм от этого не изменился
            // для items и viewItem подставляем айдишник объекта в routeTail

            $item_prefix = $catalog['item_prefix'];

            if (empty($item_prefix) && $catalog['allow_children'] == 1) {
                $item_prefix_pos = strpos($route, '/');
            } else {
                $item_prefix_pos = $item_prefix ? strpos($route, '/' . $item_prefix) : ($route == 'index' ? FALSE : 0);
            }
            $type_url =  '/' . static::DEFAULT_CATALOG_KEY . '/' . (($item_prefix_pos !== FALSE) ? substr($route, 0, $item_prefix_pos) : $route) . '/';

            if ($catalog['allow_children']){
                $type = TypeEntity::getByUrl($type_url, $this->segment['id']);
                if ($item_prefix_pos !== FALSE){
                    $tail = substr($route, $item_prefix_pos + strlen($item_prefix) + 1);
                    $routeTokens = explode('/', $tail);
                } else {
                    $routeTokens = array();
                }
            } else {
                // Если каталог не содержит вложенных типов, то префиксов айтема нет, а каталог является типом айтемов
                $type = $catalog;
                $routeTokens = explode('/', $route);
            }
            if (!empty($type)){
                if ($item_prefix_pos !== FALSE){
                    $item = !empty($routeTokens[0])
                        ? $this->getItemByKeyAndTypeId($routeTokens[0], $type['id'], $this->segment['id']) //ItemEntity::getByKeyAndTypeId($routeTokens[0], $type['id'])
                        : NULL;
                    if (!empty($item)){
                        if (!in_array($item['status'], array(ItemEntity::S_PUBLIC, ItemEntity::S_TEMPORARY_HIDE))) {
                            // Редиректы скрытых айтемов на родителя
                            $redirect_type = $type;
                            while($redirect_type['key'] != static::DEFAULT_CATALOG_KEY && $type['status'] != TypeEntity::STATUS_VISIBLE) {
                                $redirect_type = $redirect_type->getParent();
                            }
                            $this->routeTail = $redirect_type->getUrl($this->segment['id']);

                            $action = 'routeRedirect';
                        } else {
                            $action = 'viewItem';
                            $this->routeTail = 'i' . $item['id'];

                            unset($routeTokens[0]);
                            if (!empty($routeTokens[1])){
                                $variants = $item->getVAriants();
                                foreach($variants as $v){
                                    if ($v['key'] == $routeTokens[1]){
                                        $variant = $v;
                                    }
                                }
                            }
                            if (!empty($variant)){
                                $this->routeTail .= '/v' . $variant['id'];
                                unset($routeTokens[1]);
                            }
                            $this->routeTail .= (!empty($routeTokens) ? '/'.implode('/', $routeTokens) : '');
                        }
                    } else {
                        // проверяем может быть урл относится к фильтрам
                        // для resale and residential
                        if ($this->filterMap->parseParams($route)) {
                            $action = 'items';
                        }
                    }
                } else {
                    if ($type['status'] != TypeEntity::STATUS_VISIBLE) {
                        // Редиректы скрытых категорий на родителя
                        $redirect_type = $type;
                        while($redirect_type['key'] != static::DEFAULT_CATALOG_KEY && $type['status'] != TypeEntity::STATUS_VISIBLE) {
                            $redirect_type = $redirect_type->getParent();
                        }
                        $this->routeTail = $redirect_type->getUrl($this->segment['id']);
                        $action = 'routeRedirect';
                    } else {
                        $action = 'items';//($type['key'] == CatalogConfig::CATALOG_KEY) ? 'index' : 'items';
                        $this->routeTail = $type['id'];
                    }
                }
            } else {
                $action = static::additionalRouting($route, $type_url);
            }
        }

        //dump($action = parent::route($route));
        if (empty($action)){
            $action = parent::route($route);
            // routeRedirect мы можем нарулить только из этой функции, если на нее вышел родительский роутер - 404
            if ($action == 'routeRedirect') {
                $action = 'notFound';
            }
        }
        return $action;
    }

    protected function getItemByKeyAndTypeId($item_key, $type_id, $segment_id = NULL){
        return CatalogSearch::factory(static::DEFAULT_CATALOG_KEY, $segment_id)
            ->setRules(array(
                Rule::make('key')->setValue($item_key),
                Rule::make('type_id')->setValue($type_id)
            ))
            ->setPublicOnly(FALSE)
            ->searchItems(0, 1)
            ->getFirst();
    }

    protected function getItemByKeyAndParentId($item_key, $parent_id, $segment_id = NULL){
        return CatalogSearch::factory(static::DEFAULT_CATALOG_KEY, $segment_id)
            ->setRules(array(
                Rule::make('key')->setValue($item_key),
                Rule::make('parent_id')->setValue($parent_id)
            ))
            ->setPublicOnly(FALSE)
            ->searchItems(0, 1)
            ->getFirst();
    }

    public function routeRedirect() {
        return $this->redirect($this->routeTail, null, 302);
    }

    /**
     * Дополнительные условия роутинга, которые необходимо впихнуть в середину роутинга каталога
     * @param $route
     * @param $type_url
     * @return null
     */
    protected function additionalRouting($route, $type_url){
        return NULL;
    }
    abstract public function items();

    abstract public function viewItem();

    public function favorites() {
    }

    public function addToFavorites() {
        $ans = $this->setJsonAns()->setEmptyContent();
        $entity_id = $this->request->request->get('entity_id');
        if (!empty($entity_id)) {
            $entity_ids = is_array($entity_id) ? $entity_id : array($entity_id);
            foreach ($entity_ids as $e_id){
                $cookie = $this->account->addFavorite(static::DEFAULT_CATALOG_KEY, $e_id);
            }
            if (empty($cookie)) {
                $ans->addErrorByKey('error', 'error');
            } else {
                $this->response = $this->redirect($this->getModuleUrl($this->segment['id']) . 'successFavorites/' . ($this->request->query->has('ajax') ? '?ajax=1' : ''));
                $this->response->headers->setCookie($cookie);
                return $this->response;
            }
        } else {
            $ans->addErrorByKey('entity_id', 'empty');
        }
    }

    public function removeFromFavorites() {
        $ans = $this->setJsonAns()->setEmptyContent();
        $entity_id = $this->request->request->get('entity_id');
        if (!empty($entity_id)){
            $entity_ids = is_array($entity_id) ? $entity_id : array($entity_id);
            foreach ($entity_ids as $e_id){
                $cookie = $this->account->removeFavorite(static::DEFAULT_CATALOG_KEY, $e_id);
            }
            if (empty($cookie)) {
                $ans->addErrorByKey('error', 'error');
            } else {
                $this->response = $this->redirect($this->getModuleUrl($this->segment['id']) . 'successFavorites/' . ($this->request->query->has('ajax') ? '?ajax=1' : ''));
                $this->response->headers->setCookie($cookie);
                return $this->response;
            }
        }else{
            $ans->addErrorByKey('entity_id', 'empty');
        }
    }

    public function successFavorites(){
        if ($this->request->query->has('ajax')) {
            return $this->getModule('Main\View')->favorites();
        }
        $this->setJsonAns()
            ->setEmptyContent()
            ->setStatus('ok')
            ->addData('count', $this->account->getFavoriteCount(static::DEFAULT_CATALOG_KEY))
            ->addData('favorites_count', $this->account->getFavoriteCount(CatalogConfig::CATALOG_KEY_REAL_ESTATE) + $this->account->getFavoriteCount(CatalogConfig::CATALOG_KEY_RESALE));
    }

    public function clearFavorites() {
        $ans = $this->setJsonAns()->setEmptyContent();
        $cookie = $this->account->setFavorite(static::DEFAULT_CATALOG_KEY, array(), array(), array());
        if (empty($cookie)) {
            $ans->addErrorByKey('error', 'error');
        } else {
            $this->response = $this->redirect($this->getModuleUrl($this->segment['id']) . 'successFavorites/' . ($this->request->query->has('ajax') ? '?ajax=1' : ''));
            $this->response->headers->setCookie($cookie);
            return $this->response;
        }
    }

    /**
     * @return int[]
     */
    protected function getFavoriteIds() {
        return $this->account->getFavoriteData(static::DEFAULT_CATALOG_KEY)['entity_ids'];
    }

    /**
     * @param string $phrase
     * @param int|null $type_id
     * @param bool $ifVariant
     * @return \int[]
     * @throws \ErrorException
     * @throws \Exception
     */
    public function sphinxSearchItemIds(&$phrase, &$type_id = null, $ifVariant = false) {
        if (!SphinxConfig::ENABLE_SPHINX) {
            throw new \LogicException('SphinxSearch отключен');
        }
        $catalog = TypeEntity::getByKey(static::DEFAULT_CATALOG_KEY, TypeEntity::DEFAULT_TYPE_ID, $this->segment['id']);
        if (!$catalog['search_by_sphinx']) {
            throw new \LogicException("В каталоге #${catalog['key']} запрещен поиск по сфинксу");
        }
        if (empty($type_id)) {
            $type_id = $catalog['id'];
            $type = $catalog;
        } else {
            $type = TypeEntity::getById($type_id, $this->segment['id']);
        }
        if (empty($type) || $type->getCatalog()['id'] != $catalog['id']) {
            throw new \ErrorException("Категория id:${type_id} не найдена");
        }
        $type_ids = $type['allow_children'] ? TypeEntity::getIds(array('parents' => $type_id, 'allow_children' => 0)) : array($type_id);
        $sphinx = SphinxSearch::factory(SphinxConfig::CATALOG_KEY, $this->segment['id'])
            ->setTypeIds($type_ids)
            ->setLimit(0, 1000000)
            ->setGroup(null);
        $result = $sphinx->select('`item_id`, `variant_id`', $phrase);
        $ids = $ifVariant ? $result->getCol('variant_id', 'variant_id') : $result->getCol('item_id', 'item_id');
        if (empty($ids)) {
            $kb_phrase = \LPS\Components\FormatString::keyboardLayout($phrase, 'both');
            $result = $sphinx->select('`item_id`, `variant_id`', $phrase);
            $ids = $ifVariant ? $result->getCol('variant_id', 'variant_id') : $result->getCol('item_id', 'item_id');
            if (!empty($ids)) {
                $phrase = $kb_phrase;
            }
        }
        return $ids;
    }

    public function sphinxSearchItems(&$phrase, $page = 1, $page_size = 20, &$count = false, $type_id = null, $ifVariant = false) {
        $ids = $this->sphinxSearchItemIds($phrase, $type_id, $ifVariant);
        if (empty($ids)) {
            $count = 0;
            return array();
        }
        $result = CatalogSearch::factory(static::DEFAULT_CATALOG_KEY, $this->segment['id'])
            ->setTypeId($type_id)
            ->setRules(array(
                    Rule::make('id')->setValue($ids)->setOrder($ids)
                )
            )
            ->searchItems(($page - 1) * $page_size, $page_size);
        $count = $result->getTotalCount();
        return $result->getSearch();
    }

    protected function implodeRoutes($routesArray) {
        return implode('/', $routesArray);
    }

    protected function explodeRoutes($routesArray) {
        return explode('/', $routesArray);
    }

    /**
     * вернет элементы uri после path в виде массива
     * например /sector/subsector/sector_path/subpath/ с path=subsector
     * вернет массив [sector_path, subpath]
     * @param $uri
     * @param $path
     * @return array
     */
    protected function cleanrequestUri($uri, $path = '') {
		
		ini_set('max_execution_time', 20);
        $uri = explode('?', $uri); //отсекаем все что после ? если есть
        $uri = $uri[0];
		//if($uri=='/arenda/'){
		//echo'<pre>';debug_print_backtrace();echo'</pre>';
		//debug_print_backtrace();
		//}
        $uri_array = $this->explodeRoutes(trim($uri, DIRECTORY_SEPARATOR));
        $path_ = '';
		//print_r($uri_array);
		//echo $path;
		//exit;
        if ($path != '') {
            while ($path_ != $path) {
                $path_ = array_shift($uri_array);
                //echo 'aha|';
            }
        }
        return $uri_array;
    }
}
