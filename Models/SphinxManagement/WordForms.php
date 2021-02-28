<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 03.10.14
 * Time: 14:22
 */

namespace Models\SphinxManagement;


use App\Configs\SphinxConfig;
use Models\CronTask;
use Models\SiteConfigManager;
use MysqlSimple\Logger;

class WordForms {
    const TABLE = 'sphinx_wordforms';
    const CHECKFORM_INDEX = 'santech_catalog';
    const MAX_SELECT = 100000000;
    const IS_WORDFORMS_MODIFIED = 'sphinx_wordforms_modified';

    private static $i = NULL;

    private $db = NULL;
    private $sphinx = NULL;

    /**
     * @return WordForms
     */
    public static function getInstance(){
        if (empty(self::$i)){
            self::$i = new self();
        }
        return self::$i;
    }

    private function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
        $this->sphinx = \App\Builder::getInstance()->getSphinx();
    }

    /**
     * @param array $params
     * @param int $offset
     * @param int $limit
     * @param bool $count
     * @return array
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    public function search($params = array(), $offset = 0, $limit = self::MAX_SELECT, &$count = FALSE){
        // занак > выбран в качестве сепаратора, поскольку он недопустим и следовательно никогда не встретится в словах
        $wordforms =  $this->db->query('SELECT' . ($count !== FALSE ? ' SQL_CALC_FOUND_ROWS ' : ' ')
            . (!empty($params['src_form_strict']) ? '`src_form`' : 'GROUP_CONCAT(`src_form` SEPARATOR ">") AS `src_form`') // если мы ищем конкретную словоформу группировка не нужна
            . ', `dst_form`, `normalized_form`, `errors` FROM `' . self::TABLE . '`
            WHERE 1
                { AND `src_form` = ?s}
                { AND `dst_form` = ?s}
                { AND `src_form` LIKE ?s}
                { AND `dst_form` LIKE ?s}
                { AND (`src_form` LIKE ?s OR `dst_form` LIKE ?s)}
                { AND `errors` IS' . (!empty($params['errors']) ? ' NOT' : '') . ' NULL AND ?d}
            GROUP BY `dst_form`
            ORDER BY `dst_form`' . (!empty($params['sort']) ? ' DESC' : ''). '
            LIMIT ?d, ?d',
            !empty($params['src_form_strict']) ? $params['src_form_strict'] : $this->db->skipIt(),
            !empty($params['dst_form_strict']) ? $params['dst_form_strict'] : $this->db->skipIt(),
            !empty($params['src_form']) ? $params['src_form'] . '%' : $this->db->skipIt(),
            !empty($params['dst_form']) ? $params['dst_form'] . '%' : $this->db->skipIt(),
            !empty($params['search']) ? '%' . $params['search'] . '%' : $this->db->skipIt(),
            !empty($params['search']) ? $params['search'] . '%' : '%',
            array_key_exists('errors', $params) ? 1 : $this->db->skipIt(),
            $offset, $limit
        )->select('dst_form');
        // если не одну конкретную - разбиваем сгруппированные исходные словоформы в массив
        if (empty($params['src_form_strict'])) {
            foreach($wordforms as &$wordform){
                $wordform['src_form'] = explode('>', $wordform['src_form']);
            }
        }
        if ($count!==false)
            $count = $this->db->query('SELECT FOUND_ROWS()')->getCell();
        return $wordforms;
    }

    /**
     * Возвращает данные по конечной словоформе
     * @param string $dst_form
     * @return array|false keys src_form, dst_form, normalized_form, errors
     */
    public function getByDstForm($dst_form, &$errors = array()){
        $result = $this->search(array('dst_form_strict' => $dst_form));
        $wordform = reset($result);
        if (!$wordform){
            $errors['dst_form'] = 'not_found';
        }
        return $wordform;
    }

    /**
     * @param $src_form
     * @param $dst_form
     * @param array $errors
     * @return bool
     */
    public function add($src_forms, $dst_form, &$errors = array()){
        if (!$this->checkConflicts($src_forms, $dst_form, $errors)){
            return FALSE;
        }
        foreach($src_forms as $src_form){
            $this->db->query('REPLACE INTO `' . self::TABLE . '` SET `src_form` = ?s, `dst_form` = ?s, `normalized_form` = ?s', $src_form, $dst_form, $this->getNormalizedForm($dst_form));
        }
        $this->setChanged(1);
        return TRUE;
    }

    /**
     * @param string $old_dst_form
     * @param array $src_forms
     * @param string $dst_form
     * @param array $errors
     * @return bool
     */
    public function update($old_dst_form, $src_forms, $dst_form, &$errors = array()){
        if (!is_array($src_forms)){
            $src_forms = array($src_forms);
        }
        $wordform_data = $this->getByDstForm($old_dst_form, $errors);
        if (empty($wordform_data)
//            || ($old_dst_form != $src_forms && $this->isExists($src_forms, $errors))
            || !$this->checkConflicts($src_forms, $dst_form, $errors))
        {
            return FALSE;
        }
        $normalized_form = ($dst_form != $wordform_data['dst_form']) ? $this->getNormalizedForm($dst_form) : $wordform_data['dst_form'];
        $this->db->query('DELETE FROM `' . self::TABLE . '` WHERE `dst_form` = ?s', $old_dst_form);
        foreach($src_forms as $src_form) {
            $this->db->query('REPLACE INTO `' . self::TABLE . '` SET `src_form` = ?s, `dst_form` = ?s, `normalized_form` = ?s', $src_form, $dst_form, $normalized_form);
        }
        $this->setChanged(1);
    }

    /**
     * @param string|string[] $dst_form конечная словоформа, одна или массив
     */
    public function delete($dst_form){
        if (!empty($dst_form)){
            $this->db->query('DELETE FROM `' . self::TABLE . '` WHERE `dst_form`' . (is_array($dst_form) ? ' IN (?l)' : ' = ?s'), $dst_form);
            $this->setChanged(1);
        }
    }

    /**
     * Проверяет на отсутствие конфликтов - исходные формы не должны встречаться среди заменяемых, заменяемые среди исходных
     * например в следующей ситуации будет конфликт
     * металл > сталь
     * железо > металл
     *
     * @param string $src_form исходная словоформа
     * @param string $dst_form словоформа для замены
     * @param array $errors
     * @return bool
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    private function checkConflicts($src_form, $dst_form, &$errors = array()){
        $src_form = is_array($src_form) ? $src_form : array($src_form);
        $src_dst_conflict = $this->db->query('SELECT `dst_form` FROM `' . self::TABLE . '` WHERE `dst_form` IN (?l)', $src_form)->getCol('dst_form', 'dst_form');
        if (!empty($src_dst_conflict)){
            $errors['src_form'] = $src_dst_conflict;
        }
        if (in_array($dst_form, $src_form)){
            $errors['src_form'] = empty($errors['src_form']) ? array($dst_form => $dst_form) : array($dst_form => $dst_form) + $errors['src_form'];
        }
        if ($this->db->query('SELECT COUNT(*) AS `count` FROM `' . self::TABLE . '` WHERE `src_form` = ?s', $dst_form)->getCell()){
            $errors['dst_form'] = 'conflict';
        }
        // Исходные словоформы не должны использоваться в других группах синонимов (другая конечная словоформа)
        $conflict_src_forms = $this->db->query('SELECT `src_form` FROM `' . self::TABLE . '` WHERE `dst_form` != ?s AND `src_form` IN (?l)', $dst_form,$src_form)->getCol('src_form', 'src_form');
        if (!empty($conflict_src_forms)){
            $errors['conflict'] = $conflict_src_forms;
        }
        return empty($errors);
    }

    /**
     * Проверка на уникальность исходной словоформы
     * @param string $src_form
     * @param array $errors
     * @return bool
     */
    private function isExists($src_form, &$errors = array()){
        $row = $this->db->query('SELECT * FROM `' . self::TABLE . '` WHERE `src_form` = ?s', $src_form)->getRow();
        if (!empty($row)){
            $errors['src_form'] = \Models\Validator::ERR_MSG_EXISTS;
            return TRUE;
        }
        return FALSE;
    }

    /**
     * возвращает нормализованную стеммером сфинкса словоформу (отрезает окончания и суффиксы)
     * @param string $dst_form
     * @return string
     */
    private function getNormalizedForm($dst_form){
        $row = $this->sphinx->query('CALL KEYWORDS(?s, ?s)', $dst_form, SphinxSearch::checkKey(SphinxConfig::CATALOG_KEY))->getRow();
        return (!empty($row) && !empty($row['normalized'])) ? $row['normalized'] : $dst_form;
    }

    /**
     * Устанавливает флаг для генерации нового списка
     * @param int $changed
     * @return int
     */
    private function setChanged($changed = 1){
        \Models\TechnicalConfig::getInstance()->set(self::IS_WORDFORMS_MODIFIED, 'sphinx', $changed, 'Синонимы сфинкса изменены');
        return $changed;
    }

    /**
     * Проверка, требуется ли обновление списка
     * @return int
     */
    public function isChanged(){
        $changed = \Models\TechnicalConfig::getInstance()->get(self::IS_WORDFORMS_MODIFIED);
        return $changed;
    }

    /**
     * генерация файла синонимов
     * @param $error
     */
    public function generateList(&$error = NULL){
        $wordforms = $this->search();
        $task_id = CronTask::add(array(
                'type' => \App\Configs\CronTaskConfig::TASK_SPHINX_WORDFORMS_GENERATE,
                'status' => CronTask::STATUS_NEW,
                'time_create' => date('Y-m-d H:i:s'),
                'time_start' => date('Y-m-d H:i:s'),
                'data' => array(
                    'wordform_count' => count($wordforms)
                )
            )
        );
        $task = CronTask::getById($task_id);
        $file = fopen(\LPS\Config::getRealDocumentRoot() . SphinxConfig::WORDFORMS_FILE, 'w');
        if (!$file){
            $error = 'file_create_error';
            return FALSE;
        }
        foreach($wordforms as $wordform){
            foreach($wordform['src_form'] as $src_form){
                fwrite($file, $src_form . ' > ' . $wordform['normalized_form'] . PHP_EOL);
            }
        }
        fclose($file);
        $this->setChanged(0);
        $task->update(array(
            'status' => CronTask::STATUS_COMPLETE,
            'percent' => 100,
            'time_end' => date('Y-m-d H:i:s')
        ));
    }
} 