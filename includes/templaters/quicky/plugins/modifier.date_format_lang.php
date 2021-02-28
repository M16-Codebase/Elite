<?php
/**
 * Description of date_format_lang
 *
 * @author olga
 */
require_once Quicky::$obj->fetch_plugin('shared.make_timestamp');
function quicky_modifier_date_format_lang($string, $format="%b %e, %Y", $lang) {
    if (strpos($format, '%B') !== false){
        if ($lang == 'ru'){
            $month = GetDateRusDeclension(strftime('%m', quicky_make_timestamp($string)));
        }else{
            $month = date('F', quicky_make_timestamp($string));
        }
        $format = str_replace('%B', $month, $format);
    }
    return strftime($format, quicky_make_timestamp($string));
}
/* функция для склонения месяцев
 * string $month номер месяца (от 01 до 12)
 */
function GetDateRusDeclension($month) {
    $n = intval($month);
    $monthes = Array("Месяца","Января","Февраля","Марта","Апреля","Мая","Июня","Июля","Августа","Сентября","Октября","Ноября","Декабря");
    $date_rus = $monthes[$n];
    return $date_rus;
}
?>
