<?php

namespace Modules\Site;
/**
 * Опросы на сайте
 *
 * @author Charles Manson
 */
use Models\Questions;
class QuestionsAdmin extends \LPS\AdminModule{
    
    const SINGLE_POOL = true;
    const DEFAULT_POOL_KEY = 'questions';
    
    /**
     * Список опросов или редирект на редактирование если опрос один
     */
    public function index(){
        if (self::SINGLE_POOL){
            return $this->redirect($this->getModuleUrl() . 'editPool/?key=' . self::DEFAULT_POOL_KEY);
        }
    }
    /**
     * Редактирование опроса
     */
    public function editPool(){
        $pool = Questions::getByKey(self::DEFAULT_POOL_KEY);
        
        $this->getAns()
            ->add('questions', $pool->getQuestions())
            ->add('title', 'Опрос на странице "Сопровождение продаж"');
    }
    
    public function addQuestion(){
        $ans = $this->setJsonAns();
        $question = $this->request->request->get('question');
        if (empty($question)){
            $ans->setEmptyContent()->addError('question', 'empty');
        } else {
            $pool = Questions::getByKey(self::DEFAULT_POOL_KEY);
            $question = $pool->addQuestion(array(
                'question' => $question,
                'answers' => array(
                    1 => 'Ответ'
                ), 
                'multi_answer' => 0
            ));
            $ans->add('question', $question);
        }
    }
    
    public function deleteQuestion(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $id = $this->request->request->get('id');
        if (empty($id)){
            $ans->addError('id', 'empty');
        } else {
            $pool = Questions::getByKey(self::DEFAULT_POOL_KEY);
            $pool->deleteQuestion($id);
            $ans->addData('status', 'ok');
        }
    }
    
    public function saveQuestions(){
        $this->setJsonAns()->setEmptyContent();
        $questions = $this->request->request->get('question');
        $answers = $this->request->request->get('answer');
        $multi_answer = $this->request->request->get('multi_answer', array());
        $notes = $this->request->request->get('note', array());
        if (empty($questions) || empty($answers)){
            $this->getAns()->addError('questions', 'empty');
        } else{
            $data = array();
            $quest_err = array();
            $ans_err = array();
            foreach($questions as $id=>$question){
                if (empty($question)){
                    $quest_err[$id] = 'empty';
                    continue;
                }
                if (empty($answers[$id])){
                    $ans_err[$id] = 'empty';
                    continue;
                }
                foreach($answers[$id] as $ans){
                    if (empty($ans)){
                        $ans_err[$id] = 'dont_filled';
                        continue;
                    }
                }
                $data[$id] = array(
                    'question' => $question,
                    'answers' => $answers[$id],
                    'note' => !empty($notes[$id]) ? $notes[$id] : '',
                    'multi_answer' => !empty($multi_answer[$id]) ? 1 : 0
                );
            }
            if (empty($quest_err) && empty($ans_err) && !empty($data)){
                $pool = Questions::getByKey(self::DEFAULT_POOL_KEY);
                $pool->editQuestions($data);
                $this->getAns()->addData('status', 'ok');
            } else {
                $this->getAns()->setErrors(array(
                    'question' => $quest_err,
                    'answer' => $ans_err
                ));
            }
        }            
    }
    
    public function questionList(){
        $pool = Questions::getByKey(self::DEFAULT_POOL_KEY);
        $question_id = $this->request->request->get('question_id');
        $new_position = $this->request->request->get('position');
        if ($question_id !== NULL && $new_position !== NULL){
            $pool->sort($question_id, $new_position);
        }
        $this->setAjaxResponse()
            ->add('questions', $pool->getQuestions());
    }
    
}
