<?php
/**
 * Description of Admin
 *
 * @author olga
 */
namespace Modules\Faq;
use Models\Faq;
class Admin extends \LPS\AdminModule{
    public function index(){
        $this->listing(true);
    }
    
    public function listing($inner = false){
        if (!$inner){
            $this->setAjaxResponse();
        }
        $faqs = Faq::search(array('status' => array(Faq::STATUS_PUBLIC, Faq::STATUS_HIDDEN)));
        $this->getAns()->add('faqs', $faqs);
    }
    
    public function edit(){
        $id = $this->request->request->get('id');
        if (empty($id)){
            $id = Faq::create();
        }
        $faq = Faq::getById($id);
        $data = $this->request->request->all();
        if (!empty($faq) && !empty($data)){
            $faq->update($data);
        }
        return $this->run('listing');
    }
    
    public function editFields(){
        $this->setAjaxResponse();
        $id = $this->request->request->get('id');
        $faq = Faq::getById($id);
        if (!empty($faq)){
            $this->getAns()->setFormData($faq->asArray());
        }
    }
    
    public function hide(){
        $id = $this->request->request->get('id');
        $faq = Faq::getById($id);
        if (!empty($faq)){
            $faq->setStatus(Faq::STATUS_HIDDEN);
        }
        return $this->run('listing');
    }
    public function show(){
        $id = $this->request->request->get('id');
        $faq = Faq::getById($id);
        if (!empty($faq)){
            $faq->setStatus(Faq::STATUS_PUBLIC);
        }
        return $this->run('listing');
    }
    public function delete(){
        $id = $this->request->request->get('id');
        $faq = Faq::getById($id);
        if (!empty($faq)){
            $faq->setStatus(Faq::STATUS_DELETE);
        }
        return $this->run('listing');
    }
    public function changePosition(){
        $id = $this->request->request->get('id');
        $position = $this->request->request->get('position');
        $faq = Faq::getById($id);
        if (!empty($faq) && !empty($position)){
            $faq->move($position);
        }
        return $this->run('listing');
    }
}

?>
