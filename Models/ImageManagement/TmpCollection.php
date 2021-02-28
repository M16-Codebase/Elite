<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 17.04.15
 * Time: 14:06
 */

namespace Models\ImageManagement;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class TmpCollection{
    const TMP_DIR = 'data/temp_images';

    private static $cache = array();

    private $data = array();
    private $dirname = NULL;
    private $public_path = NULL;
    private $real_path = NULL;
    private $images = array();
    private $cover = NULL;
    private $next_position = 1;

    /**
     * @param $dirname
     * @param array $gallery_data
     * @return self
     */
    public static function getGallery($dirname, $gallery_data = array()){
        if (empty($dirname)){
            return NULL;
        }
        if (empty(self::$cache[$dirname])){
            self::$cache[$dirname] = new self($dirname, $gallery_data);
        }
        return self::$cache[$dirname];
    }

    private function __construct($dirname, $gallery_data){
        $this->dirname = $dirname;
        $this->setDir($dirname);
        $files = scandir($this->real_path);
        foreach($files as $k=>$file){
            if (!preg_match('~^(\d+)\.~', $file)){
                unset($files[$k]);
            }
        }
        $max_position = 0;
        if (!empty($gallery_data['images'])){
            foreach($gallery_data['images'] as $item){
                if (in_array($item['filename'], $files)){
                    $position = !empty($item['num']) ? $item['num'] : $max_position+1;
                    if ($position > $max_position){
                        $max_position = $position;
                    }
                    $this->images[$item['filename']] = array(
                        'filename' => $item['filename'],
                        'hidden' => !empty($item['hidden']) ? $item['hidden'] : 0,
                        'gravity' => !empty($item['gravity']) ? $item['gravity'] : 'C',
                        'text' => !empty($item['text']) ? $item['text'] : '',
                        'num' => $position
                    );
                }
            }
        }
        foreach($files as $file){
            if (empty($this->images[$file])){
                $max_position++;
                $this->images[$file] = array(
                    'filename' => $file,
                    'hidden' => 0,
                    'gravity' => 'C',
                    'text' => '',
                    'num' => $max_position
                );
            }
        }
        uasort($this->images, array($this, 'sortPosition'));
        $this->next_position = $max_position+1;
        if (!empty($gallery_data['cover']) && isset($this->images[$gallery_data['cover']])){
            $this->cover = $gallery_data['cover'];
        }
    }

    /**
     * @param Collection $collection
     * @return array - массив урлов изображений для замены
     */
    public function importToCollection(Collection $collection){
        $url_list = array('from' => array(), 'to' => array());
        foreach($this->images as $i){
            $image = $collection->addImage(new UploadedFile($this->real_path.$i['filename'], $i['filename']), !empty($i['text']) ? $i['text'] : '', $error);
            if (!empty($this->cover) && $this->cover == $i['filename']){
                $collection->setCover($image['id']);
            }
            $image->setGravity(!empty($i['gravity']) ? $i['gravity'] : 'C');
            $image->hide(!empty($i['hidden']) ? $i['hidden'] : 0);
            if (!empty($i['num'])){
                $image->move($i['num']);
            }
            if (!empty($i['text'])){
                $image->update(array('text' => $i['text']));
            }
            $url_list['from'][$i['filename']] = $this->public_path . $i['filename'];
            $url_list['to'][$i['filename']] = $image->getUrl();
        }
        return $url_list;
    }

    public function deleteGallery(){
        $this->rrmdir($this->real_path);
        unset(self::$cache[$this->dirname]);
    }

    private static function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") self::rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public function getImages(){
        $gallery_data = json_encode(array(
            'cover' => $this->cover,
            'images' => $this->images
        ));
        foreach($this->images as $file => $data){
            $this->images[$file]['cover'] = $file == $this->cover ? 1 : 0;
            $this->images[$file]['gallery_data'] = $gallery_data;
            $this->images[$file]['url'] = $this->public_path . $data['filename'];
        }
        usort($this->images, array($this, 'sortPosition'));
        return $this->images;
    }

    private function sortPosition($a, $b){
        if ($a['num'] == $b['num']) {
            return 0;
        }
        return ($a['num'] < $b['num']) ? -1 : 1;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param array $errors
     * @return bool
     */
    public function upload(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE, &$errors = array()){
        return $this->uploadFile($FILE, $filename, $errors);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param null $filename
     * @param array $errors
     * @return bool
     */
    public function reload(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE, &$filename = NULL, &$errors = array()){
        if (empty($filename)){
            $errors['filename'] = 'empty';
            return FALSE;
        }
        return $this->uploadFile($FILE, $filename, $errors);
    }

    public function delete($filename, &$errors = array()){
        if (empty($this->images[$filename])){
            $errors['file'] = 'not_found';
            return FALSE;
        }
        unlink($this->real_path.$filename);
        unset($this->images[$filename]);
        if ($this->cover == $filename){
            if(empty($this->images)){
                $this->cover = NULL;
            } else {
                $i = reset($this->images);
                $this->cover = $i['filename'];
            }
        }
        return TRUE;
    }

    /**
     * @param $filename
     * @param $image_text
     * @param array $errors
     * @return bool
     */
    public function setText($filename, $image_text, &$errors = array())
    {
        if (empty($this->images[$filename])) {
            $errors['file'] = 'not_found';
            return FALSE;
        }
        $this->images[$filename]['text'] = $image_text;
        return TRUE;
    }

    /**
     * @param string $filename
     * @param int $hidden (1/0)
     * @param array $errors
     * @return bool
     */
    public function changeHidden($filename, $hidden, &$errors = array())
    {
        if (empty($this->images[$filename])) {
            $errors['file'] = 'not_found';
            return FALSE;
        }
        $this->images[$filename]['hidden'] = $hidden;
        return TRUE;
    }

    /**
     * @param string $filename
     * @param int $position
     * @param array $errors
     * @return bool
     */
    public function changePosition($filename, $position, &$errors = array())
    {
        if (empty($this->images[$filename])) {
            $errors['file'] = 'not_found';
            return FALSE;
        }
        $old_pos = $this->images[$filename]['num'];
        if ($position > $old_pos){
            foreach($this->images as $fn => $i){
                if ($fn != $filename && $i['num'] > $old_pos && $i['num'] <= $position) {
                    $this->images[$fn]['num'] --;
                }
            }
            $this->images[$filename]['num'] = $position;
        } else {
            foreach($this->images as $fn => $i){
                if ($fn != $filename && $i['num'] < $old_pos && $i['num'] >= $position) {
                    $this->images[$fn]['num'] ++;
                }
            }
            $this->images[$filename]['num'] = $position;
        }
        return TRUE;
    }

    /**
     * @param string $filename
     * @param string $gravity
     * @param array $errors
     * @return bool
     */
    public function setGravity($filename, $gravity, &$errors = array())
    {
        if (empty($this->images[$filename])) {
            $errors['file'] = 'not_found';
            return FALSE;
        }
        $this->images[$filename]['gravity'] = $gravity;
        return TRUE;
    }

    /**
     * @param $filename
     * @param array $errors
     * @return bool
     */
    public function setCover($filename, &$errors = array())
    {
        if (empty($this->images[$filename])) {
            $errors['file'] = 'not_found';
            return FALSE;
        }
        $this->cover = $filename;
        return TRUE;
    }

    public function crop($filename, $coords, &$errors = array()){
        if (!file_exists($this->real_path.$filename)){
            $errors['file'] = 'not_found';
        } else {
            $fileInfo = GetImageSize($this->real_path.$filename);
            $ext = Image::getExtByType($fileInfo[2]);
            $x = min($coords['x1'], $coords['x2']);
            $y = min($coords['y1'], $coords['y2']);
            $width = abs($coords['x2'] - $coords['x1']);
            $height = abs($coords['y2'] - $coords['y1']);

            $image = ($ext == 'png') ? imagecreatefrompng($this->real_path.$filename) : imagecreatefromjpeg($this->real_path.$filename);
            $src_width = imagesx($image);
            $src_height = imagesy($image);
            if ($x+$width > $src_width || $y+$height > $src_height){
                $errors['coords'] = 'too_big';
            } else {
                $cropped_image = imagecreatetruecolor($width, $height);
                $result = imagecopy($cropped_image, $image, 0, 0, $x, $y, $width, $height);
                if (!$result){
                    $errors['crop'] = 'error';
                } else {
                    if ($ext == 'png'){
                        $result = imagepng($cropped_image, $this->real_path.$filename, 100);
                    } else {
                        $result = imagejpeg($cropped_image, $this->real_path.$filename, 100);
                    }
                    if (!$result){
                        $errors['save'] = 'error';
                    }
                }
            }
        }
        return empty($errors);
    }

    /**
     * Загружает или заменяет изображение временной галереи
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param string $filename
     * @param array $errors
     * @return bool
     */
    private function uploadFile(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE, &$filename = NULL, &$errors = array()){
        $fileInfo = GetImageSize($FILE->getRealPath());
        $fileWidth = $fileInfo[0];
        $fileHeight = $fileInfo[1];
        $ext = Image::getExtByType($fileInfo[2]);
        if (!$ext){
            $errors['image_format'] = 'unknown';
        }
        if ($fileWidth < Image::MIN_WIDTH || $fileHeight < Image::MIN_HEIGHT) {
            $errors['image'] = 'too_small';
        }
        if (!empty($errors)){
            return FALSE;
        }
        if (empty($filename)){
            $filename = $this->getNextFileNumber($this->real_path).'.'.$ext;
            if (empty($this->images)){
                $this->cover = $filename;
            }
            // Вписываем в реестр данные о новом файле
            $this->images[$filename] = array(
                'filename' => $filename,
                'hidden' => 0,
                'gravity' => 'C',
                'image_text' => '',
                'num' => $this->next_position
            );
            $this->next_position++;
        } elseif (empty($this->images[$filename])){
            $errors['file'] = 'not_found';
            return FALSE;
        } else {
            // Корректируем данные реестра об изображении. Расширение файла может измениться, поэтому пересоздаем запись в реестре
            $file_data = $this->images[$filename];
            unset($this->images[$filename]);
            unlink($this->real_path.$filename);
            $filename = explode('.', $filename)[0].'.'.$ext;
            $file_data['filename'] = $filename;
            $this->images[$filename] = $file_data;
        }
        $error = Image::uploadImage($FILE->getRealPath(), $this->real_path.$filename, Image::MAX_WIDTH, Image::MAX_HEIGHT);
        if (!empty($error)){
            $errors['image'] = $error;
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Возвращает полную
     * @param string $dirname
     * @return string|FALSE
     */
    private function setDir($dirname){
        if (empty($dirname)){
            throw new \LogicException('Gallery directory doesn\'t specified');
        }
        $parent_dir = self::TMP_DIR;
        if (!file_exists(\LPS\Config::getRealDocumentRoot() . $parent_dir)){
            mkdir($parent_dir, 0770);
        }
        $dirname = $parent_dir.'/'.$dirname;
        if (!file_exists(\LPS\Config::getRealDocumentRoot() . $dirname)){
            mkdir($dirname, 0770);
        }
        $this->public_path = "/{$dirname}/";
        $this->real_path = \LPS\Config::getRealDocumentRoot() . $dirname.'/';
        return TRUE;
    }

    private function getNextFileNumber(){
        $files = scandir($this->real_path);
        $number = 1;
        if (!empty($files)){
            foreach($files as $file){
                if (preg_match('~^(\d+)\.~', $file, $match)){
                    $n = intval($match[1]);
                    if ($n >= $number) {
                        $number = $n + 1;
                    }
                }
            }
        }
        return $number;
    }

    public static function garbageCollector(){
        self::rrmdir(\LPS\Config::getRealDocumentRoot() . self::TMP_DIR);
    }
}