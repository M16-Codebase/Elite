<?php
/**
 * Составное свойство
 *
 * @author olga
 */
namespace Models\CatalogManagement\Properties;

use App\Configs\CatalogConfig;
use Models\CatalogManagement\Item as ItemEntity;

class View extends Property{
    const TYPE_NAME = 'view';
    const ALLOW_SET = FALSE;
    const VALUES_TYPE_ARRAY = FALSE;
    /**
     * Чем склеивать множественные значения в составных свойствах
     */
    const GLUE_FOR_SET_IN_VIEWS = ', ';
    
    public function isRequired($type_id) {
        //свойство view не может быть обязательным, т.к. оно автоматически генерируется
        return false;
    }
    public function prepareValueToSave($value) {
        return $value;
    }

    /**
     * Проверка на отсутствие среди свойств, входящих в составное свойство ключей свойств,
     * для коротых разрешено использование составных свойств (упрощенная защита от замыкания представлений на себя)
     * @param array $data
     * @param array $e
     * @return array
     */
    protected static function checkData($id, &$data, &$e){
        if (!empty($data['values'])) {
            if (preg_match_all('~\{([^}]+)\}~i', $data['values'], $keys)) {
                foreach($keys[1] as $key) {
                    if (in_array($key, CatalogConfig::getPropKeysCanContainViews())) {
                        $e['values'] = \Models\Validator::ERR_MSG_INCORRECT;
                        break;
                    }
                }
            }
        }
        return array();
    }
    /**
     * Составить свойство (если требуется его составить из других)
     * @param array $values
     * @param Property[] $propList
     * @param int $segment_id
     * @param int $item_id
     * @param string[] $used_views
     * @return string
     * @throws \LogicException
     */
    public function composeValue($values, $propList, $segment_id = 0, $item_id = NULL, $used_views = array()) {
        if ($this['multiple'] == 1 && empty($item_id)){
            throw new \LogicException('Для расщепляемого свойства должен передаваться item_id');
        }
        // добавляем текущее составное свойство в стек использованных свойств
        // необходимо, чтобы не было рекурсивных обращений к составным свойствам
        $used_views[$this['key']] = $this['key'];
        $pValue = '';//конечное значение
        if (!empty($this['values'])){
            $pValue = $this['values'];
            preg_match_all('~{([^}]*)}~', $pValue, $out); //TODO: Рассширить синтаксис, дав возможность использовать модификаторы.
            $has_value = FALSE;//есть ли хотя бы одно значение. Если нет ни одного, оставлять пустым.
            foreach ($out[1] as $subPropertyKey) {
                $subValue = '';
                if (!isset($propList[$subPropertyKey]) && $this['multiple'] != 1){
                    //ничего не делаем, т.к. реально может и не быть (мы при удалении свойства не можем удалять его ключ из вышестоящих)
//                    $subValue = '{ERROR: unknown property key "'. $subPropertyKey .'"}';
                }elseif (isset($propList[$subPropertyKey]) && $propList[$subPropertyKey] instanceof View){
                    if (!empty($used_views) && in_array($subPropertyKey, $used_views)) {
                        $subValue = '{ERROR: view can\'t use views recursive ("'. $subPropertyKey .'"), views stack: ' . var_export($used_views, true) . '}';
                    } else {
                        $subValue = $propList[$subPropertyKey]->composeValue($values, $propList, $segment_id, $item_id, $used_views);
                    }
                }else{
                    if (isset($values[$subPropertyKey])){
                        if (!empty($propList[$subPropertyKey]) && $propList[$subPropertyKey] instanceof Post) {
                            // Запихиваем заголовок поста
                            $posts = $propList[$subPropertyKey]->getCompleteValue($values[$subPropertyKey], $segment_id);
                            if (is_array($posts)) {
                                $val_list = array();
                                foreach($posts as $post) {
                                    if (!empty($post['title'])) {
                                        $val_list[] = $post['title'];
                                    }
                                }
                                $subValue = implode(', ', $val_list);
                            } else {
                                $subValue = !empty($posts['title']) ? $posts['title'] : null;
                            }
                        } else {
                            $subValue = is_array($values[$subPropertyKey]['real_value'])
                                ? implode(self::GLUE_FOR_SET_IN_VIEWS, $values[$subPropertyKey]['real_value'])
                                : $values[$subPropertyKey]['real_value'];
                        }
                        if (isset($subValue) && $subValue != ''){
                            $has_value = TRUE;
                        }
                    }elseif ($this['multiple'] == 1){//если свойство варианта, то могут браться значения у товара
                        $item = ItemEntity::getById($item_id, $segment_id);
                        if (empty($item)){
                            throw new \LogicException('Товар id# '.$item_id.' не найден');
                        }
                        $item_properties = $item->getSegmentProperties($segment_id);
                        if (isset($item_properties[$subPropertyKey])){
                            if (!empty($propList[$subPropertyKey]) && $propList[$subPropertyKey] instanceof Post) {
                                // Запихиваем заголовок поста
                                $posts = $propList[$subPropertyKey]->getCompleteValue($item_properties[$subPropertyKey], $segment_id);
                                if (is_array($posts)) {
                                    $val_list = array();
                                    foreach($posts as $post) {
                                        if (!empty($post['title'])) {
                                            $val_list[] = $post['title'];
                                        }
                                    }
                                    $subValue = implode(', ', $val_list);
                                } else {
                                    $subValue = !empty($posts['title']) ? $posts['title'] : null;
                                }
                            } else {
                                $subValue = is_array($item_properties[$subPropertyKey]['real_value'])
                                    ? implode(self::GLUE_FOR_SET_IN_VIEWS, $item_properties[$subPropertyKey]['real_value'])
                                    : $item_properties[$subPropertyKey]['real_value'];
                            }
                            if (isset($subValue) && $subValue != ''){
                                $has_value = TRUE;
                            }
                        }
                    }
                }
                $pValue = str_replace('{' . $subPropertyKey . '}', is_array($subValue) ? implode(', ', $subValue) : $subValue, $pValue);
            }
        }
        return !empty($has_value) ? $pValue : NULL;
    }
}