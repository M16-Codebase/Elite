<?php
namespace Models\Payments;

use Models\OrderManagement\Order;
/**
 * Модель работы с Pay2Pay (оплата заказов)
 *
 * @author olga
 * 
CREATE TABLE `payment_types` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`key` VARCHAR( 20 ) NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`commission_project` DOUBLE( 4, 2 ) NOT NULL COMMENT  'процент комиссии для продавца',
`commission_user` DOUBLE( 4, 2 ) NOT NULL COMMENT  'процент комиссии для пользователя',
`currency` VARCHAR( 5 ) NOT NULL COMMENT  'валюта',
`payment_min` INT UNSIGNED NOT NULL COMMENT  'минимальный платеж',
`payment_max` INT UNSIGNED NOT NULL COMMENT  'максимальный платеж',
`attributes` TEXT NOT NULL COMMENT  'дополнительные атрибуты',
`used` TINYINT( 1 ) UNSIGNED NOT NULL COMMENT  'используется ли на сайте'
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;
 */
class Pay2Pay implements iPayment{
	/**
	 * запись всех уведомлений об оплате
	 */
	const TABLE = 'payment_check';
    /* ************************* настройки оплаты **/
	/**
	 * Валюта по умолчанию
	 */
	const CURRENCY_CODE = 'RUB';
	/**
	 * Номер магазина в системе
	 */
	const MERCHANT_ID = '4332';
	/**
	 * Url, куда отправлять запрос на оплату
	 */
    const SERVICE_GET_URL = 'https://merchant.pay2pay.com/?page=init';
    /**
     * Url на другие api
     */
    const API_URL = 'https://merchant.pay2pay.com/output/?module=xml';
	/**
	 * если запрос на оплату происходит в тестовом режиме, то 
	 * это значение должно быть равно «1», иначе «0».
	 */
	const TEST_MODE = 0;
    /**
     * Таблица способов оплаты
     */
    const TABLE_PAY_TYPES = 'payment_types';
	/**
	 * типы оплаты разделены на группы
	 */
	const TABLE_PAY_GROUPS = 'payment_types_groups';
	/**
	 * Произвольный набор символов, используется для подписи сообщений при переадресации
	 */
    const SECRET_KEY = 'cnf9NMbvpx';
    /**
     * Произвольный набор символов, используется для подписи скрытых сообщений на Result URL
     */
    const HIDDEN_KEY = 'ZLPIvnKVDz';
    /**
     * Произвольный набор символов, используется в случае дальнейшего расширения функций модуля разработчиками проекта
     */
    const API_KEY = 'YH511mvUuS';
    /**
	 * (ru|en) Язык пользовательского интерфейса.
	 */
    const LANGUAGE = 'ru';
    /**
     * Версия api
     */
    const VERSION = '1.3';
    
    /* *********************** Коды ошибок/статусов */
    
    /**
     * заказ оплачен
     */
    const STATUS_OK = 'success';
    /**
     * оплата отменена
     */
    const STATUS_CANCEL = 'fail';
    /**
     * ожидается оплата
     */
    const STATUS_PROCESS = 'process';
    /**
     * код успешной оплаты от нас
     */
    const STATUS_YES = 'yes';
    /**
     * код при ошибках оплаты
     */
	const STATUS_NO = 'no';
	/* *********************** Обязательные параметры при составлении ссылки */
	
	/**
	 * Идентификатор магазина в системе
	 */
	const PAY_ID = 'merchant_id';
	/**
	 * Внутренний идентификатор заказа, однозначно определяющий заказ в магазине. 
	 */
	const PAY_ORDER_ID = 'order_id';
	/**
	 * валюта в которой указана стоимость заказа. Перечень допустимых значений можно получить через API интерфейс Currency.
	 */
	const PAY_CURRENCY_CODE = 'currency';
	/**
	 * стоимость заказа, которую пользователь видит на вашем сайте
	 */
	const PAY_AMOUNT = 'amount';
    
    const PAY_LANGUAGE = 'language';
    
    const PAY_SECURE_STRING = 'sign';
	
	/* *************************** Необязательные параметры при составлении ссылки ** */
	
	/**
	 * Необязательный параметр. Указание, что запрос 
	 * происходит в тестовом режиме.
	 */
	const PAY_TEST_MODE = 'test_mode';
	/**
	 * Описание оплаты
	 */
	const PAY_DESCRIPTION = 'description';
	/**
	 * (ru|en) Язык пользовательского интерфейса.
	 */
	const PAY_LOCALE = 'language';
	/**
	 * Предварительный выбор платежной системы. 
	 */
	const PAY_SYSTEM_ID = 'paymode';
	/**
	 * Номер операции в системе
	 */
	const PAY_OPERATION_ID = 'trans_id';
    /**
     * url на который будет перенаправлен плательщик при отказе от оплаты
     */
    const PAY_FAIL_URL = 'fail_url';
    /**
     * url на который будет перенаправлен плательщик после оплаты
     */
    const PAY_SUCCESS_URL = 'success_url';
    /**
     * url на который будет отправлено уведомление о состоянии платежа
     */
    const PAY_RESULT_URL = 'result_url';
    
    /* ********************* переменные класса\объекта */
	/**
	 * Id валют из системы
	 * @var type 
	 */
    private static $payMethods = array();

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
	 * Способ оплаты
	 * @var type 
	 */
    private $pay_type = NULL;
	/**
	 * id системы оплаты
	 * @var type 
	 */
	private $pay_system_code = NULL;
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
	 * @param \Models\OrderManagement\Order $order
	 * @return Pay2Pay
	 */
	public static function get(Order $order){
		if (empty(self::$registry[$order['id']])){
			self::$registry[$order['id']] = new self($order);
		}
		return self::$registry[$order['id']];
	}
	/**
	 * @param type $url
	 * @param type $status
	 * @param type $err
	 * @return null
	 */
    private static function getPage($url, &$status, &$err, $post_params = array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if (!empty($post_params)){
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $post_params);
        }
        $page = curl_exec($ch);

        $err = curl_error($ch);
        if (!empty($err))
          return NULL;

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $page;
    }
	/**
	 * Значение параметра цены должно быть с двумя десятичными знаками, отделенными точкой, например, «1.23» или «123.00». 
	 * @param type $price
	 */
	private static function getPriceFormated($price){
		return !empty($price) ? number_format($price, 2, '.', '') : '0.00';
	}
    /**
     * 
     * @param type $data POST параметр xml
     * @return type
     */
    public static function getXmlFromRequest($data){
        return base64_decode(str_replace(' ', '+', $data));
    }
    /**
	 * Отдает виды оплаты
	 * @param type $used используется ли на нашем сайте
	 * @param bool $usedSystem позволяет ли платежная система использовать метод оплаты
	 * @return type
	 */
    public static function getPayMethods($usedOnSite = FALSE, $usedInSystem = FALSE){
        if (empty(self::$payMethods)){
            self::$payMethods = \App\Builder::getInstance()->getDB()->query(''
				. 'SELECT `m`.*, `g`.`title` AS `group_title` FROM `'.self::TABLE_PAY_TYPES.'` AS `m` '
				. 'LEFT JOIN `'.self::TABLE_PAY_GROUPS.'` AS `g` ON (`m`.`group_id` = `g`.`id`)'
				. 'WHERE 1 ORDER BY `g`.`position`')->select('key');
        }
		if (empty($usedOnSite) && empty($usedInSystem)){
			return self::$payMethods;
		}
		$result = self::$payMethods;
		foreach (self::$payMethods as $key => $method){
			if (!empty($usedOnSite) && $method['used'] != 1){
				unset($result[$key]);
			}
			if (!empty($usedInSystem) && $method['system_used'] != 1){
				unset($result[$key]);
			}
		}
		return $result;
    }
    /**
     * Вызываем из крона каждый день, для просмотра дступных систем оплаты
     * @return type
     */
    public static function parseSystems(){
        $db = \App\Builder::getInstance()->getDB();
        //запрос
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<request>'
            . ' <type>Paymode</type>'
            . ' <version>1.0</version>'
            . ' <merchant_id>'.self::MERCHANT_ID.'</merchant_id>'
            . '</request>';
        $sign = md5(self::API_KEY.$xml.self::API_KEY);
        $post_params = "xml=".base64_encode($xml)."&sign=".base64_encode($sign);
        //ответ
        $xml_result = self::getPage(self::API_URL, $status, $err, $post_params);
        if (!empty($err)){
            return;
        }
        $xml_data = simplexml_load_string($xml_result);
        $xml_data_array = json_decode(json_encode($xml_data), TRUE);//ыыыы :)
        if ($xml_data_array['status'] == 1){
            //ошибка
            return;
        }
        $paySystems = self::getPayMethods();
        foreach ($xml_data_array['results']['paymode_list']['paymode'] as $res){//реальные данные из результата запроса
            /* @var $res \SimpleXMLElement */
            $params = array();
            $params['key'] = $res['code'];
            if (isset($paySystems[$params['key']])){
                $params = $paySystems[$params['key']];//записываем старые данные, чтобы потом переопределить, а те, которые не забираем из апи, чтобы не затирались
                unset($params['group_title']);//убираем кастомное поле
            }
            $params['name'] = $res['name'];
            $params['system_used'] = $res['state'];
            $modes = (string) $res['modes'];
            $params['mode'] = strpos($modes, 'redirect') !== FALSE ? 1 : 0;
            $params['currency'] = $res['currency'];
            $params['commission_project'] = $res['commission']['project'];
            $params['commission_user'] = $res['commission']['user'];
            $params['attributes'] = !empty($res['attribute_list']) ? json_encode($res['attribute_list']['attribute']) : NULL;
            $params['payment_min'] = $res['payment']['min'];
            $params['payment_max'] = $res['payment']['max'];            
            $db->query('REPLACE INTO `'.self::TABLE_PAY_TYPES.'` SET ?a', $params);
        }
    }
    
    private function __construct(Order $order){
        self::getPayMethods();//загрузили все платежные системы
        if (!array_key_exists($order['pay_type'], self::$payMethods)){
            throw new \LogicException('Pay system ' . $order['pay_type'] . ' not found');
        }        
        $this->pay_amount = self::getPriceFormated($order['total_cost']);
        $this->pay_for = $order['id'];
        $this->currency = self::CURRENCY_CODE;
        $this->pay_type = self::$payMethods[$order['pay_type']]['key'];
		$this->pay_system_code = self::$payMethods[$order['pay_type']]['key'];
//		$this->user_email = $order['email'];
		$this->order = $order;
    }
    /**
     * Создает ссылку на сервис для оплаты
     */
    public function getUrl(&$error = NULL, &$status = NULL){
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<request>'
            . ' <version>1.3</version>'
            . ' <merchant_id>'.self::MERCHANT_ID.'</merchant_id>'
            . ' <language>'.self::LANGUAGE.'</language>'
            . ' <order_id>'.$this->pay_for.'</order_id>'
            . ' <amount>'.$this->pay_amount.'</amount>'
            . ' <currency>'.self::CURRENCY_CODE.'</currency>'
            . ' <description>Заказ</description>'
            . ' <paymode>'
            . '     <code>'.self::$payMethods[$this->pay_type]['key'].'</code>'
//            . '     <mode>redirect</mode>'
//            . '     <attributes>...</attributes>'
            . ' </paymode>'
//            . ' <success_url><![CDATA[...]]></success_url>'
//            . ' <fail_url><![CDATA[...]]></fail_url>'
//            . ' <result_url><![CDATA[...]]></result_url>'
            . ' <test_mode>1</test_mode>'
//            . ' <item_list>'
//            . '     <item><name>Товар</name><qty>1.00</qty><price>10.50</price></item>...</item_list>'
//            . ' <other><![CDATA[...]]></other>'
            . '</request>';
        $sign = $this->getSecureStringToPay($xml);
        $get_params = "xml=".base64_encode($xml)."&sign=".base64_encode($sign);
        $str = self::SERVICE_GET_URL . '&' . $get_params;
        return $str;
    }
	/**
	 * Кодирование ключа для отправки на оплату
	 */
	private function getSecureStringToPay($xml){
		return md5(self::SECRET_KEY . $xml . self::SECRET_KEY);
	}
	/**
	 * Разбор данных запроса уведомления об оплате
	 * @param array $data
	 * @return $result
	 */
	public function payRequest($data){
        $xml = self::getXmlFromRequest($data['xml']);
		$signature = $this->getSecureStringFromCheck($xml);
        $data_xml_array = json_decode(json_encode(simplexml_load_string($xml)), TRUE);
		$description = '';
        if ($data_xml_array['order_id'] != $this->pay_for){
            $code = self::STATUS_NO;
			$description = 'Неверный id заказа';
        }elseif ($this->pay_amount < $data_xml_array['amount']){
            $code = self::STATUS_NO;
			$description = 'Неверная сумма заказа';
        }elseif (!in_array($this->order['status'], array(
			Order::STATUS_NEW, Order::STATUS_PAY_WAITING, Order::STATUS_PROCESSED
		))){
			$code = self::STATUS_NO;
			$description = 'Неверный статус заказа';
		}elseif($signature != $data[self::PAY_SECURE_STRING]){
			$code = self::STATUS_NO;
			$description = 'Сигнатура оплаты не совпадает';
        }elseif($data_xml_array['currency'] != self::CURRENCY_CODE){
            $code = self::STATUS_NO;
			$description = 'Неверная валюта';
		}else{
			$code = self::STATUS_YES;
			$this->order->setStatus(Order::STATUS_PAYED);
			$this->order->edit(array('payed' => 1));
		}
		\App\Builder::getInstance()->getDB()->query('INSERT INTO `'.self::TABLE.'` SET '
			. '`order_id` = ?d, '
			. '`operation_id` = ?s, '
			. '`date` = NOW(), '
			. '`error_inner` = ?s, '
			. '`error_outer` = ?s,'
			. '`status` = ?s',
			$data_xml_array['order_id'],
			$data_xml_array['trans_id'],
			!empty($description) ? $description : NULL,
			!empty($data_xml_array['error_msg']) ? $data_xml_array['error_msg'] : NULL,
			$data_xml_array['status']);
        return array(
			'code' => $code,
			'description' => $description
		);
	}
	/**
	 * Код проверки целостности данных для уведомления с проверкой
	 * MNT_SIGNATURE = MD5( 
	 * MNT_COMMAND + 
	   MNT_ID + MNT_TRANSACTION_ID + MNT_OPERATION_ID + 
	   MNT_AMOUNT + MNT_CURRENCY_CODE + MNT_SUBSCRIBER_ID + 
	   MNT_TEST_MODE + 
	   КОД ПРОВЕРКИ ЦЕЛОСТНОСТИ ДАННЫХ 
	   )
	 */
	private function getSecureStringFromCheck($xml){
		return base64_encode(md5(self::HIDDEN_KEY.$xml.self::HIDDEN_KEY));
	}
	public function asArray(){
		return array(
			'price' => $this->pay_amount,
			'order_id' => $this->pay_for,
			'currency' => $this->currency,
			'pay_type' => $this->pay_type,
			'pay_system_code' => $this->pay_system_code,
//			'user_email' => $this->user_email,
			'bill_id' => self::MERCHANT_ID
		);
	}
}
?>