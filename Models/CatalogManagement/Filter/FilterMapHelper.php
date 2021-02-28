<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 07.11.2017
 * Time: 0:46
 */

namespace Models\CatalogManagement\Filter;


class FilterMapHelper
{

    const NUMERIC = 'numeric';
    const OBJECT = 'object';
    const WORD_ANALOG = 'word_analog';


    // храним значения тегов и их соответствия
    private $synonyms = array(
        'bedrooms_word' => 'bed_number',
        'bedrooms_numeric' => 'bed_number',
        'district' => 'district'
    );

    //как выводить
    private $writeRules = array(
        'bedrooms_word' => self::WORD_ANALOG,
    );

    // как обрабатывать каждое значение
    private $types = array(
        'bed_number' => self::NUMERIC,
        'district' => self::OBJECT
    );

    public static $instance;
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    public function checkMatch($match)
    {
        $match = mb_strtolower($match);
        $result = array();
        // сначала смотрим есть ли в синимах такой ключ
        if (array_key_exists($match, $this->synonyms)) {
            $type = $this->synonyms[$match];
            return $type;
        }
        return false;
    }

    public function getType ($searchElem) {
        if (array_key_exists($searchElem, $this->types)) {
            return $this->types[$searchElem];
        }
    }

    public function getNumericRule($match, $value)
    {
        $match = mb_strtolower($match);
        if (array_key_exists($match, $this->writeRules)) {
            return $this->{$this->writeRules[$match]}((int)$value);
        }
    }

    public function word_analog($value)
    {
        if (!is_integer($value)) {
            return $value;
        }
        $analog = '';
        switch ($value) {
            case 1: $analog = 'одно'; break;
            case 2: $analog = 'двух'; break;
            case 3: $analog = 'трех'; break;
            case 4: $analog = 'четырех'; break;
            case 5: $analog = 'пяти'; break;
            case 6: $analog = 'шести'; break;
        }
        return $analog;
    }

    public static function s_word_analog(){

    }

}
