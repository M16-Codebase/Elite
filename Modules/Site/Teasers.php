<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 09.07.15
 * Time: 18:00
 */

namespace Modules\Site;


use Models\Teaser;

class Teasers extends \LPS\AdminModule{

    public function index(){
        $this->teasers(true);
    }

    public function teasers($inner = FALSE){
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        $teasers = Teaser::search();
        $ans->add('teasers', $teasers);
    }

    public function teaserFields(){
        $ans = $this->setAjaxResponse();
        $teaser_id = $this->request->request->get('id');
        $teaser = Teaser::getById($teaser_id);
        if (!empty($teaser)){
            $ans->add('teaser', $teaser)
                ->setFormData(array(
                    'id' => $teaser['id'],
                    'url' => $teaser['url'],
                    'title' => $teaser['title'],
                    'date_start' => !empty($teaser['date_start']) ? date('d.m.Y', strtotime($teaser['date_start'])) : NULL,
                    'date_end' => !empty($teaser['date_end']) ? date('d.m.Y', strtotime($teaser['date_end'])) : NULL,
                    'active' => $teaser['active'],
                    'cant_activate' => $teaser['cant_activate']
                ));
        }
    }

    public function edit(){
        $errors = array();
        $teaser_id = $this->request->request->get('id');
        $params['url'] = $this->request->request->get('url');
        $params['image'] = $this->request->files->get('image');
        $params['title'] = $this->request->request->get('title');
        $active = $this->request->request->get('active');
        if (!is_null($active)){
            $params['active'] = !empty($active) ? 1 : 0;
        }
        if (empty($params['title'])){
            $errors['title'] = 'empty';
        }
        if (!empty($teaser_id)){
            $teaser = Teaser::getById($teaser_id);
            if (!empty($params['image'])) {
                /** @var \Models\ImageManagement\Image $image */
                $image = $teaser['image'];
                $image->reload($params['image']);
                \Models\ImageManagement\Image::clearRegistry(array($image['id']));
            }
        } elseif (!empty($params['image'])){
            if (!empty($params['title'])){
                $teaser = Teaser::create($params['image'], $params['title'], $errors);
            }
        } else {
            $teaser = NULL;
            $errors['image'] = 'empty';
        }
        if (empty($teaser) && empty($errors)){
            $errors['teaser'] = 'cant_create';
        }
        if (empty($errors)){
            $date_start = $this->request->request->get('date_start');
            $date_end = $this->request->request->get('date_end');
            $params['date_start'] = !empty($date_start) ? strtotime($date_start) : NULL;
            $params['date_end'] = !empty($date_end) ? strtotime($date_end) : NULL;
            $teaser->update($params, $errors);
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            Teaser::clearRegistry();
            return $this->run('teasers');
        }
    }

    public function activate(){
        $active = $this->request->request->get('active');
        $active = !empty($active) ? 1 : 0;
        $id = $this->request->request->get('id');
        $teaser = Teaser::getById($id);
        $teaser->update(array('active' => $active), $errors);
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('teasers');
        }
    }

    public function delete(){
        Teaser::delete($this->request->request->get('id'), $errors);
        if (empty($errors)){
            return $this->run('teasers');
        }else{
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }
}