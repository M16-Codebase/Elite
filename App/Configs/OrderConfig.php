<?php
namespace App\Configs;
/**
 * Настройки заказов
 *
 * @author olya
 */
class OrderConfig {
    /* ***************** Ключи параметров заказа ****************** */
    const KEY_ORDER_NUMBER = 'number';
    const KEY_ORDER_STATUS = 'state';
    const KEY_ORDER_PRICE = 'total_price';
    const KEY_ORDER_PERSON_TYPE = 'person_type';
    const KEY_ORDER_EMAIL = 'email';
    const KEY_ORDER_NAME = 'name';
    const KEY_ORDER_SURNAME = 'surname';
    const KEY_ORDER_PATRONYMIC = 'patronymic';
    const KEY_ORDER_DELIVERY_PRICE = 'delivery_price';
    const KEY_ORDER_COMMENT = 'comment';
    const KEY_ORDER_PAY_TYPE = 'pay_type';
    const KEY_ORDER_PAY_SYSTEM_METHOD = 'pay_system_method';
    /**
     * идентификатор операции в системе оплаты
     */
    const KEY_ORDER_PAY_ID = 'pay_id';
    /**
     * хозяин заказа
     */
    const KEY_ORDER_USER = 'user';
    /**
     * флаг о том, заплатил ли пользователь за заказ
     */
    const KEY_ORDER_PAYED = 'payed';
    /**
     * enum 'self', 'company', 'courier'
     */
    const KEY_ORDER_DELIVERY_TYPE = 'delivery_type';
    /**
     * данные о способе доставки. если самовывоз, то откуда, если курьером на адрес, то какой, если транспортной компанией, то куда
     */
    const KEY_ORDER_DELIVERY_DATA = 'delivery_data';
    /**
     * данные о конечном адресе доставки
     */
    const KEY_ORDER_DELIVERY_ADDRESS = 'delivery_address';
    /**
     * время обновления резерва
     */
    const KEY_ORDER_RESERVE_DATE = 'reserve_date';
	/**
	 * записываем процент бонуса на момент оформления заказа
	 */
	const KEY_ORDER_BONUS_RATIO = 'bonus_ratio';
    /**
     * сколько бонусов потрачено
     */
    const KEY_ORDER_BONUS_SPEND = 'bonus_spend';
    /**
     * название компании, только для юрлиц
     */
    const KEY_ORDER_COMPANY_NAME = 'company_name';
    /**
     * inn только для юрлиц
     */
    const KEY_ORDER_INN = 'inn';
    
    /* ***************** Ключи параметров позиций заказов ****************** */
    const KEY_POSITION_ENTITY = 'entity';
    const KEY_POSITION_ENTITY_TYPE = 'entity_type';
    const KEY_POSITION_COUNT = 'count';
    const KEY_POSITION_URL = 'url';
    const KEY_POSITION_TITLE = 'variant_title';
    const KEY_POSITION_IMAGE = 'image';
    const KEY_POSITION_PRICE = 'price';
    const KEY_POSITION_BONUS = 'bonus';
    const KEY_POSITION_AVAILABLE = 'available';
//    const KEY_POSITION_RESERVE = 'reserve';
    
    public static $positionEntities = array(
        'variant' => 'Models\CatalogManagement\Variant'
    );
    
    /* ***************** Статусы заказов ****************** */
    
    const STATUS_TMP = 'tmp';
    const STATUS_NEW = 'new';//не подтвержен
    const STATUS_PROCESSED = 'processed';
	const STATUS_PREPARE = 'prepare';
    const STATUS_COMPLETE = 'complete';
    const STATUS_DELETE = 'deleted';
	const STATUS_PAY_WAITING = 'pay_waiting';
	const STATUS_PAYED = 'payed';
	const STATUS_PAY_START = 'pay_start';
    const STATUS_DELIVERING = 'delivering';//отгружен
    
	public static $statuses = array(
		self::STATUS_TMP => 'Не оформлен', 
		self::STATUS_NEW => 'В ожидании', 
		self::STATUS_PROCESSED => 'В обработке', 
		self::STATUS_PAY_WAITING => 'Ожидает оплаты',
		self::STATUS_PAY_START => 'Частично оплачен',
		self::STATUS_PAYED => 'Оплачен', 
		self::STATUS_PREPARE => 'Частично отгружен',
		self::STATUS_DELIVERING => 'Отгружен', 
		self::STATUS_COMPLETE => 'Выполнен', 
		self::STATUS_DELETE => 'Отменен'
	);
    /**
     * Статусы, при которых можно оплачивать заказ
     * @var type 
     */
    public static $statusToPayed = array(
		self::STATUS_NEW,
		self::STATUS_PROCESSED,
		self::STATUS_PAY_WAITING
	);
    const PAY_TYPE_NAL = 'nal';
    const PAY_TYPE_BEZNAL = 'beznal';
    const PAY_TYPE_ONLINE = 'online';
    /**
     * Варианты оплаты
     * @var type 
     */
    public static $payTypes = array(
        self::PAY_TYPE_NAL => 'Налик',
        self::PAY_TYPE_BEZNAL => 'Безнал',
        self::PAY_TYPE_ONLINE => 'Онлайн'
    );
    const DELIVER_TYPE_SELF = 'self';
    const DELIVER_TYPE_COURIER = 'courier';
    const DELIVER_TYPE_COMPANY = 'company';
    /**
     * Варианты доставки
     * @var type 
     */
    public static $deliverTypes = array(
        self::DELIVER_TYPE_SELF => 'Самовывоз',
        self::DELIVER_TYPE_COURIER => 'Курьер',
        self::DELIVER_TYPE_COMPANY => 'Транспортная компания'
    );
	/**
	 * на что потратить бонусы
	 */
	const BONUS_SPEND_ORDER_PAY = 'order_pay';
	const BONUS_SPEND_GIFT = 'gift';
	public static $bonusSpend = array(
		self::BONUS_SPEND_ORDER_PAY => 'Оплата заказа',
		self::BONUS_SPEND_GIFT => 'Подарочные карты'
	);
    
    /* ***************** Настройки заказов ****************** */
    /**
     * значения по умолчанию, настоящие значения будут в настройках в БД
     * @var array 
     */
    private static $settings = array(
        Settings::KEY_POSITION_COUNT_CONSIDER => FALSE, //учитывать количество сущности при добавлении позиции в заказ
        Settings::KEY_POSITION_PRICE_CONSIDER => FALSE, //можно ли добавлять в корзину товары без цены
        Settings::KEY_POSITION_RESERVE => FALSE,//резервировать ли остатки
        Settings::KEY_BONUS_ENABLE => FALSE,//начислять бонусы
        Settings::KEY_PAY_ONLINE_SYSTEM => NULL,//система оплаты
        Settings::KEY_PERSON_TYPE => 'fiz_org',//типы пользователей
        Settings::KEY_PAY_TYPE_FIZ => array(
            self::PAY_TYPE_NAL => TRUE, //возможность оплаты наликом
            self::PAY_TYPE_BEZNAL => FALSE, //возможность оплаты безналом
            self::PAY_TYPE_ONLINE => FALSE//возможность онлайн оплаты
        ),
        Settings::KEY_PAY_TYPE_ORG => array(
            self::PAY_TYPE_NAL => FALSE, //возможность оплаты наликом
            self::PAY_TYPE_BEZNAL => TRUE, //возможность оплаты безналом
            self::PAY_TYPE_ONLINE => FALSE //возможность онлайн оплаты
        ),
        Settings::KEY_PAY_ONLINE_COMMISION_PLUS => FALSE,//включить ли процент комиссии платежной системы в цену заказа
        Settings::KEY_DELIVER_TYPES => array(
            self::DELIVER_TYPE_SELF => TRUE,//самовывоз
            self::DELIVER_TYPE_COURIER => TRUE,//курьер
            self::DELIVER_TYPE_COMPANY => FALSE//транспортные компании
        ),
        Settings::KEY_AVAILBALE_CONSIDER => array(
            CatalogConfig::AVAILABLE_NO => FALSE,
            CatalogConfig::AVAILABLE_SOON => TRUE,
            CatalogConfig::AVAILABLE_YES => TRUE
        ),
		Settings::KEY_ORDER_BONUS_RATIO => 0,
		Settings::KEY_ORDER_BONUS_TEXT_ADD_NEW => 'Начисление бонусов за выполненный заказ',
		Settings::KEY_ORDER_BONUS_TEXT_CHANGE_STATUS => 'Снятие бонусов из-за смены статуса заказа с {old_status} на {new_status}',
        Settings::KEY_ORDER_BONUS_TEXT_SPEND => 'Снятие бонусов за оплату заказа №{order_number}',
        Settings::KEY_ORDER_BONUS_TEXT_UNSPEND => 'Возвращение бонусов за отмену заказа №{order_number}',
		Settings::KEY_ORDER_BONUS_SPEND => array(
			self::BONUS_SPEND_ORDER_PAY => TRUE,
			self::BONUS_SPEND_GIFT => FALSE
		),
        Settings::KEY_ORDER_BONUS_TO_ORDER => 100,
        Settings::KEY_ORDER_SEND_PAYMENT_LINK => FALSE,
        Settings::KEY_AVAILBALE_LOCK => TRUE
    );
    /**
     * Взять настройку (она может быть в настройках в БД, если нет, то берем из констант)
     * @param string $key
     */
    public static function getParameter($key){
        if (!array_key_exists($key, self::$settings)){
            throw new \Exception('Настройка заказа «'.$key.'» не предусмотрена');
        }
        throw new \Exception('!!!');
        //забираем весь айтем
        $config_item = \Models\CatalogManagement\Positions\Settings::getConfigByKey(CatalogConfig::CONFIG_ORDERS_KEY);
        $param = NULL;
        if (isset($config_item['properties'][$key])){
            $prop = $config_item['properties'][$key];
            if ($prop['set']){
                if (!empty($prop['value_key'])){
                    foreach ($prop['value_key'] as $n => $v){
                        $param[$v] = $prop['value'][$n];
                    }
                }
            }else{
                $param = $prop['data_type'] == \Models\CatalogManagement\Properties\Enum::TYPE_NAME ? $prop['value_key'] : $prop['value'];
            }
        }
        return is_null($param) ? self::$settings[$key] : $param;
    }
    /**
     * Забираем настройку оплаты
     * @param string $key
     */
    public static function getPaymentParameter($key){
        $config_item = \Models\CatalogManagement\Positions\Settings::getConfigByKey(self::getParameter(Settings::KEY_PAY_ONLINE_SYSTEM));
        $param = NULL;
        if (isset($config_item['properties'][$key])){
            $prop = $config_item['properties'][$key];
            if ($prop['set']){
                if (!empty($prop['value_key'])){
                    foreach ($prop['value_key'] as $n => $v){
                        $param[$v] = $prop['value'][$n];
                    }
                }
            }else{
                $param = $prop['data_type'] == \Models\CatalogManagement\Properties\Enum::TYPE_NAME ? $prop['value_key'] : $prop['value'];
            }
        }
        return $param;
    }
}
