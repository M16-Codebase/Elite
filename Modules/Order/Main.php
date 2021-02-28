<?php
/**
 * Description of main
 *
 * @author pochka
 */
namespace Modules\Order;

use App\Configs\CatalogConfig;
use App\Configs\OrderConfig;
use Models\Validator;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\Positions\Order;

class Main extends \LPS\WebModule{
    /** Самовывоз */
    const DELIVERY_TYPE_SELF = 'self';
    /** Доставка курьером */
    const DELIVERY_TYPE_COURIER = 'courier';
    /** Отправка транспортной компанией */
    const DELIVERY_TYPE_COMPANY = 'company';

    /** Какие поля на что проверять */
    protected static $params_check = array(
        'name' => array('type' => 'checkEmpty'),
        'person_type' => array('type' => 'checkEmpty'),
        'phone' => array('type' => 'checkPhone'),
        'email' => array('type' => 'checkEmail'),
//        'pay_type' => array('type' => 'checkEmpty'),
    );
    /** Какие поля могут быть */
    protected static $params_get = array('descr', 'mailer', 'pay_type');
    private static $catalog = NULL;
    protected function init(){
        parent::init();
        self::$catalog = Type::getByKey(\App\Configs\CatalogConfig::ORDERS_KEY);
    }
    /**
     * Корзина
     */
    public function index(){
        //заказ пользователя всегда выводится на все страницы
    }

    /**
     * Добавляем в заказ
     * @ajax
     */
    public function addVariantToOrder(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $variant_id = $this->request->request->get('variant_id');
        $order_id = $this->request->cookies->get('order_id');
        if (empty($order_id)){
            $response = $this->createOrder($order_id, $this->router->getUrlPrefix() . $this->getModuleUrl() . __FUNCTION__ . '/');
        }
        $count = $this->request->request->get('count');
        $order = Order::getById($order_id);
		$variant = \Models\CatalogManagement\Variant::getById($variant_id);
        if (empty($variant)){
            $ans->addErrorByKey('variant_id', \Models\Validator::ERR_MSG_NOT_FOUND);
            return;
        }
        $result = $order->addPosition($variant, $count, $errors);
        if (!empty($errors)) {
            $ans->setErrors($errors);
            return;
        }
        $ans->addData('order_id', $order['id'])->addData('added_position', $result['id']);
        if (!empty($response)){
            return $response;
        }
    }
    /**
	 * 
	 * @param type $order_id
	 * @param type $redirect_to
	 * @return \Symfony\Component\HttpFoundation\Response;
	 */
    private function createOrder(&$order_id, $redirect_to = NULL){
        $order_id = $this->request->cookies->get('order_id');
        if (empty($order_id)){
            //у заказов свои статусы, поэтому тут паблик
            $order_type_key = CatalogConfig::CATALOG_KEY_ORDERS_FIZ;
            $user = $this->account->getUser();
            if (!empty($user) && $user['person_type'] == 'org') {
                $order_type_key = CatalogConfig::CATALOG_KEY_ORDERS_ORG;
            }
            $order_type = Type::getByKey($order_type_key, self::$catalog['id'], $this->segment['id']);
            $order_id = Order::create($order_type['id'], Order::S_PUBLIC, array(), $errors, $this->segment['id']);
            $response = $this->redirect(!empty($redirect_to) ? $redirect_to : $this->router->getUrlPrefix() . $this->getModuleUrl() . __FUNCTION__ . '/');
            $this->setCookie('order_id', $order_id, NULL, $response);
            return $response;
        }
        return;
    }

    /**
     * Удаляем из заказа
     * @ajax
     */
    public function delOrderPosition(){
		$ans = $this->setJsonAns()->setEmptyContent();
        $position_id = $this->request->request->get('position_id');
        $order_id = $this->request->cookies->get('order_id');
        if (empty($order_id)){
            //если нет заказа, то наш косяк
            throw new \Exception('Order not found');
        }
        $order = Order::getById($order_id);
        $order->removePosition($position_id, $errors);
        if (!empty($errors)) {
            $ans->setErrors($errors);
        }
    }

    /*
     * Оформление заказа
     */

    /**
     * @return string|\Symfony\Component\HttpFoundation\Response
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    public function form(){
        $site_config = \App\Builder::getInstance()->getSiteConfig();
        //пользователь может изменять только тот заказ, который лежит в куках
        $order_id = \App\Builder::getInstance()->getCurrentSession()->get('order');
		$order = Order::getById($order_id);
        if (!empty($order)){
            $this->getAns()->add('order', $order);
            $data = $this->request->request->all();
            $errors = array();
            if (!empty($data)){
                $params = $this->checkParams($errors);
                if (!empty($errors)){
					return json_encode(array('errors' => $errors));
                }else{
                    $params['status'] = Order::STATUS_NEW;
                    $params['date'] = date('Y-m-d H:i:s');
                    if (!empty($params['delivery_type_courier'])){
                        if ($order['total_cost'] < $site_config['delivery_price_free']){
                            $params['delivery_price'] = $site_config['delivery_price'];
                        }else{
                            $params['delivery_price'] = 0;
                        }
                    }
                    $order->edit($params);
					return json_encode(array('url' => $this->getModuleUrl() . 'sended/'));
                }
            }else{
                $this->getAns()->setFormData($order->asArray());
            }
        }else{
            return $this->redirect('/');
        }
    }
	public function getOrderViewLink(Order $order){
		return 'http://' . $this->request->server->get('SERVER_NAME') . '/order/view/?order_id=' . $order['id'] . '&hash=' . self::getOrderHash($order['id']);
	}
	public function sended(){
		$s = \App\Builder::getInstance()->getCurrentSession();
		$order_id = $s->get('order');
		if (empty($order_id)){
            return $this->redirect('/');
		}else{
			$order = Order::getById($order_id);
			$this->getAns()->add('order', $order)
                    ->add('order_view_link', $this->getOrderViewLink($order));
			$s->remove('order');
		}
	}
	public function view(){
		$order_id = $this->request->query->get('order_id');
		if (self::getOrderHash($order_id) == $this->request->query->get('hash')){
			$order = Order::getById($order_id);
			if (!empty($order) && $order->canPayed()){
				$this->getAns()->add('payment_link', $order->getPayment()->getUrl());
			}
			$this->getAns()->add('order', $order);
		}else{
		}
	}
	private static function getOrderHash($order_id){
		return md5($order_id . \LPS\Config::HASH_SOLT_STRING);
	}
    /**
     * Установить количество позиции в заказе
     * @ajax
     */
    public function changePositionCount(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $position_id = $this->request->request->get('position_id', 8);
        $count = $this->request->request->get('count', 666);
        $position = \Models\CatalogManagement\Positions\OrderItem::getById($position_id);
        if (empty($position)) {
            $ans->addErrorByKey('position_id', \Models\Validator::ERR_MSG_EMPTY);
            return;
        }
        if ($this->account instanceof \App\Auth\Account\Admin){//админ может изменять любой заказ
            $admin_order_id = $position['item_id'];
        }
        if (!empty($admin_order_id)){
            $order_id = $admin_order_id;
        }else{
            //пользователь может изменять только тот заказ, который лежит в сессии
            $order_id = $this->request->cookies->get('order_id');
        }
        if (empty($order_id)){
            //если нет заказа, то наш косяк
            throw new \LogicException('Order not found');
        }
        if ($position['item_id'] != $order_id){
            $ans->addErrorByKey('access', 'denied'); //можно редактировать только свой заказ, если не админ
            return;
        }
        $position->updateValues(array(OrderConfig::KEY_POSITION_COUNT => array(0 => array('val_id' => null, 'value' => $count))), $errors, $this->segment['id']);
        if (!empty($errors)) {
            $ans->setErrors($errors);
            return;
        }
        if ($this->account instanceof \App\Auth\Account\Admin){
            $order = Order::getById($position['item_id']);
//            $order->log('position #' . $position_id . ' change count to ' . $count, $this->account->getUser()->getId());
        }
    }
    /**
     * @ajax отдать шаблон для маленькой корзины
     */
    public function smallCart(){
        $this->setAjaxResponse();
        $order = Order::getById($this->request->request->get('order_id'));
        $this->getAns()->add('user_order', $order);
    }
    /**
     * Проверка всех полей формы оформления заказа
     * @param array $errors ошибки в формате errors[название поля] = код ошибки
     * @return array $params чистые данные только нужных полей, если поле с ошибкой, значение = NULL
     */
    protected function checkParams(&$errors){
        $data = $this->request->request->all();
        $check_params = static::$params_check;
        $params = Validator::getInstance($this->request)->checkFewResponseValues($check_params, $errors);
        $params['address'] = $this->checkAddress(1);
        foreach (static::$params_get as $key){
            $params[$key] = isset($data[$key]) ? $data[$key] : NULL;
        }
        if (!empty($data['delivery_type'])){
            $params['delivery_type_self'] = $data['delivery_type'] == self::DELIVERY_TYPE_SELF ? (!empty($data['store_id']) ? $data['store_id'] : 0) : 0;
            $params['delivery_type_courier'] = $data['delivery_type'] == self::DELIVERY_TYPE_COURIER ? 1 : 0;
            $params['delivery_type_company'] = $data['delivery_type'] == self::DELIVERY_TYPE_COMPANY ? (!empty($data['transport_company_id']) ? $data['transport_company_id'] : 0) : 0;
            if ($data['delivery_type'] == self::DELIVERY_TYPE_COMPANY && empty($data['transport_company_id']) && $this instanceof Admin){
                $errors['transport_company_id'] = Validator::ERR_MSG_EMPTY;
            }
        }elseif($this instanceof Admin){
            $errors['delivery_type'] = Validator::ERR_MSG_EMPTY;
        }
        return $params;
    }

    /**
     * Функция проверки адреса. Обязательные поля различны для разных типов доставки
     * @param bool $inner вызывается из модуля(true) или аяксом(false)
     * @param array $errors
     * @return для аякса - ошибки, для внутреннего вызова получившийся адрес
     */
    public function checkAddress($inner = FALSE, &$errors = array()){
        $data = $this->request->request->all();
		$address = '';
        if ($data['delivery_type'] == self::DELIVERY_TYPE_SELF){

        }elseif ($data['delivery_type'] == self::DELIVERY_TYPE_COURIER){
            if (empty($data['street'])) {
                $errors['street'] = Validator::ERR_MSG_EMPTY;
            }
            if (empty($data['house'])) {
                $errors['house'] = Validator::ERR_MSG_EMPTY;
            }
            if (empty($errors['street']) && empty($errors['house'])){
                $address = trim(
                        ' ул. ' . $data['street'] .
                        ' д. ' . $data['house'] .
                        (!empty($data['korpus']) ? (' кор. ' . $data['korpus']) : '') .
                        (!empty($data['apart']) ? (' кв. ' . $data['apart']) : '') .
                        (!empty($data['floor']) ? (' этаж ' . $data['floor']) : '')
                );
            }
        }elseif ($data['delivery_type'] == self::DELIVERY_TYPE_COMPANY){
            if (empty($data['index'])){
                $errors['index'] = Validator::ERR_MSG_EMPTY;
            }
            if (empty($data['city'])){
                $errors['city'] = Validator::ERR_MSG_EMPTY;
            }
            if (empty($data['street'])){
                $errors['street'] = Validator::ERR_MSG_EMPTY;
            }
            if (empty($data['house'])){
                $errors['house'] = Validator::ERR_MSG_EMPTY;
            }
            if (empty($errors['index']) && empty($errors['city']) && empty($errors['street']) && empty($errors['house'])){
                $address = trim(
                    $data['index'] .
                    ' г. ' . $data['city'] .
                    ' ул. ' . $data['street'] .
                    ' д. ' . $data['house'] .
                    (!empty($data['stroenie']) ? (' стр. ' . $data['stroenie']) : '') .
                    (!empty($data['korpus']) ? (' кор. ' . $data['korpus']) : '') .
                    (!empty($data['apart']) ? (' кв. ' . $data['apart']) : '')
                );
            }
        }else{
            $errors['delivery_type'] = Validator::ERR_MSG_INCORRECT_FORMAT;
        }
        if ($inner){
            return $address;
        }else{
            return json_encode(array('errors' => $errors));
        }
    }
}

?>