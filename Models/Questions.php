<?php

namespace Models;
/**
 * Опросы на сайте
 *
 * @author mac-proger
 */
class Questions {
    private static $registry = array();
    
    const TABLE = 'questions';
    private static $load_fields = array('id', 'pool_key', 'question', 'note', 'multi_answer', 'answers', 'position');
    
    private $pool_key;
    private $questions = array();
    /**
	 * @TODO Denis сделать фабрику
	 * @param string $key
	 */
    private function __construct($key){
        $this->pool_key = $key;
        $this->init();
    }
    /**
     * 
     * @param string $key
     * @return Questions
     */
    public static function getByKey($key){
        if (empty(self::$registry[$key])){
            self::$registry[$key] = new self($key);
        }
        return self::$registry[$key];
    }
    
    private function init(){
        $db = \App\Builder::getInstance()->getDB();
        $quest_list = $db->query('SELECT `' . implode('`, `', self::$load_fields) . '` FROM `' . self::TABLE . '` WHERE `pool_key` = ?s ORDER BY `position`', $this->pool_key)->select('id');
        $this->questions = array();
        foreach($quest_list as $id => $quest){
            $quest['answers'] = json_decode($quest['answers'], TRUE);
            $quest['answer'] = reset($quest['answers']);
            $this->questions[$id] = $quest;
        }
    }
    
    public function getQuestions(){
        return $this->questions;
    }
    
    public function getRandomQuestion(){
        $question = \App\Builder::getInstance()->getDB()->query('SELECT `' . implode('`, `', self::$load_fields) . '` FROM `' . self::TABLE . '` WHERE `pool_key` = ?s ORDER BY RAND() LIMIT 1', $this->pool_key)->getRow();
        if (!empty($question) && !empty($question['answers'])){
            $question['answers'] = json_decode($question['answers'], TRUE);
            $question['answer'] = reset($question['answers']);
        }
        return $question;
    }
    /**
     * Создание вопроса
     * @param array $data keys question multi_answer answers
     * @return int 
     */
    public function addQuestion($data){
        $db = \App\Builder::getInstance()->getDB();
        $position = $db->query('SELECT MAX(`position`) FROM `' . self::TABLE . '` WHERE `pool_key` = ?s', $this->pool_key)->getCell() + 1;
        $allow_fields = array('question', 'multi_answer', 'answers');
        foreach($data as $key => $field){
            if (!in_array($key, $allow_fields)){
                unset($data[$key]);
            }
        }
        $data['pool_key'] = $this->pool_key;
        $data['position'] = $position;
        $data['answers'] = json_encode($data['answers']);
        $data['note'] = '';
        $id = $db->query('INSERT INTO `' . self::TABLE . '` SET ?a', $data);
        $data['answers'] = json_decode($data['answers'], TRUE);
        $data['id'] = $id;
        $this->questions[$id] = $data;
        return $this->questions[$id];
    }
    /**
     * Удаление вопроса
     * @param int $id id вопроса
     * @return boolean
     */
    public function deleteQuestion($id){
        if (empty($this->questions[$id])){
            return FALSE;
        }
        \App\Builder::getInstance()->getDB()->query('DELETE FROM `' . self::TABLE . '` WHERE `id` = ?d', $id);
        unset($this->questions[$id]);
        return TRUE;
    }
    /**
     * Обновление вопросов
     * @param array $questions
     * @return bool
     */
    public function editQuestions($questions){
        $allow_fields = array('question', 'note', 'multi_answer', 'answers');
        $db = \App\Builder::getInstance()->getDB();
        foreach($questions as $id => $quest){
            if (empty($this->questions[$id])){
                continue;
            }
            if (!empty($quest['answers'])){
                $quest['answers'] = json_encode($quest['answers']);
            }
            $db->query('UPDATE `' . self::TABLE . '` SET ?a WHERE `id` = ?d', $quest, $id);
        }
        return TRUE;
    }
    /**
     * 
     * @param int $question_id
     * @param int $new_position
     */
    public function sort($question_id, $new_position){
        $db = \App\Builder::getInstance()->getDB();
        if (empty($this->questions[$question_id])){
            return FALSE;
        }
        $old_position = $this->questions[$question_id]['position'];
        if ($new_position < $old_position) {
            $db->query('
                    UPDATE `' . self::TABLE . '`
                    SET `position`=`position`+1
                    WHERE `position`>=?d AND `position`<?d', $new_position, $old_position
            );
        } else {
            $db->query('
                    UPDATE `' . self::TABLE . '`
                    SET `position`=`position`-1
                    WHERE `position`<=?d AND `position`>?d', $new_position, $old_position
            );
        }
        $db->query('UPDATE `' . self::TABLE . '` SET `position` = ?d WHERE `id` = ?d', $new_position, $question_id);
        $this->init();
        return true;
    }
    
}
