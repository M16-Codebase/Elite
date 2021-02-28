<?php
namespace LPS\Components;

/**
 *  Форматируем числа
 *
 * @author olga
 */
class FormatNumber
{
    /**
     * Форматирует дробное число
     * Если целое, то возвращает без нулей после точки
     * @param float $number
     * @return float
     */
    public static function getFloat($number, $decimal_sep = '.', $countAfter = 2, $round = TRUE)
    {
        if (!empty($round)) {
            if ($round === TRUE) {
                $number = round($number * pow(10, $countAfter)) / pow(10, $countAfter);
            } elseif ($round === 'up') {
                $number = ceil($number * pow(10, $countAfter)) / pow(10, $countAfter);
            } elseif ($round === 'down') {
                $number = floor($number * pow(10, $countAfter)) / pow(10, $countAfter);
            }
        }
        return self::getNumber($number, '', $decimal_sep, $countAfter);
    }

    /**
     * Форматирует дробное число с разделителем тысяч
     * Если целое, то возвращает без нулей после точки
     * @param float $number
     * @param string $thousands_sep — разделитель тысяч
     * @param string $decimal_sep — десятичный разделитель
     * @param int $countAfter — количество знаков после запятой
     * @return string
     */
    public static function getNumber($number, $thousands_sep = '', $decimal_sep = '.', $countAfter = 2)
    {
        $divided = explode('.', str_replace(',', '.', strval($number)));
        $decimal = !empty($divided[1]) ? intval($divided[1]) : 0;
        return number_format(floatval($number), !empty($decimal) ? $countAfter : 0, $decimal_sep, $thousands_sep);
    }

    public static function getFloatDouble($v)
    {
        $v = str_replace(',', '.', $v);
        return str_replace(',', '.', strpos($v, '.') ? preg_replace('~\.?,?0+$~', '', sprintf('%f', $v)) : $v);//не будем приводить к float, чтобы не получить E+
    }

    /**
     * Форматирует дробное число с разделителем - пробелом тысяч
     * @param float $number
     * @param string $decimal_sep — десятичный разделитель
     * @return string
     */
    public static function getPrice($number, $decimal_sep = '.')
    {
        return self::getNumber($number, ' ', $decimal_sep);
    }

    public static function getInt($number)
    {
        return round($number);
    }
}

?>
