<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 15.07.2017
 * Time: 21:21
 */

namespace Models\XmlLoad;

use App\Configs\MetroConfig;
use App\Configs\RealEstateConfig;
use App\Configs\ReviewConfig;
use Models\CatalogManagement\Catalog;
use Models\CatalogManagement\CatalogPosition;
use Models\CatalogManagement\Properties\CatalogPosition as CatalogPositionProp;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Rules\RuleAggregator;
use Models\CatalogManagement\Search\CatalogSearch;




use Models\CatalogManagement\Variant;
use Models\ContentManagement\Post AS PostEntity;
use Models\CatalogManagement\Properties;
use Models\FilesManagement\File;
use Models\Validator;
use LPS\Components\FS;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\CatalogManagement\Item AS ItemEntity;
use Models\CatalogManagement\Type AS TypeEntity;
use App\Configs\CatalogConfig;


class XmlLoadAvitoFormat extends XmlLoadFactory {

    const UPLOAD_URI = "http://realtyposter.ru/data/export/avito/54c63d4848fa58d659570c63.xml";

    public function loadData() {
        $rootNodeArray = $this->xml->getElementsByTagName('Ad');

        foreach ($rootNodeArray as $rootNode) {

            /**
             * @var $id
             * @todo Сейчас нет id в фиде, нужно поговорить о том, что нужно внедрить туди id и key, type(вторичка, загород)
             */
            $itemId = 3751;
            $key = 'test';
            $typeKey = 'resale';

            $type = TypeEntity::getByKey($typeKey);

            if (empty($itemId)){

                $catalog = $type->getCatalog();
                $entity_class = CatalogConfig::getEntityClass($catalog['key'], 'item');

                $itemId = $entity_class::create($type['id'], $entity_class::S_TMP, array());
            }

            $item = ItemEntity::getById($itemId);
            $item_properties = PropertyFactory::search($type['id'],
                PropertyFactory::P_NOT_VIEW|PropertyFactory::P_NOT_DEFAULT|PropertyFactory::P_NOT_RANGE|PropertyFactory::P_ITEMS,
                'key', '', 'parents', array());


            /*
             * остановился на том что надо теперь светси воедино параметры из xml
             * и свойства объекта из БД
             */



            dump($item_properties);
            exit;

            $descr = $rootNode->getElementsByTagName('Description')->item(0);


        }
    }

}