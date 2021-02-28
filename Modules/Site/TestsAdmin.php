<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 06.08.15
 * Time: 14:11
 */

namespace Modules\Site;

use Models\Test as TestEntity;

class TestsAdmin extends \LPS\AdminModule
{
    const SINGLE_TEST = true;
    const DEFAULT_TEST_KEY = 'sealex';
    const DEFAULT_TEST_TITLE = 'Тест о сеалексе';

    public function index(){
        if (self::SINGLE_TEST) {
            return $this->redirect($this->getModuleUrl() . 'viewTest/');
        }
    }

    public function viewTest(){
        if (self::SINGLE_TEST) {
            $test = TestEntity::getByKey(self::DEFAULT_TEST_KEY);
            if (empty($test)) {
                $test_id = TestEntity::create(self::DEFAULT_TEST_TITLE, self::DEFAULT_TEST_KEY, $errors);
                $test = TestEntity::getById($test_id);
            }
        } else {
            $test = TestEntity::getById($this->request->query->get('id'));
        }
        if (empty($test)) {
            return $this->notFound();
        }

        $this->getAns()
            ->add('test_entity', $test);
    }

    /** *********************************************** Questions ************************************************** */

    public function questionsList() {
        $ans = $this->setJsonAns();
        $test = self::SINGLE_TEST
            ? TestEntity::getByKey(self::DEFAULT_TEST_KEY)
            : TestEntity::getById($this->request->request->get('test_id'));
        if (empty($test)) {
            $ans->setEmptyContent()
                ->addErrorByKey('test_id', \Models\Validator::ERR_MSG_EMPTY);
        }
        $ans->add('test_entity', $test);
    }

    public function questionFields() {
        $ans = $this->setJsonAns();
        $errors = array();
        $test = self::SINGLE_TEST
            ? TestEntity::getByKey(self::DEFAULT_TEST_KEY)
            : TestEntity::getById($this->request->request->get('test_id'));
        if (empty($test)) {
            $errors['test_id'] = \Models\Validator::ERR_MSG_EMPTY;
        } else {
            $question_id = $this->request->request->get('id');
            if (empty($question_id)) {
                $ans->add('test_entity', $test)
                    ->setFormData(array(
                        'test_id' => $test['id']
                    ));
            } else {
                $question = $test->getQuestionById($question_id);
                if (empty($question)) {
                    $errors['id'] = \Models\Validator::ERR_MSG_EMPTY;
                } else {
                    $ans->add('test_entity', $test)
                        ->add('question', $question)
                        ->setFormData($question);
                }
            }
        }
        if (!empty($errors)) {
            $ans->setEmptyContent()
                ->setErrors($errors);
        }
    }

    public function editQuestion() {
        $errors = array();
        $test = self::SINGLE_TEST
            ? TestEntity::getByKey(self::DEFAULT_TEST_KEY)
            : TestEntity::getById($this->request->request->get('test_id'));
        if (empty($test)) {
            $errors['test_id'] = \Models\Validator::ERR_MSG_EMPTY;
        } else {
            $question_id = $this->request->request->get('id');
            if (empty($question_id)) {
                $test->addQuestion($this->request->request->all(), $errors);
            } else {
                $test->editQuestion($question_id, $this->request->request->all(), $errors);
            }
        }
        if (empty($errors)) {
            return $this->run('questionsList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }

    public function moveQuestion(){
        $errors = array();
        $test = self::SINGLE_TEST
            ? TestEntity::getByKey(self::DEFAULT_TEST_KEY)
            : TestEntity::getById($this->request->request->get('test_id'));
        if (empty($test)) {
            $errors['test_id'] = \Models\Validator::ERR_MSG_EMPTY;
        } else {
            $question_id = $this->request->request->get('id');
            $new_position = $this->request->request->get('position');
            $test->moveQuestion($question_id, $new_position, $errors);
        }
        if (empty($errors)) {
            return $this->run('questionsList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }

    public function deleteQuestion(){
        $errors = array();
        $test = self::SINGLE_TEST
            ? TestEntity::getByKey(self::DEFAULT_TEST_KEY)
            : TestEntity::getById($this->request->request->get('test_id'));
        if (empty($test)) {
            $errors['test_id'] = \Models\Validator::ERR_MSG_EMPTY;
        } else {
            $test->deleteQuestion($this->request->request->get('id'), $errors);
        }
        if (empty($errors)) {
            return $this->run('questionsList');
        } else {
            $this->setJsonAns()
                ->setEmptyContent()
                ->setErrors($errors);
        }
    }

    /** ************************************************ Results *************************************************** */

    public function resultsList() {
        $ans = $this->setJsonAns();
        $test = self::SINGLE_TEST
            ? TestEntity::getByKey(self::DEFAULT_TEST_KEY)
            : TestEntity::getById($this->request->request->get('test_id'));
        if (empty($test)) {
            $ans->setEmptyContent()
                ->addErrorByKey('test_id', \Models\Validator::ERR_MSG_EMPTY);
        }
        $ans->add('test_entity', $test);
    }

    public function resultFields(){
        $ans = $this->setJsonAns();
        $errors = array();
        $test = self::SINGLE_TEST
            ? TestEntity::getByKey(self::DEFAULT_TEST_KEY)
            : TestEntity::getById($this->request->request->get('test_id'));
        if (empty($test)) {
            $errors['test_id'] = \Models\Validator::ERR_MSG_EMPTY;
        } else {
            $result_id = $this->request->request->get('id');
            if (empty($result_id)) {
                $ans->add('test_entity', $test)
                    ->setFormData(array(
                        'test_id' => $test['id']
                    ));
            } else {
                $result = $test->getResultById($result_id);
                if (empty($result)) {
                    $errors['id'] = \Models\Validator::ERR_MSG_EMPTY;
                } else {
                    $ans->add('test_entity', $test)
                        ->add('result', $result)
                        ->setFormData($result);
                }
            }
        }
        if (!empty($errors)) {
            $ans->setEmptyContent()
                ->setErrors($errors);
        }
    }

    public function editResult(){
        $errors = array();
        $test = self::SINGLE_TEST
            ? TestEntity::getByKey(self::DEFAULT_TEST_KEY)
            : TestEntity::getById($this->request->request->get('test_id'));
        if (empty($test)) {
            $errors['test_id'] = \Models\Validator::ERR_MSG_EMPTY;
        } else {
            $result_id = $this->request->request->get('id');
            if (empty($result_id)) {
                $test->addResult($this->request->request->get('score'), $errors);
            } else {
                $test->editResult($result_id, $this->request->request->get('score'), $errors);
            }
        }
        if (empty($errors)) {
            return $this->run('resultsList');
        } else {
            $this->setJsonAns()
                ->setEmptyContent()
                ->setErrors($errors);
        }
    }

    public function deleteResult(){
        $errors = array();
        $test = self::SINGLE_TEST
            ? TestEntity::getByKey(self::DEFAULT_TEST_KEY)
            : TestEntity::getById($this->request->request->get('test_id'));
        if (empty($test)) {
            $errors['test_id'] = \Models\Validator::ERR_MSG_EMPTY;
        } else {
            $test->deleteResult($this->request->request->get('id'), $errors);
        }
        if (empty($errors)) {
            return $this->run('resultsList');
        } else {
            $this->setJsonAns()
                ->setEmptyContent()
                ->setErrors($errors);
        }
    }

    public function edit(){
        $post_id = $this->request->query->get('id');
        $test = TestEntity::getByPostId($post_id);
        if (empty($test)){
            return $this->notFound();
        }
        $post_score_range = $test->getScoreRangeByPostId($post_id);
        $this->getAns()
            ->add('test_entity', $test)
            ->add('post_score_range', $post_score_range);
        $result = $this->editPost(TRUE);
        if (!is_null($result)){
            return $result;
        }
    }

    public function editPost($inner = false){
        $this->getModule('Posts\Pages')->editPost($inner);
    }

    public function checkTest(){
        $test = self::SINGLE_TEST
            ? TestEntity::getByKey(self::DEFAULT_TEST_KEY)
            : TestEntity::getById($this->request->request->get('test_id'));
        $errors = array();
        if (empty($test)){
            return $this->notFound();
        }
        $post = $test->getResult($this->request->request->get('answer'), $errors);
        if (empty($errors)){
            return $this->redirect($this->getModuleUrl() . 'edit/?id=' . $post['id']);
        } else {
            $this->setJsonAns()
                ->setEmptyContent()
                ->setErrors($errors);
        }
    }
}