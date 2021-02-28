<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 20.11.17
 * Time: 1:00
 */

namespace Models\Entities;


class EntityFactory
{
    public static function getEntity($entityName)
    {
        if (class_exists(__NAMESPACE__ . '\\' .  $entityName)) {
            $className = __NAMESPACE__ . '\\' .  $entityName;
            return new $className();
        }
        return null;
    }
}