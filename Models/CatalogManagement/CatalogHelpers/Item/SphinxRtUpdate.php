<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 26.09.14
 * Time: 15:19
 */

namespace Models\CatalogManagement\CatalogHelpers\Item;

use Models\CatalogManagement\Item;
use Models\CatalogManagement\CatalogHelpers\Variant\SphinxRtUpdate as SphinxRtUpdateVariant;

class SphinxRtUpdate extends ItemHelper{
    protected static $i = NULL;

    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties){
        $variants = $item->getVariants();
        $variant_helper = SphinxRtUpdateVariant::factory();
        foreach($variants as $v){
            $variant_helper->onUpdate($updateKey, $v, array(), NULL);
        }
    }
} 