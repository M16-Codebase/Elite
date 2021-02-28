<?php
/**
 * Description of Admin
 *
 * @author olga
 */
namespace Modules\Order;
use Models\CatalogManagement\Positions\Order AS OrderEntity;
use Models\CatalogManagement\Positions\OrderItem AS Position;
use Models\CatalogManagement\Item AS ItemEntity;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\Catalog;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;
class Admin extends \Modules\Catalog\Item{
    /**
     * Значение по умолчанию статуса товара
     */
    const DEFAULT_ITEM_STATUS = ItemEntity::S_PUBLIC;
    /**
     * Значение по умолчанию статуса варианта
     */
    const DEFAULT_VARIANT_STATUS = Variant::S_PUBLIC;
    const PAGE_SIZE = 30;
    private static $catalog = NULL;
    protected function init(){
        parent::init();
        self::$catalog = Type::getByKey(\App\Configs\CatalogConfig::ORDERS_KEY);
		if (empty(self::$catalog)){
			throw new \Exception('Для использования данного модуля, должен быть создан каталог с заказами');
		}
    }
    public function index(){
        $this->request->query->set('id', self::$catalog['id']);
        $this->request->query->set('sort', array('id' => 0));
        $children = self::$catalog->getChildren(Type::STATUS_VISIBLE);
        $childrenByKeys = array();
        foreach ($children as $ch){
            $childrenByKeys[$ch['key']] = $ch;
        }
        $this->getAns()->add('catalog_children', $childrenByKeys);
        return parent::index();
    }
    public function listItems($inner = FALSE){
        $this->request->query->set('id', self::$catalog['id']);
        return parent::listItems($inner);
    }
    protected function setContentData(ItemEntity $item){
        if (!empty($item) && !empty($item['mail_sent'])){
            $this->getAns()->addData('mail_sent', 1);
        }
    }
    //будем передавать type_id из шаблона, т.к. решили разделить на два типа - для юриков и физиков
//    public function create(){
//        //в родительской функции определяется ajax по типу запроса POST - ajax, GET - обычный
//        $this->request->request->set('type_id', $this->request->query->get('type_id'));
//        $this->request->query->remove('type_id');
//        return parent::create();
//    }
	/**
	 * Найти вариант по артикулу
	 * @ajax
	 */
	public function findVariant(){
        $this->setJsonAns();
        $value = $this->request->request->get('value');
        $field = $this->request->request->get('field');
        if ($field == 'id'){
            $variant = Variant::getById($value);
        }else{
            $property = \Models\CatalogManagement\Properties\Factory::getSingleByKey($field);
            if (empty($property)){
                $this->getAns()->addErrorByKey('exception', 'Свойство не найдено ' . $field);
                return;
            }
            $variants = CatalogSearch::factory(self::$catalog['key'])
                ->setRules(array(
                    \Models\CatalogManagement\Rules\Rule::make($property['key'])
                    ->setValue($value)
                    ->setExists(1)
                ))->searchVariants();
            if (count($variants) != 1){
                $this->getAns()->addErrorByKey('error', 'many')->setEmptyContent();
                return;
            }
            $variant = reset($variants);
        }//возможно мы хотим поиск по какому-то другому уникальному свойству
        if (empty($variant)){
            $this->getAns()->addErrorByKey('error', 'not found')->setEmptyContent();
            return;
        }
        $this->getAns()->add('variant', $variant)->add('item', $variant->getItem());
	}
    public function addVariant(){
        $this->setJsonAns('Modules/Order/Admin/order.tpl');
        $variant_id = $this->request->request->get('variant_id');
        $order_id = $this->request->request->get('order_id');
        $count = $this->request->request->get('count');
        /* @var $order OrderEntity */
        $order = OrderEntity::getById($order_id);
        $this->getAns()->add('order', $order);
		$variant = \Models\CatalogManagement\Variant::getById($variant_id);
		if (!empty($variant)){
			$result = $order->addPosition($variant, $count, $error);
            if ($result !== FALSE){
                $this->getAns()->addData('order_id', $order['id'])->addData('added_position', $result['id']);
            }else{
                $this->getAns()->addErrorByKey('add', $error)->setEmptyContent();
                return;
            }
		}else{
            $this->geAns()->addErrorByKey('exception', 'Variant not found')->setEmptyContent();
            return;
		}
    }
    public function delOrderPosition(){
        $this->setJsonAns('Modules/Order/Admin/order.tpl');
        $position_id = $this->request->request->get('position_id');
        $order_id = $this->request->request->get('order_id');
        if (empty($order_id)){
            //если нет заказа, то наш косяк
            $this->getAns()->addErrorByKey('exception', 'Order id empty')->setEmptyContent();
            return;
        }
        $order = OrderEntity::getById($order_id);
        $this->getAns()->add('order', $order);
        if (empty($order)){
            $this->getAns()->addErrorByKey('exception', 'Order not found')->setEmptyContent();
            return;
        }
        $order->removePosition($position_id);
    }
    public function changeCount(){
        $this->setJsonAns('Modules/Order/Admin/order.tpl');
        $position_id = $this->request->request->get('position_id');
        $count = $this->request->request->get('count');
        if (empty($count)){
            $this->getAns()->addErrorByKey('exception', 'Не передано количество')->setEmptyContent();
            return;
        }
        $position = Position::getById($position_id);
        $order = $position->getItem();
        $this->getAns()->add('order', $order);
        $countProp = \Models\CatalogManagement\Properties\Factory::getSingleByKey(\App\Configs\OrderConfig::KEY_POSITION_COUNT, self::$catalog['id']);
        $position->updateValue($countProp['id'], $count, $errors);
        if (!empty($errors)){
            $this->getAns()->setErrors($errors)->setEmptyContent();
            return;
        }
    }
    public function changePrice(){
        $this->setJsonAns('Modules/Order/Admin/order.tpl');
        $position_id = $this->request->request->get('position_id');
        $price = $this->request->request->get('price');
        if (empty($price)){
            $this->getAns()->addErrorByKey('exception', 'Не передана цена')->setEmptyContent();
            return;
        }
        $position = Position::getById($position_id);
        $order = $position->getItem();
        $this->getAns()->add('order', $order);
        $priceProp = \Models\CatalogManagement\Properties\Factory::getSingleByKey(\App\Configs\OrderConfig::KEY_POSITION_PRICE, self::$catalog['id']);
        $position->updateValue($priceProp['id'], $price, $errors);
        if (!empty($errors)){
            $this->getAns()->setErrors($errors)->setEmptyContent();
            return;
        }
    }
    /**
     * отправить письмо пользователю о текущем состоянии заказа
     */
    public function sendEmail(){
        $this->setJsonAns()->setEmptyContent();
        $order_id = $this->request->request->get('id');
        $order = OrderEntity::getById($order_id);
        $mail_ans = new \LPS\Container\WebContentContainer();
        $mail_ans->setTemplate('mails/actual_order_data.tpl');
        $mail_ans->add('order', $order);
        \Models\Email::send($mail_ans,  $order[\App\Configs\OrderConfig::KEY_ORDER_EMAIL], NULL, array(), FALSE, $errors);      
        $this->getAns()->setStatus(empty($errors) ? 'ok' : 'error');
    }
}
