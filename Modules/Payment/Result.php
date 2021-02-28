<?php
/**
 * @TODO разъединить отдельно OnPay, отдельно Raiffeisen
 *
 * @author olga
 */
namespace Modules\Payment;
use Models\Payments\OnPay;
use Models\OrderManagment\Order;
use Models\CatalogManagment\ProductConfig;
use Models\Payments\Raiffeisen;
class Result extends \LPS\WebModule{
    /**
     * через OnPay
     */
    public function index(){
        $this->setAjaxResponse();
        $comment = '';
        $pay_for = '';
        $code = '';
        $md5 = '';
        $params = $this->request->request->all();
//		if (empty($params)){//для теста на локалке
//			$params = $this->request->query->all();
//		}
        $order = Order::getById($params['pay_for']);
        if (empty($order)){
            $code = Payment::STATUS_ERROR_PARAMS;
			$comment = 'Cant find order #id' . $params['pay_for'];
        }else{
            $pay_for = $order['id'];
            $payment = $order->getPayment();
            if (in_array($order['status'], array(Order::STATUS_TMP, Order::STATUS_COMPLETE, Order::STATUS_REMOVED))){
                $code = Payment::STATUS_CANCEL;
                $comment = 'Order canceled';
            }else{
                if ($params['type'] == Payment::REQUEST_TYPE_CHECK){
                    if ($params['order_amount'] != $payment->getData('pay_amount')){
                        $code = Payment::STATUS_ERROR_PARAMS;
                        $comment = 'Pay amount not matches.';
                    }elseif ($params['md5'] != $payment->getMd5ForCheckFromOnpay($string)){
                        $code = Payment::STATUS_ERROR_MD5;
                        $comment = 'Md5ForCheckFromOnpay not matches.';
                    }else{
						if ($order->canPayed()){
							$code = Payment::STATUS_OK;
						}else{
							$code = Payment::STATUS_CANCEL;
							$comment = 'Order is prohibited to paying';
						}
						$md5 = $payment->getMd5ForCheckToOnpay($code);
                    }
                }elseif($params['type'] == Payment::REQUEST_TYPE_PAY){
                    if ($params['paid_amount'] != $payment->getData('pay_amount')){
                        $code = Payment::STATUS_ERROR_PARAMS;
                        $comment = 'Pay amount not matches.';
                    }elseif ($params['md5'] != $payment->getMd5ForPayFromOnpay($params['onpay_id'], $string, $params['order_amount'])){
                        $code = Payment::STATUS_ERROR_MD5;
                        $comment = 'Md5ForPayFromOnpay not matches.';
                    }else{
						$order->setStatus(Order::STATUS_PAYED);
						$order->edit(array('payed', 1));
                        self::sendEmail($order);
                        $code = Payment::STATUS_OK;
                        $md5 = $payment->getMd5ForPayToOnpay($params['onpay_id'], $code, $params['order_amount']);
                    }
                }else{
                    $code = Payment::STATUS_ERROR_PARAMS;
                    $comment = 'Wrong type';
                }
            }
        }
        $this->getAns()->add('code', $code)->add('pay_for', $pay_for)->add('comment', $comment)->add('md5', $md5);
    }
    /**
     * Через райффайзен
     * @return type
     */
    public function payCard(){
        $comment = '';
        $code = '';
        $params = $this->request->request->all();
		if (empty($params)){
			$params = $this->request->query->all();
		}
        $db = \App\Builder::getInstance()->getDB();
        $log_id = $db->query('INSERT INTO `'.Raiffeisen::TABLE.'` SET `data` = ?s, `date` = NOW()', json_encode($params));
        if (empty($params['descr'])){
            $code = Raiffeisen::STATUS_NO;
            $comment = 'Empty descr';
        }else{
            $order = Order::getById($params['descr']);
            if (empty($order)){
                $code = Raiffeisen::STATUS_NO;
                $comment = 'Empty order_id';
            }else{
                $payment = $order->getPayment();
                if ($params['type'] == 'conf_pay'){
                    $result = $payment->payRequest($params, $log_id);
                    $code = $result['code'];
                    $comment = $result['description'];
                    if ($code == Raiffeisen::STATUS_YES){//если у нас всё ок, то отправляем на мыло инфу о доставке
                        if ($params['result'] == 0){//пока не придумают шаблон письма в таком случае
                            self::sendEmail($order);
                        }
                    }
                }else{
                    $code = Raiffeisen::STATUS_NO;
                    $comment = 'Wrong request type ' . $params['type'];
                }
            }
            if (!empty($comment)){
                $db->query('UPDATE `'.Raiffeisen::TABLE.'` SET `error_inner` = ?s WHERE `id` = ?d', $comment, $log_id);
            }
        }
        $db->query('UPDATE `'.Raiffeisen::TABLE.'` SET `code` = ?s WHERE `id` = ?d', $code, $log_id);
        return 'RESP_CODE=' . $code;
    }
    private static function sendEmail(Order $order){
        $post_id = 
            !is_null($order['delivery_type_self']) && $order['delivery_type_self'] != 0 
            ? ProductConfig::TEXT_DS 
            : (
                !is_null($order['delivery_type_courier']) && $order['delivery_type_courier'] != 0 
                ? ProductConfig::TEXT_PAYED_DC
                : (
                    !is_null($order['delivery_type_company']) && $order['delivery_type_company'] != 0) 
                    ? ProductConfig::TEXT_PAYED_DT 
                    : NULL);
        $mailAns = new \LPS\WebContentContainer('mails/payed.tpl');
        $mailAns->add('order', $order)
                ->add('order_view_link', \Modules\Order\Main::getOrderViewLink($order))
                ->add('post', !empty($post_id) ? \Models\ContentManagment\Post::getById($post_id) : NULL)
                ->add('site_config', \App\Builder::getInstance()->getSiteConfig());
        \Models\Email::send($mailAns, array($order['email'] => ''));
    }
}
?>