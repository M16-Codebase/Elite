<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 17.08.15
 * Time: 15:26
 */

namespace Models\CatalogManagement\CatalogHelpers\Type;


use Models\CatalogManagement\CatalogHelpers\Property\NestedInSearchProps;
use Models\CatalogManagement\Type;

class NestedInFinalFlag extends TypeHelper
{
    protected static $i = null;

    private $need_check_props = array();

    public function preCreate(&$params, &$errors){
        $parent_type = Type::getById($params['parent_id']);
        $catalog = $parent_type->getCatalog();
        if (!empty($catalog) && $catalog['nested_in']) {
            $params['nested_in_final'] = 1;
        }
        return true;
    }

    public function onCreate(Type $type, $params){
        $catalog = $type->getCatalog();
        if ($type['id'] != $catalog['id'] && $type['nested_in']) {
            NestedInSearchProps::factory()->checkTypeSearchProps($type);
            $parent_type = Type::getById($type['nested_in']);
            $parent_type->_updateParams(array('nested_in_final' => NULL), $e);
            if (!empty($e)) {
                throw new \ErrorException('Не удалось сбросить флаг конечности у родительской категории Errors: ' . var_export($e, true));
            }
        }
    }

    /**
     * Создаем/удаляем копии родительских свойств поиска
     * @param Type $type
     */
    public function onUpdate(Type $type){
        if (!empty($this->need_check_props[$type['id']])) {
            NestedInSearchProps::factory()->checkTypeSearchProps($type);
            unset($this->need_check_props[$type['id']]);
        }
    }
    public function preUpdate(Type $type, &$params, &$errors){
        $catalog = $type->getCatalog();
        if ($catalog['nested_in'] &&array_key_exists('nested_in_final', $params) && $params['nested_in_final'] != $type['nested_in_final']) {
            $this->need_check_props[$type['id']] = true;
        }
    }

    /**
     * При удалении кустовой категории ставим у родительской категории флаг конечного типа, если у него нет других детей
     * @param Type $type
     * @throws \Exception
     */
    public function onDelete(Type $type){
        $catalog = $type->getCatalog();
        if ($type['id'] != $catalog['id'] && $type['nested_in']) {
            $type_ids = Type::getIds(array('nested_in' => $type['nested_in'], 'not_ids' => array($type['id'])));
            if (empty($type_ids)) {
                $parent = Type::getById($type['nested_in']);
                $parent->_updateParams(array('nested_in_final' => 1));
            }
        }
    }
}