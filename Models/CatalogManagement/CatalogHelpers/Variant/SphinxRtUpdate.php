<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 26.09.14
 * Time: 14:36
 */

namespace Models\CatalogManagement\CatalogHelpers\Variant;

use Models\CatalogManagement\Variant;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;

class SphinxRtUpdate extends VariantHelper{
    const INDEX_NAME = 'lps_catalog_rt';

    protected static $i = NULL;

    private $sphinx = NULL;

    protected function __construct(){
        $this->sphinx = \App\Builder::getInstance()->getSphinx();
            parent::__construct();
    }

    public function onUpdate($updateKey, Variant $variant, $segment_id, $updatedProperties){
        $segments = \App\Segment::getInstance()->getAll();
        foreach($segments as $segment_id => $segment){
            $v = Variant::getById($variant['id'], $segment_id);
            $i = $v->getItem();
            $type = $i->getType();
            $props = PropertyFactory::search($type['id'], PropertyFactory::P_SPHINX_SEARCH);
            $value = '';
            foreach($props as $p){
                $value .= (!empty($value) ? ' ' : '') . ($p['multiple'] ? $v[$p['key']] : $i[$p['key']]);
            }
            $id = $this->getVariantSphinxId($v, $segment_id, $exists);
            if (!empty($value)){
                $this->sphinx->query(($exists ? 'REPLACE' : 'INSERT') . ' INTO `' . self::INDEX_NAME . '` (`id`, `item_id`, `variant_id`, `value`, `segment_id`) VALUES (?d, ?d, ?d, ?s, ?d)',
                    $id, $i['id'], $v['id'], $value, $segment_id
                );
            } elseif($exists){
                $this->sphinx->query('DELETE FROM `' . self::INDEX_NAME . '` WHERE `id`=?d', $id);
            }
        }
    }

    public function onDelete($id, $entity, $remove_from_db){
        $this->sphinx->query('DELETE FROM `' . self::INDEX_NAME . '` WHERE `variant_id`=?d', $id);
    }

    /**
     * Находит новый индекс
     * @param Variant $variant
     * @param int $segment_id
     * @param bool $exists возвращает true если для данного
     * @return mixed
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    private function getVariantSphinxId($variant, $segment_id, &$exists = TRUE){
        $exists = TRUE;
        $id = $this->sphinx->query('SELECT `id` FROM `' . self::INDEX_NAME . '` WHERE `item_id` = ?d AND `variant_id` = ?d AND `segment_id` = ?d', $variant['item_id'], $variant['id'], !empty($segment_id) ? $segment_id : 0)->getCell();
        if (empty($id)){
            $exists = FALSE;
            $id = $this->sphinx->query('SELECT MAX(`id`) AS `last_id` FROM `' . self::INDEX_NAME . '`')->getCell() + 1;
        }
        return $id;
    }
} 