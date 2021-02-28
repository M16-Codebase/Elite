<?php
namespace LPS\Components;

/**
 * Работа с csv
 *
 * @author olya
 */
class CsvData {
    /**
     * Достает строку из файла и преобразует в массив данных
     * @param resource $f
     * @param string $delim_cell
     * @param string $delim_row
     * @param string $quotes
     * @return array
     */
    public static function fileGet($f, $delim_cell = \LPS\Config::CSV_SEPARATOR_CELL, $delim_row = \LPS\Config::CSV_SEPARATOR_ROW, $quotes = \LPS\Config::CSV_QUOTES){
        $str = fgets($f);
        if ($str === FALSE){
            return FALSE;
        }
        return self::getDataFromString($str, $delim_cell, $delim_row, $quotes);
    }
    /**
     * Преобразует массив в строку и дописывает в файл
     * @param resource $f
     * @param string $delim_cell
     * @param string $delim_row
     * @param string $quotes
     * @return int|FALSE
     */
    public static function filePut($f, $data, $delim_cell = \LPS\Config::CSV_SEPARATOR_CELL, $delim_row = \LPS\Config::CSV_SEPARATOR_ROW, $quotes = \LPS\Config::CSV_QUOTES){
        return fputs($f, self::getStringFromData($data, $delim_cell, $delim_row, $quotes));
    }
    /**
     * Самописный парсер csv.
     * @param string $str
     * @param string $delim_cell
     * @param string $delim_row
     * @param string $quotes
     * @return array
     */
    public static function getDataFromString($str, $delim_cell = \LPS\Config::CSV_SEPARATOR_CELL, $delim_row = \LPS\Config::CSV_SEPARATOR_ROW, $quotes = \LPS\Config::CSV_QUOTES){
        if (preg_match('~^'.$quotes.'[^'.$delim_cell.$quotes.']*'.$delim_cell.'.*'.$quotes.'[\r'.$delim_row.']*$~', $str)){//убираем первую и последнюю кавычку, если обрамлена вся строка. а так же знаки конца строки
            $str = preg_replace('~^'.$quotes.'([^'.$delim_cell.$quotes.']*'.$delim_cell.'.*)'.$quotes.'[\r'.$delim_row.']*$~', '$1', $str);
        }else{
            $str = preg_replace('~[\r'.$delim_row.']*~', '', $str);
        }
        $my_array = explode($delim_cell, $str);
        $current_num = 0;//т.к. новую ячейку пишем в следующий элемент массива - тут маленький хак для первой ячейки
        $new_array = array();
        $count_simbol = 0;
        $first_part = FALSE;
        foreach ($my_array as $num => $cell){
            if ($count_simbol == 0){
                $first_part = TRUE;
            }
            $count_simbol += substr_count($cell, $quotes);
            //закрывающая должна быть нечетной по счету с конца, т.к. четные - это экранируемая кавычка
            $current_encl = $count_simbol%2 != 0;//как только кавычек становится четное количество, значит это последний кусок ячейки
            if (!isset($new_array[$current_num])){//если только что сюда зашли
                $new_array[$current_num] = '';
            }
            //проверяем, если мы внутри ячейки, то прилепляем данные ячейки к предыдущему элементу массива
            if ($current_encl){
                $new_array[$current_num] .= (!$first_part ? $delim_cell : '') . $cell;
            }else{
                $new_array[$current_num++] .= $cell;
                $count_simbol = 0;
            }
            $first_part = FALSE;
        }
//        vaR_dump($new_array);
        foreach ($new_array as &$val){//в каждой ячейке убираем обрамление кавычками
            $val = preg_replace('~^'.$quotes.'(.*)'.$quotes.'$~', '$1', $val);
            $val = str_replace(str_repeat($quotes, 2), $quotes, $val);//двойные кавычки заменяем на одинарные
        }
        return $new_array;
    }
    /**
     * Для составления корректного csv, надо обрамлять кавычками ячейки в которых есть разделитель ячеек или перенос строки
     * а так же экранировать кавычки
     * @param array $data
     * @param string $delim_cell
     * @param string $delim_row
     * @param string $quotes
     * @return string
     */
    public static function getStringFromData($data, $delim_cell = \LPS\Config::CSV_SEPARATOR_CELL, $delim_row = \LPS\Config::CSV_SEPARATOR_ROW, $quotes = \LPS\Config::CSV_QUOTES){
        foreach ($data as $d){
            if (strpos($d, $quotes) !== FALSE){
                $d = str_replace($quotes, str_repeat($quotes, 2), $d);
            }
            if (strpos($d, $delim_cell) !== FALSE){
                $d = $quotes . $d . $quotes;
            }
        }
        return implode($delim_cell, $data) . $delim_row;
    }
}
