<?php
/**
 * Прикрепляет объекты инфраструктуры к объектам первичной и вторичной недвижимости
 *
 * User: Charles Manson
 * Date: 03.09.15
 * Time: 17:57
 */

namespace Models\CatalogManagement\CatalogHelpers\Item;


use App\Configs\CatalogConfig;
use App\Configs\RealEstateConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Properties\Address;
use Models\CatalogManagement\Properties\Coords;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;

class InfrastructureHelper extends ItemHelper
{
    /**
     * Радиус, в пределах которого привязываем объекты инфраструктуры
     */
    const ATTACH_DISTANCE = 3;

    protected static $i = null;
    /**
     * @var \MysqlSimple\Controller
     */
    private $db = null;
    private $ids_for_update = array();

    protected function __construct() {
        parent::__construct();
        $this->db = \App\Builder::getInstance()->getDB();
    }

    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors){
        if (empty($properties) || $segment_id != 1) {
            // Координаты — несегментированное свойство
            return;
        }
        $type = $item->getType();
        $catalog = $type->getCatalog();
        if (in_array($catalog['key'], array(CatalogConfig::CATALOG_KEY_RESALE, CatalogConfig::CATALOG_KEY_INFRASTRUCTURE))
            || $catalog['key'] == CatalogConfig::CATALOG_KEY_REAL_ESTATE && $type['key'] == RealEstateConfig::CATEGORY_KEY_COMPLEX
        ) {
            $prop_key =
                ($catalog['key'] == CatalogConfig::CATALOG_KEY_INFRASTRUCTURE
                    ? CatalogConfig::KEY_INFRA_ADDRESS
                    :(CatalogConfig::CATALOG_KEY_REAL_ESTATE ? RealEstateConfig::KEY_OBJECT_ADDRESS : RealEstateConfig::KEY_APPART_ADDRESS))
                . Address::COORDS_PROP_SUFFIX;
            if (!empty($properties[$prop_key]) && $properties[$prop_key][0]['value'] != $item[$prop_key]) {
                $this->ids_for_update[$item['id']] = $item['id'];
            }
        }
    }

    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties){
        if (!empty($this->ids_for_update[$item['id']])) {
            unset($this->ids_for_update[$item['id']]);
            $item->save(); // Мы берем данные напрямую из БД, поэтому необходимо сохранить айтем до начала работ
            $type = $item->getType();
            $catalog = $type->getCatalog();
            if ($catalog['key'] == CatalogConfig::CATALOG_KEY_INFRASTRUCTURE) {
                $this->attachInfraToBuildings($item, $this->getRealEstateCoordsProp());
                $this->attachInfraToBuildings($item, $this->getResaleCoordsProp());
            } else {
                $prop = $catalog['key'] == CatalogConfig::CATALOG_KEY_REAL_ESTATE
                    ? $this->getRealEstateCoordsProp()
                    : $this->getResaleCoordsProp();
                $infra_ids = $this->getInfraIds($prop, null, $item['id']);
                $infra_ids = !empty($infra_ids[$item['id']]) ? $infra_ids[$item['id']] : array();
                $this->attachInfraToBuilding($item, $infra_ids);
            }
        }
    }

    /**
     * Привязка объектов инфраструктуры ко всему каталогу
     */
    public function attachInfraFullCatalog() {
        foreach(array($this->getRealEstateCoordsProp(), $this->getResaleCoordsProp()) as $prop) {
            /** @var Coords $prop */
            $data = $this->getInfraIds($prop);
            $buildings_type = $prop->getType();
            $buildings_catalog = $buildings_type->getCatalog();
            $items = CatalogSearch::factory($buildings_catalog['key'])
                ->setTypeId($buildings_type['id'])
                ->setPublicOnly(false)
                ->searchItems()
                ->getSearch();
            if (!empty($items)) {
                foreach($items as $i) {
                    $this->attachInfraToBuilding($i, !empty($data[$i['id']]) ? $data[$i['id']] : array());
                }
            }
        }
    }

    /**
     * @param Item $infra
     * @param Coords $prop
     */
    private function attachInfraToBuildings(Item $infra, $prop) {
        $building_type = $prop->getType();
        $building_catalog = $building_type->getCatalog();
        $building_ids = CatalogSearch::factory($building_catalog['key'])
            ->setTypeId($building_type['id'])
            ->setRules(array(Rule::make(RealEstateConfig::KEY_APPART_INFRA)->setValue($infra['id'])))
            ->searchItemIds()
            ->getItemIds();
        $data = $this->getInfraIds($prop, $infra['id']);
        $building_ids = array_merge($building_ids, array_keys($data));
        if (!empty($building_ids)) {
            $buildings = Item::factory($building_ids);
            $data = $this->getInfraIds($prop, null, $building_ids);
            foreach($buildings as $building) {
                $this->attachInfraToBuilding($building, !empty($data[$building['id']]) ? $data[$building['id']] : array());
            }
        }
    }

    /**
     * @param Item $item
     * @param array $infra_ids
     */
    private function attachInfraToBuilding(Item $item, $infra_ids) {
        $attached = $item['properties'][RealEstateConfig::KEY_APPART_INFRA]['value'];
        $attached = array_filter($attached);
        $ids2add = array_diff($infra_ids, $attached);
        $ids2delete = array_diff($attached, $infra_ids);
        $edit_data = array();
        foreach($attached as $val_id => $val){
            $obj = array(
                'val_id' => $val_id,
                'value' => $val
            );
            if (in_array($val, $ids2delete)) {
                $obj['options'] = array('delete' => 1);
            }
            $edit_data[] = $obj;
        }
        foreach($ids2add as $val) {
            $edit_data[] = array(
                'val_id' => null,
                'value' => $val
            );
        }
        if (!empty($edit_data)) {
            $item->update(array(), array(RealEstateConfig::KEY_APPART_INFRA => $edit_data), $err);
        }
    }

    /**
     * @return Coords
     */
    private function getInfraCoordsProp() {
        $catalog = Type::getByKey(CatalogConfig::CATALOG_KEY_INFRASTRUCTURE);
        $props = PropertyFactory::getByKey(CatalogConfig::KEY_INFRA_ADDRESS.Address::COORDS_PROP_SUFFIX, $catalog['id']);
        return reset($props);
    }

    /**
     * @return Coords
     */
    public function getRealEstateCoordsProp() {
        $catalog = Type::getByKey(CatalogConfig::CATALOG_KEY_REAL_ESTATE);
        $type = Type::getByKey(RealEstateConfig::CATEGORY_KEY_COMPLEX, $catalog['id']);
        $props = PropertyFactory::getByKey(RealEstateConfig::KEY_OBJECT_ADDRESS.Address::COORDS_PROP_SUFFIX, $type['id']);
        return reset($props);
    }

    /**
     * @return Coords
     */
    public function getResaleCoordsProp() {
        $catalog = Type::getByKey(CatalogConfig::CATALOG_KEY_RESALE);
        $props = PropertyFactory::getByKey(RealEstateConfig::KEY_APPART_ADDRESS.Address::COORDS_PROP_SUFFIX, $catalog['id']);
        return reset($props);
    }

    /**
     * @param Coords $building_prop
     * @param int|array|null $infra_id
     * @param int|array|null $building_id
     * @return int[][]
     */
    public function getInfraIds($building_prop, $infra_id = null, $building_id = null) {
        $infra_prop = $this->getInfraCoordsProp();
        return $this->db->query("
            select `i_id`, `o_id`,
                ( 6371 * acos( cos( radians(`i_lat`) )
                      * cos( radians( `o_lat` ) )
                      * cos( radians( `i_lng` ) - radians(`o_lng`) )
                      + sin( radians(`i_lat`) )
                      * sin( radians( `o_lat` ) ) ) ) AS `distance`
            from  (
              SELECT `i`.`item_id` AS `i_id`, `i`.`lat` AS `i_lat`, `i`.`lng` AS `i_lng`, `o`.`item_id` AS `o_id`, `o`.`lat` AS `o_lat`, `o`.`lng` AS `o_lng`
              FROM
                (SELECT `item_id`, TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(`value`, ',', 1), ',', -1)) AS `lat`, TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(`value`, ',', 2), ',', -1)) AS `lng`
                    FROM ?# WHERE `property_id` = ?d{ AND `item_id` " . (is_array($infra_id) ? 'IN (?l)' : '= ?d') . "}) as `i`
                JOIN (SELECT `item_id`, TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(`value`, ',', 1), ',', -1)) AS `lat`, TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(`value`, ',', 2), ',', -1)) AS `lng`
                    FROM ?# WHERE `property_id` = ?d{ AND `item_id` " . (is_array($building_id) ? 'IN (?l)' : '= ?d') . "}) AS `o`) AS `t`
            HAVING `distance` <= ?d",
            Item::TABLE_PROP_STRING,
            $infra_prop['id'],
            !empty($infra_id) ? $infra_id : $this->db->skipIt(),
            Item::TABLE_PROP_STRING,
            $building_prop['id'],
            !empty($building_id) ? $building_id : $this->db->skipIt(),
            self::ATTACH_DISTANCE
            )->getCol(array('o_id', 'i_id'), 'i_id');

    }
}