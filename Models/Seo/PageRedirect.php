<?php
/**
 * Класс для работы со специальными редиректами
 *
 * @author olga
 */
namespace Models\Seo;
use App\Configs\SeoConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;
use Models\CatalogManagement\Variant;
use Models\ContentManagement\Post;
use Symfony\Component\HttpFoundation\RedirectResponse;
class PageRedirect {
    const TABLE = 'seo_redirect';
    const TABLE_META_TAGS = 'seo';
    /**
     * @var PageRedirect
     */
    private static $instance = NULL;
    /**
     * @return PageRedirect
     */
    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new PageRedirect();
        }
        return self::$instance;
    }
    private $db = null;
    private $default_segment = null;
    private $segments = null;
    public function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
        $this->default_segment = \App\Segment::getInstance()->getDefault();
        $this->segments = \App\Segment::getInstance()->getAll();
    }
    /**
     * Проверка на возможные редиректы
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|null
     */
    public function exceptionRedirect(){
        $url = $_SERVER['REQUEST_URI'];
        //проверям что урл заканчивается на "/", и редиректим если нет. Т.к. страница одна, то и урл должен быть один.
        if(strpos($url, '&') === FALSE && strpos($url, '?') === FALSE && !preg_match('~\.[a-z]{2,4}$~i', $url) && strrpos($url, '/') !== strlen($url)-1){
            return new RedirectResponse($url . '/', '301');
        }
        $url = rtrim($url, '/');
        if (!empty($url)){
            //редиректы установленные вручную
            $redirect_urls = $this->get($url);
            if (!empty($redirect_urls)){
                $url_data = reset($redirect_urls);
                return new RedirectResponse($url_data['to'], '301');
            }
        }
        return null;
    }

    /**
     * Достать редиректы
     * @param string $from
     * @param string $from_like
     * @param string $to_like
     * @param int|FALSE $filter_auto 0 - ручные редиректы, 1 - автоматические, false - все
     * @return array
     */
    public function get($from = null, $from_like = null, $to_like = null, $filter_auto = false){
        $urls = $this->db->query('
            SELECT `fr`, `to`, `old_to`
            FROM `'.self::TABLE.'`
            WHERE 1 {AND `fr` = ?s}
            { AND `fr` LIKE ?s}
            { AND `to` LIKE ?s}
            { AND `auto` = ?d}',
                !empty($from) ? $from : $this->db->skipIt(),
                !empty($from_like) ? '%' . $from_like . '%' : $this->db->skipIt(),
                !empty($to_like) ? '%' . $to_like . '%' : $this->db->skipIt(),
                $filter_auto !== FALSE ? ($filter_auto ? 1 : 0) : $this->db->skipIt()
            )->select('fr');
        return $urls;
    }
    /**
     * Создать специальный редирект
     * @param string $from с какой страницы
     * @param string $to на какую страницу
     * @return boolean
     */
    public function create($from, $to, &$errors = array()){
        $uncut_from = $from;
        $from = rtrim($from, '/');
        $from_exists = $this->get($from);
        $to_cut = rtrim($to, '/');
        $to_exitst = $this->get(!empty($to_cut) ? $to_cut : '/');
        if ($from_exists) {
            $errors['from'] = \Models\Validator::ERR_MSG_EXISTS;
        }
        if ($to_exitst){
            $errors['to'] = \Models\Validator::ERR_MSG_EXISTS;
        }
        if ($to_cut == $from) {
            $errors['to'] = 'same';
        }
        if (empty($errors)) {
            // Склеиваем старые редиректы с новыми, во избежание многоступенчатых редиректов
            // Например редирект A -> B, при создании редиректа B -> C, превратится в A -> C
            $this->db->query('UPDATE ?# SET `to` = ?s WHERE `to` = ?s', self::TABLE, $to, $uncut_from);
            // Удаляем редирект сам на себя, если есть, может возникнуть при обновлении старых редиректов
            // Есть два редиректа A -> B и C -> B, добавляем B -> A, на предыдущей итерации получится A -> A, C -> A
            // Поэтому нужно удалить циклический редирект
            $this->db->query('DELETE FROM ?# WHERE `fr` = ?s AND `to` = ?s', self::TABLE, rtrim($to, '/'), $to);
            return $this->db->query('INSERT INTO `'.self::TABLE.'` SET `fr`=?, `to`=?, `old_to`=""', $from, $to);
        }
        return false;
    }
    /**
     * Редактировать редирект
     * @param string $from – исходный урл редактируемого редиректа, не подлежит редактированию
     * @param string $to – новый урл назначения
     * @param array $errors
     * @return boolean
     */
    public function edit($from, $to, &$errors = array()){
        $cut_from = rtrim($from, '/');
        $cut_to = rtrim($to, '/');
        $exists = $this->get($from);
        $destination_exists = $this->get(!empty($cut_to) ? $cut_to : '/');
        if (empty($exists)){
            $errors['from'] = 'not_found';
        }
        if ($cut_from == $cut_to) {
            $errors['to'] = 'same';
        } elseif (!empty($destination_exists)) {
            $errors['to'] = \Models\Validator::ERR_MSG_EXISTS;
        }
        if (empty($errors)) {
            $old_url = reset($exists);
            if ($old_url['to'] != $to){
                $this->db->query('UPDATE `'.self::TABLE.'` SET `to`=?, `old_to` = ? WHERE `fr` = ?', $to, $old_url['to'], $cut_from);
            }
        }
        return empty($errors);
    }
//    /**
//     * Редактировать редирект
//     * @param array $params
//     * @return boolean
//     */
//    public function edit($params){
//        foreach ($params as $param){
//            $exists = $this->get($param['from']);
//            if (!empty($exists)){
//                $old_url = reset($exists);
//                if ($old_url['to'] != $param['to']){
//                    $this->db->query('UPDATE `'.self::TABLE.'` SET `to`=?, `old_to` = ? WHERE `fr` = ?', $param['to'], $old_url['to'], rtrim($param['from'], '/'));
//                }
//            }
//        }
//        return true;
//    }
    /**
     * Удалить
     * @param string|array $from с какого урла редиректить
     * @return bool
     */
    public function delete($from){
        return $this->db->query('DELETE FROM `'.self::TABLE.'` WHERE `fr`' . (is_array($from) ? 'IN (?l)' : '=?s'), $from);
    }

    /**
     * Удалить редиректы с заданного урла по всем сегментам
     * @param string $from
     * @return bool
     */
    public function deleteWithAllSegments($from){
        $from_to_delete = array();
        foreach($this->segments as $segment) {
            $segment_prefix = ($segment['id'] != $this->default_segment['id']) ? '/' . $segment['key'] : '';
            $from_to_delete[] = $segment_prefix.$from;
        }
        return $this->delete($from_to_delete);
    }
    /**
     * Отчистить поле, в котором хранится старый урл для редиректа
     */
    public function clearOldRedirects(){
        $this->db->query('UPDATE `'.self::TABLE.'` SET `old_to`=NULL');
    }

    /**
     * Генерация редиректа при смене урла сущности
     * @param array $redirects array(array('from' => ..., 'to' => ...))
     */
    private function createRedirect($redirects){
        if (array_key_exists('to', $redirects)){
            $redirects = array($redirects);
        }
        $records = '';
        $from_to_delete = array();
        foreach($this->segments as $segment) {
            $segment_prefix = ($segment['id'] != $this->default_segment['id']) ? '/' . $segment['key'] : '';
            foreach($redirects as $r){
                $from_to_delete[] = $segment_prefix.$r['to'];
                $records .= (empty($records) ? '' : ', ') . '(\'' . $segment_prefix.$r['from'] . '\', \'' . $segment_prefix.$r['to'] . '\', 1)';
            }
        }
        $this->db->query('INSERT IGNORE `' . self::TABLE . '` (`fr`, `to`, `auto`) VALUES ' . $records);
        $this->delete($from_to_delete);
    }

    /**
     * Смена урла назначения для созданных ранее авторедиректов, и коррекция метатегов
     * @param string $old_url
     * @param string $new_url
     */
    private function updateAutoRedirects($old_url, $new_url){
        $old_url = rtrim($old_url, '/');
        foreach($this->segments as $segment){
            $segment_prefix = ($segment['id'] != $this->default_segment['id']) ? '/'.$segment['key'] : '';
            // корректируем редиректы
            $this->db->query('UPDATE `' . self::TABLE . '` SET `to` = REPLACE(`to`, ?s, ?s) WHERE `auto`=1 AND `to` LIKE ?s', $segment_prefix.$old_url.'/', $segment_prefix.$new_url, $segment_prefix.$old_url.'/%');
            $this->db->query('DELETE FROM `' . self::TABLE . '` WHERE `to` = CONCAT(`fr`, "/")');
            // и метатеги
            $this->db->query('UPDATE `' . self::TABLE_META_TAGS . '` SET `page_uid` = REPLACE(`page_uid`, ?s, ?s) WHERE `page_uid` LIKE ?s', $segment_prefix.$old_url, rtrim($segment_prefix.$new_url, '/'), $segment_prefix.$old_url.'%');
            // перелинковочные ссылки
            $this->db->query('UPDATE `' . SeoLinks::TABLE . '` SET `url` = REPLACE(`url`, ?s, ?s) WHERE `url` LIKE ?s', $segment_prefix.$old_url.'/', $segment_prefix.$new_url, $segment_prefix.$old_url.'/%');
            // архив вставленных ссылок
            $old = rtrim($segment_prefix.$old_url, '/');
            $new = rtrim($segment_prefix.$new_url, '/');
            $this->db->query('UPDATE `' . SeoLinks::INSERTED_LINKS_LIST_TABLE . '` SET `from` = REPLACE(`from`, ?s, ?s) WHERE `from` LIKE ?s', $old, $new, $old.'%');
            $this->db->query('UPDATE `' . SeoLinks::INSERTED_LINKS_LIST_TABLE . '` SET `to` = REPLACE(`to`, ?s, ?s) WHERE `to` LIKE ?s', $old, $new, $old.'%');
            // тексты метатегов
            $this->db->query('UPDATE `' . self::TABLE_META_TAGS . '` SET `complete_text` = REPLACE(`complete_text`, ?s, ?s) WHERE `complete_text` IS NOT NULL AND `complete_text` LIKE ?s', $segment_prefix.$old_url.'/', $segment_prefix.$new_url, '%'.$segment_prefix.$old_url.'/%');
            // и постов
            $this->db->query('UPDATE `' . Post::TABLE_NAME . '` SET `complete_text` = REPLACE(`complete_text`, ?s, ?s) WHERE `complete_text` IS NOT NULL AND `complete_text` LIKE ?s', $segment_prefix.$old_url.'/', $segment_prefix.$new_url, '%'.$segment_prefix.$old_url.'/%');

        }
    }

    /****************** Операции с авторедиректами каталога ******************/

    /**
     * Создание авторедиректов для типа, всех его потомков и айтемов/вариантов
     * @param Type $type
     * @param string $old_url
     * @param string $new_url
     */
    public function createTypeAutoRedirect(Type $type, $old_url, $new_url){
        $old_url = rtrim($old_url, '/');
        $this->updateAutoRedirects($old_url, $new_url);
//        $this->createRedirect(array('from' => $old_url, 'to' => $new_url));
        $redirects = array(array('from' => $old_url, 'to' => $new_url));
        if ($type['allow_children']){
            // Если тип не конечный - собираем редиректы для всех его потомков и айтемов вариантов его конечных потомков
            $all_children = $type->getAllChildren();
            foreach($all_children as $child_list){
                foreach($child_list as $child_type){
                    /** @var Type  $child_type */
                    $child_url = $child_type->getUrl();
                    $child_old_url = rtrim(str_replace($new_url, $old_url.'/', $child_url), '/');
                    $child_new_url = str_replace($old_url.'/', $new_url, $child_url);
                    $redirects[] = array('from' => $child_old_url, 'to' => $child_new_url);
                    if (!$child_type['allow_children']){
                        $redirects = array_merge($redirects, $this->createTypeItemsAutoRedirects($child_type, $child_old_url, $child_new_url));
                    }
                }
            }
        } else {
            // Для конечного редиректы только для его айтемов и вариантов
            $redirects = array_merge($redirects, $this->createTypeItemsAutoRedirects($type, $old_url, $new_url));
        }
        // создаем созданные редиректы
        $this->createRedirect($redirects);
    }

    /**
     * Сбор данных для создания редиректов айтемов и вариантов конечного типа
     * @param Type $type
     * @param string $old_type_url
     * @param string $new_type_url
     * @return array
     * @throws \Exception
     */
    private function createTypeItemsAutoRedirects(Type $type, $old_type_url, $new_type_url){
        if ($type['allow_children']){
            throw new \Exception('Only final types needed');
        }
        $redirects = array();
        $item_keys = $this->db->query('SELECT `id`, `key` FROM `' . Item::TABLE_ITEMS . '` WHERE `type_id` = ?d', $type['id'])->getCol('id', 'key');
        if (empty($item_keys)){
            return $redirects;
        }
        $item_prefix = $type->getCatalog()['item_prefix'];
        $variant_keys = $this->db->query('SELECT `id`, `item_id`, `key` FROM `' . Variant::TABLE_VARIANTS . '` WHERE `item_id` IN (?i)', array_keys($item_keys))->getCol(array('item_id', 'id'), 'key');
        foreach($item_keys as $item_id => $item_key){
            $old_item_url = $old_type_url . '/' . $item_prefix . $item_key;
            $new_item_url = $new_type_url . $item_prefix . $item_key . '/';
            $redirects[] = array('from' => $old_item_url, 'to' => $new_item_url);
            if (!empty($variant_keys[$item_id])){
                foreach($variant_keys[$item_id] as $variant_key){
                    $redirects[] = array('from' => $old_item_url . '/' . $variant_key, 'to' => $new_item_url . $variant_key . '/');
                }
            }
        }
        return $redirects;
    }

    /**
     * Создание редиректов для айтема и его вариантов
     * @param Item $item
     * @param string $old_url
     * @param string $new_url
     */
    public function createItemAutoRedirect(Item $item, $old_url, $new_url){
        // В базе мы храним исходный урл без замыкающего слеша
        $old_url = rtrim($old_url, '/');
        $this->updateAutoRedirects($old_url, $new_url);
        $redirects = array(array('from' => $old_url, 'to' => $new_url));
        $catalog = $item->getType()->getCatalog();
        if ($catalog['nested_in']){
            // Кустик. У кустика нет вариантов, пробегаем только дочерние типы
            $this->makeKustikAutoRedirects(array($item['id'] => array('from' => $old_url, 'to' => $new_url)), $redirects);
        } else {
            $variants = $item->getVariants(array(Variant::S_PUBLIC, Variant::S_HIDE));
            if (!empty($variants)){
                foreach($variants as $v){
                    $redirects[] = array('from' => $old_url.'/'.$v['key'], 'to' => $new_url.$v['key'].'/');
                }
            }
        }
        $this->createRedirect($redirects);
    }

    /**
     * Собираем редиректы для дочерних айтемов кустика
     * рекурсия!
     * @param array $key_to_urls
     * @param $redirects
     */
    private function makeKustikAutoRedirects(Array $key_to_urls, &$redirects){
        $keys_map = $this->db->query('SELECT `parent_id`, `id`, `key` FROM ?# WHERE `parent_id` IN (?i)', Item::TABLE, array_keys($key_to_urls))->getCol(array('parent_id', 'id'), 'key');
        $new_k2u = array();
        if (!empty($keys_map)){
            foreach($keys_map as $p_id => $keys){
                foreach($keys as $id => $key){
                    $from = $key_to_urls[$p_id]['from'].'/'.$key;
                    $to = $key_to_urls[$p_id]['to'].$key.'/';
                    $new_k2u[$id] = array('from' => $from, 'to' => $to);
                    $redirects[] = array('from' => $from, 'to' => $to);
                }
            }
            if (!empty($new_k2u)){
                $this->makeKustikAutoRedirects($new_k2u, $redirects);
            }
        }
    }

    /**
     * Создание редиректов для варианта
     * @param string $old_url
     * @param string $new_url
     */
    public function createVariantAutoRedirect($old_url, $new_url){
        $old_url = rtrim($old_url, '/');
        $this->updateAutoRedirects($old_url, $new_url);
        $this->createRedirect(array('from' => $old_url, 'to' => $new_url));
    }
}

?>
