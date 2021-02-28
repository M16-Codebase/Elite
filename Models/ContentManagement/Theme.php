<?php
/**
 * темы статей
 *
 * @author poche_000
 */
namespace Models\ContentManagement;
use App\Configs\PostConfig;

class Theme {
    const TABLE = 'themes';
    private static $editableFields = array('title', 'keyword', 'parent_id', 'show', 'hide', 'position');
    /**
     *
     * @var Theme
     */
    private static $instance = NULL;
    /**
     * @return Theme
     */
    public static function getInstance(){
        if (is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    public static function getPostTypes(){
        return PostConfig::$post_types;
    }
    /**
     *
     * @var \MysqlSimple\Controller
     */
    private $db = NULL;
    private function __construct() {
        $this->db = \App\Builder::getInstance()->getDB();
    }
    public function search($params = array(), $byParents = FALSE){
//        $this->db->setLogger(\MysqlSimple\Logger::factory());
        $result = $this->db->query(''
            . 'SELECT `t`.*, IF(`t`.`parent_id` IS NULL, 0, `t`.`parent_id`) AS `parent_id`, COUNT(`p`.`id`) AS `count`, COUNT(`t_child`.`id`) AS `theme_count` FROM `themes` AS `t`'
            . (!empty($params['status']) && !empty($params['only_filled']) ? 'INNER' : 'LEFT') . ' JOIN `posts` AS `p` ON '
            . '(`p`.`theme_id` = `t`.`id`{ AND '
            . ' (`p`.`segment_id` IS NULL OR `p`.`segment_id` = ?d)} ) '
            . '{  AND `p`.`status` IN (?l)}{ AND `p`.`type` = ?s}'
            . ' AND `p`.`status` != ?s'
            . '{ AND `p`.`type` = ?s }'
            . ' LEFT JOIN `themes` AS `t_child` ON `t`.`id` = `t_child`.`parent_id`'
            . 'WHERE 1'
            . '{ AND `t`.`keyword` = ?s}'
            . '{ AND `t`.`id` IN (?i)}'
            . '{ AND `t`.`id` != ?d}'
            . '{ AND (`t`.`parents` NOT LIKE ?s OR `t`.`parents` IS NULL)}'
            . '{ AND `t`.`show` LIKE ?s}'
            . '{ AND `t`.`parent_id` IS NULL AND ?d}'
            . '{ AND `t`.`parent_id` IS NULL AND ?d}'
            . '{ AND `t`.`parent_id` ' . ((!empty($params['parent_id']) && is_array($params['parent_id'])) ? 'IN (?i)' : '= ?d') . '}'
            . '{ AND `t`.`child_level` < ?d}'
            . '{ AND (`t`.`parents` LIKE ?s OR `t`.`id` = ?d)}'
            . ' GROUP BY `t`.`id`'
            . '{ HAVING `count` = ?d}'
            . '{ HAVING `theme_count` = ?d}'
            . ' ORDER BY `position`',
            !empty($params['segment_id']) ? $params['segment_id'] : $this->db->skipIt(),
            !empty($params['status']) ? $params['status'] : $this->db->skipIt(),
            !empty($params['post_type']) ? $params['post_type'] : $this->db->skipIt(),
            Post::STATUS_DELETE,
            !empty($params['type']) ? $params['type'] : $this->db->skipIt(),
            !empty($params['key']) ? $params['key'] : $this->db->skipIt(),
            !empty($params['id']) ? (is_array($params['id']) ? $params['id'] : array($params['id'])) : $this->db->skipIt(),
            !empty($params['not_id']) ? $params['not_id'] : $this->db->skipIt(),
            !empty($params['not_children']) ? '%.'.$params['not_children'].'.%' : $this->db->skipIt(),
            !empty($params['post_type']) ? ('%.' . $params['post_type'] . '.%') : $this->db->skipIt(),
            !empty($params['top_level']) ? 1 : $this->db->skipIt(),
            !empty($params['empty_parent']) ? 1 : $this->db->skipIt(),
            !empty($params['parent_id']) ? $params['parent_id'] : $this->db->skipIt(),
            !empty($params['max_level']) ? $params['max_level'] : $this->db->skipIt(),
            !empty($params['theme_all']) ? '%.'.$params['theme_all'].'.%' : $this->db->skipIt(),
            !empty($params['theme_all']) ? $params['theme_all'] : NULL,
            !empty($params['empty_posts']) ? 0 : $this->db->skipIt(),
            !empty($params['empty_child_themes']) ? 0 : $this->db->skipIt()
        );
        return $this->prepareTitles($byParents ? $result->select('parent_id', 'id') : $result->select('id'), $byParents, !empty($params['segment_id']) ? $params['segment_id'] : NULL);
    }

    /**
     * Распаковывает заголовки тем, и подставляет заголовок нужного сегмента для паблика
     * @param array $themes
     * @param bool $byParents
     * @param int|null $segment_id
     * @return array
     */
    private function prepareTitles($themes, $byParents, $segment_id = NULL){
        if (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE){
            if ($byParents){
                foreach($themes as $parent_id => $child_themes){
                    $themes[$parent_id] = $this->prepareTitles($child_themes, FALSE, $segment_id);
                }
            } else {
                foreach($themes as $id => $theme){
                    $title = json_decode($theme['title'], true);
                    if ($title){
                        $themes[$id]['title'] = !empty($segment_id) ? (!empty($title[$segment_id]) ? $title[$segment_id] : NULL) : $title;
                    }
                }
            }
        }
        return $themes;
    }
    public function getByKey($key){
        if (empty($key)){
            return array();
        }
        $result = $this->search(array('key' => $key));
        if (empty($result)){
            return array();
        }
        return reset($result);
    }
    public function getById($id, $segment_id = NULL){
        if (empty($id)){
            return array();
        }
        $result = $this->search(array('id' => $id, 'segment_id' => $segment_id));
        if (empty($result)){
            return array();
        }
        return reset($result);
    }
    public function getByPostType($type){
        if (empty($$type)){
            return array();
        }
        $result = $this->search(array('post_type' => $$type));
        if (empty($result)){
            return array();
        }
        return reset($result);
    }
    private function getChildLevel($parent_id, &$parents = NULL){
        $parents = NULL;
        if (empty($parent_id)){
            return 0;
        } else {
            $level = 0;
            $parents = array();
            do{
                array_unshift($parents, $parent_id);
                $level ++;
                $parent = $this->getById($parent_id);
                $parent_id = $parent['parent_id'];
            } while($parent_id);
            $parents = '.' . implode('.', $parents) . '.';
            return $level;
        }
    }
    public function create($title, $parent_id = NULL, $default_post_id = NULL, &$errors = array()){
        if (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE xor is_array($title)){
            throw new \Exception(\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE
                ? 'На сайте включены сегменты, $title должен быть массивом вида array(<segment_id> => <title_for_segment>)'
                : 'На сайте выключены сегменты, $title должен быть строкой'
            );
        }
        $segments = \App\Segment::getInstance()->getAll();
        $default_segment = \App\Segment::getInstance()->getDefault(true);
        if (empty($title)){
            $errors['title'] = 'empty';
        } elseif (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE){
            foreach($segments as $s_id => $seg){
                if (empty($title[$s_id])){
                    $errors['title[' . $s_id . ']'] = 'empty';
                }
            }
        }

        if (!empty($errors)){
            return FALSE;
        }

        $key = \LPS\Components\Translit::Supertag(\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE ? $title[$default_segment['id']] : $title);
        if (!empty($default_post_id)){
            $position = $this->db->query('SELECT MAX(`position`) FROM `' . self::TABLE . '` WHERE `show` LIKE ?s AND `parent_id` '.(!empty($parent_id) ? '= '.$this->db->escape_value($parent_id) : 'IS NULL'), '%.'.$default_post_id.'.%')->getCell() + 1;
        }
        $unique = FALSE;
        $i = 1;
        while (!$unique){
            $unique = $this->isKeyUnique($key);
            if (!$unique){
                $key .= $i++;
            }
        }
        $level = $this->getChildLevel($parent_id, $parents);
        $id = $this->db->query(''
            . 'INSERT INTO `themes` SET '
            . '`parent_id` = ?d, '
            . '`title` = ?s, '
            . '`keyword` = ?s'
            . '{, `show` = ?s}'
            . '{, `position` = ?d}'
            . ', `child_level` = ?d'
            . ', `parents` = ?s',
            $parent_id,
            \LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE ? json_encode($title) : $title,
            $key,
            !empty($default_post_id) ? '.' . $default_post_id . '.' : $this->db->skipIt(),
            !empty($position) ? $position : $this->db->skipIt(),
            $level, $parents);
        return $id;
    }
    private function isKeyUnique($key, $not_id = NULL){
        $isset_key = $this->db->query('SELECT 1 FROM `'.self::TABLE.'` WHERE `keyword` = ?s{ AND `id` != ?d}', $key, !empty($not_id) ? $not_id : $this->db->skipIt())->getCell();
        return $isset_key == 1 ? FALSE : TRUE;
    }
    public function edit($id, $params, &$errors){
        $theme = $this->getById($id);
        if (empty($theme)){
            $errors['id'] = 'not_found';
            return FALSE;
        }
        if (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE xor is_array($params['title'])){
            throw new \Exception(\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE
                    ? 'На сайте включены сегменты, $title должен быть массивом вида array(<segment_id> => <title_for_segment>)'
                    : 'На сайте выключены сегменты, $title должен быть строкой'
            );
        }
        $segments = \App\Segment::getInstance()->getAll();
        $default_segment = \App\Segment::getInstance()->getDefault(true);
        if (empty($params['title'])){
            $errors['title'] = 'empty';
        } elseif (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE){
            foreach($segments as $s_id => $seg){
                if (empty($params['title'][$s_id])){
                    $errors['title[' . $s_id . ']'] = 'empty';
                }
            }
            $params['title'] = json_encode($params['title']);
        }

        if (isset($params['key']) && !$this->isKeyUnique($params['key'], $id)){
            $errors['key'] = \Models\Validator::ERR_MSG_EXISTS;
        }
        if (!empty($errors)){
            return FALSE;
        }
        $saved_data = array();
        foreach (self::$editableFields as $f){
            if (isset($params[$f])){
                $saved_data[$f] = $params[$f];
            }
        }
        if (empty($theme['position'])){
            $saved_data['position'] = $this->db->query('SELECT MAX(`position`) FROM `' . self::TABLE . '` WHERE `show` LIKE ?s', '%'.$theme['show'].'% AND `parent_id` '.(!empty($theme['parent_id']) ? '= '.$this->db->escape_value($theme['parent_id']) : 'IS NULL'))->getCell() + 1;
        }
        if (empty($saved_data)){
            return FALSE;
        }
        if (array_key_exists('parent_id', $saved_data) && $saved_data['parent_id'] != $theme['parent_id']) {
            $saved_data['parent_id'] = !empty($saved_data['parent_id']) ? $saved_data['parent_id'] : NULL;
            $saved_data['child_level'] = $this->getChildLevel($saved_data['parent_id'], $parents);
            $saved_data['parents'] = $parents;
            $saved_data['position'] = $this->db->query('SELECT MAX(`position`) FROM `' . self::TABLE . '` WHERE `show` LIKE ?s', '%'.$theme['show'].'% AND `parent_id` '.(!empty($saved_data['parent_id']) ? '= '.$this->db->escape_value($saved_data['parent_id']) : 'IS NULL'))->getCell() + 1;
            $recountThemes = $this->search(array('theme_all' => $id));
            unset($recountThemes[$id]);
        }
        $result = $this->db->query('UPDATE `themes` SET ?a WHERE `id`=?d', $saved_data, $id);
        if (!empty($recountThemes)){
            $this->recountLevel($recountThemes);
        }
        return $result;
    }
    public function delete($id){
        $this->db->query('DELETE FROM `themes` WHERE `id`=?d', $id);
        $this->db->query('UPDATE `themes` SET `parent_id` = 0 WHERE `parent_id` = ?d', $id);
    }

    /**
     * Пересчет уровня вложенности тем при переезде родительской темы
     * @param $themes
     */
    private function recountLevel($themes){
        foreach($themes as $theme){
            $level = $this->getChildLevel($theme['parent_id'], $parents);
            $this->db->query('UPDATE `' . self::TABLE . '` SET `child_level` = ?d, `parents` = ?s WHERE `id` = ?d', $level, $parents, $theme['id']);
        }
    }
    /**
     * сменить порядок показа темы
     * @param int $id
     * @param int $new_position новая позиция темы
     */
    public function move($id, $new_position){
        $theme = $this->getById($id);
        if (empty($theme)){
            return FALSE;
        }
        $old_position = $theme['position'];
        if ($new_position < $old_position) {
            $this->db->query('
                UPDATE `' . self::TABLE . '`
                SET `position`=`position`+1
                WHERE `position`>=?d AND `position`<?d AND `show` LIKE ?s AND `parent_id` '.(!empty($theme['parent_id']) ? '= '.$this->db->escape_value($theme['parent_id']) : 'IS NULL'), $new_position, $old_position, '%'.$theme['show'].'%'
            );
        } else {
            $this->db->query('
                UPDATE `' . self::TABLE . '`
                SET `position`=`position`-1
                WHERE `position`<=?d AND `position`>?d AND `show` LIKE ?s AND `parent_id` '.(!empty($theme['parent_id']) ? '= '.$this->db->escape_value($theme['parent_id']) : 'IS NULL'), $new_position, $old_position, '%'.$theme['show'].'%'
            );
        }
        $this->db->query('UPDATE `' . self::TABLE . '` SET `position` = ?d WHERE `id` = ?d', $new_position, $id);
        return true;
    }
}