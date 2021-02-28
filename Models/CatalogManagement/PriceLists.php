<?php
namespace Models\CatalogManagement;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Type;
use Models\ExcelExport;
use Models\CatalogManagement\Search\CatalogSearch;
/**
 * Description of PriceLists
 *
 * @author olya
 */
class PriceLists {
    const TABLE = 'price_list_log';
    const EXT = 'xls';
    const TEMPLATE_PATH = 'data/xlsTemplates/';//путь к шаблону
    const TEMPLATE_NAME_ALL = 'price_list.xls';//название шаблона для всех товаров
    const TEMPLATE_NAME = 'price_list_type.xls';//название шаблона для отдельных типов
    const FILE_DESTINATION_PATH = 'data/price_lists/';//путь, куда складывать прайс листы
    const ITEMS_COUNT_PART = 500;//сколько за раз выгружать из базы товаров
    const ENCODE_FROM = 'utf-8';
    const ENCODE_TO = 'Windows-1251';
    private static $variant_fields = array(
        'id' => 'id',
        CatalogConfig::KEY_VARIANT_TITLE => CatalogConfig::KEY_VARIANT_TITLE,
        CatalogConfig::KEY_ITEM_UNIT => CatalogConfig::KEY_ITEM_UNIT,
        'price' => 'price',
        CatalogConfig::KEY_VARIANT_COUNT => CatalogConfig::KEY_VARIANT_COUNT
    );
    /**
     * по всем товарным направлениям и товарным категориям, 
     * а также общего прайс-листа по всем товарным направлениям для каждого региона.
     */
    public static function generate(){
        \App\Builder::getInstance()->getDB()->query('DELETE FROM `'.self::TABLE.'`');//чистим всё
        $file_name_part = \App\Builder::getInstance()->getConfig()->getParametr('site', 'price_list_site_name');
        $template_path = \LPS\Config::getRealDocumentRoot() . '/' . self::TEMPLATE_PATH;
        if (!file_exists($template_path)){
            \LPS\Components\FS::makeDirs($template_path);
        }
        $price_properties = Properties\Factory::search(Type::DEFAULT_TYPE_ID, Properties\Factory::P_VARIANTS, 'key', 'position', 'self', array('group_key' => CatalogConfig::GROUP_KEY_PRICES));
        $prices = array_keys($price_properties);
        foreach ($prices as $p){
            $price_lists_path = \LPS\Config::getRealDocumentRoot() . '/' . self::FILE_DESTINATION_PATH . $p;
            if (!file_exists($price_lists_path)){
                \LPS\Components\FS::makeDirs($price_lists_path);
            }
            if ($handle = opendir($price_lists_path)) {
                while (false !== ($file = readdir($handle))) { 
                    if ($file != "." && $file != "..") { 
                        @unlink($price_lists_path . '/' . $file);
                    } 
                }
                closedir($handle); 
            }
        }
        $main_type = Type::getById(Type::DEFAULT_TYPE_ID);
        foreach ($prices as $p){
            Catalog::clearAllRegistry();
//            $excel_all = NULL;
            $excel_parents = NULL;
//            $excel_current = NULL;
            $types = $main_type->getChildren();//товарные направления
            foreach ($types as $t){
                $children = $t->getChildren();//запихнули в фабрику, чтобы из вариантов не каждый раз в базу лезло
                $rules = array_merge(array(
                    Rule::make(CatalogConfig::KEY_VARIANT_AVAILABLE)->setValue(array(CatalogConfig::VARIANT_AVAILABLE_VALUE, CatalogConfig::VARIANT_FOR_REQUEST_VALUE)),
                    Rule::make('type_id')->setOrder(0),//по порядку типов
                    Rule::make(CatalogConfig::KEY_VARIANT_TITLE)->setOrder(0)//по названию
                ), \App\CatalogMethods::getVisibleRules());
                $variants = CatalogSearch::factory()->setRules($rules)->searchVariants(0, 1);
                $count = $variants->getTotalCount();
                if (empty($count)){
                    continue;
                }
//                if (is_null($excel_all)){
//                    $excel_all = ExcelExport::factory($template_path . self::TEMPLATE_NAME_ALL, array('type_id' => Type::DEFAULT_TYPE_ID, 'list_name' => self::getValue($t['title']), 'price' => $p['id']));//общий файл
//                }else{
//                    if (!empty($load_data)){
//                        $excel_all->loadData($load_data);
//                    }
//                    $excel_all->addSheet(self::getValue($t['title']));
//                }
                $count_iteration = ceil($variants / self::ITEMS_COUNT_PART);
                for ($i = 1; $i <= $count_iteration; $i++){//чтобы не забивать память, разбиваем на несколько итераций
                    $variants = CatalogSearch::factory()->setRules($rules)->searchVariants(($i-1)*self::ITEMS_COUNT_PART, self::ITEMS_COUNT_PART);
                    $current_type = NULL;
                    $load_data = array();
                    foreach ($variants as $v){
                        $type = $v->getType();
                        //сначала инициализируем документы, если таковых ещё нет
                        if (is_null($excel_parents)){
                            $excel_parents = ExcelExport::factory($template_path . self::TEMPLATE_NAME, array('type_id' => $t['id'], 'list_name' => self::getValue('price_list'), 'price' => $p));//файл для родительского типа
                            $excel_parents->loadData(array('item_title' => self::getValue($type['title'])));//записываем заголовок типа
                        }
//                        if (is_null($excel_current)){
//                            $excel_current = ExcelExport::factory($template_path . self::TEMPLATE_NAME, array('type_id' => $type['id'], 'list_name' => self::getValue($type['title']), 'price' => $p));//файл для конечного типа
//                        }
                        $v_data = array();
                        foreach (self::$variant_fields as $f){
                            if ($f == CatalogConfig::KEY_ITEM_UNIT){
                                $item = $v->getItem();
                                $value = $item[$f];
                            }elseif($f == CatalogConfig::KEY_VARIANT_COUNT){
                                $value =  $v[$f];
                                if (empty($value)){
                                    $value = $v[CatalogConfig::KEY_VARIANT_COUNT_WAIT];
                                }
                            }elseif($f == 'price'){
                                $value = $v[$p];
                                if (empty($value)){
                                    $value = 'По запросу';
                                }
                            }elseif($f == CatalogConfig::KEY_VARIANT_TITLE){
                                $item = $v->getItem();
                                $value = $item[CatalogConfig::KEY_ITEM_TITLE] . ' ' . $v[$f];
                            }else{
                                $value =  $v[$f];
                            }
                            $v_data[$f] = self::getValue($value);
                        }
                        if (is_null($current_type) || $current_type['id'] != $type['id']){//сменился тип
                            if (!is_null($current_type)){//если не только что зашли
//                                $excel_all->loadData($load_data);
                                $excel_parents->loadData($load_data);
//                                $excel_parents->addSheet(self::getValue($type['title']));//следующий тип со следующего листа
                                $excel_parents->loadData(array('item_title' => self::getValue($type['title'])));//записываем заголовок типа
//                                $excel_current->loadData($load_data);
//                                if (!is_null($excel_current)){
//                                    self::saveFile($excel_current, $current_type['id'], $current_type['title'] . ' — ' . $file_name_part, $p);//записываем файл конечного типа
//                                    $excel_current = NULL;//в следующей итерации создастся новый для нового типа
//                                }
                                $load_data = array();
                            }
//                            $excel_all->loadData(array('item_title' => self::getValue($type['title'])));//в общий записываем заголовок типа
                        }
                        $current_type = $type;
                        $load_data['variant'][$v['id']] = $v_data;
                    }
                }
//                if (!is_null($excel_current) && !empty($load_data)){
//                    $excel_current->loadData($load_data);//дозапись остатков
//                    //type возьмется от последней итерации - так и надо!!!
//                    self::saveFile($excel_current, $type['id'], $type['title'] . ' — ' . $file_name_part, $p);//записываем файл конечного типа
//                    $excel_current = NULL;
//                }
                if (!is_null($excel_parents)){
                    if (!empty($load_data)){
                        $excel_parents->loadData($load_data);//дозапись остатков
                    }
                    self::saveFile($excel_parents, $t['id'], $t['title'] . ' — ' . $file_name_part, $p);
                    $excel_parents = NULL;//в следующей итерации создастся новый для нового родительского типа
                }
            }
//            if (!is_null($excel_all)){
//                if (!empty($load_data)){
//                    $excel_all->loadData($load_data);//дозапись остатков
//                }
//                self::saveFile($excel_all, Type::DEFAULT_TYPE_ID, $file_name_part, $p);
//                $excel_all = NULL;//в следующей итерации создастся новый для нового сегмента
//            }
        }
    }
    /**
     * Записывает файл
     * @param \Models\ExcelExport $excel
     * @param string $file_name название файла, которое сохранять
     * @param string $file_title наименование файла, которое будет видно при скачивании
     * @param string $p
     * @return NULL
     */
    private static function saveFile(ExcelExport $excel, $file_name, $file_title, $p){
        $path_to_file = self::FILE_DESTINATION_PATH . $p . '/';
        if (!file_exists($path_to_file)){
            \LPS\Components\FS::makeDirs($path_to_file);
        }
		$full_file_path = $path_to_file . $file_name . '.' . self::EXT;
        $result = $excel->save($full_file_path);
		if (!$result){
			return;
		}
        $date = date('Y-m-d H:i:s');
        $file_title = ucfirst($file_title) . ' — ' . date('d.m.Y', strtotime($date)) . '.' . self::EXT;
        \App\Builder::getInstance()->getDB()->query(''
            . 'REPLACE INTO '.self::TABLE.' '
            . 'SET '
            . '`type_id` = ?d, '
            . '`name` = ?s, '
            . '`price` = ?s, '
            . '`date` = ?s, '
			. '`file_size` = ?s', 
            $file_name, 
            $file_title, 
            $p, 
            $date,
            \Models\FilesManagement\File::f_bafsize(filesize($full_file_path))
        );
        return;
    }
    private static function getValue($v){
        return $v;
//        return mb_convert_encoding($v, self::ENCODE_TO, self::ENCODE_FROM);
    }
	
	public static function getFilesData($type_ids, $p = NULL){
		if (!is_array($type_ids)){
			$type_ids = array($type_ids);
		}
        $types = Type::factory($type_ids);
		$db = \App\Builder::getInstance()->getDB();
		$result = $db->query('SELECT * FROM `'.self::TABLE.'` WHERE `type_id` IN (?i){ AND `price` = ?s}', $type_ids, !empty($p) ? $p : $db->skipIt())->select('type_id', 'price');
		foreach ($result as $type_id => &$data){
            foreach ($data as $pr => &$d){
                $d['real_path'] = self::FILE_DESTINATION_PATH . $pr . '/' . $type_id . '.' . self::EXT;
                $d['download_path'] = '/files/priceList/' . $pr . '/' . urlencode($types[$type_id]['title']) . '-' . $type_id . '.' . self::EXT;
            }
		}
		return $result;
	}
	
	public static function getData($type_id, $p){
		$data = self::getFilesData($type_id, $p);
		return !empty($data[$type_id][$p]) ? $data[$type_id][$p] : NULL;
	}
}