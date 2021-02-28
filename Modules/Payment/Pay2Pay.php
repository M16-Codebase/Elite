<?php
namespace Modules\Payment;
use Models\Payments\Pay2Pay AS Payment;
use Models\OrderManagment\Order;
/**
 * Description of Pay2Pay
 *
 * @author olya
 */
class Pay2Pay {
    private function getTestData(){
		$xml = '<?xml version="1.0" encoding="UTF-8"?> 
			<response> 
			 <version>1.3</version> 
			 <merchant_id>4332</merchant_id> 
			 <type>result</type> 
			 <language>ru</language> 
			 <order_id>13837</order_id> 
			 <amount>1925</amount> 
			 <currency>RUB</currency> 
			 <description>Описание</description> 
			 <paymode>wmr</paymode> 
			 <trans_id>12345</trans_id> 
			 <status>success</status> 
			 <error_msg></error_msg> 
			 <test_mode>1</test_mode> 
			 <other></other>
			</response> ';
		$sign = base64_encode(md5(Payment::HIDDEN_KEY.$xml.Payment::HIDDEN_KEY));
		return array(
			'xml' => base64_encode($xml),
			'sign' => $sign
		);
	}
    public function index(){
        $this->setAjaxResponse();
        $params = $this->request->request->all();
		if (empty($params) && \LPS\Config::isLocal()){//для теста на локалке
			$params = $this->getTestData();
		}
		if (empty($params)){
			return $this->notFound();
		}
        $xml = \Models\Payments\Pay2Pay::getXmlFromRequest($params['xml']);
        $data_xml_array = json_decode(json_encode(simplexml_load_string($xml)), TRUE);
        $order = Order::getById($data_xml_array['order_id']);
        $payment = NULL;
        $result = array();
        $description = '';
        if (empty($order)){
            $result_code = Payment::STATUS_NO;
			$description = 'Заказ #'.$data_xml_array['order_id'].' не найден';
        }else{
            $payment = Payment::get($order);
            $result = $payment->payRequest($params);
            $result_code = $result['code'];
            $description = $result['description'];
            if ($result_code == Payment::STATUS_YES && $order['payed'] == 1){
                $mailAns = clone $this->getAns();
                $mailAns->setTemplate('mails/payed.tpl');
                $mailAns->add('order', $order)
                        ->add('order_view_link', $this->getModule('Order\Main')->getOrderViewLink($order))
                        ->add('site_config', \App\Builder::getInstance()->getSiteConfig())
                        ->add('transport_companies', \Models\TransportCompany::search());
                \Models\Email::send($mailAns, array($order['email'] => ''));
            }
        }
        $this->getAns()->add('result_code', $result_code)
            ->add('order', $order)
            ->add('payment', !empty($payment) ? $payment->asArray() : array())
            ->add('description', $description);
    }
	
	public function systems(){
		$db = \App\Builder::getInstance()->getDB();
		$this->getAns()->add('pay_methods', Payment::getPayMethods())
			->add('groups', $db->query('SELECT * FROM `'.Payment::TABLE_PAY_GROUPS.'`')->select('id'));
	}
	
	public function setSystemUsed(){
		$this->setJsonAns()->setEmptyContent();
		$db = \App\Builder::getInstance()->getDB();
		$system_key = $this->request->request->get('key');
		$used = $this->request->request->get('used');
		$db->query('UPDATE `'.Payment::TABLE_PAY_TYPES.'` SET `used` = ?d WHERE `key` = ?s', !empty($used) ? 1 : 0, $system_key);
	}
	
	public function setSystemGroup(){
		$this->setJsonAns()->setEmptyContent();
		$db = \App\Builder::getInstance()->getDB();
		$system_key = $this->request->request->get('key');
		$group_id = $this->request->request->get('group_id');
		$db->query('UPDATE `'.Payment::TABLE_PAY_TYPES.'` SET `group_id` = ?d WHERE `key` = ?s', !empty($group_id) ? $group_id : 0, $system_key);
	}
}
