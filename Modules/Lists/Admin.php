<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Admin
 *
 * @author olga
 */
namespace Modules\Lists;

class Admin extends \LPS\AdminModule{
    public function index(){
        return $this->notFound();
    }
    
    /* ******* Языки ******* */
    public function segment(){
        $this->segmentList(true);
    }

    public function segmentList($inner = false){
        if (!$inner){
            $this->setJsonAns();
        }
        $segments = \App\Segment::getInstance()->getAll();
        $this->getAns()->add('segments', $segments);
    }

    public function editSegment(){
        $id = $this->request->request->get('id');
        $data = array(
            'title' => $this->request->request->get('title'),
            'key' => $this->request->request->get('key')
        );
        $segment_controller = \App\Segment::getInstance();
        if (empty($id)){
            $segment_controller->create($data['key'], $data['title'], $errors);
        } else {
            $segment = $segment_controller->getById($data['id']);
            if (empty($segment)){
                $errors['id'] = \Models\Validator::ERR_MSG_EMPTY;
            } else {
                $segment->update($data);
            }
        }
        if (empty($errors)) {
            return $this->run('segmentList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }
    public function editFieldsSegment(){
        $this->setJsonAns();
        $segment = \App\Segment::getInstance()->getById($this->request->request->get('id'));
        if (!empty($segment)){
            $this->getAns()->setFormData($segment->asArray());
        }
    }

    public function delSegment(){
        $id = $this->request->request->get('id');
        $segment_controller = \App\Segment::getInstance();
        if ($segment_controller->delete($id, $error)) {
            return $this->run('segmentList');
        } else {
            $this->setJsonAns()->setEmptyContent()->addErrorByKey('id', 'empty');
        }
    }
}