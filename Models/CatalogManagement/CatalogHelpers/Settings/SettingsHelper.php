<?php
/**
 * Заглушка
 *
 * @author Pochepochka
 */
namespace Models\CatalogManagement\CatalogHelpers\Settings;
use Models\CatalogManagement\CatalogHelpers\Item\ItemHelper;
use Models\CatalogManagement\Positions\Settings;

abstract class SettingsHelper extends ItemHelper{
    protected static $i = NULL;
    protected function __construct(){
        Settings::addDataProvider($this);
    }
}