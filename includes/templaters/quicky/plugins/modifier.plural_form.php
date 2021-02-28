<?php
function quicky_modifier_plural_form($number, $once_form, $second_form, $five_form, $out_num=true)
{
	$titles=array($once_form, $second_form, $five_form);
	$cases = array (2, 0, 1, 1, 1, 2);
    return ($out_num ? $number.' ':'').$titles[($number%100>4 && $number%100<20) ? 2 : $cases[min($number%10, 5)] ];
}
