<?php
/**
 * 
 */
namespace Models\CatalogManagement\CatalogHelpers\CatalogPosition;
use Models\CatalogManagement\CatalogPosition;
class SegmentEnumTitles extends CatalogPositionHelper{
    protected static $i = NULL;
	public function onPropertyLoad(CatalogPosition $e, &$propertiesBySegments, $fromDB = FALSE){
		$add_enums = array();
		if (empty($propertiesBySegments[0])){
			return;
		}
		foreach ($propertiesBySegments[0] as $p_key => &$pr){
			$property = $e['properties'][$p_key]['property'];
			if ($property['data_type'] == \Models\CatalogManagement\Properties\Enum::TYPE_NAME){
				if ($property['segment'] != 1){
					$add_enums[$p_key] = $pr;
					unset($propertiesBySegments[0][$p_key]);
				}
			}
		}
		$segments = \App\Segment::getInstance()->getAll();
		foreach ($segments as $s_id => $s){
			foreach ($add_enums as $p_k => $p){
				if (!empty($p['value'])){
                    $property = $e['properties'][$p_k]['property'];
					$prop_mask = !empty($property['segment_data']['mask'][$s_id]) ? $property['segment_data']['mask'][$s_id] : '';
					$segment_enum = $property['segment_enum'];
					if (is_array($p['value'])){
						foreach ($p['value'] as $k => $v){
							$p['complete_value'][$k] = isset($segment_enum[$v][$s_id]) ? $property->markerReplace($segment_enum[$v][$s_id], $prop_mask) : '';
							$p['real_value'][$k] = isset($segment_enum[$v][$s_id]) ? $segment_enum[$v][$s_id] : '';
						}
					}else{
						$p['complete_value'] = isset($segment_enum[$p['value']][$s_id]) ? $property->markerReplace($segment_enum[$p['value']][$s_id], $prop_mask) : '';
						$p['real_value'] = isset($segment_enum[$p['value']][$s_id]) ? $segment_enum[$p['value']][$s_id] : '';
					}
					$propertiesBySegments[$s_id][$p_k] = $p;
				}
			}
		}
	}
}