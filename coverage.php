<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 22.08.2017
 * Time: 23:25
 */

//mysql://dev-elite:1H0e9P8j@localhost/dev-elite
ini_set('display_errors', 1);
$dbh = new PDO('mysql:host=localhost;dbname=elite', 'sql-estate', 'L9o8T2j5');

if (!$dbh) {
    var_dump($dbh);
}

$query = "SELECT * FROM `image_collection` WHERE `type`='PropertyValue' AND `cover` =0 AND `data`='N;'";

$res = $dbh->query($query)->fetchAll();
echo '<pre>';
var_dump($res);
echo '</pre>';
/*

foreach ($res as $col) {
    $query = "SELECT * FROM `items_properties_int` WHERE `value` = {$col['id']}";
    $isset = $dbh->query($query)->fetchAll();

    if (!count($isset)) continue;

    $query = "SELECT MAX(id) AS id FROM `images` WHERE `collection_id` = {$col['id']}";
    $cover = $dbh->query($query)->fetchColumn();
    $cover = intval($cover);

    $upd = "UPDATE `image_collection` SET `cover`={$cover} WHERE `id`={$col['id']}";

    $resUpd = $dbh->query($upd);
    if (!$resUpd) {
        dump($resUpd);
    }

}*/