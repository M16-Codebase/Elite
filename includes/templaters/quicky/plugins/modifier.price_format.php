<?php
function quicky_modifier_price_format($number)
{
    $number = str_replace(',', '.', $number);
	$number = preg_replace('~[^0-9\.]~', '', $number);
    $divided = explode('.', $number);
    $decimal = !empty($divided[1]) ? intval($divided[1]) : 0;
	$number = floatval($number);
	if (strlen($decimal) > 2){
		$number = round($number, 2, PHP_ROUND_HALF_UP);
	}
	$number = str_replace(',', '.', $number);
	$divided = explode('.', $number);
    $decimal = !empty($divided[1]) ? intval($divided[1]) : 0;
    return number_format($number, !empty($decimal) ? 2 : 0, ',', ' ');
}