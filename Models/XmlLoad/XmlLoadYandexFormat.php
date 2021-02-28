<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 15.07.2017
 * Time: 21:20
 */

namespace Models\XmlLoad;

use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Item AS ItemEntity;
use Models\CatalogManagement\Type AS TypeEntity;
use Models\ContentManagement\Post AS PostEntity;
use Models\ImageManagement\Collection;
use Models\XmlLoad\LoadXMLParser;
use App\Configs\CatalogConfig;


class XmlLoadYandexFormat extends XmlLoadFactory {

    const UPLOAD_URI = 'http://realtyposter.ru/data/export/yandex/5710c25e48fa58bd2c8b4567.xml';
    const SEGMENT_ID = 1;
    const SECONG_SEGMENT_ID = 2;
    const TYPE_SELLING = 'продажа';
    const TYPE_RENT = 'аренда';
    const CATEGORY_FLAT = 'квартира';
    const CATEGORY_HOUSE = 'дом';
    const CATEGORY_COMMERCE = 'коммерческая';
    const COLLECTION_TYPE = 'PropertyValue';

    const STATUS = ItemEntity::S_HIDE;

    private $category_flat = ['Квартира', 'квартира'];
    private $category_house = ['Дом', 'дом'];
    private $category_commerce = ['Коммерческая', 'коммерческая'];

    private $conf;

    //private $errors = [];

    public function loadData() {
        $this->conf = $this->config();

        $begin_time = time() - 1272000000 + floatval(microtime());

        $reader = new LoadXMLParser;
        $reader->open(self::UPLOAD_URI);
        //передаем себя в парсер, он оттуда вызывает метод loadItem
        $reader->parse($this);
        $reader->close();

        $time = time() - 1272000000 + floatval(microtime()) - $begin_time;
        $mem = $this->memSize(memory_get_usage());

        $mail_ans = new \LPS\Container\WebContentContainer();
		$mail_ans->setTemplate('mails/xmlLoad.tpl');
		$mail_ans->add('memory', $mem)->add('time', $time);
	//	\Models\Email::send($mail_ans, array('pahuss@mail.ru' => ''));

        //$this->log($this->errors);
        return true;

    }

    function memSize($size) {
        $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) .$filesizename[$i] : '0 Bytes';
    }


    public function loadItem($objectId, $offer) {
        //dump($objectId);
        //if ($objectId != 1108522) {
        //    return;
        //}
        $offer = $this->xml2array($offer);

        // не элитный - нах
        if ( !isset($offer['is-elite']) || $offer['is-elite'] == 0) {
            return false;
        }
        // если новострой, то пропускаем
        $new_flat = 0;
        if (isset($offer['new-flat'])) {
            if ($offer['new-flat'] == 1) {
                return false;
            }
            $new_flat = $offer['new-flat'];
        }
        if (!isset($offer['image'])) {
            //$this->errors[] = "Отсутствует category в ID {$offer[self::ELITE_OBJ_ID_ELEM]}";
            return false;
        }
        if (!isset($offer['category'])) {
            //$this->errors[] = "Отсутствует category в ID {$offer[self::ELITE_OBJ_ID_ELEM]}";
            return false;
        }
        // коммерческую пропускаем, аренду тоже
        if (in_array($offer['category'], $this->category_commerce) || $offer['type'] == self::TYPE_RENT) return false;

        /* если такого объекта в базе нет, то добавляем */
        if (!in_array($objectId, $this->real_items)) {

            $typeKey = null; //по умолчанию null
            if (in_array($offer['category'], $this->category_house)) {
                /**
                 * пока что для загородки пропускаем
                 * */
                return;
                // $typeKey = $this->conf['category'][$this->category_house[1]];
            } elseif (in_array($offer['category'], $this->category_flat)) {
                $typeKey = $this->conf['category'][$this->category_flat[1]][$new_flat];
            }

            // если такой тип на сайте не предусмотрен, то пропускаем
            if (is_null($typeKey))return;
            // на случай если этот момент не был обработан выше
            // если этот новый объект - новостройка, то пропускаем

            if ($typeKey == self::FLAT_TYPE) return;

            // $typeId возвращается массивом вида array( 'typeId' => (int)value )
            $typeIdArr = TypeEntity::getIds(['key' => $typeKey]);

            foreach ($typeIdArr as $tid) {
                $typeId = $tid;
            }
            $errors = [];
            $idd = ItemEntity::create($typeId, self::STATUS, [], $errors, self::SEGMENT_ID,
                null, $objectId);


        }

        //continue;
        $item = ItemEntity::getById($objectId, self::SEGMENT_ID);

        $item->update(array('status' => self::STATUS));


        $type = $item->getType();
        $item_properties = PropertyFactory::search($type['id'],
            PropertyFactory::P_NOT_VIEW | PropertyFactory::P_NOT_DEFAULT | PropertyFactory::P_NOT_RANGE | PropertyFactory::P_ITEMS,
            'key', 'group', 'parents', array(), self::SEGMENT_ID);


        $valsFromXml = $this->convertXmlObjectToArray($offer);
        $itemProperties = $item['properties'];


        //сделать title

        if ($type['id'] == 17 ) {
            $title = $valsFromXml['address'];
            $valsFromXml['title'] = $valsFromXml['address'];
        }else if ($type['id'] == 31 ) {
            $title = $valsFromXml['locality-name'];
            $valsFromXml['title'] = $valsFromXml['locality-name'];
        }

        // title обновляем сразу потому что ниже он не обновляется

        if ($item['title'] != $title) {
            $item->updateValueByKey('title', $title);
        }


        /**
         * Тут мы перебираем массив со свойствами
         * объекта из xml файла и обновляем объект
         * @todo для статьи в xml файле нужно поле с заголовком к статье
         * @todo так же имя консультанта жеоательно вводить в составном формате
         * с полями отдельно имя, отдельно фамилия, отдельно отчество.
         */
        foreach ($valsFromXml as $key => $value) {
            if ($value == null || $value == '') {
                continue;
            }
            $cur_val = $value;

            //exit;
            $resultArray = [];
            //if ($key != 'description') continue;
            if (isset($item_properties[$key])) {

                $property = $item_properties[$key];

                if ($property instanceof CatalogPositionProp ||
                    $property instanceof \Models\CatalogManagement\Properties\Region ||
                    $property instanceof \Models\CatalogManagement\Properties\Metro ||
                    $property instanceof \Models\CatalogManagement\Properties\Post ||
                    $property instanceof \Models\CatalogManagement\Properties\Item
                ) {
                    $entities_list = $property->getEntitiesList(self::SEGMENT_ID, false);
                }

                if ($key == 'price') {
                    $cur_val = $this->getValidPrice($cur_val);
                }

                if ($key == 'infra_text') {
                    $cur_val = trim($cur_val);
                }

                //house_type
                if ($key == 'house_type') {
                    foreach ($property['values'] as $p_value) {
                        if (mb_strtolower($value) == mb_strtolower($p_value['value'])) {
                            $cur_val = $p_value['id'];
                        }
                    }
                }

                //wc_number
                if ($key == 'wc_number') {
                    $cur_val = null;
                    if ($value == 'совмещенный') {
                        $value = 1;
                    }
                    $value = (int)$value;
                    if (!empty($value)) {
                        $cur_val = $value;
                    }
                }

                //repair
                if ($key == 'repair') {
                    $cur_val = '';
                    foreach ($property['values'] as $p_value) {
                        if (mb_strtolower($value) == mb_strtolower($p_value['value'])) {
                            $cur_val = $p_value['id'];
                        }
                    }
                }

                if ($key == 'district' || $key == 'complex_district') {
                    $cur_val = 0;
                    foreach ($entities_list as $entity) {
                        $sp = stripos(mb_strtolower($entity['title']), mb_strtolower($value));
                        if ($sp !== false) {
                            $cur_val = $entity['id'];
                        }
                    }
                }

                if ($key == 'metro' || $key == 'complex_metro') {
                    $cur_val = 0;
                    foreach ($entities_list as $entity) {
                        $metro = $entity->getSegmentProperties(self::SEGMENT_ID);
                        if (trim(mb_strtolower($metro['variant_title']['value'])) == trim(mb_strtolower($value))) {
                            $cur_val = $metro['variant_title']['val_id'];
                        }
                    }
                }

                if ($key == 'typerk') {
                    // достать все значения для этого свойства
                    // подобрать нужное нам
                    // записать
                    $getTypesRk = "SELECT `id`, `value` FROM `enum_properties` WHERE `property_id`=?d";
                    $tps = $this->db->query($getTypesRk, $itemProperties['typerk']['id']);
                    while ($row = $tps->getRow()) {
                        if (trim(mb_strtolower($valsFromXml['typerk'])) == trim(mb_strtolower($row['value']))) {
                            $cur_val = $row['id'];
                            //break;
                        }
                    }
                }

                if ($key == 'description') {
                    $post = PostEntity::getById($itemProperties['description']['value'], self::SEGMENT_ID);
                    //dump($post);
                    if (!empty($post)) {
                        $post->edit(array(
                            'title' => $valsFromXml['description']['title'],
                            'text' => $valsFromXml['description']['text']),
                            $error);
                        continue;
                    } else {

                        $postIdSegment1 = PostEntity::create('property_value');
                        $post = PostEntity::getById($postIdSegment1, self::SEGMENT_ID);
                        $post->edit([
                            'title' => $valsFromXml['description']['title'],
                            'text' => $valsFromXml['description']['text']],
                            $errors);

                        $item->updateValueByKey($key, $postIdSegment1, $errors, self::SEGMENT_ID);
                        //dump($postIdSegment1);

                        $postIdSegment2 = PostEntity::create('property_value');
                        $item->updateValueByKey($key, $postIdSegment2, $errors, self::SECONG_SEGMENT_ID);
                        //dump($ur, $post['id']);
                    }
                    continue;
                }

                //consultant
                //dump($objectId);
                if ($key == 'consultant') {
                    // достать всех возможных
                    // подобрать нужного, распарсив строку
                    // для парсинга использовать код ниже

                    $name_atts = explode(' ', $cur_val);
                    $maybe_cons = array();
                    $cons = null;
                    foreach ($entities_list as $entity) {
                        $search_success = false;

                        if (in_array($entity['surname'], $name_atts)) {
                            // если совпала фамилия, то вписываем в список возможных
                            // консультантов id этого консу
                            $maybe_cons[] = $entity['id'];
                            // и ищем по имени
                            if (in_array($entity['name'], $name_atts)) {
                                // если совпало и имя, то заканчиваем поиск и считаем, что мы нашли
                                $cons = $entity['id'];
                                break;
                            }
                        }
                    }
                    // если в списке возможных консультантов
                    if (!is_null($cons)) {
                        $cur_val = $cons;
                    } elseif (!empty($maybe_cons)) {
                        // если совпадали только фамилии,
                        // то берем первого попавшегося и на парим голову,
                        // потому что передавать нам нормально они не желают ничего
                        $cur_val = $maybe_cons[0];
                    }
                    // если не нашлось подходящего значения среди консультантов
                    // то не сохраняем ничего, а пропускаем итерацию.
                    // может потом изменим принцип, по идее это должно проверяться
                    if (is_null($cur_val)) {
                        continue;
                    }
                }
                /*
                $resultArray[$key] = [
                    'val_id' => $itemProperties[$key]['val_id'],
                    'value' => !empty($cur_val) ? $cur_val : '',
                    'options' => '',
                ];*/
                // обновляем значение и очищаем переменную
                //
                $item->updateValueByKey($key, $cur_val);
                $cur_val = null;

            }
        }

        /**
         * Далее мы смотрим, если у объекта есть изображения, то
         * нужно создать для него галлерею и залить туда фото
         * 1. Create gallery
         * 2. Upload images for object
         */

        if (isset($offer['image']) && is_null($item['gallery'])) {
            $property = PropertyFactory::getById($itemProperties['gallery']['id'], self::SEGMENT_ID);
            $gallery_id = Collection::create(self::COLLECTION_TYPE);
            $item->updateValue($property['id'], $gallery_id, $errors);
                // почему-то посторно добавляются одни и те же фотки при повторнм прогоне
                // этого быть не должно поэтому пока что если галерея у объекта есть мы ее
                // уже не заполняем
            $collection = Collection::getById($gallery_id);
            $images = (array)$offer['image'];
            $img_counter = 0;
            foreach ($images as $image_url) {
                $image_url = trim($image_url);
                $image = $collection->addImageByUrl($image_url, $errors);
                if ($img_counter == 0) {
                    $collection->setCover($image['id']);
                }
                $img_counter ++;
            }
        }

        return true;
    }



    public function loadDataBySimpleXml () {
        $this->conf = $this->config();

        foreach ($this->xml->offer as $offer) {
            //$this->loadItem($objectId, $offer);
        }
        $this->log($this->errors);
    }


    protected function convertXmlObjectToArray($offer) {

        return [
            'typerk' => isset($offer['category']) ? $offer['category'] : null,
            'price' => isset($offer['price']['value']) ? $offer['price']['value'] : null,
            'description' => array(
                'title' => isset($offer['description_title']) ? $offer['description_title'] : null,
                'text' => isset($offer['description']) ? $offer['description'] : null,
                'annotation' => '',
            ),

                isset($offer['description']) ? $offer['description'] : null,
            'images' => isset($offer['image']) ? $offer['image'] : null,

            'area_all' => isset($offer['area']['value']) ? $offer['area']['value'] : null,
            //$area = isset($offer['lot-area']['value']) ? $offer['lot-area']['value'] : null, // пл участка
            'ceiling_height' => isset($offer['ceiling-height']) ? $offer['ceiling-height'] : null,
            'area_living' => isset($offer['living-space']['value']) ? $offer['living-space']['value'] : null,
            'area_kitchen' => isset($offer['kitchen-space']['value']) ? $offer['kitchen-space']['value'] : null,
            'bed_number' => isset($offer['rooms']) ? intval($offer['rooms']) : null,
            /*floors во вторичке - это количество уровней*/
            //'floors' => isset($offer['floors-total']) ? intval($offer['floors-total']) : null,
            'floor_number' => isset($offer['floor']) ? intval($offer['floor']) : null,
            'floor_floor' => isset($offer['floor']) ? intval($offer['floor']) : null,
            'floor_floor_number' => isset($offer['floor']) ? intval($offer['floor']) : null,

            'number_storeys' => isset($offer['floors-total']) ? intval($offer['floors-total']) : null,
            'house_type' => isset($offer['building-type']) ? $offer['building-type'] : null,
            'repair' => isset($offer['renovation']) ? $offer['renovation'] : null,
            'wc_number' => isset($offer['bathroom-unit']) ? $offer['bathroom-unit'] : null,


            'country' => isset($offer['location']['country']) ? $offer['location']['country'] : null,

            'region' => isset($offer['location']['region']) ? $offer['location']['region'] : null,
            //'district' = isset($offer['location']['district']) ? $offer['location']['district'] : null,
            'locality-name' => isset($offer['location']['locality-name']) ? $offer['location']['locality-name'] : null,
            'district' => isset($offer['location']['district']) ? $offer['location']['district'] : null,
            'complex_district' => isset($offer['location']['sub-locality-name']) ? $offer['location']['sub-locality-name'] : null,
            'address' => isset($offer['location']['address']) ? $offer['location']['address'] : null,

            'center_distance' => isset($offer['location']['distance']) ? $offer['location']['distance'] : null,
            'metro' => isset($offer['location']['metro']['name']) ? $offer['location']['metro']['name'] : null,
            'complex_metro' => isset($offer['location']['metro']['name']) ? $offer['location']['metro']['name'] : null,
            'metro_drive_time' => isset($offer['location']['metro']['time-on-transport']) ?
                $offer['location']['metro']['time-on-transport'] : null, //до метро на транспорте
            'metro_walk_time' => isset($offer['location']['metro']['time-on-foot']) ?
                $offer['location']['metro']['time-on-foot'] : null, //до метро на пешком
            'address_coords' => (isset($offer['location']['latitude']) && isset($offer['location']['longitude']))
                ? ($offer['location']['latitude'] . ',' . $offer['location']['longitude']) : null,

            'consultant' => isset($offer['sales-agent']['name']) ? $offer['sales-agent']['name'] : null,
            'complex_consultant' => isset($offer['sales-agent']['name']) ? $offer['sales-agent']['name'] : null,
            'object_title' => isset($offer['building-name']) ? $offer['building-name'] : null,
            'complex_title' => isset($offer['building-name']) ? $offer['building-name'] : null,
            'typerk' => $offer['category'],

            'infra_text' => isset($offer['infrastructure']) ? $offer['infrastructure'] : null,
            //'icon' => isset($offer['new-flat']) ? $offer['new-flat'] : null,
        ];
    }

    private function config() {
        return [
            'type' => [

            ],
            'category' => [
                self::CATEGORY_HOUSE => 'residential',
                self::CATEGORY_FLAT => [
                    'resale', 'flat'
                ]
            ]
        ];
    }


    public function getSyncData () {

        //$file = 'http://dev.m16-elite.ru/objects.txt';
        $uri = 'http://realtyposter.ru/data/export/yandex/54c63c9848fa589959570c63.xml';

        $xml = simplexml_load_file($uri);
        $sale = 'продажа';
        $commerc = 'коммерческая';
        $flat = 'квартира';
        $house = 'дом';



        $resale_type_id = 17;
        $residential_type_id = 31;

        $db = \App\Builder::getInstance()->getDB();

        $fd = fopen("sync_result.html", 'w') or die("не удалось создать файл");

        $stl = '<style>
            table td {
                padding: 10px;
                font-family: monospace;
                text-align: center;
            }
            </style>';
        $fh = "<!DOCTYPE html><html><head><title>Elite Sync Data</title><meta charset='utf-8'>$stl<head>" . PHP_EOL;
        $fh .= "<body><table>" . PHP_EOL;
        $fh .= "<tr><th>ID в яндексе</th>";
        $fh .= "<th>ID на сайте</th>";
        $fh .= "<th>Адрес из файла | Адрес из БД<br><small>для наглядного сравнения</small></th>";
        $fh .= "<th>Тип</th>";
        $fh .= "<th>Площадь</th>";
        $fh .= "<th>Цена</th>" ;
        $fh .= "<th>Этаж</th>" . PHP_EOL;
        fwrite($fd, $fh);


        foreach ($xml->offer as $offer) {

            $offer = $this->xml2array($offer);
            $internal_id = $offer['@attributes']["internal-id"];
            if (mb_strtolower($offer['type']) != $sale) continue;

            $category = mb_strtolower($offer['category']);


            $address = '-';

            if (isset($offer['location']['address'])) {
                $address = $offer['location']['address'];
            }
            //dump($offer['location']['address']);
            if ($category == $flat) {

                $price_prop_id = 175;
                $area_prop_id = 177;
                $floor_prop_id = 190;
                $address_prop_id = 152;

                $min_price = $this->getMinPrice($price_prop_id, $db);
                if ( ($price = floatval($offer['price']['value'] / 1000000)) < $min_price ) continue;

                $area = floatval($offer['area']['value']);
                $floor = intval($offer['floor']);

                // делаем два запроса, потом вычисляем пересечение результатов
                $result_by_price = $db->query(
                    "SELECT `items`.id FROM `items`
                    LEFT JOIN `items_properties_float` ipf ON  ipf.`item_id`=items.id
                    LEFT JOIN `items_properties_int` ipi ON  ipi.`item_id`=items.id

                    WHERE (ipf.`property_id` = ?d AND ipf.value = ?f)
                    AND (ipi.`property_id` = ?d AND ipi.value = ?d)
                    AND `items`.`type_id` = ?d
                    GROUP BY `items`.id",
                    $price_prop_id,
                    $price,
                    $floor_prop_id,
                    $floor,
                    $resale_type_id
                )->getRow();

                $result_by_area = $db->query(
                    "SELECT `items`.id FROM `items`
                    LEFT JOIN `items_properties_float` ipf ON  ipf.`item_id`=items.id
                    LEFT JOIN `items_properties_int` ipi ON  ipi.`item_id`=items.id

                    WHERE (ipf.`property_id` = ?d AND ipf.value=?f)
                    AND (ipi.`property_id` = ?d AND ipi.value = ?d)
                    AND `items`.`type_id` = ?d
                    GROUP BY `items`.id",
                    $area_prop_id,
                    $area,
                    $floor_prop_id,
                    $floor,
                    $resale_type_id
                )->getRow();


                if (empty($result_by_price) || empty($result_by_area)) { continue; }
                $result = array_intersect($result_by_price, $result_by_area);
                if (empty($result)) continue;
                $id = $result['id'];

                $addr = $db->query("SELECT `value` FROM `items_properties_string`
                  WHERE `item_id` = ?d AND `property_id` = ?d", $id, $address_prop_id)->getRow();
                $addr = $addr['value'];

                //dump($addr);
                $line = "<tr><td>$internal_id</td>";
                $line .= "<td>$id</td>";
                $line .= "<td>$address | $addr</td>";
                $line .= "<td>$category</td>";
                $line .= "<td>$area</td>";
                $line .= "<td>$price</td>";
                $line .= "<td>$floor</td>". PHP_EOL;

                fwrite($fd, $line);
            } elseif ($category == $house) {
                $area = floatval($offer['area']['value']);
                $price_prop_id = 300;
                $area_prop_id = 332;
                $address_prop_id = 311;

                $min_price = $this->getMinPrice($price_prop_id, $db);
                if ( ($price = floatval($offer['price']['value'] / 1000000)) < $min_price ) continue;

                $result_by_price = $db->query(
                    "SELECT `items`.id FROM `items`
                    LEFT JOIN `items_properties_float` ipf ON  ipf.`item_id`=items.id
                    WHERE (ipf.`property_id` = ?d AND ipf.value = ?d)
                    AND `items`.`type_id` = ?d
                    GROUP BY `items`.id",
                    $price_prop_id,
                    $price,
                    $residential_type_id
                )->getRow();

                $result_by_area = $db->query(
                    "SELECT `items`.id FROM `items`
                    LEFT JOIN `items_properties_float` ipf ON  ipf.`item_id`=items.id
                    WHERE (ipf.`property_id` = ?d AND ipf.value = ?d)
                    AND `items`.`type_id` = ?d
                    GROUP BY `items`.id",
                    $area_prop_id,
                    $area,
                    $residential_type_id
                )->getRow();


                if (empty($result_by_price) || empty($result_by_area)) { continue; }
                $result = array_intersect($result_by_price, $result_by_area);
                if (empty($result)) continue;
                $id = $result['id'];


                $addr = $db->query("SELECT `value` FROM `items_properties_string`
                  WHERE `item_id` = ?d AND `property_id` = ?d", $id, $address_prop_id)->getRow();
                $addr = $addr['value'];

                $line = "<tr><td>$internal_id</td>";
                $line .= "<td>$id</td>";
                $line .= "<td>$address | $addr</td>";
                $line .= "<td>$category</td>";
                $line .= "<td>$area</td>";
                $line .= "<td>$price</td>";
                $line .= "<td></td></tr>" . PHP_EOL;
                fwrite($fd, $line);
            } else {
                continue;
            }
        }
        $line = '</table></html>';
        fwrite($fd, $line);
        fclose($fd);

        include 'sync_result.html';
    }

    private function getMinPrice($propId, & $db) {
        $price = $db->query("SELECT MIN(`value`) as 'value' FROM `items_properties_float` WHERE `property_id` = ?d", $propId)->getRow();
        return $price['value'];
    }

}


function dump($var, $info = false) {
        $bt = debug_backtrace();

        echo '<br />';
        echo "========= file : {$bt[0]['file']}, line: {$bt[0]['line']} ==========";
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
        if ($info) {
            foreach ($bt as $b) {
                echo '<small>file : ' .$b['file'].', line: '. $b['line'].'</small>';
                echo '<br />';
            }
        } else {
            if (isset($bt[1]['file'])) {
                echo 'file : ' .$bt[1]['file'].', line: '. $bt[1]['line'];
            }
        }

        echo '<br />'; echo '==================='; echo '<br />';
    }
