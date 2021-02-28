<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 15.07.2017
 * Time: 21:21
 */

namespace Models\XmlLoad;

use LPS\Config;
use Symfony\Component\Config\Util\XmlUtils;
use Models\XmlLoad\LoadXMLParser;


abstract class XmlLoadFactory {

    const XML_LOAD_FORMAT_YANDEX = 'Yandex';
    const XML_LOAD_FORMAT_AVITO = 'Avito';

    const FLAT_TYPE = 'flat';
    const LOG_FILE_NAME = 'LoadLog.txt';
    const LOG_FILE_PREFIX = 'loadlog';
    const ELITE_OBJ_ID_ELEM = 'elite-object-id';
    const ITEM_TABLE = '`items`';

    protected $xml;
    protected $time;
    protected $file;
    protected $errors;
    protected $db;
    protected $real_items = array();


    protected function __construct() {
        $now = new \DateTime();
        $this->time = $now->format('Y-m-d H:i:s');
        //$this->xml = $this->getXmlByParser();
        $this->db = \App\Builder::getInstance()->getDB();
        $this->real_items = $this->checkItems();
    }

    protected function __detruct() {
        $this->log($this->errors);
    }

    /*
     * вынимает из БД id`s айтемов, для последующей
     * проверки на существование добавляемого пункта
     * @return array()
     */
    protected function checkItems() {
        $query = "SELECT it.id FROM `items` it
            LEFT JOIN `item_types` itt on it.type_id = itt.id
            WHERE itt.key = 'resale' OR itt.key = 'residential' 
            ";
        $result = $this->db->query($query);
        while ($row = $result->getRow('id')) {
            $res[] = $row['id'];
        }
        return $res;
    }

    protected function log($messages) {
        $now = new \DateTime();
        $filename = self::LOG_FILE_PREFIX . $now->format('dmY') . '.txt';
        $filename = self::LOG_FILE_NAME;
        $this->file = fopen($filename, 'w+');
        $message = '';
        if (!is_array($messages)) {
            $message = (string)$messages . PHP_EOL;
        } else {
            foreach ($messages as $mes) {
                $message .= (string)$mes . PHP_EOL;
            }
        }
        fwrite($this->file, $this->time . PHP_EOL);
        fwrite($this->file, $message . PHP_EOL);
        fclose($this->file);
    }


    /**
     * Фабрика для XmlLoader`а
     * @return XmlLoadAvitoFormat|XmlLoadYandexFormat
     */
    public static function getLoader() {

        $format = Config::XML_LOAD_FORMAT;

        if ($format == self::XML_LOAD_FORMAT_AVITO) {
            return new XmlLoadAvitoFormat();
        } elseif ($format == self::XML_LOAD_FORMAT_YANDEX) {
            return new XmlLoadYandexFormat();
        }
    }

    abstract protected function loadData();
    abstract public function loadItem($objectId, $offer);

    public function getXml() {
        //return XmlUtils::loadFile(static::UPLOAD_URI);
        return simplexml_load_file(static::UPLOAD_URI);
    }

    //new SimpleXmlIterator($fname, null, true);
    public function getXmlAsIterator() {
        return new \SimpleXmlIterator(static::UPLOAD_URI, null, true);
    }

    protected function getXmlByParser() {
        $parser = new LoadXMLParser();
        $xml = $parser->parse(static::UPLOAD_URI);
        return $xml;
    }


    /**
     * function xml2array
     *
     * This function is part of the PHP manual.
     *
     * The PHP manual text and comments are covered by the Creative Commons
     * Attribution 3.0 License, copyright (c) the PHP Documentation Group
     *
     * @author  k dot antczak at livedata dot pl
     * @date    2011-04-22 06:08 UTC
     * @link    http://www.php.net/manual/en/ref.simplexml.php#103617
     * @license http://www.php.net/license/index.php#doc-lic
     * @license http://creativecommons.org/licenses/by/3.0/
     * @license CC-BY-3.0 <http://spdx.org/licenses/CC-BY-3.0>
     */
    protected function xml2array ( $xmlObject, $out = array () ) {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;

        return $out;
    }

    protected function getValidPrice($price) {
        $res = $price / 1000000;
        if ( $res > 1 ) {
            return floatval($res);
        }
        return $price;
    }


}