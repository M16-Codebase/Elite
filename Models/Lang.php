<?php
namespace Models;

class Lang {
    private static $i = NULL;
    public static function getInstance(){
        if (is_null(static::$i)){
            static::$i = new self();
        }
        return static::$i;
    }
    public function get($ru_text, $en_text){
        $request_segment = \App\Segment::getInstance()->getDefault(TRUE);
        if ($request_segment['key'] == 'ru' || empty($en_text)){
            return $ru_text;
        }
        return $en_text;
    }
}
