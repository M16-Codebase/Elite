<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 05.10.15
 * Time: 11:16
 */

namespace Models\CatalogManagement\Properties;


class Video extends Property
{
    const TYPE_NAME = 'video';
    public function getCompleteValue($v, $segment_id = NULL){
        if ($this['set']){
            foreach ($v['value'] as $id => $str){
                $this->match($str, $regs);
                $numbers[$id] = !empty($regs[1]) ? $regs[1] : NULL;
            }
            return $numbers;
        }
        $this->match($v['value'], $regs);
        return !empty($regs[1]) ? $regs[1] : NULL;
	}
    /**
     * Проверка значения на соответствие типу данных
     * @param $val
     * @return boolean
     */
    public function isValueFormatCorrect($val){
        if (parent::isValueFormatCorrect($val)){
            return $this->match($val, $regs);
        } else {
            return FALSE;
        }
    }
    private function match($val, &$regs){
        return (preg_match('~^([a-z0-9\-_]+)$~i', $val, $regs)
            || preg_match('~youtube\.com/watch\?v=([a-z0-9\-_]+)~i', $val, $regs)
            || preg_match('~youtu\.be/([a-z0-9\-_]+)~i', $val, $regs)
        );
    }
}