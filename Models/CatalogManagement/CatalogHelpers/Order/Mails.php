<?php
namespace Models\CatalogManagement\CatalogHelpers\Order;

use Models\CatalogManagement\Item;
use App\Configs\OrderConfig;
/**
 * В каких случаях отправлять письма, если мы захотим условия не зависящие от заказа, то надо придумать, как передать их сюда
 * @author olga
 */
class Mails extends OrderHelper{
    protected static $i = NULL;
    protected static $fieldsList = array('mail_sent');
    private $orderEmailSent = array();
    private $cacheData = array();
    public function get(Item $i, $field) {
        if (!in_array($field, static::$fieldsList)){
            return;
        }
        if (!array_key_exists($i['id'], $this->orderEmailSent)){
            return;
        }
        return $this->orderEmailSent[$i['id']];
    }
    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors) {
        if (array_key_exists(OrderConfig::KEY_ORDER_STATUS, $properties)
            || array_key_exists(OrderConfig::KEY_ORDER_PAY_SYSTEM_METHOD, $properties)
            || array_key_exists(OrderConfig::KEY_ORDER_PAYED, $properties)
        ){
            $this->cacheData[$updateKey] = array(
                'old_data' => $item['properties']
            );
        }
    }
    /**
     * Принцип такой: собираем шаблоны с данными и куда отправлять, а потом отправляем.
     * @param Item $item
     * @param type $segment_id
     * @param array $updated_properties
     * @return type
     */
    public function onUpdate($updateKey, Item $item, $segment_id, $updated_properties){
        if (empty($this->cacheData[$updateKey])){
            return;
        }
        $old_data = $this->cacheData[$updateKey]['old_data'];
        $mails = array();
        $user_email_sent = FALSE;
        $old_status = $old_data[OrderConfig::KEY_ORDER_STATUS]['value_key'];
        $new_status = $item['properties'][OrderConfig::KEY_ORDER_STATUS]['value_key'];
        $site_config = \App\Builder::getInstance()->getSiteConfig();
        $emails_config = $site_config->get(NULL, \App\Configs\CatalogConfig::CONFIG_NOTIFICATION_KEY);
        //данные в письмах одни и те же, пока не доказано обратно
        $admin_emails = !empty($emails_config[\App\Configs\Settings::KEY_ORDER_SEND]) ? $emails_config[\App\Configs\Settings::KEY_ORDER_SEND] : \LPS\Config::getParametr('email', 'to');
        $user_email = array($item[OrderConfig::KEY_ORDER_EMAIL] => !empty($item[OrderConfig::KEY_ORDER_NAME]) ? $item[OrderConfig::KEY_ORDER_NAME] : '');
        //т.к. статус поменялся, надо отправлять email
        if ($old_status != $new_status){
            $just_formed = $new_status == OrderConfig::STATUS_NEW && $old_status == OrderConfig::STATUS_TMP;
            $mails[] = array(
                'template' => 
                    $just_formed
                    ? 'mails/order_sent.tpl' 
                    : 'mails/status_change.tpl',
                'email_to' => $user_email
            );
            $user_email_sent = TRUE;
            //надо отправить письмо админу, если только что оформили
            if ($just_formed){
                $mails[] = array(
                    'template' => 'mails/order_send_admin.tpl',
                    'email_to' => $admin_emails
                );
            }
        }
        if (OrderConfig::getParameter(\App\Configs\Settings::KEY_ORDER_SEND_PAYMENT_LINK)){
            //при смене способа оплаты, надо отправить письмо об этом
            $old_pay_method = $old_data[OrderConfig::KEY_ORDER_PAY_SYSTEM_METHOD]['value'];
            $new_pay_method = $item['properties'][OrderConfig::KEY_ORDER_PAY_SYSTEM_METHOD]['value'];
            if ($old_pay_method != $new_pay_method){//если изменился способ оплаты, надо отправить письмо о том, как оплатить
                $mails[] = array(
                    'template' => 'mails/payment.tpl',
                    'email_to' => $user_email
                );
            }
            $user_email_sent = TRUE;
        }
        //при смене галки "оплачено" надо всем отправить уведомление
        $old_payed = $old_data[OrderConfig::KEY_ORDER_PAYED]['value'];
        $new_payed = $item['properties'][OrderConfig::KEY_ORDER_PAYED]['value'];
        if (empty($old_payed) && !empty($new_payed)){
            $mails[] = array(
                'template' => 'mails/payed.tpl',
                'email_to' => $user_email
            );
            $user_email_sent = TRUE;
            $mails[] = array(
                'template' => 'mails/payed_admin.tpl',
                'email_to' => $admin_emails
            );
        }
        if (!empty($mails)){
            $mail_ans = new \LPS\Container\WebContentContainer();
            foreach ($mails as $m){
                $mail_ans->setTemplate($m['template']);
                $mail_ans->add('order', $item);
                \Models\Email::send($mail_ans,  $m['email_to']);
            }
            $this->orderEmailSent[$item['id']] = $user_email_sent;
        }
        unset($this->cacheData[$updateKey]);
    }
}
