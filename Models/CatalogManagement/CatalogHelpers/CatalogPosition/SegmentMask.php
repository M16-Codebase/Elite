<?php
/**
 * Подстановка маски из нужного сегмента
 */
namespace Models\CatalogManagement\CatalogHelpers\CatalogPosition;
use Models\CatalogManagement\CatalogPosition;
class SegmentMask extends CatalogPositionHelper{
    protected static $i = NULL;
	public function onPropertyLoad(CatalogPosition $e, &$propertiesBySegments, $fromDB = FALSE){
		if (empty($propertiesBySegments)){
			return;
		}
		$segments = \App\Segment::getInstance()->getAll();
		foreach ($propertiesBySegments as $prop_segment_id => &$properties){
			foreach ($properties as $p_key => &$p){
				$property = $e['properties'][$p_key]['property'];
				if (in_array($property['data_type'], array(\Models\CatalogManagement\Properties\Flag::TYPE_NAME, \Models\CatalogManagement\Properties\Enum::TYPE_NAME)) || $property['segment'] || $property instanceof \Models\CatalogManagement\Properties\Entity){
					continue;//для enum есть свой хелпер с заменой самих названий, и заодно и маской, а сегментированные сами умеют
				}
				$same_masks = $this->isMasksSame($property);
				if ($prop_segment_id == 0){
					//если маски для сегментов различны, то свойство разделяем
					if (!$same_masks){
						foreach ($segments as $s_id => $s){
							$this->rewritePropertyValue($p, $property, $s_id);
							$propertiesBySegments[$s_id][$p_key] = $p;
						}
						unset($propertiesBySegments[$prop_segment_id][$p_key]);
					}
				}elseif (!$same_masks){
					$this->rewritePropertyValue($p, $property, $prop_segment_id);
				}
			}
		}
	}

    /**
     * Заменяем complete_value на значение с маской из нужного сегмента
     * @param array $propertyValue
     * @param \Models\CatalogManagement\Properties\Property $property
     * @param int $segment_id
     */
	private function rewritePropertyValue(&$propertyValue, \Models\CatalogManagement\Properties\Property $property, $segment_id){
		if (empty($property['segment_data']['mask'][$segment_id])){
			return;
		}
		if (is_array($propertyValue['value'])){
			foreach ($propertyValue['value'] as $k => &$v){
				$propertyValue['complete_value'][$k] = $property->markerReplace($v, $property['segment_data']['mask'][$segment_id]);
			}
		}else{
			$propertyValue['complete_value'] = $property->markerReplace($propertyValue['value'], $property['segment_data']['mask'][$segment_id]);
		}
	}

    /**
     * @param \Models\CatalogManagement\Properties\Property $property
     * @return bool
     */
	private function isMasksSame($property){
		$segments = \App\Segment::getInstance()->getAll();
		//для всех остальных, если маска разная для сегментов, то надо разделить свойство по сегментам
		$prev_segment_mask = NULL;
		$same_masks = TRUE;
		foreach ($segments as $s_id => $s){
			$new_segment_mask = !empty($property['segment_data']['mask'][$s_id]) ? $property['segment_data']['mask'][$s_id] : '';
			if (is_null($prev_segment_mask)){
				$prev_segment_mask = $new_segment_mask;
			}
			if ($prev_segment_mask != $new_segment_mask){
				$same_masks = FALSE;
			}
		}
		return $same_masks;
	}
}