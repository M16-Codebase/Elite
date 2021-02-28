<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 22.07.2017
 * Time: 21:32
 */

namespace Modules\XmlLoad;

use Models\ImageManagement\Collection;


class Img extends \LPS\AdminModule
{
    public function index() {

        $url = "http://realtyposter.ru/data/images/58e409b213647cb1148b4572/bada14f6a5ddbc17957e8f46e9c8eff9.jpg";

        $file = file_get_contents($url);

        $collection = Collection::factory();
        $err = [];
        $collection = Collection::getById(3655);

        $image = $collection->addImage($file, '', $err);
        dump($image);

        exit;
    }

}