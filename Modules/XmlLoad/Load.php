<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 15.07.2017
 * Time: 10:41
 */

namespace Modules\XmlLoad;

use Models\CronTasks\XmlLoad;
use Models\CronTasks\SiteMap;
use Models\ImageManagement\Collection;

class Load extends \LPS\AdminModule {

    public function index() {


        //$load = new XmlLoad();
        //$load->start();

        XmlLoad::createAndStart();

        exit();



        $url = "http://realtyposter.ru/data/images/58e409b213647cb1148b4572/bada14f6a5ddbc17957e8f46e9c8eff9.jpg";

        $file = new \Symfony\Component\HttpFoundation\File\UploadedFile($url, '58e409b213647cb1148b4572/bada14f6a5ddbc17957e8f46e9c8eff9.jpg');

        //$file = file_get_contents($url);

        $collection = Collection::factory();
        $err = [];
        $collection = Collection::getById(3655);

        $image = $collection->addImage($file, '', $err);
        dump($image);


    }

    public function sync() {
        $load = new XmlLoad();
        $load->sync();
        exit();
    }

}