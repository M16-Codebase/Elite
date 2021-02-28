<?php
namespace Models\Payments;

use Models\CatalogManagement\Positions\Order;
use App\Configs\OrderConfig;
/**
 * Модель работы с Moneta.Assistant (оплата заказов)
 *
 * @author olga
 */
class MonetaAssistant implements iPayment{
	/**
	 * секурный ключ
	 */
    const SECRET_KEY = 'IqIPSSQ6K3Xw';
	
	/* Обязательные параметры при составлении ссылки */
	
	/**
	 * Идентификатор магазина в системе MONETA.RU.
	 * Соответствует номеру расширенного счета магазина. 
	 */
	const PAY_ID = 'MNT_ID';
	/**
	 * Внутренний идентификатор заказа, однозначно определяющий заказ в магазине. 
	 * Ограничение на размер – 255 символов.
	 */
	const PAY_ORDER_ID = 'MNT_TRANSACTION_ID';
	/**
	 * ISO код валюты, в которой производится оплата заказа в магазине. 
	 * Значение должно соответствовать коду валюты счета получателя (MNT_ID).
	 */
	const PAY_CURRENCY_CODE = 'MNT_CURRENCY_CODE';
	/**
	 * Сумма оплаты. Десятичные символы отделяются 
	 * точкой. Количество знаков после запятой - максимум 
	 * два символа. Значение суммы носит рекомендательный 
	 * характер и технически может быть изменено 
	 * пользователем. Необязательный параметр, если указан 
	 * «Check URL» в настройках счета. Если параметр не 
	 * задан, то сумма будет запрошена в учетной системе 
	 * магазина соответствующим проверочным запросом.
	 */
	const PAY_AMOUNT = 'MNT_AMOUNT';
	
	/* ** Необязательные параметры при составлении ссылки ** */
	
	/**
	 * Необязательный параметр. Указание, что запрос 
	 * происходит в тестовом режиме. Если параметр 
	 * «MNT_TEST_MODE» равен 1, то реального списания и 
	 * зачисления средств не произойдет. Запросы также 
	 * будут происходить в тестовом режиме, если выставить 
	 * флаг «Тестовый режим» в настройках счета.
	 */
	const PAY_TEST_MODE = 'MNT_TEST_MODE';
	/**
	 * Описание оплаты
	 */
	const PAY_DESCRIPTION = 'MNT_DESCRIPTION';
	/**
	 * Внутренний идентификатор пользователя, однозначно 
	 * определяющий получателя в учетной системе 
	 * магазина.
	 */
	const PAY_USER_ID = 'MNT_SUBSCRIBER_ID';
	/**
	 * Код для идентификации отправителя и проверки 
	 * целостности данных. Если в запросе есть данный 
	 * параметр, то MONETA.RU сгенерирует собственный код 
	 * на основе параметров запроса и сравнит его с данным 
	 * параметром. Если параметр «MNT_SIGNATURE» и код 
	 * сгенерированный MONETA.RU не совпадут, то 
	 * MONETA.Assistant завершится с ошибкой. Является 
	 * обязательным, если в настройках счета выставлен 
	 * флаг «Подпись формы оплаты обязательна».
	 */
	const PAY_SECURE_STRING = 'MNT_SIGNATURE';
	/**
	 * Поля произвольных параметров. Будут возращены 
	 * магазину в параметрах отчета о проведенной оплате.
	 */
	const PAY_PARAM1 = 'MNT_CUSTOM1';
	/**
	 * Поля произвольных параметров. Будут возращены 
	 * магазину в параметрах отчета о проведенной оплате.
	 */
	const PAY_PARAM2 = 'MNT_CUSTOM2';
	/**
	 * Поля произвольных параметров. Будут возращены 
	 * магазину в параметрах отчета о проведенной оплате.
	 */
	const PAY_PARAM3 = 'MNT_CUSTOM3';
	/**
	 * Необязательный параметр. URL страницы магазина, 
	 * куда должен попасть покупатель после успешно 
	 * выполненных действий. Переход пользователя 
	 * произойдет независимо от получения магазином 
	 * средств и отчета о проведенной оплате. Этот параметр 
	 * используется только тогда, когда в настройках счета 
	 * выставлен флаг: «Можно переопределять настройки в 
	 * URL». Если данный флаг не выставлен или параметр 
	 * «MNT_SUCCESS_URL» не задан, то используются 
	 * данные из «Success URL» в настройках счета. Если не 
	 * передать параметр «MNT_SUCCESS_URL» и не 
	 * выставить поле «Success URL», но указать флаг 
	 * «Можно переопределять настройки в URL», то по 
	 * умолчанию в качестве success URL будет 
	 * использоваться ссылка: 
	 * https://www.moneta.ru/paymentSuccess.htm.
	 */
	const PAY_SUCCESS_URL = 'MNT_SUCCESS_URL';
	/**
	 * Необязательный параметр. URL страницы магазина, 
	 * куда должен попасть покупатель после успешного 
	 * запроса на авторизацию средств, до подтверждения 
	 * списания и зачисления средств. Поддерживается 
	 * ограниченным количеством методов оплаты. Переход 
	 * пользователя произойдет независимо от получения 
	 * магазином средств и отчета о проведенной оплате. 
	 * Этот параметр используется только тогда, когда в 
	 * настройках счета выставлен флаг: «Можно 
	 * переопределять настройки в URL». Если данный флаг 
	 * не выставлен или параметр «MNT_INPROGRESS_URL» 
	 * не задан, то используются данные из «InProgress URL» 
	 * в настройках счета.
	 */
	const PAY_INPROGRESS_URL = 'MNT_INPROGRESS_URL';
	/**
	 * Необязательный параметр. URL страницы магазина, 
	 * куда должен попасть покупатель после отмененной 
	 * или неуспешной оплаты. Отчет об оплате в этом 
	 * случае магазину не отсылается. Этот параметр 
	 * используется только тогда, когда в настройках счета 
	 * выставлен флаг: «Можно переопределять настройки в 
	 * URL». Если данный флаг не выставлен или параметр 
	 * «MNT_FAIL_URL» не задан, то используются данные из 
	 * «Fail URL» в настройках счета. Если не передать 
	 * параметр «MNT_FAIL_URL» и не выставить поле «Fail 
	 * URL», но указать флаг «Можно переопределять 
	 * настройки в URL», то по умолчанию в качестве fail URL 
	 * будет использоваться ссылка: 
	 * https://www.moneta.ru/paymentFail.htm.
	 */
	const PAY_FAIL_URL = 'MNT_FAIL_URL';
	/**
	 * Необязательный параметр. URL страницы магазина, 
	 * куда должен вернуться покупатель при добровольном 
	 * отказе от оплаты. Отчет об оплате в этом случае 
	 * магазину не отсылается. Этот параметр используется 
	 * только тогда, когда в настройках счета выставлен 
	 * флаг: «Можно переопределять настройки в URL». Если 
	 * данный флаг не выставлен или параметр 
	 * «MNT_RETURN_URL» не задан, то используются данные 
	 * из «Return URL» в настройках счета.
	 */
	const PAY_RETURN_URL = 'MNT_RETURN_URL';
	/**
	 * (ru|en) Язык пользовательского интерфейса.
	 */
	const PAY_LOCALE = 'moneta.locale';
	/**
	 * (1015 – МОНЕТА.РУ, 1020 – Яндекс.Деньги, 1017 – 
	 * WebMoney и т.п.) Предварительный выбор платежной 
	 * системы. Список доступных способов оплаты для 
	 * заданного счета можно посмотреть на странице 
	 * «Рабочий кабинет / Способы оплаты» 
	 * (https://www.moneta.ru/viewPaymentMethods.htm)
	 */
	const PAY_SYSTEM_ID = 'paymentSystem.unitId';
	/**
	 * Список (разделенный запятыми) идентификаторов 
	 * платежных систем, которые необходимо показывать 
	 * пользователю в MONETA.Assistant. Например, 
	 * «1015,1017» - пользователю в MONETA.Assistant будут 
	 * показаны только платежные системы МОНЕТА.РУ и 
	 * WebMoney.
	 */
	const PAY_SYSTEMS_LIST = 'paymentSystem.limitIds';
	/**
	 * CHECK для проверочных запросов. Параметр 
		отсутствует для запросов с уведомлением об 
		оплате на адрес, указанный в «Pay URL».
	 */
	const PAY_COMMAND = 'MNT_COMMAND';
	/**
	 * Номер операции в системе MONETA.RU
	 */
	const PAY_OPERATION_ID = 'MNT_OPERATION_ID';
	/**
	 * Валюта по умолчанию
	 */
	const CURRENCY_CODE = 'RUB';
	/**
	 * Номер счета в системе Moneta.Assistant
	 */
	const PAYMENT_BILL = '23089736';
	/**
	 * Url, куда отправлять запрос на оплату
	 */
    const SERVICE_GET_URL = 'https://www.moneta.ru/assistant.htm';
	/**
	 * CHECK для проверочных запросов. Параметр 
		отсутствует для запросов с уведомлением об 
		оплате на адрес, указанный в «Pay URL».
	 */
	const COMMAND_TO_CHECK = 'CHECK';
	/**
	 * Id валют из системы Moneta.Assistant
	 * @var type 
	 */
    private static $systems = array(
		'moscow' => 499669,//VISA, MasterCard
		'webmoney' => 1017,//WebMoney
		'yandex2' => 1020,//Яндекс.Деньги
		'qiwi' => 822360,//QIWI Кошелек
		'dmr2' => 545234,//Деньги@Mail.Ru_
		'ubrr' => 805519,//ОАО "УБРиР"
		'forwardmobile' => 83046,//Форвард Мобайл
		'platika' => 226272,//Платика
		'mkb' => 295339,//Московский Кредитный Банк
		'elecsnet' => 232821,//Элекснет
		'fsg' => 426904,//Федеральная Система ГОРОД
		'sberbank' => 510801,//Сбербанк - Волго-Вятский банк
		'sbsevkav' => 891407,//Сбербанк - Северо-Кавказский банк
		'oplataru' => 772632,//Оплата.Ру
		'lider' => 878406,//НКО ЗАО "Лидер" в системе монеты нет названия "lider" для этого способа
		'mailofrussia' => 1029,//Отделения "Почта России"
		'faktura' => 609111,//Интернет-банк "Faktura.ru"
		'ubrr' => 786203,//Интернет-банк ОАО "УБРиР"
		'bank' => 705000,//Оплата банком
		'contact' => 1028,//Contact
		'alfa' => 587412,//Интернет-банк "Альфа-Клик"
		'psbank' => 661709,//Интернет-банк "Промсвязьбанк"
		'rapida' => 248362,//Евросеть, Связной
		'impulsplus' => 727446,//МегаФон, МТС, Utel, Tele-2, Cмартс
	);
	/**
	 * О результате приема отчета об оплате магазину необходимо в обработчике адреса 
	 * «Pay URL» вернуть в качестве ответа текстовую строку в формате UTF-8
	 * Если текстовая строка начинается словом «SUCCESS», то отчет считается принятым, 
	 * и операция благополучно завершается. 
	 * Ответ об успешном получении уведомления 
	 * следует возвращать также в том случае, если учетной системой магазина 
	 * уведомление принято повторно, то есть, в том случае, когда магазин уже отвечал 
	 * результатом «SUCCESS» на предшествующие уведомления.
	 */
	const SUCCESS_ANS = 'SUCCESS';
	/**
	 * Если система «MONETA.RU» не смогла получить ответ от обработчика, либо сервер был 
	 * недоступен, либо текстовая строка начинается словом «FAIL», то уведомление 
	 * считается не доставленным. Попытки отправки уведомления будут повторены. 
	 */
	const FAIL_ANS = 'FAIL';
	/**
	 * если запрос на оплату происходит в тестовом режиме, то 
	 * это значение должно быть равно «1», иначе «0».
	 */
	const TEST_MODE = 0;
	
    /**
     * ОК – означает, что “уведомление о платеже принято” если тип запроса был “pay” или “может быть принято” если тип запроса был “check”
     */
    const STATUS_OK = 200;
    /**
     * Только для запросов типа “check” Платёж отклонён. В этом случае OnPay не примет платёж от Клиента.
     */
    const STATUS_CANCEL = 500;
    /**
     * Временная ошибка. Пробует повторно послать это уведомление несколько раз в течение следующих 72 часов 
     * после чего пометит платёж статусом “уведомление не доставлено в API”
     */
    const STATUS_ERROR_TMP = 402;
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
	 * id системы оплаты в Moneta.Assistent
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
	 * @var MonetaAssistant[]
	 */
	private static $registry = array();
	/**
	 * 
	 * @param \Models\OrderManagment\Order $order
	 * @return MonetaAssistant
	 */
	public static function get(Order $order){
		if (empty(self::$registry[$order['id']])){
			self::$registry[$order['id']] = new self($order);
		}
		return self::$registry[$order['id']];
	}
	/**
	 * @deprecated
	 * @param type $url
	 * @param type $status
	 * @param type $err
	 * @return null
	 */
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
	/**
	 * //Значение параметра MNT_AMOUNT должно быть с двумя десятичными знаками, отделенными точкой, например, «1.23» или «123.00». 
	 * @param type $price
	 */
	private static function getPriceFormated($price){
		return !empty($price) ? number_format($price, 2, '.', '') : '0.00';
	}
    
    private function __construct(Order $order){
        if (!array_key_exists($order['pay_type'], self::$systems)){
            throw new \LogicException('Pay system ' . $order['pay_type'] . ' not found');
        }
        $this->pay_amount = self::getPriceFormated($order['total_cost']);
        $this->pay_for = $order['id'];
        $this->currency = self::CURRENCY_CODE;
        $this->pay_type = $order['pay_type'];
		$this->pay_system_code = self::$systems[$order['pay_type']];
		$this->user_email = $order['email'];
		$this->order = $order;
    }
    /**
     * Создает ссылку на сервис для оплаты
     */
    public function getUrl(&$error = NULL, &$status = NULL){
		$request_data = array(
			self::PAY_ID => self::PAYMENT_BILL,
			self::PAY_AMOUNT => $this->pay_amount,
			self::PAY_ORDER_ID => $this->pay_for,
			self::PAY_CURRENCY_CODE => self::CURRENCY_CODE,
			self::PAY_USER_ID => $this->user_email,
			self::PAY_SYSTEM_ID => $this->pay_system_code,
			self::PAY_SECURE_STRING => $this->getSecureStringToPay()
		);
		$request_str_part = array();
		foreach ($request_data as $key => $data){
			$request_str_part[] = $key . '=' . $data;
		}
        $str = self::SERVICE_GET_URL . '?' . implode('&', $request_str_part);
        return $str;
    }
	/**
	 * @deprecated
	 * @param type $url
	 * @return null
	 */
	public function getPageByUrl(){
		$url = $this->getUrl();
		$result = self::getPage($url, $status, $error);
        if (empty($error) && $status == 200){
            return $result;
        }else{
            return NULL;
        }
	}
	/**
	 * Кодирование ключа производится путем конкатенации в одну строку значений 
	 * параметров отчета и кода проверки целостности данных, кодированием по 
	 * алгоритму Message Digest 5 (MD5) - RFC 1321 и представлением массива байт 
	 * в виде строки шестнадцатеричных чисел: 
	 * MNT_SIGNATURE = MD5( 
	 *	MNT_ID + MNT_TRANSACTION_ID + MNT_AMOUNT + MNT_CURRENCY_CODE + 
	 *	MNT_SUBSCRIBER_ID + ТЕСТОВЫЙ РЕЖИМ + КОД ПРОВЕРКИ ЦЕЛОСТНОСТИ 
	 *	ДАННЫХ 
	 * ) 

	 */
	private function getSecureStringToPay(){
		return md5(
            OrderConfig::getParameter('merchant_id') . 
			$this->pay_for . 
			$this->pay_amount . 
			self::CURRENCY_CODE .
			$this->user_email .
			OrderConfig::getParameter('test') . 
			OrderConfig::getParameter('secret_key')
		);
	}
	/**
	 * Разбор данных запроса уведомления об оплате
	 * @param array $data
	 * @return $result
	 */
	public function payRequest($data){
		$signature = $this->getSecureStringFromCheck($data);
		$description = '';
		if (!in_array($this->order['status'], array(
			Order::STATUS_NEW, Order::STATUS_PAY_WAITING, Order::STATUS_PROCESSED
		))){
			$code = self::STATUS_CANCEL;
			$description = 'Неверный статус заказа';
		}elseif($signature != $data[self::PAY_SECURE_STRING]){
			$code = self::STATUS_CANCEL;
			$description = 'Сигнатура оплаты не совпадает';
		}else{
			$code = self::STATUS_OK;
			$this->order->setStatus(Order::STATUS_PAYED);
			$this->order->edit(array('payed' => 1));
		}
		return array(
			'code' => $code,
			'description' => $description,
			'signature' => $this->getSecureStringForCheck(array('code' => $code))
		);
	}
	/**
	 * Код проверки целостности данных для уведомления без проверки
		MNT_ID + MNT_TRANSACTION_ID + MNT_OPERATION_ID + 
		MNT_AMOUNT + MNT_CURRENCY_CODE + 
		MNT_TEST_MODE + 
		КОД ПРОВЕРКИ ЦЕЛОСТНОСТИ ДАННЫХ )
	 */
	private function getSecureStringFromPayCheck($data){
		return md5(
			self::PAYMENT_BILL . 
			$this->pay_for . 
			$data[self::PAY_OPERATION_ID] . 
			$this->pay_amount . 
			$this->pay_system_code . 
			self::TEST_MODE . 
			self::SECRET_KEY
		);
	}
	/**
	 * Разбор данных запроса проверки заказа
	 * @param array $data
	 * @return int $result
	 */
	public function checkRequest($data){
		$signature = $this->getSecureStringFromCheck($data);
		$description = '';
		if (in_array($this->order['status'], array(
			Order::STATUS_DELETE, Order::STATUS_TMP
		))){
			$code = self::STATUS_CANCEL;
			$description = 'Неверный статус заказа';
		}elseif($signature != $data[self::PAY_SECURE_STRING]){
			$code = self::STATUS_CANCEL;
			$description = 'Сигнатура проверки не совпадает';
		}elseif($this->order['payed'] == 1){
			$code = self::STATUS_OK;
		}else{
			$code = self::STATUS_ERROR_TMP;
		}
		if (!empty($data[self::PAY_OPERATION_ID])){
			$this->order->edit(array('operation_id' => $data[self::PAY_OPERATION_ID]));
		}
		return array(
			'code' => $code,
			'description' => $description,
			'signature' => $this->getSecureStringForCheck(array('code' => $code))
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
	private function getSecureStringFromCheck($data){
		return md5(
			(!empty($data[self::PAY_COMMAND]) ? self::COMMAND_TO_CHECK : '') . 
			self::PAYMENT_BILL . 
			$this->pay_for .
			(!empty($data[self::PAY_OPERATION_ID]) ? $data[self::PAY_OPERATION_ID] : '') .
			$this->pay_amount .
			self::CURRENCY_CODE .
			$this->user_email .
			self::TEST_MODE .
			self::SECRET_KEY
		);
	}
	/**
	 * код проверки для ответа на запрос проверки данных
	 *  MNT_SIGNATURE = MD5( 
		MNT_RESULT_CODE + MNT_ID + MNT_TRANSACTION_ID + 
		КОД ПРОВЕРКИ ЦЕЛОСТНОСТИ ДАННЫХ 
		) 
	 */
	public function getSecureStringForCheck($data){
		return md5(
			$data['code'] .
			self::PAYMENT_BILL .
			$this->pay_for .
			self::SECRET_KEY
		);
	}
	public function asArray(){
		return array(
			'price' => $this->pay_amount,
			'order_id' => $this->pay_for,
			'currency' => $this->currency,
			'pay_type' => $this->pay_type,
			'pay_system_code' => $this->pay_system_code,
			'user_email' => $this->user_email,
			'bill_id' => self::PAYMENT_BILL
		);
	}
}
?>