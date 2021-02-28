<?php
/**
 * Description of PropertyComment
 *
 * @author olga
 */
namespace Models\CatalogManagement\CatalogHelpers\Item;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\CatalogHelpers\Item\ItemHelper;
class PropertyComment extends ItemHelper{
    protected static $i = NULL;
	const TABLE = 'item_property_comments';
	const FIELD_NAME = 'property_comments';
	private $dataCache = array();
    private static $additional_data = array();
    private $loadItemsQuery = array();
    protected static $fieldsList = array(self::FIELD_NAME);
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Item $i, $field){
		if (!in_array($field, $this->fieldsList())){
			throw new LogicException('Неверное название дополнительного поля: ' . $field);
		}
		$this->loadData();
		if (!empty($this->dataCache[$i['id']])){
			return $this->dataCache[$i['id']];
		}
        return NULL;
    }
    /**
     * предупреждение, что данные для указанных Items попали в кеш данных чтобы можно было подготовить доп. данные
     * @param \Models\CatalogManagement\Item $i
     * @param array $propertiesBySegments свойства по сегментам
     */
    public function onLoad(Item $i, $propertiesBySegments = NULL){
        $this->loadItemsQuery[$i['id']] = $i['id'];
    }
    
    private function loadData(){
        if (empty ($this->loadItemsQuery)){
            return;
        }
		$db = \App\Builder::getInstance()->getDB();
        $comments = $db->query('SELECT * FROM `'.static::TABLE.'` WHERE `item_id` IN (?i)', $this->loadItemsQuery)->getCol(array('item_id', 'property_id', 'segment_id'), 'comment');
		if (!empty($comments)){
			$this->dataCache = $this->dataCache + $comments;
		}
        $this->loadItemsQuery = array();
    }
    
    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors){
		if (array_key_exists(self::FIELD_NAME, $params)){
			self::$additional_data[$item['id']][self::FIELD_NAME] = $params[self::FIELD_NAME];
			self::$additional_data[$item['id']]['old_' . self::FIELD_NAME] = $item[self::FIELD_NAME];
			unset($params[self::FIELD_NAME]);
		}
    }

    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties){
        $id = $item['id'];
		if (!empty(self::$additional_data[$item['id']][self::FIELD_NAME])){
			$db = \App\Builder::getInstance()->getDB();
			$old_data = self::$additional_data[$item['id']]['old_' . self::FIELD_NAME];
			if ($old_data != self::$additional_data[$item['id']][self::FIELD_NAME]){
				foreach (self::$additional_data[$item['id']][self::FIELD_NAME] as $p_id => $data){
					if (!empty($data) && (empty($old_data) || empty($old_data[$p_id]) || $data != $old_data[$p_id])){
						foreach ($data as $s_id => $comment){
							if (empty($old_data[$p_id][$s_id]) || $comment != $old_data[$p_id][$s_id]){
								$db->query('REPLACE INTO `'.self::TABLE.'` SET `item_id` = ?d, `property_id` = ?d, `segment_id` = ?d, `comment` = ?s', $id, $p_id, !empty($s_id) ? $s_id : 0, $comment);
							}
						}
					}
				}
			}
            unset(self::$additional_data[$item['id']]);
		}
    }
}
?>