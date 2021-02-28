<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 23.09.14
 * Time: 17:53
 *
 * Автосоздание проперти для поиска через сфинкс
 */

namespace Models\CatalogManagement\CatalogHelpers\Type;

use App\Configs\CatalogConfig;
use App\Configs\SphinxConfig;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Properties\View;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;

class SphinxSearchProp extends TypeHelper{
    protected static $i = NULL;
    private $properties = array();

    public function fieldsList(){
        return array('sphinx_search_prop');
    }

    public function get(Type $type, $field, $segment_id = NULL){
        if ($field == 'sphinx_search_prop'){
            if (empty($this->properties[$type['id']])){
                $prop = PropertyFactory::search($type['id'], PropertyFactory::P_ALL, 'key', 'type_group', 'self', array('key' => SphinxConfig::CATALOG_SEARCH_PROP_KEY));
                $prop = reset($prop);
                $this->properties[$type['id']] =  $prop;
            }
            return $this->properties[$type['id']];
        }
        return NULL;
    }

    public function onUpdate(Type $type) {
        if ($type->isCatalog()) {
            if (!empty($type['search_by_sphinx'])) {
                // проверить свойства, если нужно насоздавать
                $child_types = Type::search(array('parents' => $type['id'], 'allow_children' => 0));
                foreach($child_types as $t) {
                    $this->checkProp($t);
                }
            } else {
                // удалить свойства
                $props = PropertyFactory::search($type['id'], PropertyFactory::P_ALL, 'id', 'type_group', 'children', array('key' => SphinxConfig::CATALOG_SEARCH_PROP_KEY));
                if (!empty($props)) {
                    foreach($props as $prop) {
                        unset($this->properties[$prop['type_id']]);
                        Property::delete($prop['id']);
                    }
                }
            }
        } else {
            $this->checkProp($type);
        }
    }

    /**
     * Проверяем свойство поиска у категории, если требуется - создаем/удаляем
     * @param Type $type
     * @throws \Exception
     */
    private function checkProp(Type $type){
        $catalog = $type->getCatalog();
        $prop = $this->get($type, 'sphinx_search_prop');
        if (!empty($prop)) {
            if ($type['allow_children'] || $type['only_items'] == $prop['multiple'] || !$catalog['search_by_sphinx']) {
                // удаляем пропертю у неконечных типов, и в случае несовпадения расщепляемости
                Property::delete($prop['id']);
                unset($this->properties[$type['id']]);
                $prop = NULL;
            }
        }
        if (empty($prop) && !$type['allow_children'] && $catalog['search_by_sphinx']) {
            // Если у конечного типа нет проперти - создаем
            $values_keys = SphinxConfig::getCatalogDefaultIndexProps($catalog['key']);
            $prop_data = array(
                'type_id' => $type['id'],
                'key' => SphinxConfig::CATALOG_SEARCH_PROP_KEY,
                'title' => 'Строка поиска Sphinx',
                'data_type' => View::TYPE_NAME,
                'values' => (!empty($values_keys)
                    ? '{' . implode('} {', $values_keys) . '}'
                    : '{' . CatalogConfig::KEY_ITEM_TITLE . '}' . ($catalog['only_items'] ? '' : ' {' . CatalogConfig::KEY_VARIANT_TITLE . '}')),
                'multiple' => $catalog['only_items'] ? 0 : 1,
                'fixed' => 'title.key.description.multiple.unique.data_type.set.search_type.filter_title.group_id.necessary.mask.visible.read_only.major.major_count.export.image',
                'segment' => \LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE && $catalog['allow_segment_properties'] ? 1 : 0
            );
            $prop_id = Property::create($prop_data, $e);
            if (empty($e)) {
                $prop = PropertyFactory::getById($prop_id);
                $prop->update($prop_data);
            }
        }
    }
} 