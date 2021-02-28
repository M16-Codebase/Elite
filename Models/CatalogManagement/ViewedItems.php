<?php
namespace Models\CatalogManagement;
/**
 * @TODO Зачем это отдельной моделью?
 */
class ViewedItems{
    
    const LIST_LENGTH = 30;

    private static $instance = null;
    private $viewedItems = array();
    
    public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->viewedItems = \App\Builder::getInstance()->getCurrentSession()->get('viewedItems', array());
    }
    
    public function addItem($item_id){
        $cnt = 1;
        foreach($this->viewedItems as $key => $item){
            if ($item['id'] == $item_id) {
                unset($this->viewedItems[$key]);
                break;
            } else {
                $cnt ++;
            }
            if ($cnt > self::LIST_LENGTH) {
                unset($this->viewedItems[$key]);
            }
        }
        array_unshift($this->viewedItems, array('id' => $item_id, 'time' => time()));
        \App\Builder::getInstance()->getCurrentSession()->set('viewedItems', $this->getList());
    }
    
    public function getList(){
        return $this->viewedItems;
    }
}