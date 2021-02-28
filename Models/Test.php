<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 05.08.15
 * Time: 18:07
 */

namespace Models;


use Models\ContentManagement\Post;

class Test implements \ArrayAccess
{
    const TABLE = 'tests';
    const TABLE_QUESTIONS = 'test_questions';
    const TABLE_RESULTS = 'test_results';

    const POST_TYPE = 'test_result';

    private static $registry = array();

    private static $key2id = array();

    private static $test_load_fields = array('id', 'key', 'title', 'post_id');
    private static $test_update_fields = array('key', 'title');

    private static $additional_fields = array('questions', 'results', 'min_score', 'max_score');

    private static $question_load_fields = array('id', 'test_id', 'question', 'note', 'answer_type', 'answers', 'position');
    private static $question_update_fields = array('question', 'note', 'answer_type', 'answers');

    private static $result_load_fields = array('id', 'test_id', 'max_score', 'post_id');
    private static $result_update_fields = array('max_score');

    /**
     * Ответ в баллах
     */
    const ANSWER_TYPE_VALUE = 'value';
    /**
     * Выбор одного варианта ответа из предложенных
     */
    const ANSWER_TYPE_ANSWER = 'answer';
    /**
     * Выбор одного или нескольких вариантов ответа из предложенных
     */
    const ANSWER_TYPE_MULTI_ANSWER = 'multi_answer';
    /**
     * Минимальное количество ответов на вопрос
     */
    const MIN_ANSWERS_COUNT = 2;

    private static $allow_answer_types = array(self::ANSWER_TYPE_VALUE, self::ANSWER_TYPE_ANSWER, self::ANSWER_TYPE_MULTI_ANSWER);

    private $data = array();
    private $questions = array();
    private $results = array();
    /**
     * @var \MysqlSimple\Controller
     */
    private $db = NULL;

    /**
     * @param int[] $ids
     * @return self[]
     */
    public static function factory($ids){
        $load_ids = array_diff($ids, array_keys(self::$registry));
        if (!empty($load_ids)) {
            $db = \App\Builder::getInstance()->getDB();
            $tests_data = $db->query('SELECT `' . implode('`, `', self::$test_load_fields) . '` FROM ?# WHERE `id` IN (?i)', self::TABLE, $load_ids)->select('id');
            if (!empty($tests_data)) {
                $questions_data = $db->query('SELECT `' . implode('`, `', self::$question_load_fields) . '`
                        FROM ?# WHERE `test_id` IN (?i) ORDER BY `position`',
                    self::TABLE_QUESTIONS,
                    array_keys($tests_data))->select('test_id', 'id');
                $results_data = $db->query('SELECT `' . implode('`, `', self::$result_load_fields) . '`
                        FROM ?# WHERE `test_id` IN (?i) ORDER BY `max_score`',
                    self::TABLE_RESULTS,
                    array_keys($tests_data))->select('test_id', 'id');
                foreach($tests_data as $id => $data){
                    self::$registry[$id] = new self($data,
                        !empty($questions_data[$id]) ? $questions_data[$id] : array(),
                        !empty($results_data[$id]) ? $results_data[$id] : array());
                    self::$key2id[$data['key']] = $id;
                }
            }
        }
        $result = array();
        foreach($ids as $id) {
            if (!empty(self::$registry[$id])) {
                $result[$id] = self::$registry[$id];
            }
        }
        return $result;
    }

    /**
     * @param string $key
     * @return self|false
     */
    public static function getByKey($key){
        if (empty($key)) {
            return false;
        } elseif (empty(self::$key2id[$key])) {
            $id = \App\Builder::getInstance()->getDB()->query('SELECT `id` FROM ?# WHERE `key` = ?s', self::TABLE, $key)->getCell();
            return self::getById($id);
        } else {
            return self::getById(self::$key2id[$key]);
        }
    }

    /**
     * Возвращает тест по id поста результата тестирования
     * @param $post_id
     * @return false|self
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    public static function getByPostId($post_id){
        if (empty($post_id)){
            return false;
        }
        $db = \App\Builder::getInstance()->getDB();
        $id = $db->query('SELECT `t`.`id` FROM ?# `t` LEFT JOIN ?# `r` ON `t`.`id` = `r`.`test_id` WHERE `t`.`post_id` = ?d OR `r`.`post_id` = ?d',
            self::TABLE,
            self::TABLE_RESULTS,
            $post_id,
            $post_id)->getCell();
        return self::getById($id);
    }

    /**
     * @return self[]
     */
    public static function getAll(){
        $ids = \App\Builder::getInstance()->getDB()->query('SELECT `id` FROM ?#', self::TABLE)->getCol('id', 'id');
        return self::factory($ids);
    }

    /**
     * @param int $id
     * @return self|false
     */
    public static function getById($id){
        if (empty($id)){
            return false;
        }
        $tests = self::factory(array($id));
        return reset($tests);
    }

    /**
     * @param array $data
     * @param array $questions
     * @param array $results
     */
    private function __construct($data, $questions = array(), $results = array()){
        $this->db = \App\Builder::getInstance()->getDB();
        foreach(self::$test_load_fields as $field){
            $this->data[$field] = !empty($data[$field]) ? $data[$field] : NULL;
        }
        $this->prepareQuestions(false, $questions);
        $this->prepareResults(false, $results);
    }

    /**
     * @param array $params
     * @param array $errors
     * @return bool
     */
    public function edit($params = array(), &$errors = array()){
        if (array_key_exists('title', $params) && empty($params['title'])){
            $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
        }
        if (array_key_exists('key', $params)){
            if (empty($params['key'])) {
                $errors['key'] = \Models\Validator::ERR_MSG_EMPTY;
            } elseif (self::checkKeyExists($params['key'], $this['id'])) {
                $errors['key'] = \Models\Validator::ERR_MSG_EXISTS;
            }
        }
        if (!empty($errors)) {
            return false;
        }
        $upd_params = array();
        foreach(self::$test_update_fields as $field) {
            if (array_key_exists($field, $params) && $params[$field] != $this[$field]) {
                $upd_params[$field] = $params[$field];
                $this->data[$field] = $params[$field];
            }
        }
        if (!empty($upd_params)) {
            $this->db->query('UPDATE ?# SET ?a WHERE `id` = ?d', self::TABLE, $upd_params, $this->id);
        }
        return true;
    }

    /**
     * @return bool
     */
    public function delete(){
        $this->db->query('DELETE FROM ?# WHERE `test_id` = ?d', self::TABLE_QUESTIONS, $this['id']);
        $this->db->query('DELETE FROM ?# WHERE `id` = ?d', self::TABLE, $this['id']);
        $post = Post::getById($this['post_id']);
        if (!empty($post)) {
            $post->delete();
        }
        return true;
    }

    /**
     * @param string $title
     * @param string $key
     * @param array $errors
     * @return false|int
     * @throws \ErrorException
     */
    public static function create($title, $key, &$errors = array()){
        if (empty($title)) {
            $errors['title'] = \Models\Validator::ERR_MSG_EMPTY;
        }
        if (empty($key)) {
            $errors['key'] = \Models\Validator::ERR_MSG_EMPTY;
        } elseif (self::checkKeyExists($key)) {
            $errors['key'] = \Models\Validator::ERR_MSG_EXISTS;
        }
        if (!empty($errors)){
            return FALSE;
        }
        $post_id = Post::create(self::POST_TYPE);
        if (empty($post_id)) {
            throw new \ErrorException('Не удается создать пост к тесту');
        }
        $test_id = \App\Builder::getInstance()->getDB()->query('INSERT INTO ?# SET ?a', self::TABLE, array('key' => $key, 'title' => $title, 'post_id' => $post_id));
        return $test_id;
    }

    /**
     * @param string $key
     * @param int $not_id
     * @return bool
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    private static function checkKeyExists($key, $not_id = NULL){
        $db = \App\Builder::getInstance()->getDB();
        return $db->query('SELECT 1 FROM ?# WHERE `key` = ?s{ AND `id` != ?d}',
            self::TABLE,
            $key,
            !empty($not_id) ? $not_id : $db->skipIt())->getCell();
    }


    /** *************************** Questions **************************** */
    /**
     * Загружает вопросы
     * @param bool $from_db — загрузить вопросы из базы
     * @param array $questions — значения вопросов из базы, передаются из фабрики
     * @return array
     */
    private function prepareQuestions($from_db = false, $questions = array()){
        if ($from_db) {
            $questions = $this->db->query('SELECT `' . implode('`, `', self::$question_load_fields) . '`
                    FROM ?# WHERE `test_id` = ?d ORDER BY `position`', self::TABLE_QUESTIONS, $this['id'])
                ->select('id');
        }
        $this->questions = array();
        foreach($questions as $id => $q){
            $q['answers'] = !empty($q['answers']) ? json_decode($q['answers'], true) : array();
            $this->questions[$id] = $q;
        }
        // Очищаем значения минимальных и маскимальных очков,
        // т.к. если мы редактировали вопросы, значения могли измениться
        unset($this->data['min_score']);
        unset($this->data['max_score']);
        return $this->questions;
    }

    /**
     * @param array $data
     * @param array $errors
     * @return array
     */
    private function checkQuestionData($data, &$errors = array()){
        $upd_data = array();
        foreach(self::$question_update_fields as $f){
            $upd_data[$f] = isset($data[$f]) ? $data[$f] : null;
        }
        if (empty($upd_data['question'])) {
            $errors['question'] = \Models\Validator::ERR_MSG_EMPTY;
        }
        if (empty($upd_data['answer_type'])) {
            $errors['answer_type'] = \Models\Validator::ERR_MSG_EMPTY;
        } elseif (!in_array($upd_data['answer_type'], self::$allow_answer_types)) {
            $errors['answer_type'] = \Models\Validator::ERR_MSG_INCORRECT;
        } else {
            if (!is_array($upd_data['answers'])){
                $errors['answers'] = \Models\Validator::ERR_MSG_INCORRECT;
            } else {
                switch($upd_data['answer_type']) {
                    case self::ANSWER_TYPE_VALUE:
                        if (!isset($upd_data['answers']['value_from'])) {
                            $errors['answers[value_from]'] = \Models\Validator::ERR_MSG_EMPTY;
                        } elseif (!is_numeric($upd_data['answers']['value_from']) || $upd_data['answers']['value_from'] < 0) {
                            $errors['answers[value_from]'] = \Models\Validator::ERR_MSG_INCORRECT;
                        }
                        if (!isset($upd_data['answers']['value_to'])) {
                            $errors['answers[value_to]'] = \Models\Validator::ERR_MSG_EMPTY;
                        } elseif (!is_numeric($upd_data['answers']['value_to']) || $upd_data['answers']['value_to'] <= 0) {
                            $errors['answers[value_to]'] = \Models\Validator::ERR_MSG_INCORRECT;
                        }
                        if (empty($errors)){
                            // Убираем лишние параметры
                            $upd_data['answers'] = array(
                                'value_from' => $upd_data['answers']['value_from'],
                                'value_to' => $upd_data['answers']['value_to']
                            );
                        }
                        break;

                    default:
                        // убираем лишние параметры
                        unset($upd_data['answers']['value_to']);
                        unset($upd_data['answers']['value_from']);
                        if (count($upd_data['answers']) < self::MIN_ANSWERS_COUNT) {
                            $errors['answers'] = \Models\Validator::ERR_MSG_TOO_SMALL;
                        }
                        foreach($upd_data['answers'] as $k => $ans_data) {
                            if (empty($ans_data['answer'])) {
                                $errors['answers['.$k.'][answer]'] = \Models\Validator::ERR_MSG_EMPTY;
                            }
                            if (empty($ans_data['value']) && !(isset($ans_data['value']) && is_numeric($ans_data['value']))) {
                                $errors['answers['.$k.'][value]'] = \Models\Validator::ERR_MSG_EMPTY;
                            } elseif (!is_numeric($ans_data['value'])) {
                                $errors['answers['.$k.'][value]'] = \Models\Validator::ERR_MSG_INCORRECT;
                            }
                        }
                        break;
                }
                $upd_data['answers'] = json_encode($upd_data['answers']);
            }
        }
        return $upd_data;
    }

    /**
     * @param array $data
     * @param array $errors
     * @return int|false
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     */
    public function addQuestion($data, &$errors = array()){
        $data = $this->checkQuestionData($data, $errors);
        if (!empty($errors)) {
            return false;
        }
        $data['test_id'] = $this['id'];
        $data['position'] = $this->db->query('SELECT MAX(`position`) FROM `' . self::TABLE_QUESTIONS . '` WHERE `test_id` = ?d', $this['id'])->getCell() + 1;
        $question_id = $this->db->query('INSERT INTO ?# SET ?a', self::TABLE_QUESTIONS, $data);
        $this->prepareQuestions(true);
        return $question_id;
    }

    /**
     * @param int $question_id
     * @param array $data
     * @param array $errors
     * @return bool
     */
    public function editQuestion($question_id, $data, &$errors = array()){
        $question = $this->getQuestionById($question_id);
        if (empty($question)) {
            $errors['id'] = \Models\Validator::ERR_MSG_EMPTY;
            return false;
        }
        $data = $this->checkQuestionData($data, $errors);
        if (!empty($errors)) {
            return false;
        }
        $this->db->query('UPDATE ?# SET ?a WHERE `id` = ?d', self::TABLE_QUESTIONS, $data, $question_id);
        $this->prepareQuestions(true);
        return true;
    }

    /**
     * @return array
     */
    public function getQuestions(){
        if (empty($this->questions)) {
            $this->prepareQuestions(true);
        }
        return $this->questions;
    }

    /**
     * @param int $question_id
     * @return array|null
     */
    public function getQuestionById($question_id){
        $questions = $this->getQuestions();
        return !empty($questions[$question_id]) ? $questions[$question_id] : null;
    }

    /**
     * @param int $question_id
     * @param array $errors
     * @return bool
     */
    public function deleteQuestion($question_id, &$errors = array()){
        $question = $this->getQuestionById($question_id);
        if (empty($question)) {
            $errors['question_id'] = \Models\Validator::ERR_MSG_EMPTY;
            return false;
        }
        $this->db->query('DELETE FROM `' . self::TABLE_QUESTIONS . '` WHERE `id` = ?d', $question_id);
        unset($this->questions[$question_id]);
        return true;
    }

    /**
     * @param int $question_id
     * @param int $new_position
     * @param array $errors
     * @return bool
     */
    public function moveQuestion($question_id, $new_position, &$errors = array()){
        $question = $this->getQuestionById($question_id);
        if (empty($question)) {
            $errors['question_id'] = \Models\Validator::ERR_MSG_EMPTY;
            return false;
        }
        if (empty($new_position)) {
            $errors['position'] = \Models\Validator::ERR_MSG_EMPTY;
            return false;
        }
        if ($question['position'] == $new_position) {
            return true;
        }
        if ($new_position > $question['position']) {
            $this->db->query('
                    UPDATE `' . self::TABLE_QUESTIONS . '`
                    SET `position`=`position`-1
                    WHERE `test_id` = ?d AND `position` <= ?d AND `position` > ?d', $this['id'], $new_position, $question['position']
            );
        } else {
            $this->db->query('
                    UPDATE `' . self::TABLE_QUESTIONS . '`
                    SET `position`=`position`+1
                    WHERE `test_id` = ?d AND `position` >= ?d AND `position` < ?d', $this['id'], $new_position, $question['position']
            );
        }
        $this->db->query('UPDATE `' . self::TABLE_QUESTIONS . '` SET `position` = ?d WHERE `test_id` = ?d AND `id` = ?d', $new_position, $this['id'], $question_id);
        $this->prepareQuestions(true);
        return true;
    }

    /** **************************** Results ***************************** */
    /**
     * Возвращает диапазон результатов тестирования
     * @return array — array('min_score' => int, 'max_score' => int)
     */
    public function getScoreRange(){
        if (empty($this->data['min_score']) || empty($this->data['max_score'])) {
            $min_score = 0;
            $max_score = 0;
            $questions = $this->getQuestions();
            foreach($questions as $q) {
                if ($q['answer_type'] == self::ANSWER_TYPE_VALUE) {
                    $min_score += $q['answers']['value_from'];
                    $max_score += $q['answers']['value_to'];
                } else {
                    $answer = reset($q['answers']);
                    $min = $answer['value'];
                    $max = $answer['value'];
                    foreach($q['answers'] as $answer){
                        if ($answer['value'] < $min) {
                            $min = $answer['value'];
                        }
                        if ($answer['value'] > $max) {
                            $max = $answer['value'];
                        }
                    }
                    $min_score += $min;
                    $max_score += $max;
                }
            }
            $this->data['min_score'] = $min_score;
            $this->data['max_score'] = $max_score;
        }
        return array(
            'min_score' => $this->data['min_score'],
            'max_score' => $this->data['max_score']
        );
    }

    private function prepareResults($from_db = false, $results = array()){
        if ($from_db) {
            $results = $this->db->query('SELECT `' . implode('`, `', self::$result_load_fields) . '` FROM ?#
                    WHERE `test_id` = ?d ORDER BY `max_score`', self::TABLE_RESULTS, $this['id'])->select('id');
        }
        $this->results = $results;
    }

    /**
     * @param int $score
     * @param array $errors
     * @param int|null $not_id
     * @return bool
     */
    private function checkScore($score, &$errors = array(), $not_id = null){
        if (empty($score)) {
            $errors['score'] = \Models\Validator::ERR_MSG_EMPTY;
        } elseif ($this->db->query('SELECT 1 FROM ?# WHERE `max_score` = ?d{ AND `id` != ?d}', self::TABLE_RESULTS, $score, !empty($not_id) ? $not_id : $this->db->skipIt())->getCell()) {
            $errors['score'] = \Models\Validator::ERR_MSG_EXISTS;
        } elseif ($score > $this['max_score'] || $score < $this['min_score']) {
            $errors['score'] = 'out_of_range';
        }
        return empty($errors);
    }

    /**
     * @param int $score
     * @param array $errors
     * @return bool|FALSE|int|\MysqlSimple\Result
     * @throws ContentManagement\Exception
     */
    public function addResult($score, &$errors = array()){
        if (!$this->checkScore($score, $errors)){
            return false;
        }
        $post_id = Post::create(self::POST_TYPE);
        $result_id = $this->db->query('INSERT INTO ?# SET `test_id` = ?d, `max_score` = ?d, `post_id` = ?d',
            self::TABLE_RESULTS,
            $this['id'],
            $score,
            $post_id);
        $this->prepareResults(true);
        return $result_id;
    }

    /**
     * @param int $result_id
     * @param int $score
     * @param array $errors
     * @return bool
     */
    public function editResult($result_id, $score, &$errors = array()){
        $result = $this->getResultById($result_id);
        if (empty($result)) {
            $errors['id'] = \Models\Validator::ERR_MSG_EMPTY;
            return false;
        }
        if (!$this->checkScore($score, $errors, $result_id)){
            return false;
        }
        $this->db->query('UPDATE ?# SET `max_score` = ?d WHERE `id` = ?d', self::TABLE_RESULTS, $score, $result_id);
        $this->prepareResults(true);
        return true;
    }

    public function deleteResult($result_id, &$errors = array()){
        $result = $this->getResultById($result_id);
        if (empty($result)) {
            $errors['id'] = \Models\Validator::ERR_MSG_EMPTY;
            return false;
        }
        $post = Post::getById($result['post_id']);
        if (!empty($post)) {
            $post->delete();
        }
        $this->db->query('DELETE FROM ?# WHERE `id` = ?d', self::TABLE_RESULTS, $result_id);
        $this->prepareResults(true);
        return true;
    }

    public function getResults(){
        if (empty($this->results)) {
            $this->prepareResults(true);
        }
        return $this->results;
    }

    public function getResultById($result_id){
        $results = $this->getResults();
        return !empty($results[$result_id]) ? $results[$result_id] : null;
    }

    /**
     * Возвращает диапазон очков теста для указанного поста
     * @param $post_id
     * @return array
     */
    public function getScoreRangeByPostId($post_id){
        $min_score = $this['min_score'];
        $max_score = NULL;
        foreach($this['results'] as $res){
            if ($res['post_id'] == $post_id) {
                $max_score = $res['max_score'];
                break;
            } else {
                $min_score = $res['max_score'];
            }
        }
        if (empty($max_score)) {
            if ($this['post_id'] == $post_id) {
                $max_score = $this['max_score'];
            } else {
                $min_score = NULL;
            }
        }
        return array(
            'min_score' => $min_score,
            'max_score' => $max_score
        );
    }

    /**
     * Обрабатывает результаты тестирования и возвращает соответствующий пост
     * @param array $test_data
     * @param array $errors
     * @return Post|false
     */
    public function getResult($test_data, &$errors = array()){
        $questions = $this->getQuestions();
        $score = 0;
        foreach($questions as $id => $q){
            if (!isset($test_data[$id])){
                $errors['answer['.$id.']'] = \Models\Validator::ERR_MSG_EMPTY;
            } elseif (is_array($test_data[$id]) xor $q['answer_type'] == self::ANSWER_TYPE_MULTI_ANSWER) {
                $errors['answer['.$id.']'] = \Models\Validator::ERR_MSG_INCORRECT;
            } else {
                switch($q['answer_type']){
                    case self::ANSWER_TYPE_VALUE:
                        if ($test_data[$id] > $q['answers']['value_to'] || $test_data[$id] < $q['answers']['value_from']) {
                            $errors['answer['.$id.']'] = \Models\Validator::ERR_MSG_INCORRECT;
                        } else {
                            $score += $test_data[$id];
                        }
                        break;
                    default:
                        $ansIds = is_array($test_data[$id]) ? $test_data[$id] : array($test_data[$id]);
                        foreach($ansIds as $ansId) {
                            if (empty($q['answers'][$ansId])) {
                                $errors['answer['.$id.']'] = \Models\Validator::ERR_MSG_INCORRECT;
                            } else {
                                $score += $q['answers'][$ansId]['value'];
                            }
                        }
                        break;
                }
            }
        }
        if (!empty($errors)){
            return false;
        } else {
            $results = $this->getResults();
            $post_id = NULL;
            foreach($results as $res) {
                if ($res['max_score'] >= $score) {
                    $post_id = $res['post_id'];
                    break;
                }
            }
            return Post::getById(!empty($post_id) ? $post_id : $this['post_id']);
        }
    }

//    public function

    /** ****************************************************************** */

    public function asArray(){
        $result = array();
        foreach(array_merge(self::$test_load_fields, self::$additional_fields) as $field) {
            $result[$field] = $this[$field];
        }
        return $result;
    }

    /** ************************** ArrayAccess *************************** */

    public function offsetExists($offset){
        return in_array($offset, array_merge(self::$test_load_fields, self::$additional_fields));
    }

    public function offsetGet($offset){
        if (in_array($offset, self::$test_load_fields)){
            return $this->data[$offset];
        } elseif (in_array($offset, self::$additional_fields)) {
            if ($offset == 'questions') {
                return $this->getQuestions();
            } elseif ($offset == 'results') {
                return $this->getResults();
            } elseif (in_array($offset, array('min_score', 'max_score'))) {
                $score_range = $this->getScoreRange();
                return $score_range[$offset];
            }
        } else {
            throw new \LogicException('No key ' . $offset . ' in ' . __CLASS__);
        }
    }

    public function offsetSet($offset, $value){
        throw new \Exception(get_class() . ' has only immutable Array Access');
    }

    public function offsetUnset($offset){
        throw new \Exception(get_class() . ' has only immutable Array Access');
    }
}