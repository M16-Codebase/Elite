<?php
namespace Modules\CliModules;

use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
/**
 * Инициализация проекта, всё, что требуется для начальной установки
 *
 * @author olya
 */
class Init extends \LPS\Module{
    public function index(){
        $this->initSegments();
        $this->createCatalogs();
    }
    /**
     * хотим иметь возможность отдельно загружать только каталоги
     */
    public function createCatalogs($data = NULL){
        if (is_null($data)){
            $initFiles = glob(\LPS\Config::getRealDocumentRoot() . 'App/Configs/Init/Catalog/*.php');
            $data = array();
            foreach ($initFiles as $f){
                $init_class = '\App\Configs\Init\Catalog\\' . basename($f, '.php');
                $data += $init_class::getInitData();
            }
        }
        if (empty($data)){
            return;
        }
        ksort($data);
        echo 'Конфигурировние каталогов' . "\n";
        $this->catalogInit($data);
    }
    /**
     * Инициализация сегментов сайта
     */
    private function initSegments() {
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_NONE) {
            return;
        }
        $segment_controller = \App\Segment::getInstance();
        $segments = $segment_controller->getAll();
        echo 'Инициализация сегментов' . PHP_EOL;
        if (empty($segments)) {
            $segments_data = \App\Configs\Init\SegmentInit::getInitData();
            foreach($segments_data as $seg) {
                $e = array();
                $segment_controller->create($seg['key'], $seg['title'], $e);
                echo "\t" . (empty($e) ? "Сегмент «${seg['title']}» создан" : "Ошибка создания сегмента «${seg['title']}» — " . var_export($e, true)) . PHP_EOL;
            }
        }
    }
    /**
     * Инициализация каталогов
     * Если каталог\тип уже существует, то он не будет создан,
     * и так же не будут созданы его свойства
     * @param array $data
     */
    private function catalogInit($data, $parent_id = \Models\CatalogManagement\Type::DEFAULT_TYPE_ID, $innerLevel = 1){
        if (empty($data)){
            return;
        }
        $keys = array();
        foreach ($data as $d){
            $keys[] = $d['data']['key'];
        }
        $exists_types = array();
        if (!empty($keys)){
            $exists_types = $this->db->query('SELECT `key`, `id` FROM `'.\Models\CatalogManagement\Type::TABLE.'` WHERE `parent_id` = ?d AND `key` IN (?i)', $parent_id, array_keys($keys))->getCol('key', 'id');
        }
        foreach ($data as $c){
            if (!empty($exists_types[$c['data']['key']])){
                echo str_repeat("\t", $innerLevel) . (is_array($c['data']['title']) ? reset($c['data']['title']) : $c['data']['title']) . ' - категория уже существует' . "\n";
                $type_id = $exists_types[$c['data']['key']];
                $this->typeRecheck($type_id, $c, $innerLevel);
            }else{
                $type_id = $this->typeInit($c);
            }
            if (!empty($c['types'])){
                foreach ($c['types'] as $k => $t_data){
                    $c['types'][$k]['data']['parent_id'] = $type_id;
                }
                $this->catalogInit($c['types'], $type_id, $innerLevel+1);
            }
        }
    }
    /**
     * Подготавливаем значения полей категорий и пропертей к сохранению с учетом настроек сегмента
     * Если у нас многоязычный сайт — переделываем массив значений с ключей на айдишники сегментов
     * @param string|array $value
     * @return string|array
     */
    private function prepareValue($value){
        if (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_LANGUAGE) {
            return $value;
        } else {
            $prepared_value = array();
            $segments = \App\Segment::getInstance()->getAll();
            if (is_array($value)) {
                foreach($segments as $seg) {
                    $prepared_value[$seg['id']] = $value[$seg['key']];
                }
            } else {
                /**
                 * @TODO пока что размножаем одно и то же значение на сегменты, потом переделать на исключение
                 */
                foreach($segments as $seg) {
                    $prepared_value[$seg['id']] = $value;
                }
            }
            return $prepared_value;
        }
    }
    /**
     * Само создание каталога\типа
     * @param array $c
     * @return int
     */
    private function typeInit($c){
        if (strlen($c['data']['key']) > \App\Configs\SeoConfig::ENTITY_KEY_MAX_LENGTH){
            $error_msg = 'Ошибка инициализации категории "' . (is_array($c['data']['title']) ? reset($c['data']['title']) : $c['data']['title']) . '"' . PHP_EOL .
                'Ключ #' . $c['data']['key'] . ' слишком длинный, максимально допустимая длина — ' . \App\Configs\SeoConfig::ENTITY_KEY_MAX_LENGTH;
            echo $error_msg . PHP_EOL;
            throw new \LogicException($error_msg);
        }
        $obj_titles = NULL;
        if (!empty($c['item_title'])){
            $word_cases = \LPS\Components\FormatString::wordCases($c['item_title'], $error);
            if (empty($error) && !empty($word_cases)){
                foreach ($word_cases as $case => $d){
                    $obj_titles['i'][1][$case] = $d[1];
                    $obj_titles['i'][2][$case] = $d[2];
                }
            }
        }
        if (!empty($c['variant_title'])){
            $word_cases = \LPS\Components\FormatString::wordCases($c['variant_title'], $error);
            if (empty($error) && !empty($word_cases)){
                foreach ($word_cases as $case => $d){
                    $obj_titles['v'][1][$case] = $d[1];
                    $obj_titles['v'][2][$case] = $d[2];
                }
            }
        }
        if (!empty($obj_titles)){
            $c['data']['word_cases'] = $obj_titles;
        }
        // Инициализация кустика
        if (!empty($c['data']['nested_in']) && !empty($c['data']['parent_id']) && $c['data']['parent_id'] != \Models\CatalogManagement\Type::DEFAULT_TYPE_ID) {
            $nested_in_type = \Models\CatalogManagement\Type::getByKey($c['data']['nested_in'], $c['data']['parent_id']);
            if (empty($nested_in_type)) {
                throw new \ErrorException('Не удалось найти категорию родительских айтемов');
            }
            $c['data']['nested_in'] = $nested_in_type['id'];
        }
        $c['data']['title'] = $this->prepareValue($c['data']['title']);
        if (!empty($c['groups'])) {
            foreach($c['groups'] as $id => $group_data) {
                $c['groups'][$id]['title'] = $this->prepareValue($c['groups'][$id]['title']);
            }
        }
        if (!empty($c['properties'])) {
            foreach($c['properties'] as $id => &$prop_data) {
                $this->preparePropertyData($prop_data);
            }
        }
        $type = \Models\CatalogManagement\Type::create(
            $c['data'], 
            !empty($c['properties']) ? $c['properties'] : array(),
            $errors,
            NULL,
            !empty($c['groups']) ? $c['groups'] : array()
        );
        $type->update($c['data'], $err);
        echo str_repeat("\t", count($type['parents'])) . (is_array($c['data']['title']) ? reset($c['data']['title']) : $c['data']['title']) . ' - категория создана' . "\n";
        return $type['id'];
    }
    /**
     * Создание свойств категории
     * @param int $type_id
     * @param array $typeData
     * @param int $innerLevel
     * @return void
     */
    private function typeRecheck($type_id, $typeData, $innerLevel){
        if (empty($typeData['properties'])){
            return;
        }
        $prop_keys = array();
        foreach ($typeData['properties'] as $propData){
            $prop_keys[] = $propData['key'];
        }
        $type = \Models\CatalogManagement\Type::getById($type_id);
        $groups = $type->getGroups();
        $groups_by_key = array();
        if (!empty($groups)) {
            foreach($groups as $g) {
                $groups_by_key[$g['key']] = $g['id'];
            }
        }
        if (!empty($typeData['groups'])) {
            foreach($typeData['groups'] as $id => $group_data) {
                if (!empty($groups_by_key[$group_data['key']])) {
                    continue;
                }
                $e = null;
                $group_id = $type->addGroup($this->prepareValue($group_data['title']), $group_data['key'], $e);
                if (empty($e)) {
                    $groups_by_key[$group_data['key']] = $group_id;
                } else {
                    throw new \ErrorException('Не удалось создать группу ' . var_export($group_data, true) . ' ' . var_export($e, true));
                }
            }
        }
        $exists_props = PropertyFactory::search($type_id, PropertyFactory::P_ALL, 'key', 'position', 'self', array('key' => $prop_keys));
        foreach ($typeData['properties'] as $propData){
            $e = NULL;
            if (array_key_exists($propData['key'], $exists_props)){
                continue;
            }
            if (!empty($propData['group_key'])) {
                $propData['group_id'] = $groups_by_key[$propData['group_key']];
                unset($propData['group_key']);
            }
			$propData['type_id'] = $type_id;
            $this->preparePropertyData($propData);
            $prop_id = \Models\CatalogManagement\Properties\Property::create($propData, $e);
            if (!empty($e)){
                echo str_repeat("\t", $innerLevel+1) . (is_array($propData['title']) ? reset($propData['title']) : $propData['title']) . ' -> ' . (is_array($e) ? json_encode($e, JSON_UNESCAPED_UNICODE) : $e) . "\n";
                continue;
            }
            $property = PropertyFactory::getById($prop_id);
            $property->update($propData, $e);
            if (empty($e)){
                echo str_repeat("\t", $innerLevel+1) . (is_array($propData['title']) ? reset($propData['title']) : $propData['title']) . ' - свойство добавлено' . "\n";
            }else{
                echo str_repeat("\t", $innerLevel+1) . (is_array($propData['title']) ? reset($propData['title']) : $propData['title']) . ' -> ' . (is_array($e) ? json_encode($e, JSON_UNESCAPED_UNICODE) : $e) . "\n";
            }
        }
    }
    
    private function preparePropertyData(&$propData){
        $propData['title'] = $this->prepareValue($propData['title']);
        if ($propData['data_type'] == \Models\CatalogManagement\Properties\Enum::TYPE_NAME){
            $propData['values']['values'] = $this->prepareValue($propData['values']['values']);
        }
        if (strpos($propData['data_type'], 'diapason') === 0){
            $propData['values'] = $this->prepareValue($propData['values']);
        }
        if ($propData['data_type'] == \Models\CatalogManagement\Properties\Flag::TYPE_NAME){
            foreach(array('yes', 'no') as $val_key) {
                if (!empty($propData['values'][$val_key])) {
                    echo PHP_EOL;
                    $propData['values'][$val_key] = $this->prepareValue($propData['values'][$val_key]);
                }
            }
        }
        if (!empty($propData['mask'])){
            $propData['mask'] = $this->prepareValue($propData['mask']);
        }
        if (!empty($propData['filter_title'])){
            $propData['filter_title'] = $this->prepareValue($propData['filter_title']);
        }
        return $propData;
    }
}
