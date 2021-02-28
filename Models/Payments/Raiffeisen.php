<?php
namespace Models\Payments;

use Models\CatalogManagement\Positions\Order;
/**
 * Description of Raiffeisen
 *
 * @author olga
 */
class Raiffeisen{
    /**
	 * запись всех уведомлений об оплате
	 */
	const TABLE = 'payment_check';
    /* ************************* настройки оплаты **/
	/**
	 * Валюта по умолчанию
	 */
	const CURRENCY_CODE = 643;
    /**
     * Код страны продавца ISO (обычно 643)
     */
    const COUNTRY_CODE = 643;
    const MERCHANT_CITY = 'MOSCOW';
    
    const MERCHANT_NAME = 'Apex-sport';
	/**
	 * Номер магазина в системе
	 */
	const MERCHANT_ID = '1680293001';
    const TERMINAL_ID = '80293001';
    const LOGIN = 'adm_29317WK';
    const PASS = 'RUR4OBVFHE';
    //for test
//    const MERCHANT_NAME = 'apex';
//	const MERCHANT_ID = '1684002001';
//    const TERMINAL_ID = '84002001';
//    const LOGIN = 'adm_002B2FE';
//    const PASS = '19NXS0T8TT';
	/**
	 * Url, куда отправлять запрос на оплату
	 */
    const SERVICE_GET_URL = 'https://e-commerce.raiffeisen.ru/vsmc3ds/pay_new/3dsproxy_init.jsp';
    const SERVICE_GET_KEY = 'https://e-commerce.raiffeisen.ru/portal/mrchtrnvw/trn_xml.jsp';
    //for test
//    const SERVICE_GET_URL = 'https://e-commerce.raiffeisen.ru/vsmc3ds_test/pay_new/3dsproxy_init.jsp';
//    const SERVICE_GET_KEY = 'https://e-commerce.raiffeisen.ru/portal_test/mrchtrnvw/trn_xml.jsp';

    const PAY_SUCCESS_URL = '/order/pay_success/';
    const PAY_FAIL_URL = '/order/pay_fail_url/';
    
    const STATUS_NO = '-2';//всё плохо
    const STATUS_YES = '0';//всё ок
    const STATUS_REPEATED = '1';//повторенное сообщение
    const STATUS_MAKE_REPEAT = '-1';//попросить повторить
    /* ********************* переменные класса\объекта */
	/**
	 * Id валют из системы
	 * @var type 
	 */
    private static $payMethods = array('card');

    /**
	 * Количество денег
	 * @var type 
	 */
    private $pay_amount = NULL;
	/**
	 * ID заказа
	 * @var type 
	 */
    private $pay_for = NULL;
	/**
	 * Валюта
	 * @var type 
	 */
    private $currency = NULL;
	/**
	 * Идентификатор пользователя
	 * @var type 
	 */
    private $user_email = NULL;
	/**
	 *
	 * @var Order
	 */
	private $order = NULL;
	/**
	 *
	 * @var Pay2Pay[]
	 */
	private static $registry = array();
	/**
	 * 
	 * @param \Models\OrderManagment\Order $order
	 * @return Pay2Pay
	 */
	public static function get(Order $order){
		if (empty(self::$registry[$order['id']])){
			self::$registry[$order['id']] = new self($order);
		}
		return self::$registry[$order['id']];
	}
    public static function getPaymentMethods(){
        return self::$payMethods;
    }
    public static function getSecretKey(){
        static $secret_key = NULL;//переменная метода, чтобы нельзя было вытащить её не проинициализировав
        if (empty($secret_key)){
            $site_config = \App\Builder::getInstance()->getSiteConfig();
            $saved_secret_key = $site_config->get('raiffeisen_secret_key');
            if (empty($saved_secret_key)){
                $post_params = array(
                    'xICBSXPProxy.ReqType' => '100',
                    'xICBSXPProxy.Version' => '05.00',
                    'xICBSXPProxy.UserName' => self::LOGIN,
                    'xICBSXPProxy.UserPassword' => self::PASS,
                    'MerchantID' => self::MERCHANT_ID
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, self::SERVICE_GET_KEY);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSLVERSION, 3);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_HEADER, array('Content-Type: application/x-www-form-urlencoded'));
                if (!empty($post_params)){
                    $post_string = http_build_query($post_params);
                    $post_string = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $post_string);
                    curl_setopt($ch,CURLOPT_POST, TRUE);
                    curl_setopt($ch,CURLOPT_POSTFIELDS, $post_string);
                }
                $result = curl_exec($ch);
                $data = new \SimpleXMLElement(preg_replace('~^[^<]*~', '', $result));
                $secret_key = strval($data->Message->Parameter[5]->Value);
                $err = curl_error($ch);
                if (!empty($err))
                  return NULL;

//                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $site_config->set('raiffeisen_secret_key', $secret_key, 'Секретный ключ для обмена информацией с райффайзен-банком');
            }else{
                $secret_key = $saved_secret_key;
            }
        }
        return $secret_key;
    }
//    public static function getConstants(){
//        return array(
//            'MerchantID' => self::MERCHANT_ID,
//            'TerminalID' => self::TERMINAL_ID
//        );
//    }
	/**
	 * Значение параметра цены должно быть с двумя десятичными знаками, отделенными точкой, например, «1.23» или «123.00». 
	 * @param type $price
	 */
	private static function getPriceFormated($price){
		return !empty($price) ? number_format($price, 2, '.', '') : '0.00';
	}
    
    private function __construct(Order $order){
        if (!in_array($order['pay_type'], self::$payMethods)){
            throw new \LogicException('Pay system ' . $order['pay_type'] . ' not found');
        }
        $this->pay_amount = self::getPriceFormated($order['total_cost']);
        $this->pay_for = $order['id'];
        $this->currency = self::CURRENCY_CODE;
		$this->user_email = $order['email'];
		$this->order = $order;
    }
    public function getPaymentData(){
        $security_string = self::MERCHANT_ID . ';' . self::TERMINAL_ID . ';' . $this->pay_for . ';' . $this->pay_amount;
        return array(
            'PurchaseAmt' => $this->pay_amount,//сколько платить
            'PurchaseDesc' => $this->pay_for,//за что платить
            'CountryCode' => self::COUNTRY_CODE, //в какой стране платить
            'CurrencyCode' => self::CURRENCY_CODE,//чем платить
            'MerchantName' => self::MERCHANT_NAME,//Имя магазина (не более 40 символов)
            'MerchantURL' => 'http://' . \LPS\Config::getParametr('site', 'url'),
            'MerchantCity' => self::MERCHANT_CITY,
            'MerchantID' => '00000' . self::MERCHANT_ID . '-' . self::TERMINAL_ID,
            'SuccessURL' => 'http://' . \LPS\Config::getParametr('site', 'url') . '/',
            'FailURL' => \Modules\Order\Main::getOrderViewLink($this->order),
            'HMAC' => $this->getSecureString($security_string),
            'pay_url' => self::SERVICE_GET_URL
        );
    }
	/**
	 * Кодирование ключа для отправки на оплату и проверки уведомлений
	 */
	private function getSecureString($string){
        return base64_encode(hash_hmac('sha256', $string, self::getHex2Bin(strtoupper(bin2hex(base64_decode(self::getSecretKey())))), TRUE));
	}
    private static function getHex2Bin($str){
        if ( !function_exists( 'hex2bin' ) ) {
            $sbin = "";
            $len = strlen( $str );
            for ( $i = 0; $i < $len; $i += 2 ) {
                $sbin .= pack( "H*", substr( $str, $i, 2 ) );
            }
            return $sbin;
        }else{
            return hex2bin($str);
        }
    }
	/**
	 * Разбор данных запроса уведомления об оплате
	 * @param array $data
	 * @return $result
	 */
	public function payRequest($data, $log_id){
        $required_fields = array(
            'result',
            'descr',
            'amt',
            'hmac',
            'id',
            'comment'
        );
        $description = '';
        $code = NULL;
        $not_exists_fields = array();
        foreach ($required_fields as $f){
            if (!array_key_exists($f, $data)){
                $not_exists_fields[] = $f;
            }
        }
		$signature = $this->getSecureString($this->pay_for . $this->pay_amount . $data['result']);
        if (!empty($not_exists_fields)){
            $code = self::STATUS_NO;
            $description = 'Отсутствуют обязательные поля ' . implode(', ', $not_exists_fields);
        }elseif ($data['descr'] != $this->pay_for){
            $code = self::STATUS_NO;
			$description = 'Неверный id заказа';
        }elseif ($this->pay_amount != str_replace(',', '.', $data['amt'])){
            $code = self::STATUS_NO;
			$description = 'Неверная сумма заказа';
        }elseif($this->order['payed'] == 1){
            $code = self::STATUS_REPEATED;
            $description = 'Заказ уже оплачен';
		}elseif (!in_array($this->order['status'], array(
			Order::STATUS_NEW, Order::STATUS_PAY_WAITING, Order::STATUS_PROCESSED
		))){
			$code = self::STATUS_NO;
			$description = 'Неверный статус заказа';
        }elseif (in_array($this->order['status'], array(Order::STATUS_TMP, Order::STATUS_COMPLETE, Order::STATUS_REMOVED))){
            $code = self::STATUS_NO;
            $description = 'Заказ отменен';
        }elseif($signature != $data['hmac']){
			$code = self::STATUS_NO;
			$description = 'Сигнатура оплаты не совпадает';
        }elseif ($data['result'] == 0){
			$code = self::STATUS_YES;
			$this->order->setStatus(Order::STATUS_PAYED);
			$this->order->edit(array('payed' => 1, 'to_export' => 1));
        }else{//остались только случаи, когда $data['result'] != 0, т.е. платеж не прошел
            $code = self::STATUS_YES;//у нас ответ - принял
            $description = 'Платеж не прошел';
        }
		\App\Builder::getInstance()->getDB()->query('UPDATE `'.self::TABLE.'` SET '
			. '`order_id` = ?d, '
			. '`operation_id` = ?s, '
			. '`error_inner` = ?s, '
			. '`error_outer` = ?s,'
			. '`status` = ?s'
            . 'WHERE `id` = ?d',
			$data['descr'],
			$data['id'],
			!empty($description) ? $description : NULL,
			!empty($data['comment']) ? $data['comment'] : NULL,
			$data['result'],
            $log_id);
        return array(
			'code' => $code,
			'description' => $description
		);
	}
}
?>