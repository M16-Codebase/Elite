<?php
/**
 * Модуль работы с PayAnyWay платежной системой
 *
 * @author olga
 */
namespace Modules\Payment;
use Models\Payments\MonetaAssistant AS Payment;
use Models\OrderManagment\Order;
class MonetaAssistant extends \LPS\WebModule{
    public function index(){
        return $this->notFound();
    }
	/**
	 * Сюда приходит уведомление об оплате
	 */
	public function pay(){
        $this->setAjaxResponse();
        $params = $this->request->query->all();
		$this->db->query('INSERT INTO `temp` SET `data` = ?s, `result` = ?s', json_encode($params), 0);
		$order = Order::getById($params[Payment::PAY_ORDER_ID]);
		$payment = NULL;
		$result = array();
		$description = '';
		if (empty($order)){
			$result_code = Payment::STATUS_CANCEL;//заказ отменен\не найден
			$description = 'Заказ #'.$params[Payment::PAY_ORDER_ID].' не найден';
		}else{
			$payment = Payment::get($order);
			$result = $payment->payRequest($params);
			$result_code = $result['code'];
			$description = $result['description'];
			if ($result_code == Payment::STATUS_OK && $order['payed'] == 1){
				$mailAns = clone $this->getAns();
				$mailAns->setTemplate('mails/payed.tpl');
				$mailAns->add('order', $order)
						->add('order_view_link', $this->getModule('Order\Main')->getOrderViewLink($order))
						->add('site_config', \App\Builder::getInstance()->getSiteConfig());
				\Models\Email::send($mailAns, array($order['email'] => ''));
			}
		}
		$this->db->query('INSERT INTO `temp` SET `data` = ?s, `result` = ?s', json_encode($params), json_encode($result));
		$this->getAns()->add('result_code', $result_code)
		->add('order', $order)
		->add('payment', !empty($payment) ? $payment->asArray() : array())
		->add('signature', !empty($result) ? $result['signature'] : '')
		->add('description', $description);
	}
	/**
	 * Сюда приходят проверочные запросы
	 */
	public function check(){
        $this->setAjaxResponse();
        $params = $this->request->query->all();
		$order = Order::getById($params[Payment::PAY_ORDER_ID]);
		$payment = NULL;
		$result = array();
		$description = '';
		if (empty($order)){
			$result_code = Payment::STATUS_CANCEL;//заказ отменен\не найден
			$description = 'Заказ #'.$params[Payment::PAY_ORDER_ID].' не найден';
		}else{
			$payment = Payment::get($order);
			$result = $payment->checkRequest($params);
			$result_code = $result['code'];
			$description = $result['description'];
		}
		$this->db->query('INSERT INTO `temp` SET `data` = ?s, `result` = ?s', json_encode($params), $result_code);
		$this->getAns()->add('result_code', $result_code)
		->add('order', $order)
		->add('payment', !empty($payment) ? $payment->asArray() : array())
		->add('signature', !empty($result) ? $result['signature'] : '')
		->add('description', $description);
	}
	public function success(){
		
	}
	public function fail(){
		
	}
	public function inProgress(){
		
	}
}
?>