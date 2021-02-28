<?php
/**
 * Класс, отвечающий за рассылку писем подписчикам
 *
 * @author olga
 */
namespace Models;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Type;
class Subscribe {
    const CRON_TABLE = 'cron_mail_task';
    const TABLE = 'subscribe';
    
    const SUBSCRIBE_VARIANT_AVAILABLE = 'variant_available';
    const SUBSCRIBE_VARIANT_PRICE = 'variant_price';
    const SUBSCRIBE_VARIANT_COUNT = 'variant_count';
    
    public static function send(){
        self::sendPrices();
        self::sendVariantAvailable();
        self::sendVariantCountAvailable();
    }
    private static function sendPrices(){
        $db = \App\Builder::getInstance()->getDB();
        $task = $db->query('SELECT * FROM `'.self::CRON_TABLE.'` WHERE `mail_type` = ?s AND `status` = "new"', self::SUBSCRIBE_VARIANT_PRICE)->select('entity_id');
        if (empty($task)){
            return;
        }
        $users = $db->query('SELECT * FROM `'.self::TABLE.'` WHERE `entity_id` IN (?i) AND `mail_type` = ?s', array_keys($task), self::SUBSCRIBE_VARIANT_PRICE)->select('entity_id', 'email');
        $rules = \App\CatalogMethods::getVisibleRules();
        $rules['id'] = Rule::make('variant.id')->setValue(array_keys($task));
        $variants = CatalogSearch::factory()->setRules($rules)->searchVariants();
        $errors = array();
        if (!empty($variants)){
            foreach ($task as $variant_id => $data){
                if (!empty($variants[$variant_id]) && !empty($users[$variant_id])){
                    $mail_ans = new \LPS\Container\WebContentContainer('/mails/priceChange.tpl');
                    $mail_ans->add('variant', $variants[$variant_id])
                            ->add('site_config', \App\Builder::getInstance()->getSiteConfig());
                    $mail_list = array();
                    $variant = $variants[$variant_id];
                    $price_change_status = unserialize($data['data']);
                    foreach ($users[$variant_id] as $us){
                        if (empty($us['data']) || !empty($price_change_status[$us['data']]) && $price_change_status[$us['data']]){
                            $mail_ans->add('unsubscribe', \LPS\Config::DOMAIN_NAME . '/welcome/unsubscribe/?key=' . md5($us['email'] . "variant_available" . \LPS\Config::HASH_SOLT_STRING))
                                    ->add('user_info', $us);
                            Email::send($mail_ans, array($us['email'] => !empty($us['name']) ? $us['name'] : ''), null, null, array(), false, $errors);//, false, true);
                            $mail_list[] = $us['email'];
                        }
                    }
                    if (!empty($errors)){
                        $db->query('UPDATE `'.self::CRON_TABLE.'` SET `status` = "complete", `errors` = ?s WHERE `mail_type` = "variant_available" AND `status` = "new" AND `entity_id` = ?d', $task['entity_id'], serialize($errors));
                    }else{
                        $db->query('DELETE FROM `'.self::CRON_TABLE.'` WHERE `mail_type` = ?s AND `status` = "new" AND `entity_id` = ?d', self::SUBSCRIBE_VARIANT_PRICE, $variant_id);
                        if (!empty($mail_list)){
                            $db->query('DELETE FROM `'.self::TABLE.'` WHERE `mail_type` = ?s AND `entity_id` = ?d AND `email` IN (?l)', self::SUBSCRIBE_VARIANT_PRICE, $variant_id, $mail_list);
                        }
                    }
                }
            }
        }
    }
    private static function sendVariantAvailable(){
        $db = \App\Builder::getInstance()->getDB();
        $task = $db->query('SELECT * FROM `'.self::CRON_TABLE.'` WHERE `mail_type` = "variant_available" AND `status` = "new"')->select('entity_id');
        if (empty($task)){
            return;
        }
        $users = $db->query('SELECT * FROM `'.self::TABLE.'` WHERE `entity_id` IN (?i) AND `mail_type` = ?s', array_keys($task), self::SUBSCRIBE_VARIANT_AVAILABLE)->select('entity_id', 'email');
        $rules = \App\CatalogMethods::getVisibleRules();
        $rules['id'] = Rule::make('variant.id')->setValue(array_keys($task));
        $variants = CatalogSearch::factory()->setRules($rules)->searchVariants();
        $errors = array();
        if (!empty($variants)){
            foreach ($task as $variant_id => $data){
                if (!empty($variants[$variant_id]) && !empty($users[$variant_id])){
                    $mail_ans = new \LPS\Container\WebContentContainer('/mails/availableChange.tpl');
                    $mail_ans->add('variant', $variants[$variant_id])
                            ->add('site_config', \App\Builder::getInstance()->getSiteConfig());
                    foreach ($users[$variant_id] as $us){
                        $mail_ans->add('unsubscribe', \LPS\Config::DOMAIN_NAME . '/welcome/unsubscribe/?key=' . md5($us['email'] . "variant_available" . \LPS\Config::HASH_SOLT_STRING))
                                ->add('user_info', $us);
                        Email::send($mail_ans, array($us['email'] => !empty($us['name']) ? $us['name'] : ''), null, null, array(), false, $errors);//, false, true);
                    }
                    if (!empty($errors)){
                        $db->query('UPDATE `'.self::CRON_TABLE.'` SET `status` = "complete", `errors` = ?s WHERE `mail_type` = "variant_available" AND `status` = "new" AND `entity_id` = ?d', $task['entity_id'], serialize($errors));
                    }else{
                        $db->query('DELETE FROM `'.self::CRON_TABLE.'` WHERE `mail_type` = ?s AND `status` = "new" AND `entity_id` = ?d', self::SUBSCRIBE_VARIANT_AVAILABLE, $variant_id);
                        $db->query('DELETE FROM `'.self::TABLE.'` WHERE `mail_type` = ?s AND `entity_id` = ?d', self::SUBSCRIBE_VARIANT_AVAILABLE, $variant_id);
                    }
                }
            }
        }
    }
    private static function sendVariantCountAvailable(){
        $db = \App\Builder::getInstance()->getDB();
        $task = $db->query('SELECT * FROM `'.self::CRON_TABLE.'` WHERE `mail_type` = ?s AND `status` = "new"', self::SUBSCRIBE_VARIANT_COUNT)->select('entity_id');
        if (empty($task)){
            return;
        }
        $users = $db->query('SELECT * FROM `'.self::TABLE.'` WHERE `entity_id` IN (?i) AND `mail_type` = ?s', array_keys($task), self::SUBSCRIBE_VARIANT_COUNT)->select('entity_id', 'email');
        $rules = \App\CatalogMethods::getVisibleRules();
        $rules['id'] = Rule::make('variant.id')->setValue(array_keys($task));
        $variants = CatalogSearch::factory()->setRules($rules)->searchVariants();
        $errors = array();
        if (!empty($variants)){
            foreach ($task as $variant_id => $data){
                if (!empty($variants[$variant_id]) && !empty($users[$variant_id])){
                    $mail_ans = new \LPS\Container\WebContentContainer('/mails/availableChange.tpl');
                    $mail_ans->add('variant', $variants[$variant_id])
                            ->add('site_config', \App\Builder::getInstance()->getSiteConfig());
                    $mail_list = array();
                    $variant = $variants[$variant_id];
                    $avail_count = max(array($variant[CatalogConfig::KEY_VARIANT_COUNT], $variant[CatalogConfig::KEY_VARIANT_COUNT_WAIT]));
                    foreach ($users[$variant_id] as $us){
                        if ($us['data'] <= $avail_count){
                            $mail_ans->add('unsubscribe', \LPS\Config::DOMAIN_NAME . '/welcome/unsubscribe/?key=' . md5($us['email'] . "variant_available" . \LPS\Config::HASH_SOLT_STRING))
                                    ->add('user_info', $us);
                            Email::send($mail_ans, array($us['email'] => !empty($us['name']) ? $us['name'] : ''), null, null, array(), false, $errors);//, false, true);
                            $mail_list[] = $us['email'];
                        }
                    }
                    if (!empty($errors)){
                        $db->query('UPDATE `'.self::CRON_TABLE.'` SET `status` = "complete", `errors` = ?s WHERE `mail_type` = "variant_available" AND `status` = "new" AND `entity_id` = ?d', $task['entity_id'], serialize($errors));
                    }else{
                        $db->query('DELETE FROM `'.self::CRON_TABLE.'` WHERE `mail_type` = ?s AND `status` = "new" AND `entity_id` = ?d', self::SUBSCRIBE_VARIANT_COUNT, $variant_id);
                        if (!empty($mail_list)){
                            $db->query('DELETE FROM `'.self::TABLE.'` WHERE `mail_type` = ?s AND `entity_id` = ?d AND `email` IN (?l)', self::SUBSCRIBE_VARIANT_COUNT, $variant_id, $mail_list);
                        }
                    }
                }
            }
        }
    }
    public static function addTask($mail_type, $entity_id, $data = array(), $segment_id = NULL){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('REPLACE INTO `'.self::CRON_TABLE.'` SET `mail_type` = ?s, `entity_id` = ?d, `status` = "new", `segment_id` = ?d, `data` = ?s', $mail_type, $entity_id, $segment_id, serialize($data));
    }
    
    public static function addUser($email, $mail_type, $data = array(), $segment_id = NULL){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('REPLACE INTO `'.self::TABLE.'` SET `email` = ?s, `mail_type` = ?s, `segment_id` = ?s, `hash` = ?s{, ?a}', $email, $mail_type, $segment_id, md5($email . $mail_type . \LPS\Config::HASH_SOLT_STRING), !empty($data) ? $data : $db->skipIt());
        self::onUserChange();
    }
    
    public static function removeUser($email, $mail_type){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('DELETE FROM `'.self::TABLE.'` WHERE `email` = ?s AND `mail_type` = ?s', $email, $mail_type);
        self::onUserChange();
    }
    
    private static function onUserChange(){
//        $db = \App\Builder::getInstance()->getDB();
//        $db->query('');
    }
}

?>