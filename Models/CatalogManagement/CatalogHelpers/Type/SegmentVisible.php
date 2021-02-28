<?php
namespace Models\CatalogManagement\CatalogHelpers\Type;
use Models\CatalogManagement\Type;
use App\Configs\CatalogConfig;

class SegmentVisible extends TypeHelper{
    protected static $i = NULL;
    private $loadItemsQuery = array();
     private $dataCache = array();
    const TABLE_SEGMENT_VISIBLE = 'type_segment_visible';
//    const VISIBLE_ANY = CatalogConfig::VALUE_VISIBLE_ANY;
//    const VISIBLE_NONE = CatalogConfig::VALUE_VISIBLE_NONE;
//    const VISIBLE_SITE = CatalogConfig::VALUE_VISIBLE_SITE;
//    const VISIBLE_EXPORT = CatalogConfig::VALUE_VISIBLE_EXPORT;
//    private static $visible = array(self::VISIBLE_ANY, self::VISIBLE_NONE, self::VISIBLE_SITE, self::VISIBLE_EXPORT);
//	const DEFAULT_VISIBLE = self::VISIBLE_ANY;
    /**
     * @return static
     */
    public static function factory($data = NULL){
        return parent::factory($data);
    }
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
//    public function fieldsList(){
//        return array('visible', 'all_visible');
//    }
//    /**
//     * возвращает значение дополнительного поля
//     */
//    public function get(Type $type, $field){
//        if ($field=='visible' || $field == 'all_visible'){
//            if (!isset ($this->dataCache[$type['id']])){
//                $this->loadData();
//            }
//            if ($field == 'visible'){
//                $segment = \App\Segment::getInstance()->getDefaultSegment();
//                $segment_id = $segment['id'];
//                return !empty($this->dataCache[$type['id']][$segment_id]) ? $this->dataCache[$type['id']][$segment_id] : array();
//            }else{
//                return !empty($this->dataCache[$type['id']]) ? $this->dataCache[$type['id']] : array();
//            }
//        }
//        return NULL;
//    }
//    /**
//     * уведомление, что данные для указанных Types попали в кеш данных и могут быть востребованы
//     */
//    public function onLoad(Type $type){
//        $this->loadItemsQuery[$type['id']] = $type['id'];
//    }
//
//    /**
//     * Вся видимость по сегментам для всех зарегистрированных типов
//     * @return array видимость данного типа по сегментам
//     */
//    public function loadData(){
//        $db = \App\Builder::getInstance()->getDB();
//		if (!empty($this->loadItemsQuery)){
//			$this->dataCache += $db->query(
//					'SELECT `type_id`, `segment_id`, `visible` FROM `'.self::TABLE_SEGMENT_VISIBLE.'`
//						WHERE `type_id` IN (?i)',
//					$this->loadItemsQuery
//			)->getCol(array('type_id', 'segment_id'), 'visible');
//		}
//        $this->loadItemsQuery = array(); // конечные данные в кеше, так что чистим очередь
//    }
//
//    public function onCreate(Type $type, $params){
//        $db = \App\Builder::getInstance()->getDB();
//        //видимость по сегментам
//		$segments = \App\Segment::getInstance()->getSegments();
//		$segments[0] = NULL;
//		foreach ($segments as $s_id => $s){
//			$db->query('INSERT INTO `'.self::TABLE_SEGMENT_VISIBLE.'` SET `type_id` = ?d, `segment_id` = ?d, `visible` = ?', $type['id'], $s_id, self::DEFAULT_VISIBLE);
//		}
//        return $id;
//    }
//    /**
//     *
//     * @param int $id type_id
//     */
//    public function onDelete($id){
//        $db = \App\Builder::getInstance()->getDB();
//        $db->query('DELETE FROM `'.self::TABLE_SEGMENT_VISIBLE.'` WHERE `type_id` = ?d', $id);
//    }
//
//    /**
//     * поменять видимость типа в сегменте
//     * @param int $segment_id
//     * @param string $visible
//     * @return bool
//     */
//    public function changeSegmentVisible($type_id, $segment_id, $visible){
//        $this->all_visible = NULL;
//        if (!in_array($visible, self::$visible)){
//            throw new \LogicException('Visible ' . $visible . ' not allow');
//        }
//        return \App\Builder::getInstance()->getDB()->query('REPLACE `'.self::TABLE_SEGMENT_VISIBLE.'` SET `visible` = ?s, `type_id` = ?d, `segment_id` = ?d', $visible, $type_id, $segment_id);
//    }
//    /**
//     * Проверка видимости родительских типов
//     * @param array $visible список значений видимости. родительские типы должны иметь видимость из данного списка
//     * @param int $segment_id
//     * @return boolean
//     */
//    public function checkVisible(Type $type, $visible, $status = NULL, $segment_id = NULL){
//        if (is_null($segment_id)){
//            $segment = \App\Segment::getInstance()->getDefaultSegment();
//            $segment_id = !empty($segment) ? $segment['id'] : 0;
//        }
//        if (!is_array($visible)){
//            $visible = array($visible);
//        }
//        if (!is_array($status) && !is_null($status)){
//            $status = array($status);
//        }
//        if (empty($type['all_visible'][$segment_id]) || !in_array($type['all_visible'][$segment_id], $visible) || (!is_null($status) && !in_array($type['status'], $status))){
//            //если сам тип не должно быть видно
//            return FALSE;
//        }
//        $parents = $type->getParents();
//        foreach ($parents as $p){
//            /* @var $p Type */
//            $parent_visible = !empty($p['all_visible'][$segment_id]) ? $p['all_visible'][$segment_id] : self::VISIBLE_NONE;
//            if (!in_array($parent_visible, $visible) || (!is_null($status) && !in_array($p['status'], $status))){
//                //если хоть одного родителя не должно быть видно
//                return FALSE;
//            }
//        }
//        return TRUE;
//    }
//
//    public static function onSegmentCreate($segment_id){
//		$types = Type::search();
//		$db = \App\Builder::getInstance()->getDB();
//		foreach ($types as $t){
//			$db->query('INSERT INTO `'.self::TABLE_SEGMENT_VISIBLE.'` SET `type_id` = ?d, `segment_id` = ?d, `visible` = ?', $t['id'], $segment_id, self::DEFAULT_VISIBLE);
//		}
//	}
//    
//    public static function getAllVisibles(){
//        return self::$visible;
//    }
}
?>