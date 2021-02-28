<?php
function quicky_modifier_price_format_range($string)
{
	preg_match('~([0-9]*)([^0-9]*)([0-9]*)(.*)~', $string, $out);
	$value_min = !empty($out[1]) ? number_format($out[1], 0, '.', ' ') : 0;
	$value_max = !empty($out[3]) ? number_format($out[3], 0, '.', ' ') : 0;
	if (!empty($value_min) && !empty($value_max)){
		if ($value_min != $value_max){
			return $value_min . $out[2] . $value_max . $out[4];
		}else{
			return $value_min . $out[4];
		}
	}elseif (!empty($value_min) && empty($value_max)){
		return $value_min . $out[4];
	}elseif (empty($value_min) && !empty($value_max)){
		return $value_max . $out[4];
	}
}