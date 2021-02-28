<?php
namespace LPS\Components;
/**
 * Разбираемся с кодировками
 */
class Encoding{
    const UTF8_BOM = 'utf-8';
    const UTF16_LITTLE_ENDIAN_BOM = 'utf16-little';
    const UTF16_BIG_ENDIAN_BOM = 'utf16-big';
    const UTF32_LITTLE_ENDIAN_BOM = 'utf32-little';
    const UTF32_BIG_ENDIAN_BOM = 'utf32-big';
    public static function getBom($type = self::UTF8_BOM){
        $boms = array(
            self::UTF8_BOM => chr(0xEF) . chr(0xBB) . chr(0xBF),
            self::UTF16_LITTLE_ENDIAN_BOM => chr(0xFF) . chr(0xFE),
            self::UTF16_BIG_ENDIAN_BOM => chr(0xFE) . chr(0xFF),
            self::UTF32_LITTLE_ENDIAN_BOM => chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00),
            self::UTF32_BIG_ENDIAN_BOM => chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF)
        );
        return $boms[$type];
    }
    /**
     * Определяет кодировку файла. Либо utf, либо cp1251. Остальные идут лесом
     * @param string $file_name
     * @return string определенная кодировка
     */
    public static function detect($file_name){
        $f = fopen($file_name, 'r');
        $first_string = fgets($f);
        $code = mb_detect_encoding($first_string, array('utf-8', 'cp1251'), TRUE);
        if (empty($code) || $code == 'UTF-8'){
            $code = self::detect_utf_encoding($first_string);
        }
        if (empty($code)) {
            $code = 'cp1251';
        }
        fclose($f);
        return $code;
    }
    /**
     * Определяем конкретный utf, требуется самая первая строка файла, либо содержимое файла целиком
     * @param string $str
     * @return string
     */
    public static function detect_utf_encoding($str) {
        $first2 = substr($str, 0, 2);
        $first3 = substr($str, 0, 3);
        $first4 = substr($str, 0, 3);

        if ($first3 == self::getBom(self::UTF8_BOM)) return 'UTF-8';
        elseif ($first4 == self::getBom(self::UTF32_BIG_ENDIAN_BOM)) return 'UTF-32BE';
        elseif ($first4 == self::getBom(self::UTF32_LITTLE_ENDIAN_BOM)) return 'UTF-32LE';
        elseif ($first2 == self::getBom(self::UTF16_BIG_ENDIAN_BOM)) return 'UTF-16BE';
        elseif ($first2 == self::getBom(self::UTF16_LITTLE_ENDIAN_BOM)) return 'UTF-16LE';
    }
}
