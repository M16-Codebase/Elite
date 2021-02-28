<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 16.04.15
 * Time: 18:45
 * Загрузка временных изображений при редактировании еще несозданного поста
 */

namespace Modules\Images;


use Models\ImageManagement\Image;
use Models\ImageManagement\TmpCollection;

class TmpImages extends \LPS\AdminModule{

    public function index(){
        $this->notFound();
    }

    public function upload(){
        $errors = array();
        $gallery_data = json_decode($this->request->request->get('gallery_data'), true);
        $post_hash = $this->request->request->get('gallery_dir');
        $gallery = TmpCollection::getGallery($post_hash, !empty($gallery_data) ? $gallery_data : array());
        if (empty($post_hash)) {
            $errors['gallery_dir'] = 'empty';
        } elseif(empty($gallery)) {
            $errors['gallery'] = 'error';
        } else {
            /* @var $file_image \Symfony\Component\HttpFoundation\File\UploadedFile */
            $file_image = $this->request->files->get('image');
            if (!empty($file_image)) {
                if (!$file_image->getError()) {
                    $gallery->upload($file_image, $errors);
                }else{
                    $errors['image_action_status'] = $file_image->getError();
                }
            } else {
                $errors['image'] = 'empty';
            }
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('fileList');
        }
    }

    public function fileList(){
        $ans = $this->setJsonAns();
        $gallery_dir = $this->request->request->get('gallery_dir');
        if (empty($gallery_dir)){
            $ans->setEmptyContent()->addErrorByKey('gallery_dir', 'empty');
        } else {
            $gallery_data = json_decode($this->request->request->get('gallery_data'), true);
            $gallery = TmpCollection::getGallery($gallery_dir, !empty($gallery_data) ? $gallery_data : array());
            $ans->add('galleryImages', $gallery->getImages())
                ->add('gallery_dir', $gallery_dir);
        }
    }

    public function crop(){
        $x1 = $this->request->request->get('x1');
        $y1 = $this->request->request->get('y1');
        $x2 = $this->request->request->get('x2');
        $y2 = $this->request->request->get('y2');
        $errors = array();
        $post_hash = $this->request->request->get('gallery_dir');
        $gallery_data = json_decode($this->request->request->get('gallery_data'), true);
        $filename = $this->request->request->get('filename');
        $gallery = TmpCollection::getGallery($post_hash, !empty($gallery_data) ? $gallery_data : array());
        if (empty($post_hash)) {
            $errors['gallery_dir'] = 'empty';
        } elseif (empty($gallery)){
            $errors['gallery'] = 'empty';
        } elseif (empty($filename)) {
            $errors['filename'] = 'empty';
        } elseif (empty($x1) || empty($y1) || empty($x2) || empty($y2)){
            $errors['coords'] = 'empty';
        } else {
            $gallery->crop($filename, array('x1' => $x1, 'x2' => $x2, 'y1' => $y1, 'y2' => $y2), $errors);
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('fileList');
        }
    }

    public function reload(){
        $errors = array();
        $gallery_data = json_decode($this->request->request->get('gallery_data'), true);
        $post_hash = $this->request->request->get('gallery_dir');
        $gallery = TmpCollection::getGallery($post_hash, !empty($gallery_data) ? $gallery_data : array());
        $filename = $this->request->request->get('filename');
        if (empty($post_hash)) {
            $errors['gallery_dir'] = 'empty';
        } elseif(empty($gallery)) {
            $errors['gallery'] = 'error';
        } elseif(empty($filename)){
            $errors['filename'] = 'empty';
        } else {
            /* @var $file_image \Symfony\Component\HttpFoundation\File\UploadedFile */
            $file_image = $this->request->files->get('image');
            if (!empty($file_image)) {
                if (!$file_image->getError()) {
                    $gallery->reload($file_image, $filename, $errors);
                }else{
                    $errors['image_action_status'] = $file_image->getError();
                }
            } else {
                $errors['image'] = 'empty';
            }
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('fileList');
        }
    }

    public function delete(){
        $errors = array();
        $post_hash = $this->request->request->get('gallery_dir');
        $gallery_data = json_decode($this->request->request->get('gallery_data'), true);
        $filename = $this->request->request->get('filename');
        $gallery = TmpCollection::getGallery($post_hash, !empty($gallery_data) ? $gallery_data : array());
        if (empty($post_hash)){
            $errors['gallery_dir'] = 'empty';
        } elseif (empty($filename)) {
            $errors['filename'] = 'empty';
        } elseif (empty($gallery)){
            $errors['gallery'] = 'empty';
        } else {
            $gallery->delete($filename, $errors);
        }
        if (!empty($errors)) {
            $this->setJsonAns()->setErrors($errors)->setEmptyContent();
        } else {
            return $this->run('fileList');
        }
    }

    public function saveDescription(){
        $errors = array();
        $post_hash = $this->request->request->get('gallery_dir');
        $gallery_data = json_decode($this->request->request->get('gallery_data'), true);
        $filename = $this->request->request->get('filename');
        $gallery = TmpCollection::getGallery($post_hash, !empty($gallery_data) ? $gallery_data : array());
        $image_text = $this->request->request->get('image_text');
        if (empty($post_hash)){
            $errors['gallery_dir'] = 'empty';
        } elseif (empty($filename)) {
            $errors['filename'] = 'empty';
        } elseif (empty($gallery)) {
            $errors['gallery'] = 'empty';
        } else {
            $gallery->setText($filename, $image_text, $errors);
        }
        if (!empty($errors)) {
            $this->setJsonAns()->setErrors($errors)->setEmptyContent();
        } else {
            return $this->run('fileList');
        }
    }

    public function setCover(){
        $errors = array();
        $post_hash = $this->request->request->get('gallery_dir');
        $gallery_data = json_decode($this->request->request->get('gallery_data'), true);
        $filename = $this->request->request->get('filename');
        $gallery = TmpCollection::getGallery($post_hash, !empty($gallery_data) ? $gallery_data : array());
        if (empty($post_hash)){
            $errors['gallery_dir'] = 'empty';
        } elseif (empty($filename)) {
            $errors['filename'] = 'empty';
        } elseif (empty($gallery)){
            $errors['gallery'] = 'empty';
        } else {
            $gallery->setCover($filename, $errors);
        }
        if (!empty($errors)) {
            $this->setJsonAns()->setErrors($errors)->setEmptyContent();
        } else {
            return $this->run('fileList');
        }

    }

    public function changeHidden(){
        $errors = array();
        $post_hash = $this->request->request->get('gallery_dir');
        $gallery_data = json_decode($this->request->request->get('gallery_data'), true);
        $filename = $this->request->request->get('filename');
        $gallery = TmpCollection::getGallery($post_hash, !empty($gallery_data) ? $gallery_data : array());
        $hidden = $this->request->request->get('hidden');
        if (empty($post_hash)){
            $errors['gallery_dir'] = 'empty';
        } elseif (empty($filename)) {
            $errors['filename'] = 'empty';
        } elseif (empty($gallery)) {
            $errors['gallery'] = 'empty';
        } elseif (!in_array($hidden, array(0, 1))) {
            $errors['hidden'] = 'incorrect';
        } else {
            $gallery->changeHidden($filename, $hidden, $errors);
        }
        if (!empty($errors)) {
            $this->setJsonAns()->setErrors($errors)->setEmptyContent();
        } else {
            return $this->run('fileList');
        }

    }

    public function setGravity(){
        $errors = array();
        $post_hash = $this->request->request->get('gallery_dir');
        $gallery_data = json_decode($this->request->request->get('gallery_data'), true);
        $filename = $this->request->request->get('filename');
        $gallery = TmpCollection::getGallery($post_hash, !empty($gallery_data) ? $gallery_data : array());
        $gravity = $this->request->request->get('gravity');
        if (empty($post_hash)){
            $errors['gallery_dir'] = 'empty';
        } elseif (empty($filename)) {
            $errors['filename'] = 'empty';
        } elseif (empty($gallery)) {
            $errors['gallery'] = 'empty';
        } elseif (!in_array($gravity, array('TL', 'T', 'TR', 'L', 'C', 'R', 'BL', 'B', 'BR'))) {
            $errors['gravity'] = 'incorrect';
        } else {
            $gallery->setGravity($filename, $gravity, $errors);
        }
        if (!empty($errors)) {
            $this->setJsonAns()->setErrors($errors)->setEmptyContent();
        } else {
            return $this->run('fileList');
        }

    }

    public function changePosition(){
        $errors = array();
        $post_hash = $this->request->request->get('gallery_dir');
        $gallery_data = json_decode($this->request->request->get('gallery_data'), true);
        $filename = $this->request->request->get('filename');
        $gallery = TmpCollection::getGallery($post_hash, !empty($gallery_data) ? $gallery_data : array());
        $position = $this->request->request->get('position');
        if (empty($post_hash)){
            $errors['gallery_dir'] = 'empty';
        } elseif (empty($filename)) {
            $errors['filename'] = 'empty';
        } elseif (empty($gallery)) {
            $errors['gallery'] = 'empty';
        } elseif (!is_numeric($position)) {
            $errors['position'] = 'incorrect';
        } else {
            $gallery->changePosition($filename, $position, $errors);
        }
        if (!empty($errors)) {
            $this->setJsonAns()->setErrors($errors)->setEmptyContent();
        } else {
            return $this->run('fileList');
        }
    }

}