<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 21.08.15
 * Time: 14:14
 */

namespace App\Configs\Init;


class SegmentInit
{
    public static function getInitData(){
        return array(
            array(
                'key' => 'ru',
                'title' => 'Русский'
            ),
            array(
                'key' => 'en',
                'title' => 'English'
            )
        );
    }
}