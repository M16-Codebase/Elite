<?php
/**
 * Простой паттерн делегирования на \Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValueComment
 *
 * @author olga
 */
namespace Models\CatalogManagement\CatalogHelpers\Variant;
use Models\CatalogManagement\Variant;
use Models\CatalogManagement\CatalogHelpers\Interfaces\iVariantDataProvider;
use Models\CatalogManagement\Properties\Property;
class GroupComment extends VariantHelper{
	const TABLE = 'variant_group_comments';
	const FIELD_NAME = 'group_comments';
    protected static $i = NULL;
	private $dataCache = array();
    private $loadItemsQuery = array();
    private static $additional_data = array();
    protected static $fieldsList = array(self::FIELD_NAME);
    /**
     * возвращает значение дополнительного поля
     */
    public function get(Variant $v, $field){
        if (!in_array($field, $this->fieldsList())){
			throw new LogicException('Неверное название дополнительного поля: ' . $field);
		}
		$this->loadData();
		if (!empty($this->dataCache[$v['id']])){
			return $this->dataCache[$v['id']];
		}
        return NULL;
    }
    /**
     * предупреждение, что данные для указанных Variants попали в кеш данных чтобы можно было подготовить доп. данные
     * @param \Models\CatalogManagement\Variant $v
     * @param array $propertiesBySegments свойства по сегментам
     */
    public function onLoad(Variant $v, $propertiesBySegments = NULL){
		$this->loadItemsQuery[$v['id']] = $v['id'];
    }
    private function loadData(){
		if (empty ($this->loadItemsQuery)){
            return;
        }
		$db = \App\Builder::getInstance()->getDB();
        $comments = $db->query('SELECT * FROM `'.static::TABLE.'` WHERE `variant_id` IN (?i)', $this->loadItemsQuery)->getCol(array('variant_id', 'group_id', 'segment_id'), 'comment');
		if (!empty($comments)){
			$this->dataCache = $this->dataCache + $comments;
		}
        $this->loadItemsQuery = array();
    }

    public function onUpdate($updateKey, Variant $variant, $segment_id, $updatedProperties){
        $id = $variant['id'];
		if (!empty(self::$additional_data[$id][self::FIELD_NAME])){
			$db = \App\Builder::getInstance()->getDB();
			$old_data = self::$additional_data[$id]['old_' . self::FIELD_NAME];
			if ($old_data != self::$additional_data[$id][self::FIELD_NAME]){
				foreach (self::$additional_data[$id][self::FIELD_NAME] as $g_id => $data){
					if (!empty($data) && (empty($old_data[$g_id]) || $data != $old_data[$g_id])){
						foreach ($data as $s_id => $comment){
							if (empty($old_data[$g_id][$s_id]) || $comment != $old_data[$g_id][$s_id]){
								$db->query('REPLACE INTO `'.self::TABLE.'` SET `variant_id` = ?d, `group_id` = ?d, `segment_id` = ?d, `comment` = ?s', $id, $g_id, !empty($s_id) ? $s_id : 0, $comment);
							}
						}
					}
				}
			}
            unset(self::$additional_data[$id]);
		}
    }

    public function preUpdate($updateKey, Variant $variant, &$params, &$properties, $segment_id, &$errors){
		if (!empty($params)){
			if (array_key_exists(self::FIELD_NAME, $params)){
                $id = $variant['id'];
				self::$additional_data[$id][self::FIELD_NAME] = $params[self::FIELD_NAME];
				self::$additional_data[$id]['old_' . self::FIELD_NAME] = $variant[self::FIELD_NAME];
				unset($params[self::FIELD_NAME]);
			}
		}
    }
}