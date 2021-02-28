<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 18.09.15
 * Time: 17:13
 */

namespace Models\CatalogManagement\Properties;


class DiapasonDate extends Diapason
{
    const TYPE_NAME = 'diapasonDate';
    const ADDITIONAL_PROPS_TYPE = Date::TYPE_NAME;
}