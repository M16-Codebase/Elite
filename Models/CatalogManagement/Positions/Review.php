<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 06.07.15
 * Time: 15:49
 *
 * Отзыв или вопрос о товаре
 */

namespace Models\CatalogManagement\Positions;


use Models\CatalogManagement\Item;

class Review extends Item{
    /**
     * Свои хелперы
     * @var array
     */
    static protected $dataProviders = array();
    static protected $dataProvidersByFields = array();

}