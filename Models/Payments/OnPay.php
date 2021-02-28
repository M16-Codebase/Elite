<?php
namespace Models\Payments;

use Models\CatalogManagement\Positions\Order;
/**
 * Description of OnPay
 *
 * @author olga
 */
class OnPay implements iPayment{
    const TABLE_METHODS = 'payment_types';
    const SECRET_KEY = 'WWpHeVaikAf';
    const SERVICE_GET_URL = 'https://secure.onpay.ru/pay/make_payment_link';
    const REQUEST_TYPE_CHECK = 'check';
    const REQUEST_TYPE_PAY = 'pay';
    const USER_LOGIN = 'apexsport';
    const PRICE_FINAL = true;//комиссию платежной системы взымать с продавца (true или false)
    const CONVERT = 1;//Принудительная конвертация платежей в валюту ценника. (1(да) или 2(нет))
    const NOTIFY_BY_API = true;
    const TICKER = 'RUR';//TST для тестовых денег, RUR для настоящих
    const PAY_MODE = 'fix';//free (любая сумма) fix (фиксированная сумма, к зачислению)
    const SEPARATOR_GET_URL = ':';
    const SEPARATOR_CHECK = ';';
    private static $systems = array( 
        'BOC', 
        'BTR',
        'OCE',
        'BVC',
        'DMR', 
        'EVS', 
        'EUS',
        'HBK',
        'MG1', 
        'MT1',
        'OSP', 
        'POT', 
        'PPL',
        'WMR', 
        'YDX',
        'TST'
    );
    private static $registry = array();
    /**
     * ОК – означает, что “уведомление о платеже принято” если тип запроса был “pay” или “может быть принято” если тип запроса был “check”
     */
    const STATUS_OK = 0;
    /**
     * Только для запросов типа “check” Платёж отклонён. В этом случае OnPay не примет платёж от Клиента.
     */
    const STATUS_CANCEL = 2;
    /**
     * Ошибка в параметрах. OnPay не будет пытаться повторно послать это уведомление 
     * в API мерчанта и отметит этот платёж статусом “уведомление не доставлено в API” если тип запроса “pay”. 
     * Если тип запроса “check” – OnPay не примет этот платеж.
     */
    const STATUS_ERROR_PARAMS = 3;
    /**
     * Ошибка авторизации. MD5 подпись неверна.
     */
    const STATUS_ERROR_MD5 = 7;
    /**
     * Временная ошибка. OnPay попробует повторно послать это уведомление несколько раз в течение следующих 72 часов 
     * после чего пометит платёж статусом “уведомление не доставлено в API”
     */
    const STATUS_ERROR_TMP = 10;
    
    private $pay_amount = NULL;
    private $pay_mode = NULL;
    private $pay_for = NULL;
    private $ticker = NULL;
    private $currency = NULL;
    private $user_login = NULL;
    private $user_email = NULL;
    private $price_final = NULL;
    private $pay_type = NULL;
    private $notify_by_api = NULL;
    private static function getPage($url, &$status, &$err){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $page = curl_exec($ch);
        $err = curl_error($ch);
        if (!empty($err))
          return NULL;

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $page;
    }
    public static function getPayMethods(){
        return self::$systems;
    }
    public static function get(Order $order){
        if (empty(self::$registry[$order['id']])){
			self::$registry[$order['id']] = new self($order);
		}
		return self::$registry[$order['id']];
    }
    
    public function __construct(Order $order){
        if (!in_array($order['pay_type'], self::$systems)){
            throw new \LogicException('Pay system ' . $order['pay_type'] . ' not found');
        }
        $total_cost = !empty($order['total_cost']) ? number_format($order['total_cost'], 2, '.', '') : '0.00';
        if (substr($total_cost, -1) == 0){
            $total_cost = substr($total_cost, 0, strlen($total_cost)-1);
        }
        $this->pay_amount = $total_cost;
        $this->pay_mode = self::PAY_MODE;
        $this->pay_for = $order['id'];
        $this->currency = self::TICKER;
        $this->one_way = $order['pay_type'];
        $this->user_login = self::USER_LOGIN;
        $this->user_email = $order['email'];
        $this->price_final = self::PRICE_FINAL;
        $this->pay_type = self::CONVERT;
        $this->notify_by_api = self::NOTIFY_BY_API;
    }
    public function getData($field){
        if (isset($this->$field)){
            return $this->$field;
        }else{
            throw new \LogicException('FIeld ' . $field . ' not exists');
        }
    }
    /**
     * Создает ссылку на сервис для оплаты
     */
    public function getUrl(){
        $str = '';
        $str .= 'pay_amount=' . $this->pay_amount;
        $str .= '&pay_for=' . $this->pay_for;
        $str .= '&currency=' . $this->currency;
        $str .= '&one_way=' . $this->one_way;
        $str .= '&user_login=' . $this->user_login;
        $str .= '&user_email=' . $this->user_email;
        $str .= '&price_final=' . $this->price_final;
        $str .= '&pay_type=' . $this->pay_type;
        $str .= '&notify_by_api=' . $this->notify_by_api;
        $str .= '&md5=' . $this->getMd5ForUrlToOnpay();
        $str = self::SERVICE_GET_URL . '?' . $str;
        $result = self::getPage($str, $status, $error);
        if (empty($error) && $status == 200){
            return $result;
        }else{
            return NULL;
        }
    }
    /**
     * type;pay_for;order_amount;order_currency;secret_key_for_api_in
     * @param type $type
     * @return string
     */
    public function getMd5ForCheckFromOnpay(&$str){
        $str = self::REQUEST_TYPE_CHECK . self::SEPARATOR_CHECK . $this->pay_for . self::SEPARATOR_CHECK . $this->pay_amount . self::SEPARATOR_CHECK . $this->currency . self::SEPARATOR_CHECK . self::SECRET_KEY;
        return strtoupper(md5($str));
    }
    /**
     * type;pay_for;onpay_id;order_amount;order_currency;secret_key_for_api_in
     * @param type $type
     * @param type $onpay_id
     * @return string
     */
    public function getMd5ForPayFromOnpay($onpay_id, &$str, $payed){
        $str = self::REQUEST_TYPE_PAY . self::SEPARATOR_CHECK . $this->pay_for . self::SEPARATOR_CHECK . $onpay_id . self::SEPARATOR_CHECK . $payed . self::SEPARATOR_CHECK . $this->currency . self::SEPARATOR_CHECK . self::SECRET_KEY;
        return strtoupper(md5($str));
    }
    /**
     * type;pay_for;order_amount;order_currency;code;secret_key_api_in
     * @param type $type
     * @param type $code
     * @return string
     */
    public function getMd5ForCheckToOnpay($code){
        $str = self::REQUEST_TYPE_CHECK . self::SEPARATOR_CHECK . $this->pay_for . self::SEPARATOR_CHECK . $this->pay_amount . self::SEPARATOR_CHECK . $this->currency . self::SEPARATOR_CHECK . $code . self::SEPARATOR_CHECK . self::SECRET_KEY;
        return strtoupper(md5($str));
    }
    /**
     * type;pay_for;onpay_id;order_id;order_amount;order_currency;code;secret_key_api_in
     * @param type $type
     * @param type $onpay_id
     * @param type $code
     * @return string
     */
    public function getMd5ForPayToOnpay($onpay_id, $code, $payed){
        $str = self::REQUEST_TYPE_PAY . self::SEPARATOR_CHECK . $this->pay_for . self::SEPARATOR_CHECK . $onpay_id . self::SEPARATOR_CHECK . self::SEPARATOR_CHECK . $payed . self::SEPARATOR_CHECK . $this->currency . self::SEPARATOR_CHECK . $code . self::SEPARATOR_CHECK . self::SECRET_KEY;
        return strtoupper(md5($str));
    }
    /**
     * pay_amount, pay_for, ticker, user_login, price_final, pay_type, notify_by_api, api_in_key, разделенных двоеточием (:) - после конкатенации через двоеточие строку перед вычислением MD5 надо перевести в верхний регистр.
     */
    private function getMd5ForUrlToOnpay(){
        $str = $this->pay_amount . self::SEPARATOR_GET_URL . $this->pay_for . self::SEPARATOR_GET_URL . $this->currency . self::SEPARATOR_GET_URL . $this->user_login . self::SEPARATOR_GET_URL . $this->price_final . self::SEPARATOR_GET_URL . $this->pay_type . self::SEPARATOR_GET_URL . $this->notify_by_api . self::SEPARATOR_GET_URL . self::SECRET_KEY;
        return md5(strtoupper($str));
    }
}

?>
