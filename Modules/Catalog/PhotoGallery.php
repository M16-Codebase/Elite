<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 08.04.15
 * Time: 13:37
 */

namespace Modules\Catalog;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type AS TypeEntity;
use Models\CatalogManagement\Item AS ItemEntity;
use Models\CatalogManagement\Variant;


class PhotoGallery extends CatalogPublic{
    const DEFAULT_CATALOG_KEY = CatalogConfig::PHOTO_GALLERY_KEY;
    const PAGE_SIZE = 12;

    public function index(){
        return $this->notFound();
    }

    public function items(){
        $type_id = $this->routeTail;
        if ($type_id == TypeEntity::DEFAULT_TYPE_ID){
            return $this->redirect($this->getModuleUrl());
        }
        $segment = \App\Segment::getInstance()->getDefault(true);
        $type = TypeEntity::getById($type_id, $segment['id']);
        if (empty($type)){
            return $this->notFound();
        }
        $page = $this->request->query->get('page', 1);
        $items = CatalogSearch::factory(static::DEFAULT_CATALOG_KEY, $segment['id'])
            ->setTypeId($type_id)
            ->setPublicOnly($this->account->isPermission('catalog-item', 'changeHidden') ? false : 1)
            ->searchItems(($page - 1) * self::PAGE_SIZE, self::PAGE_SIZE);
        $this->getAns()
            ->add('pageNum', $page)
            ->add('pageSize', self::PAGE_SIZE)
            ->add('count', $items->count())
            ->add('items', $items);
    }

    public function viewItem(){
        $rout = explode('/', $this->routeTail);
        $id = !empty($rout[0]) ? str_replace('i', '', $rout[0]) : null;
        $request_variant_id = null;
        $request_tab = 'view';
        if (!empty($rout[1]) && preg_match('~^v([0-9]*)$~', $rout[1], $route_data)){
            $request_variant_id = $route_data[1];
        }elseif(!empty($rout[1]) && empty($rout[2])){
            $request_tab = $rout[1];
        }
        $segment = \App\Segment::getInstance()->getDefault(true);
        $item = ItemEntity::getById($id, $segment['id']);
        if (empty($item)){
            return $this->notFound();
        }
        $type = TypeEntity::getById($item['type_id'], $segment['id']);
        if (empty($type)){
            return $this->notFound();
        }
        $this->getAns()
            ->add('gallery', $item)
            ->add('current_type', $type);
    }
}