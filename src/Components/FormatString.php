<?php
namespace LPS\Components;
/**
 * Видоизменения строк
 **/
class FormatString
{
    /**
     * Функция склонения числительных в русском языке
     *
     * @param int $number Число которое нужно просклонять
     * @param array $titles Массив слов для склонения
     * @return string
     **/
    public static function pluralForm($number, $titles, $out_num = true)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        return ($out_num ? $number . ' ' : '') . $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * Смена раскладки
     * @param string $text
     * @param int $arrow сдвиг
     * @return string
     */
    public static function keyboardLayout($text, $arrow = 0)
    {
        $str[0] = array('й' => 'q', 'ц' => 'w', 'у' => 'e', 'к' => 'r', 'е' => 't', 'н' => 'y', 'г' => 'u', 'ш' => 'i', 'щ' => 'o', 'з' => 'p', 'х' => '[', 'ъ' => ']', 'ф' => 'a', 'ы' => 's', 'в' => 'd', 'а' => 'f', 'п' => 'g', 'р' => 'h', 'о' => 'j', 'л' => 'k', 'д' => 'l', 'ж' => ';', 'э' => '\'', 'я' => 'z', 'ч' => 'x', 'с' => 'c', 'м' => 'v', 'и' => 'b', 'т' => 'n', 'ь' => 'm', 'б' => ',', 'ю' => '.', 'Й' => 'Q', 'Ц' => 'W', 'У' => 'E', 'К' => 'R', 'Е' => 'T', 'Н' => 'Y', 'Г' => 'U', 'Ш' => 'I', 'Щ' => 'O', 'З' => 'P', 'Х' => '[', 'Ъ' => ']', 'Ф' => 'A', 'Ы' => 'S', 'В' => 'D', 'А' => 'F', 'П' => 'G', 'Р' => 'H', 'О' => 'J', 'Л' => 'K', 'Д' => 'L', 'Ж' => ';', 'Э' => '\'', '?' => 'Z', 'ч' => 'X', 'С' => 'C', 'М' => 'V', 'И' => 'B', 'Т' => 'N', 'Ь' => 'M', 'Б' => ',', 'Ю' => '.',);
        $str[1] = array('q' => 'й', 'w' => 'ц', 'e' => 'у', 'r' => 'к', 't' => 'е', 'y' => 'н', 'u' => 'г', 'i' => 'ш', 'o' => 'щ', 'p' => 'з', '[' => 'х', ']' => 'ъ', 'a' => 'ф', 's' => 'ы', 'd' => 'в', 'f' => 'а', 'g' => 'п', 'h' => 'р', 'j' => 'о', 'k' => 'л', 'l' => 'д', ';' => 'ж', '\'' => 'э', 'z' => 'я', 'x' => 'ч', 'c' => 'с', 'v' => 'м', 'b' => 'и', 'n' => 'т', 'm' => 'ь', ',' => 'б', '.' => 'ю', 'Q' => 'Й', 'W' => 'Ц', 'E' => 'У', 'R' => 'К', 'T' => 'Е', 'Y' => 'Н', 'U' => 'Г', 'I' => 'Ш', 'O' => 'Щ', 'P' => 'З', '[' => 'Х', ']' => 'Ъ', 'A' => 'Ф', 'S' => 'Ы', 'D' => 'В', 'F' => 'А', 'G' => 'П', 'H' => 'Р', 'J' => 'О', 'K' => 'Л', 'L' => 'Д', ';' => 'Ж', '\'' => 'Э', 'Z' => '?', 'X' => 'ч', 'C' => 'С', 'V' => 'М', 'B' => 'И', 'N' => 'Т', 'M' => 'Ь', ',' => 'Б', '.' => 'Ю',);
        return strtr($text, isset($str[$arrow]) ? $str[$arrow] : array_merge($str[0], $str[1]));
    }

    /**
     * Самописный парсер csv.
     * @param string $str
     * @return string
     */
    public static function getCsv($str, $delim = ',')
    {
        if (preg_match('~^"[^' . $delim . '"]*' . $delim . '.*"[\r\n]*$~', $str)) {//убираем первую и последнюю кавычку, если обрамлена вся строка. а так же знаки конца строки
            $str = preg_replace('~^"([^' . $delim . '"]*' . $delim . '.*)"[\r\n]*$~', '$1', $str);
        } else {
            $str = preg_replace('~[\r\n]*~', '', $str);
        }
        $my_array = explode($delim, $str);
        $current_num = 0;//т.к. новую ячейку пишем в следующий элемент массива - тут маленький хак для первой ячейки
        $new_array = array();
        $count_simbol = 0;
        $first_part = FALSE;
        foreach ($my_array as $num => $cell) {
            if ($count_simbol == 0) {
                $first_part = TRUE;
            }
            $count_simbol += substr_count($cell, '"');
            //закрывающая должна быть нечетной по счету с конца, т.к. четные - это экранируемая кавычка
            $current_encl = $count_simbol % 2 != 0;//как только кавычек становится четное количество, значит это последний кусок ячейки
            if (!isset($new_array[$current_num])) {//если только что сюда зашли
                $new_array[$current_num] = '';
            }
            //проверяем, если мы внутри ячейки, то прилепляем данные ячейки к предыдущему элементу массива
            if ($current_encl) {
                $new_array[$current_num] .= (!$first_part ? $delim : '') . $cell;
            } else {
                $new_array[$current_num++] .= $cell;
                $count_simbol = 0;
            }
            $first_part = FALSE;
        }
//        vaR_dump($new_array);
        foreach ($new_array as &$val) {//в каждой ячейке убираем обрамление кавычками
            $val = preg_replace('~^"(.*)"$~', '$1', $val);
            $val = str_replace('""', '"', $val);//двойные кавычки заменяем на одинарные
        }
        return $new_array;
    }

    /**
     * @param string $str
     * @return string
     */
    public static function ucfirstUtf8($str)
    {
        if (empty($str)) {
            return $str;
        }
        if ($str{0} >= "\xc3") {
            return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1);
        } else {
            return ucfirst($str);
        }
    }

    private static $cases = array(
        'И' => 'i',
        'Р' => 'r',
        'Д' => 'd',
        'В' => 'v',
        'Т' => 't',
        'П' => 'p',
    );

    public static function wordCases($word, &$error)
    {
        if (empty($word)) {
            return;
        }
        $word = mb_strtolower($word);
        $result = simplexml_load_file('http://api.morpher.ru/WebService.asmx/GetXml?s=' . $word, NULL, LIBXML_COMPACT);//может не читаться, тогда надо отлавливать
        if (empty($result)) {
            $error['query_error'] = 'empty';
            return;
        }
        $data = array(
            'i' => array(
                'case' => 'i',
                '1' => self::ucfirstUtf8($word),
                '2' => self::ucfirstUtf8(strval($result->множественное->И))
            )
        );
        if ($result->code) {
            $error['query_error'] = $result;
            return;
        }
        foreach ($result as $r) {
            $name = strval($r->getName());
            if ($name != 'множественное') {
                if (!isset(self::$cases[$name])) {
                    $error['unknown name ' . $name] = $result;
                    return;
                }
                $data[self::$cases[$name]] = array(
                    'case' => self::$cases[$name],
                    1 => self::ucfirstUtf8(strval($r)),
                    2 => self::ucfirstUtf8(strval($result->множественное->$name))
                );
            }
        }
        return $data;
    }
	public function getNumericHash($s, $digits = 9){
		return substr(base_convert(hash('md5', $s), 16, 10), 0, $digits);
	}
}
