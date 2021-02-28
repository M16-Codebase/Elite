<?php

/**
 * Description of Admin
 *
 * @author olga
 */

namespace Modules\Images;
use Models\ImageManagement\Image;
use Models\ImageManagement\Collection;
use Models\ImageManagement\CollectionImage;
use Models\ImageManagement\DefaultImageCollection;
use Models\CatalogManagement\CatalogHelpers\CatalogPosition\ValuesLogger;
use Models\Logger;

class Admin extends \LPS\AdminModule{

    public function index(){
        $collection = Collection::getById(DefaultImageCollection::COLLECTION_ID);
        $this->getAns()->add('collection', $collection);
    }

    public function upload(){
        $errors = array();
        $id = $this->request->request->get('id');
        if (!empty($id)){ //id коллекции
            $collection = Collection::getById($id);
			/* @var $file_image \Symfony\Component\HttpFoundation\File\UploadedFile */
            $file_image = $this->request->files->get('image');
            if (!empty($file_image)){
				if (!$file_image->getError()){
					$image = $collection->addImage(
						$file_image, $this->request->request->get('image_text'), $error
					);
                    if (!empty($error)){
                        $errors['image'] = $error;
                    } elseif (empty($collection['cover_id'])) {
                        $collection->setCover($image['id']);
                    }
				}else{
                    $errors['image_action_status'] = $file_image->getError();
				}
            }else{
                $errors['image'] = 'empty';
            }
        }else{
            $errors['collection_id'] = 'empty';
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('fileList');
        }
    }

    public function crop(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $image_id = $this->request->request->get('id');
        $image = Image::getById($image_id);
        $x1 = $this->request->request->get('x1');
        $y1 = $this->request->request->get('y1');
        $x2 = $this->request->request->get('x2');
        $y2 = $this->request->request->get('y2');
        if (empty($image)){
            $ans->addErrorByKey('image_id', 'empty');
        } elseif (empty($x1) || empty($y1) || empty($x2) || empty($y2)){
            $ans->addErrorByKey('coords', 'empty');
        } else {
            $x = min($x1, $x2);
            $y = min($y1, $y2);
            $width = abs($x2 - $x1);
            $height = abs($y2 - $y1);
            $image->crop($x, $y, $width, $height, $error);
            if (!empty($error)){
                $ans->addErrorByKey('image', $error);
            }
        }
    }

    public function reload(){
        $errors = array();
        $image_id = $this->request->request->get('image_id');
        if (!empty($image_id)){//id картинки
            $image = CollectionImage::getById($image_id);
            if (empty($image)){
                return $this->run('fileList');
            }
			$collection_id = $image['collection_id'];
            if (!empty($image)) {
                $collection = Collection::getById($collection_id);
            }
            $file_image = $this->request->files->get('image');
            if (!empty($file_image)) {
				$error = $image->reload($file_image);
                if (!empty($error)){
                    $errors['image'] = $error;
                }
            }else{
                $errors['image'] = 'empty';
            }
        }else{
            $errors['image_id'] = 'empty';
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('fileList');
        }
    }

    public function fileList(){
        $this->setJsonAns();
        $collection_id = $this->request->request->get('id', $this->request->query->get('id'));
        if (empty($collection_id)){
            $image_id = $this->request->request->get('image_id', $this->request->query->get('image_id'));
            $image = CollectionImage::getById($image_id);
            $collection_id = $image['collection_id'];
        }
        $collection = Collection::getById($collection_id);
        if (empty($collection))
            return 'error: no_collection';
        $form_data = array();
        $this->getAns()->add('images', $collection->getImages())
            ->add('hasSide', 0)
            ->add('images_path', $collection->getPath())
            ->add('gallery', $collection)
            ->setFormData($form_data);
    }

    public function delete(){
        $errors = array();
        $image_id = $this->request->request->get('image_id', $this->request->query->get('image_id'));
        if (!empty($image_id)){
			$image = CollectionImage::getById($image_id);
            if (empty($image)){
                return $this->run('fileList');
            }
			$collection_id = $image['collection_id'];
            $this->request->query->set('id', $collection_id);
            if (!empty($image)) {
                $collection = Collection::getById($collection_id);
            }
			$image_data = $image->asArray();
            $image_action_status = CollectionImage::del($image_id);
            $this->getAns()->add('image_action_status', $image_action_status ? $image_action_status : '');
        }else{
            $errors['image'] = 'empty';
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('fileList');
        }
    }

    public function changePosition(){
        $errors = array();
        $image_id = $this->request->request->get('image_id');
        $image = CollectionImage::getById($image_id);
        if (!empty($image)) {
            $position = $this->request->request->get('position', 0);
            $image->move($position);
        }else{
            $errors['image'] = 'empty';
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('fileList');
        }
    }

    public function saveDescription(){
        $image_id = $this->request->request->get('image_id');
        if (!empty($image_id)){
            $image = CollectionImage::getById($image_id);
            $text = $this->request->request->get('image_text', '');
            $image->update(array('text' => $text), $errors);
        }else{
            $errors['image'] = 'empty';
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('fileList');
        }
    }

    public function changeHidden(){
        $errors = array();
        $image_id = $this->request->request->get('image_id');
        if (!empty($image_id)){
            $image = CollectionImage::getById($image_id);
            $hidden = $this->request->request->get('hidden');
            $image->hide($hidden);
        }else{
            $errors['image'] = 'empty';
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('fileList');
        }
    }
	/**
	 * Установить обложку
	 * @return type
	 */
    public function setCover(){
        $errors = array();
        $image_id = $this->request->request->get('image_id'); //id картинки
		$remove = $this->request->request->get('remove');//убрать обложку
        if (!empty($image_id)){
            $image = CollectionImage::getById($image_id);
            $collection_id = $image['collection_id'];
            $collection = Collection::getById($collection_id);
            $image_action_status = $collection->setCover(!empty($remove) ? NULL : $image_id);
            $this->getAns()->add('image_action_status', $image_action_status ? $image_action_status : '');
        }else{
            $errors['image_id'] = 'empty';
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('fileList');
        }
    }

    public function setGravity(){
        $errors = array();
        $image_id = $this->request->request->get('image_id');
        $gravity = $this->request->request->get('gravity');
        if (!empty($image_id)){
            $image = CollectionImage::getById($image_id);
            $image_action_status = $image->setGravity($gravity);
            $this->getAns()->add('image_action_status', $image_action_status !==true ? $image_action_status : '');
        }else{
            $errors['image_id'] = 'empty';
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('fileList');
        }
    }
}
?>