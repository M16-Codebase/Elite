<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 23.09.2017
 * Time: 11:32
 */

namespace Models\XmlLoad;



class LoadXMLParser extends SimpleXMLReader
{
    const INNER_ID_PROP = 'inner-id';

    public function __construct()
    {
        $this->registerCallback("offer", array($this, "callbackOffer"));
        $this->registerCallback("#comment", array($this, "callbackComment"), \XMLReader::COMMENT);
    }
    protected function callbackOffer($reader)
    {
        $this->offer = $reader->expandSimpleXml();
        return true;
    }
    protected function callbackComment($comment)
    {
        $comment = $comment->value;

        if (strpos($comment, self::INNER_ID_PROP) !== false) {
            $comment = explode(':', $comment);
            $this->innerId = intval($comment[1]);
        }

        return true;
    }
}