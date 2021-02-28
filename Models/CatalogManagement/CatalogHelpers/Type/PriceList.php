<?php
namespace Models\CatalogManagement\CatalogHelpers\Type;
use Models\CatalogManagement\Type;

class PriceList extends TypeHelper{
    protected static $i = NULL;
	private $loadItemsQuery = NULL;
	private $dataCache = array();
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList(){
        return array('price_list');
    }
	/**
     * уведомление, что данные для указанных Types попали в кеш данных и могут быть востребованы
     */
    public function onLoad(Type $type){
		$this->loadItemsQuery[] = $type['id'];
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Type $type, $field, $segment_id = NULL){
        if ($field == 'price_list'){
			if (!isset($this->dataCache[$type['id']]) && !empty($this->loadItemsQuery)){
				$files_data = \Models\CatalogManagement\PriceLists::getFilesData($this->loadItemsQuery);
				$this->dataCache = $this->dataCache + $files_data;
				$this->loadItemsQuery = NULL;
			}
			if (isset($this->dataCache[$type['id']])){
                foreach ($this->dataCache[$type['id']] as $pr => $d){
                    if (!isset($this->dataCache[$type['id']][$pr]['file_exists'])){
                        $this->dataCache[$type['id']][$pr]['file_exists'] = file_exists($this->dataCache[$type['id']][$pr]['real_path']);
                        if (!$this->dataCache[$type['id']][$pr]['file_exists']){
                            unset($this->dataCache[$type['id']][$pr]);//если файл не существует, удаляем нахер из массива
                        }
                    }
                }
			}
			return $this->dataCache[$type['id']];
        }
        return NULL;
    }
}