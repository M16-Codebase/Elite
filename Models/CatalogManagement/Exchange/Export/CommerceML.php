<?php
/**
 * Description of CML
 *
 * @author olga
 */
namespace Models\CatalogManagement\Exchange\Export;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\Properties;
use Models\CatalogManagement\Properties\Property;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Variant;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Rule;
class CommerceML {
    const FILE_PATH = '/data/exchange/export/commerceML/';
    const FILE_EXT = 'xml';
    const SCHEMA_VERSION = '2.05';
    const LIMIT_ITEMS = '500';

    const NAME = 'КоммерческаяИнформация';
    const NAME_CLASS = 'Классификатор';
    const NAME_CATALOG = 'Каталог';

    const NAME_ID = 'Ид';
    const NAME_TITLE = 'Наименование';
    const NAME_DESCRIPTION = 'Описание';
    const NAME_CODE = 'НоменклатурныйНомер';
    const NAME_URL = 'Ссылка';
    const NAME_TYPE_ID = 'ИдГруппы';

    const NAME_GROUPS = 'Группы';
    const NAME_GROUP = 'Группа';
    const NAME_PROPS = 'Свойства';
    const NAME_PROP = 'Свойство';

    const NAME_PROP_KEY = 'Ключ';
    const NAME_PROP_NESS = 'Обязательное';
    const NAME_PROP_SET = 'Множественное';
    const NAME_PROP_DATA_TYPE = 'ТипЗначений';

    const DATA_TYPE_STRING = 'Строка';
    const DATA_TYPE_NUMERIC = 'Число';
    const DATA_TYPE_DATE = 'Время';
    const DATA_TYPE_ENUM = 'Справочник';
    private static $relation_data_types = array(
        Properties\String::TYPE_NAME => self::DATA_TYPE_STRING,
        Properties\View::TYPE_NAME => self::DATA_TYPE_STRING,
        Properties\Int::TYPE_NAME => self::DATA_TYPE_NUMERIC,
        Properties\Float::TYPE_NAME => self::DATA_TYPE_NUMERIC,
        Properties\Flag::TYPE_NAME => self::DATA_TYPE_STRING,
        Properties\Enum::TYPE_NAME => self::DATA_TYPE_ENUM
    );

    const NAME_PROP_VARIANT_VALUES = 'ВариантыЗначений';
    const NAME_PROP_ENUM = 'Справочник';
    const NAME_PROP_ENUM_VALUE_ID = 'ИдЗначения';
    const NAME_PROP_VALUE = 'Значение';

    const NAME_ITEMS = 'Товары';
    const NAME_ITEM = 'Товар';

	const NAME_ITEM_ID = 'КарточкаТовара';
    const NAME_ARTICUL = 'Артикул';
    const NAME_IMAGE = 'Картинка';

    const NAME_ITEM_VALUES = 'ЗначенияСвойств';
    const NAME_ITEM_VALUE = 'ЗначенияСвойства';

	/**
	 *
	 * @var \SimpleXMLElement
	 */
    private $xml = NULL;
	/**
	 * ID региона
	 * @var integer
	 */
    private $segment_id = NULL;
    private $segment = NULL;
    private static $allow_visible = array(CatalogConfig::VALUE_VISIBLE_ANY, CatalogConfig::VALUE_VISIBLE_EXPORT);
    private static $allow_type_statuses = array(Type::STATUS_VISIBLE, Type::STATUS_HIDDEN);
    private static $allow_item_statuses = array(Item::S_PUBLIC, Item::S_HIDE);
    private static $allow_variant_statuses = array(Variant::S_PUBLIC, Variant::S_HIDE);
	/**
	 * Все типы, разбитые по родителям
	 * @var array
	 */
    private $typesByParents = NULL;
	/**
	 * Экспортируемые типы
	 * @var type
	 */
    private $type_ids = array();
    private $items_count = 0;
    private $variants_count = 0;
	/**
	 * Время с начала формирования файла
	 */
	private $exportTime = NULL;
    /**
     * Наименование файла
     */
    private $file_name = NULL;
    /**
     * Экземпляр класса
     * @var CommerceML
     */
    static protected $instance = null;
    /**
     * @return CommerceML
     */
    public static function factory($reg_id){
		if (is_null($reg_id)){
			throw new \LogicException('Region id not found');
		}
        if (empty (self::$instance[$reg_id])){
            self::$instance[$reg_id] = new CommerceML($reg_id);
        }
        return self::$instance[$reg_id];
    }
    private function __construct($segment_id){
        $this->exportTime = time();
        $this->xml = new \SimpleXMLElement($this->getStartString());
        $this->segment_id = $segment_id;
        $this->segment = \App\Builder::getInstance()->getSegment($this->segment_id);
        $this->file_name = \LPS\Config::getRealDocumentRoot() . self::FILE_PATH . $this->segment_id . '.' . self::FILE_EXT;
    }
	/**
	 *
	 * @return boolean
	 */
    public function export(){
        if (!file_exists($this->file_name)){
            $this->formData();
            $this->saveFile();
            return TRUE;
        }
        return FALSE;
    }
	/**
	 *
	 */
    private function formData(){
        //Типы
        \Models\CatalogManagement\CatalogHelpers\Type\SegmentVisible::factory();
        \Models\CatalogManagement\CatalogHelpers\Type\Code::factory();
        $def_type = Type::getById(Type::DEFAULT_TYPE_ID);
        $this->typesByParents = $def_type->getAllChildren(self::$allow_type_statuses);
		$xml_classifier = $this->xml->Классификатор;
		$xml_groups = $xml_classifier->addChild(self::NAME_GROUPS);
        $this->formType($xml_groups, $this->typesByParents[Type::DEFAULT_TYPE_ID]);
        $type_properties = $def_type['properties'];
        if (!empty($type_properties)){
            $xml_props = $xml_classifier->addChild(self::NAME_PROPS);
            foreach ($type_properties as $p){//все сквозные свойства
                $this->formProperty($xml_props, $p);
            }
        }
        //Товары
        $xml_catalog = $this->xml->Каталог;
        \Models\CatalogManagement\CatalogHelpers\Variant\Code::factory();
        $rules = array(
            Rule::make('status')->setValue(self::$allow_item_statuses),
            Rule::make('variant.last_update')->setMin($this->segment['exportCommerceML_last_update']),
            Rule::make('variant.status')->setValue(self::$allow_variant_statuses),
            Rule::make(CatalogConfig::KEY_VARIANT_VISIBLE)->setValue(self::$allow_visible)->setSearchByEnumValue(),
            Rule::make(CatalogConfig::KEY_ITEM_VISIBLE)->setValue(self::$allow_visible)->setSearchByEnumValue()
        );
        $items = \Models\CatalogManagement\Search\CatalogSearch::factory($this->segment_id)
            ->setTypeId($this->type_ids)
            ->setRules($rules)
            ->setPublicOnly(FALSE)
            ->searchItems(0, 10000000);
        if (empty($items->getTotalCount())){
            $this->xml = NULL;
            return;
        }
        $this->items_count = $items->getTotalCount();
        $variant_ids = array();
        foreach ($items as $item_id=>$item){
            $variant_ids = array_merge($variant_ids, $items->getFoundVariantIdsByItem($item_id));
        }
        $variants = Variant::factory($variant_ids);
        $this->variants_count = count($variant_ids);
        if (!empty($variants)){
            $xml_items = $xml_catalog->addChild(self::NAME_ITEMS);
            foreach ($variants as $v){
                $this->formVariant($xml_items, $v, $items[$v['item_id']]);
            }
        }
    }
    /**
     * Формирует группы(типы) рекурсивно пробегая по всему дереву групп(типов)
     * @param \SimpleXMLElement $xml_groups
     * @param type $level_types
     */
    private function formType(\SimpleXMLElement $xml_groups, $level_types){
        $domainName = strpos(\LPS\Config::getRealDocumentRoot(), 'dev.') !== FALSE ? \LPS\Config::DEV_DOMAIN_NAME : \LPS\Config::DOMAIN_NAME;
        $segment_helper = \Models\CatalogManagement\CatalogHelpers\Type\SegmentVisible::factory();
        foreach ($level_types as $type){
            if (!$segment_helper->checkVisible($type, self::$allow_visible, self::$allow_type_statuses, $this->segment_id)){
                continue;
            }
            $xml_child = $xml_groups->addChild(self::NAME_GROUP);
            $xml_child->addChild(self::NAME_ID, $type['id']);
            $xml_child->addChild(self::NAME_TITLE, htmlspecialchars($type['title']));
            $xml_child->addChild(self::NAME_CODE, $type['full_code']);
            
//            $xml_child->addChild(self::NAME_URL, 'http://' . $domainName . '/catalog-type/?id=' . $type['id']);
            $xml_props = $xml_child->addChild(self::NAME_PROPS);
            foreach ($type['properties'] as $p){
                //нам нужны свойства только данного конкретного типа (а в массиве все наследуемые свойства)
                if ($p['type_id'] == $type['id']){
                    $this->formProperty($xml_props, $p);
                }
            }
            if ($type['allow_children'] == 1 && !empty($this->typesByParents[$type['id']])){
                $xml_child_group = $xml_child->addChild(self::NAME_GROUPS);
                $this->formType($xml_child_group, $this->typesByParents[$type['id']]);
            }elseif($type['allow_children'] != 1){
                $this->type_ids += array($type['id'] => $type['id']);
            }
        }
    }
	/**
	 *
	 * @param \SimpleXMLElement $xml_props
	 * @param Property $property
	 */
    private function formProperty(\SimpleXMLElement $xml_props, Property $property){
        if ($property['visible'] & CatalogConfig::V_EXPORT != 0){// нам нужны только свойства для экспорта
            return true;
        }
        $xml_child = $xml_props->addChild(self::NAME_PROP);
        $xml_child->addChild(self::NAME_ID, $property['id']);
        $xml_child->addChild(self::NAME_PROP_KEY, $property['key']);
        $name_title = self::NAME_TITLE;
        $xml_child->addChild($name_title);
        $xml_child->$name_title = $property['title'];
        $xml_child->addChild(self::NAME_PROP_NESS, $property['necessary'] == 1 ? 'true' : 'false');
        $xml_child->addChild(self::NAME_PROP_SET, $property['set'] == 1 ? 'true' : 'false');
        $xml_child->addChild(self::NAME_PROP_DATA_TYPE, self::$relation_data_types[$property['data_type']]);
        if ($property['data_type'] == Properties\Enum::TYPE_NAME){
            $xml_values = $xml_child->addChild(self::NAME_PROP_VARIANT_VALUES);
            $enum_values = $property['values'];
            if (!empty($enum_values)){//enum значения
                foreach ($enum_values as $e_id => $ev){
                    $xml_enum = $xml_values->addChild(self::NAME_PROP_ENUM);
                    $xml_enum->addChild(self::NAME_PROP_ENUM_VALUE_ID, $e_id);
                    /* @var $value \SimpleXMLElement */
                    $name_value = self::NAME_PROP_VALUE;
                    $xml_enum->addChild($name_value);//обход бага с экранированием '&'
                    $xml_enum->$name_value = $ev['value'];
                }
            }
        }
//        @TODO "ИспользованиеСвойства"?
        return true;
    }
    /**
	 *
	 * @param \SimpleXMLElement $xml_items
	 * @param \Models\CatalogManagement\Item $item
	 */
    private function formVariant(\SimpleXMLElement $xml_items, Variant $variant, Item $item){
        $domainName = strpos(\LPS\Config::getRealDocumentRoot(), 'dev.') !== FALSE ? \LPS\Config::DEV_DOMAIN_NAME : \LPS\Config::DOMAIN_NAME;
        $xml_variant = $xml_items->addChild(self::NAME_ITEM);
        $xml_variant->addChild(self::NAME_ID, $variant['id']);//id варианта
        $xml_variant->addChild(self::NAME_ITEM_ID, $item['id']);
        $xml_variant->addChild(self::NAME_TYPE_ID, $item['type_id']);
        $name_title = self::NAME_TITLE;
        $xml_variant->addChild($name_title);//наименование
        $xml_variant->$name_title = !empty($variant[CatalogConfig::KEY_VARIANT_TITLE]) ? $variant[CatalogConfig::KEY_VARIANT_TITLE] : '';
        $xml_variant->addChild(self::NAME_CODE, $variant['code']);//номенклатурный номер
//        $name_url = self::NAME_URL;
//        $xml_variant->addChild($name_url);//обход бага с экранированием '&'
//        $xml_variant->$name_url = 'http://' . $domainName . '/catalog-view/?id=' . $item['id'] . '&var=' . $variant['id'] . '#view-variants';
        //описание
//                if (!empty($item['post'])){
//                    $xml_variant->addChild(self::NAME_DESCRIPTION, $item['post']['text']);
//                }
        //картинки
        $gallery = $item['gallery'];
        if (!empty($gallery)){
            /* @var $gallery \Models\CatalogManagement\ItemImageCollection */
            $images = $gallery->getImages();
            foreach ($images as $img){
                /* @var $img \Models\ImageManagement\CollectionImage */
                $xml_variant->addChild(self::NAME_IMAGE, 'http://' . $domainName . $img->getUrl());
            }
        }
        //значения свойств
        $xml_values = $xml_variant->addChild(self::NAME_ITEM_VALUES);
        $item_properties = $item->getPropertyList('id', PropertyFactory::P_EXPORT);
        $variant_properties = PropertyFactory::search($item['type_id'], PropertyFactory::P_VARIANTS | PropertyFactory::P_EXPORT);
        $item_properties_values = $item->getSegmentProperties($this->segment_id);
        $variant_properties_values = $variant->getSegmentProperties($this->segment_id);
        foreach ($item_properties as $ip){
            if (!empty($item_properties_values[$ip['key']])){
                $this->formValue($xml_values, $ip, $item_properties_values[$ip['key']]['value']);
            }
        }
        foreach ($variant_properties as $vp){
            if (!empty($variant_properties_values[$vp['key']])){
                $this->formValue(
                        $xml_values, 
                        $vp, 
                        $variant_properties_values[$vp['key']]['value']
                    );
            }
        }
    }
	/**
	 * Формирование значения свойства
	 * @param \SimpleXMLElement $xml_values
	 * @param Property $property
	 * @param mixed $value
	 */
    private function formValue(\SimpleXMLElement $xml_values, Property $property, $value){
        $xml_val = $xml_values->addChild(self::NAME_ITEM_VALUE);
        $xml_val->addChild(self::NAME_ID, $property['id']);
		if (!is_array($value)){
			$value = array($value);
		}
		foreach ($value as $v){
			$xml_val->addChild(self::NAME_PROP_VALUE, htmlspecialchars($v));//обход бага с экранированием '&'
		}
    }
	/**
	 *
	 * @return boolean
	 */
    private function saveFile(){
        if (!empty($this->xml)){
            if (!file_exists(\LPS\Config::getRealDocumentRoot() . self::FILE_PATH)){
                \LPS\Components\FS::makeDirs(\LPS\Config::getRealDocumentRoot() . self::FILE_PATH);
            }
            if (file_put_contents($this->file_name . '_tmp', $this->xml->asXML())){
                rename($this->file_name . '_tmp', $this->file_name);
                chmod($this->file_name, 0777);
                return TRUE;
            }
        }
    }
	/**
	 *
	 * @return string
	 */
    private function getStartString(){
        $xmlstr = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlstr .= '
            <КоммерческаяИнформация ВерсияСхемы="'.self::SCHEMA_VERSION.'" ДатаФормирования="'.date('Y-m-d', $this->exportTime).'" ВремяФормирования="'.date('H:i:s', $this->exportTime).'">
                <Классификатор>
                    <Наименование>Классификатор (Каталог товаров)</Наименование>
                </Классификатор>
                <Каталог></Каталог>
            </КоммерческаяИнформация>';
		return $xmlstr;
    }
}

?>
