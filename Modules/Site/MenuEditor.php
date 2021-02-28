<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 08.09.14
 * Time: 17:04
 */

namespace Modules\Site;

use LPS\AdminModule;
use Models\MenuManagement\MenuItem;

class MenuEditor extends AdminModule{

    public function index(){
        $this->getAns()->add('menu_list', MenuItem::getMenuList());
    }

    public function addMenu(){
        $errors = array();
        $ans = $this->setJsonAns()->setTemplate('Modules/Site/MenuEditor/menuList.tpl');
        $key = $this->request->request->get('key');
        if (empty($key)){
            $errors['key'] = 'empty';
        } else {
            if (!MenuItem::createMenu($key)){
                $errors['key'] = 'already_exists';
            }
        }
        if (!empty($errors)){
            $ans->setEmptyContent()->setErrors($errors);
        } else {
            $ans->add('menu_list', MenuItem::getMenuList());
        }
    }

    public function editMenu(){
        $ans = $this->setJsonAns()->setTemplate('Modules/Site/MenuEditor/menuList.tpl');
        $id = $this->request->request->get('id');
        $key = $this->request->request->get('key');
        if (empty($id)){
            $errors['id'] = 'empty';
        }
        if (empty($key)){
            $errors['key'] = 'empty';
        }
        if (empty($errors)){
            if (!MenuItem::changeMenuKey($id, $key)){
                $errors['key'] = 'already_used';
            }
        }

        if (!empty($errors)){
            $ans->setEmptyContent()->setErrors($errors);
        } else {
            $ans->add('menu_list', MenuItem::getMenuList());
        }
    }

    public function deleteMenu(){
        $ans = $this->setJsonAns()->setTemplate('Modules/Site/MenuEditor/menuList.tpl');
        $id = $this->request->request->get('id');
        if (empty($id)){
            $errors['id'] = 'empty';
        } else {
            MenuItem::deleteMenu($id);
        }
        if (!empty($errors)){
            $ans->setEmptyContent()->setErrors($errors);
        } else {
            $ans->add('menu_list', MenuItem::getMenuList());
        }
    }

    public function menuItems(){
        $menu = MenuItem::getMenuById($this->request->query->get('id'));
        if (empty($menu)){
            return $this->notFound();
        }
        $this->getAns()
            ->add('menu', $menu);
        $this->menuItemsList($menu['key']);
    }

    public function menuItemsList($key = NULL){
        $ajax = empty($key);
        if ($ajax){
            $ans = $this->setJsonAns();
            $key = $this->request->request->get('key');
        }
        $menu_items = MenuItem::getMenuItemsByKey($key);
        $this->getAns()->add('menu_items', $menu_items);
        if ($ajax){
            if (empty($key)){
                $ans->setEmptyContent()->addErrorByKey('key', 'empty');
            }
        }
    }

    public function menuItemFields(){
        $ans = $this->setJsonAns();
        $menu_item = MenuItem::getById($this->request->request->get('id'));
        if (empty($menu_item)){
            $ans->setEmptyContent()->addErrorByKey('item', 'empty');
        } else {
            $ans->setFormData($menu_item->asArray());
        }
    }

    public function editMenuItem(){
        $ans = $this->setJsonAns();
        $errors = array();
        $data = $this->request->request->all();
        if (isset($data['id'])){
            $id = $data['id'];
            unset($data['id']);
        }
        if (empty($id)){
            $id = MenuItem::create($data, $errors);
            $menu_item = MenuItem::getById($id);
        } else {
            $menu_item = MenuItem::getById($id);
            if (empty($menu_item)){
                $errors['menu_item'] = 'not_found';
            } else {
                $menu_item->update($data, $errors);
            }
        }
        $image = $this->request->files->get('image');
        if (!empty($image)){
            $menu_item->setImage($image);
        }
        if (!empty($errors)){
            $ans->setEmptyContent()->setErrors($errors);
        } else{
            $menu = MenuItem::getMenuById($data['menu_id']);
            $ans->setTemplate('Modules/Site/MenuEditor/menuItemsList.tpl')->add('menu_items', MenuItem::getMenuItemsByKey($menu['key']));
        }
    }

    public function deleteMenuItem(){
        $ans = $this->setJsonAns();
        $menu_item = MenuItem::getById($this->request->request->get('id'));
        if (empty($menu_item)){
            $ans->setEmptyContent()->addErrorByKey('id', 'empty');
        } else {
            $menu = MenuItem::getMenuById($menu_item['menu_id']);
            MenuItem::delete($menu_item['id']);
            $ans->setTemplate('Modules/Site/MenuEditor/menuItemsList.tpl')->add('menu_items', MenuItem::getMenuItemsByKey($menu['key']));
        }
    }

    public function changePosition(){
        $ans = $this->setJsonAns();
        $id = $this->request->request->get('id');
        $position = $this->request->request->get('position');
        $menu_item = !empty($id) ? MenuItem::getById($id) : NULL;
        $errors = array();
        if (empty($menu_item)){
            $errors['id'] = 'empty';
        }
        if (empty($position)){
            $errors['position'] = 'empty';
        }
        if (empty($errors)){
            $menu_item->changePosition($position);
            $menu = MenuItem::getMenuById($menu_item['menu_id']);
            $ans->setTemplate('Modules/Site/MenuEditor/menuItemsList.tpl')->add('menu_items', MenuItem::getMenuItemsByKey($menu['key']));
        } else {
            $ans->setEmptyContent()->setErrors($errors);
        }
    }

} 