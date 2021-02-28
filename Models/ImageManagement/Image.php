<?php
/**
 * Класс любой загружаемой картинки
 *
 * @author olga
 */
namespace Models\ImageManagement;
use LPS\Config;
class Image implements \ArrayAccess{
    const TABLE = 'images';
    const MAX_REGISTRY_LEN = 1000;
    const MAX_SIZE   = 512;
    const MIN_WIDTH  = 10;
    const MIN_HEIGHT = 10;
    const MAX_WIDTH  = 2200;
    const MAX_HEIGHT = 2200;
    const UPLOAD_IMAGE_MEMORY_LIMIT = 300;
    const UPLOAD_IMAGE_MAX_WIDTH = 7000;
    const UPLOAD_IMAGE_MAX_HEIGHT = 7000;
    const IMAGE_EXT = 'png';
    const PATH = '/data/images/single';
    const THUMBS_PATH = '/data/thumbs';
    const HASH_SOLT_IMAGE_STRING = 'IMAGES SECURITY STRING';//такая же в /utilites/thumb.php
    /** Загружаемые поля объекта */
    protected static $loadFields = array('id', 'collection_id', 'num', 'width', 'height', 'hidden', 'gravity', 'info', 'ext', 'last_update');
    /* Доступный поля объекта из arrayAccess */
    protected static $accessFields = array('id', 'width', 'height', 'hidden', 'gravity', 'url', 'info', 'ext', 'last_update');
    /** Поля, которые можно редактировать */
    protected static $updateFields = array('width', 'height', 'hidden', 'gravity', 'info', 'ext');
    protected static $gravity = array('L', 'R', 'T', 'B', 'TR', 'TL', 'BR', 'BL', 'C');
    protected static $registry = array();
    private static $loadIds = array();
    private $data = NULL;
    private $needSave = FALSE;
    protected $db = NULL;
    /**
     *
     * @param array $ids
     */
    public static function prepare(array $ids){
        if (!empty($ids)){
            $ids = array_diff($ids, array_keys(self::$registry), self::$loadIds);
            if (!empty($ids)){
                self::$loadIds = array_merge($ids, self::$loadIds);
            }
        }
    }

    /**
     * Фабрика
     * @param array $ids
     * @param null $segment_id
     * @return Image[]
     */
    public static function factory($ids, $segment_id = NULL){
        $getIds = array_unique(array_merge($ids, self::$loadIds));
        if (count(self::$registry) + count($getIds) > self::MAX_REGISTRY_LEN){
            self::clearRegistry();
        }
        if (!empty(self::$registry)){
            $getIds = array_diff($getIds, array_keys(self::$registry));
        }
        if (!empty($getIds)){
            $db = \App\Builder::getInstance()->getDB();
            $images = $db->query(
                'SELECT `'.implode('`,`', static::$loadFields).'`
                FROM `'.static::TABLE.'`
                WHERE `id` IN (?i)',
                $getIds
            )->select('id');
            foreach ($getIds as $id){
                $imageClass = !empty($images[$id]['collection_id']) ? (__NAMESPACE__ . '\CollectionImage') : __CLASS__;
                $image = !empty($images[$id]) ? new $imageClass($images[$id], $segment_id) : NULL;
                self::$registry[$id] = $image;
            }
        }
        $result = array();
        foreach ($ids as $id_result){
            $result[$id_result] = self::$registry[$id_result];
        }
        return $result;
    }

    /**
     *
     * @param int $id
     * @param null $segment_id
     * @return Image
     */
    public static function getById($id, $segment_id = NULL) {
        if (!empty(self::$registry[$id])){
            return self::$registry[$id];
        }
        $images = self::factory(array($id), $segment_id);
        return !empty($images[$id]) ? $images[$id] : NULL;
    }
    /**
     * Добавить картинку - обертка для функции create
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|null $FILE
     * @param array $data
     * @param bool $resize
     * @return static|NULL
     * @todo параметр текст взяться
     */
    public static function add(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE, $data = array(), $resize = FALSE, &$error = NULL) {
        $image = static::create($FILE, $data, $resize, $error);
        if (!empty($error)){
            static::del($image['id']);
            return NULL;
        }
        return $image;
    }

    /**
     * @param array $params
     * @param array $errors
     * @return Image|CollectionImage
     */
    public function copy($params = array(), &$errors = array()){
        if ($this->isDefault()){
            /** @TODO что делать?? */
            $file = NULL;
        } else {
            $path = $this->getPath('absolute') . '/' . $this['id'] . '.' . $this['ext'];
            $file = new \Symfony\Component\HttpFoundation\File\UploadedFile($path, $this['id'] . '.' . $this['ext']);
        }
        $old_params = $this->asArray();
        unset($old_params['collection_id']);
        unset($old_params['id']);
        $params = array_merge($params, $old_params);
        return static::add($file, $params, FALSE, $errors);
    }

    /**
     * Создание картинки
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param array $db_params
     * @param bool $resize
     * @return static
     */
    protected static function create(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE, $db_params = array(), $resize = FALSE, &$error = NULL) {
        $db = \App\Builder::getInstance()->getDB();
		if (empty($db_params)){
			$db_params = array('gravity' => 'C');
		}
        foreach (static::$dataProviders as $p){
            $p->preCreate($FILE, $db_params, $error);
        }
        if (isset($db_params['info'])){
            $db_params['info'] = self::packInfo($db_params['info']);
        }
        $db_params['last_update'] = date('Y-m-d H:i:s');
        $image_id = $db->query('INSERT INTO `' . static::TABLE . '` SET ?a', $db_params);
        $image = self::getById($image_id);
        foreach (static::$dataProviders as $p){
            $p->onCreate($image, array('file' => $FILE) + $db_params);
        }
        $error = $image->reload($FILE, $resize, TRUE);
        return $image;
    }

    /**
     * вычистить информацию из реестра
     * @param type $ids
     */
    public static function clearRegistry($ids = array()) {
        if (empty($ids)) {
            $ids = !empty(self::$registry) ? array_keys(self::$registry) : array();
        }
        foreach ($ids as $id) {
            if (!empty(self::$registry[$id])) { //не используем getById, т.к. данная функция используется в factory, т.е. получится бесконечная рекурсия
                $image = self::$registry[$id];
                /* @var $image Image */
                $image->save();
                if ($image instanceof CollectionImage){
                    $c_id = $image['collection_id'];
                    unset(CollectionImage::$collectionRegistrationQuery[$c_id][$image['id']]);
                }
                unset(self::$registry[$id]);
            }
        }
    }

    /**
     * Возвращает путь к картинкам на сервере
     * @param int $collection_id
     * @param string $type
     * @return string
     */
    protected function getPath($type = 'relative') {
        if ($type == 'relative') {
            return static::PATH;
        } else {
            return Config::getRealDocumentRoot() . static::PATH;
        }
        return '';
    }

    /**
     * Удаляем объект
     * @param int $item_id
     * @param int $image_id
     * @return bool
     */
    public static function del($id) {
        $img = static::getById($id);
        if (!empty($img)) {
            $path = $img->getPath('absolute');
            $name = $img->getData('id') . '.' . $img['ext'];
            if (file_exists($path . '/' . $name) and !is_dir($path . '/' . $name)) {
                unlink($path . '/' . $name);
            }
            $db = \App\Builder::getInstance()->getDB();
            $db->query('DELETE FROM `' . static::TABLE . '` WHERE `id`=?d', $img->getData('id'));
            self::walkThumbnails($img->getPath('short'), $img->getData('id'));
            foreach (static::$dataProviders as $p){
            /** @var $p iImageDataProvider */
                $p->onDelete($img);
            }
            self::clearRegistry(array($id));
            return;
        }else{
            return 'already_deleted';
        }
    }
    
    protected static function packInfo($data){
        return serialize($data);
    }
    protected static function unpackInfo($data){
        return unserialize($data);
    }

    /**
     * @param array $data
     * @param int $segment_id
     */
    protected function __construct($data, $segment_id = NULL) {
        foreach (static::$loadFields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new \LogicException('Переданы не все данные для создания объекта изображения.');
            }
            $this->data[$field] = $field == 'info' ? static::unpackInfo($data[$field]) : $data[$field];
        }
        $this->data['segment_id'] = $segment_id;
        $this->db = \App\Builder::getInstance()->getDB();
        foreach (static::$dataProviders as $p){
            /** @var $p iImageDataProvider */
            $p->onLoad($this);
        }
    }

    public function __destruct() {
        $this->save();
    }

    /**
     * Возвращает данные объекта
     * @param string $key
     * @return mixed
     * @throws \LogicException
     */
    public function getData($key) {
        if (in_array($key, static::$accessFields)){
            if (($key == 'height' || $key == 'width') && $this->isDefault()){//если требуется дефолтная картинка, то подменяем высоту и ширину на дефолтную картинку
                $default = $this->getDefault();
                return $default[$key];
            }elseif($key == 'url'){
                return $this->getUrl();
            }
            return $this->data[$key];
        } elseif (!empty(static::$dataProvidersByFields[$key])) {
            return static::$dataProvidersByFields[$key]->get($this, $key, $this->data['segment_id']);
        } else {
            throw new \LogicException('Для объекта изображения не предусмотрен параметр ' . $key);
        }
    }

    /**
     * Переписывает данные объекта
     * @param string $key
     * @param mixed $value
     * @throws \LogicException
     */
    protected function setData($key, $value) {
        if (!array_key_exists($key, $this->data)) {
            throw new \LogicException('Для объекта изображения не предусмотрен параметр ' . $key);
        }
        if (!in_array($key, static::$updateFields)){
            throw new \LogicException('Поле '.$key.' нельзя редактировать');
        }
        if ($this->data[$key] == $value){
            return FALSE;
        }
        $this->data[$key] = $value;
        $this->needSave = TRUE;
        return TRUE;
    }
    /**
     * Проверяет, загружено ли изображение, и нужно ли использовать картинку по умолчанию.
     */
    public function isDefault(){
        return $this->data['width'] == 0 || $this->data['height'] == 0;
    }
    /**
     * Возвращает урл на тамб картинки
     * @param int $w
     * @param int $h
     * @param bool|string $p L|R|T|B|TR|TL|BR|BL|C
     * @param bool $f true, если прозрачный png
     * @param array $filter фильтры @see \phpthumb
     * @return string
     */
    public function getUrl($w = null, $h = null, $p = null, $f = false, $filter = array()) {
        if ($this->isDefault()) {
            $default = $this->getDefault();
            if (!empty($default)){
                $path = $default->getPath() . '/' . $default['id'] . '.' . $default['ext'];
            }else{
                return '';
            }
        }else{
            $path = $this->getPath() . '/' . $this['id'] . '.' . $this['ext'];
        }
        if ($this['ext'] == 'png'){
            $f = true; // прозрачность вкл всегда
        }
        if (empty($w) && empty($h)){
//            return $path;
            //может надо выставлять ширину, высоту картинки?
			$w = $this['width'];
			$h = $this['height'];
        }
        if ($p === true){
            $p = $this['gravity'];
        }
        if ($p === false){
            $p = 'N';
        }
        $str = (!empty($w)? $w : '') . (!empty($h) ? $h : '') . (!empty($p) ? $p : '') . (!empty($f) ? $this['ext'] : '') . (!empty($filter) ? serialize($filter) : '') . $path . self::HASH_SOLT_IMAGE_STRING;
        $hash = substr(md5($str), 0, 6);
        $timestamp = strtotime($this['last_update']);
        return str_replace('/images/', 
                '/thumbs/' . 
                (!empty($w) ? ('w' . $w) : '') . 
                (!empty($h) ? ('h' . $h) : '') . 
                (!empty($p) ? $p : '') . 
                (!empty($f) ? ('f_' . $this['ext']) : '') . 
                (!empty($filter) ? ('fltr_' . str_replace('|', '-', implode('_', $filter))) : '') .
                '/' . $hash . '/', 
                $path)
                . (!empty($timestamp) ? '?'.$timestamp : ''); // Подставляем timestamp к урлу, чтобы отображать закешированные картинки
        
    }
    
    /**
     * по умолчанию путь к картинке
     * @return string
     */
    public function getCleanUrl() {
        $f = false; $filter = array();
        if ($this->isDefault()) {
            $default = $this->getDefault();
            if (!empty($default)){
                $path = $default->getPath() . '/' . $default['id'] . '.' . $default['ext'];
            }else{
                return '';
            }
        }else{
            $path = $this->getPath() . '/' . $this['id'] . '.' . $this['ext'];
        }
        return $path;
    }
    
    private function getSource() {
        $path = Config::getRealDocumentRoot() . $this->getPath() . '/' . $this['id'] . '.' . $this['ext'];
        if (empty($path) || !file_exists($path) || $this->data['width'] == 0 || $this->data['height'] == 0){
            $error = 'file_empty';
            return FALSE;
        }
        $image = ($this['ext'] == 'png') ? imagecreatefrompng($path) : imagecreatefromjpeg($path);
        return $image;
    }

    public function getWidth() {
        $image = $this->getSource();
        if($image)
            return imagesx($image);
        else
            return false;
    }

    public function getHeight() {
        $image = $this->getSource();
        if($image)
            return imagesy($image);
        else
            return false;
    }    

    public function crop($x, $y, $width, $height, &$error = NULL){
        $path = Config::getRealDocumentRoot() . $this->getPath() . '/' . $this['id'] . '.' . $this['ext'];
        if (empty($path) || !file_exists($path) || $this->data['width'] == 0 || $this->data['height'] == 0){
            $error = 'file_empty';
            return FALSE;
        }
        $image = ($this['ext'] == 'png') ? imagecreatefrompng($path) : imagecreatefromjpeg($path);
        $src_width = imagesx($image);
        $src_height = imagesy($image);
        if ($x+$width > $src_width || $y+$height > $src_height){
            $error = 'big_coords';
            return FALSE;
        }
        $cropped_image = imagecreatetruecolor($width, $height);
        $result = imagecopy($cropped_image, $image, 0, 0, $x, $y, $width, $height);
        if (!$result){
            $error = 'image_crop_error';
            return FALSE;
        }
        if ($this['ext'] == 'png'){
            $result = imagepng($cropped_image, $path, 100);
        } else {
            $result = imagejpeg($cropped_image, $path, 100);
        }
        if (!$result){
            $error = 'image_save_error';
            return FALSE;
        }
        $this->update(array(
            'width' => $width,
            'height' => $height
        ), $errors);
        return TRUE;
    }

    /**
     * Возвращает картинку по-умолчанию.
     * @param string $default
     * @param int $width
     * @param int $height
     * @return boolean
     */
    protected function getDefault() {
        return NULL;
    }

    /**
     * Сохраняет все поля объекта в базу
     */
    public function save() {
        if ($this->needSave) {
            $update_fields = array();
            foreach (static::$updateFields as $field) {
                $update_fields[$field] = $field == 'info' ? static::packInfo($this->data[$field]) : $this->data[$field];
            }
            $update_fields['last_update'] = date('Y-m-d H:i:s');
            $this->db->query('UPDATE `' . self::TABLE . '` SET ?a WHERE `id` = ?d', $update_fields, $this->data['id']);
        }
        $this->needSave = array();
    }

    /**
     * Получает константу типа, возвращает стандартное расширение.
     * @param type $type
     * @return string
     */
    public static function getExtByType($type){
        switch ($type){
            case IMAGETYPE_BMP: 
                return 'bmp';
            case IMAGETYPE_GIF: 
                return 'gif';
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000: 
                return 'jpg';
            case IMAGETYPE_PNG:
                return 'png';
            default : return '';
        }
    }
    
    /**
     * Обновляет картинку
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param string $default
     * @param bool $resize
     * @return возвращает сообщение об ошибке или NULL если ошибок нет
     */
    public function reload(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE, $resize = false, $from_create = FALSE) {
        $fileInfo = GetImageSize($FILE->getRealPath());
        $fileWidth = $fileInfo[0];
        $fileHeight = $fileInfo[1];
        $ext = $this->getExtByType($fileInfo[2]);
        if (!$ext){
            return 'unknown image type (not: gif, jpg, png)';
        }
        if ($fileWidth < static::MIN_WIDTH or $fileHeight < static::MIN_HEIGHT) {
            return 'too small';
        }
        if (!is_null($FILE) && empty($result['error'])) {
            $result = self::saveImage($this->getPath('absolute'), $FILE, $this->getData('id'), $ext, static::MAX_WIDTH, static::MAX_HEIGHT, $resize);
        }
        if (empty($result['error']) && !empty($result['full_name'])) {//если загрузилась, вычисляем размеры
            $image_data = getimagesize($result['full_name']);
            $width = !empty($image_data) && !empty($image_data[0]) ? $image_data[0] : 0;
            $height = !empty($image_data) && !empty($image_data[1]) ? $image_data[1] : 0;
            //делаем в обход хелперов, т.к. эта информация изменяется только при загрузке картинки, и её можно использовать в методе хелера onUpload
            $this->setData('width', $width);
            $this->setData('height', $height);
            $this->setData('ext', $ext);
            $this->needSave = TRUE;
            foreach (static::$dataProviders as $p){
                $p->onUpload($this, $FILE, $from_create);
            }
        }
        return !empty($result['error']) ? $result['error'] : NULL;
    }

    /**
     * Обновляем данные картинки
     * @param array $params
     * @param array $errors
     * @return bool
     * @throws \Exception
     */
    public function update($params, &$errors = array()){
        foreach (static::$dataProviders as $p){
            $p->preUpdate($this, $params, $errors);
        }
        $changed = FALSE;
        foreach ($params as $pk => $pv){
            if (!in_array($pk, self::$updateFields)){
                throw new \Exception('Нельзя редактировать поле «' . $pk . '»');
            }
            if ($this->setData($pk, $pv)){
                $changed = TRUE;
            }
        }
        foreach (static::$dataProviders as $p){
            $p->onUpdate($this, $params, $errors);
        }
        return $changed;
    }
    /**
     * меняем видимость изображения(видна ли картинка в выводе галереи)
     * @param int $image_id
     * @param int $hidden 1|0
     * @return int
     */
    public function hide($hidden) {
        $this->update(array('hidden' => $hidden ? 1 : 0), $errors);
    }
    public function setGravity($gravity){
        if (array_search($gravity, self::$gravity) !== false){
            $this->update(array('gravity' => $gravity), $errors);
            return true;
        }else{
            return 'gravity not exists';
        }
    }

    /**
     * добавляем закаченное изображение
     * проверяем - тип, размеры, объем файла
     *
     * @param string $path
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param string $name //имя файла без расширения
     * @param int $max_width
     * @param int $max_height
     * @return mixed(string, false)  false if success or error
     */
    private static function saveImage($path, \Symfony\Component\HttpFoundation\File\UploadedFile $FILE, $name = null, $ext, $max_width = null, $max_height = null, $resize = false) {
        if (empty($max_width)) {
            $max_width = static::MAX_WIDTH;
        }
        if (empty($max_height)) {
            $max_height = static::MAX_HEIGHT;
        }
        if (empty($name)) {
            $name = self::prepareImageName($FILE->getClientOriginalName());
        }
        \LPS\Components\FS::makeDirs($path);
        $full_name = $path . '/' . $name . '.' . $ext;
        $ans['error'] = self::uploadImage($FILE->getRealPath(), $full_name, $max_width, $max_height, !$resize);
        $ans['name'] = basename($full_name);
        $ans['full_name'] = $full_name;
        return $ans;
    }

    public static function uploadImage($source, $destination, $maxImgWidth, $maxImgHeight, $trustCopy = FALSE){
        $fileInfo = GetImageSize($source);
        $fileWidth = $fileInfo[0];
        $fileHeight = $fileInfo[1];
        $memoryLimit = self::UPLOAD_IMAGE_MEMORY_LIMIT;
        $fileInfo['channels'] = !empty($fileInfo['channels']) ? $fileInfo['channels'] : 3;
        $fileInfo['bits'] = !empty($fileInfo['bits']) ? $fileInfo['bits'] : 8;
        $requiredMemoryMB = ( $fileInfo[0] * $fileInfo[1] * ($fileInfo['bits'] / 8) * $fileInfo['channels'] * 5 ) / 1024 / 1024;
        if ($requiredMemoryMB > $memoryLimit || $fileWidth > self::UPLOAD_IMAGE_MAX_WIDTH || $fileHeight > self::UPLOAD_IMAGE_MAX_HEIGHT){
            return 'image too big';
        }
        ini_set('memory_limit', $memoryLimit + ceil(memory_get_usage(true)/1024/1024).'M');
        $accessExt = array();
        switch ($fileInfo[2]){
            case 1:{	$src = ImageCreateFromGIF($source);	 $accessExt = array('gif');}break;  //If work whith gif
            case 2:{	$src = ImageCreateFromJpeg($source); $accessExt = array('jpg','jpeg');}break;
            case 3:{	$src = ImageCreateFromPNG($source);	 $accessExt = array('png');}break;
            default:
                return 'incorrect type';
        }

        if (
            $trustCopy  // разрешен доверенный источник, означает что фото можно не пережимать, тоесть не портить его качества
            & ($fileWidth <= $maxImgWidth) //размеры подходят
            & ($fileHeight <= $maxImgHeight)
            & in_array(strtolower(substr($destination, strrpos($destination, '.')+1)), $accessExt) // тип файла совпадает с ожидаемым
        ){
            if (file_exists($destination))
                unlink($destination);
            if (file_exists($destination))
                return 'file already exist and can\'t be deleted';
            copy($source, $destination);
        }else{
            $xScale = $maxImgWidth  / $fileWidth;
            $yScale = $maxImgHeight / $fileHeight;
            if (($fileWidth <= $maxImgWidth) && ($fileHeight <= $maxImgHeight)) {
                $dstWidth = $fileWidth;
                $dstHeight = $fileHeight;
            }elseif (ceil($xScale * $fileHeight) < $maxImgHeight) {
                $dstHeight = ceil($xScale * $fileHeight);
                $dstWidth = $maxImgWidth;
            }else {
                $dstWidth = ceil($yScale * $fileWidth);
                $dstHeight = $maxImgHeight;
            }
            $dst = imagecreatetruecolor($dstWidth, $dstHeight);
            //Заливка прозрачностью
            $transparencyIndex = imagecolortransparent($src);
            $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);
            if ($transparencyIndex >= 0) {
                $transparencyColor    = @imagecolorsforindex($src, $transparencyIndex);
            }
            $transparencyIndex    = imagecolorallocate($dst, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
            imagefill($dst, 0, 0, $transparencyIndex);
            imagecolortransparent($dst, $transparencyIndex);
            //Копирование изображения
            @imagecopyresampled($dst, $src, 0, 0, 0, 0, $dstWidth, $dstHeight, $fileWidth, $fileHeight);
            imageDestroy($src);
            //unlink($source);
            if (file_exists($destination))
                unlink($destination);
            if (file_exists($destination))
                return 'file already exist and can\'t be deleted';
            //echo $destination.'<BR>';
            if (preg_match('~\.gif$~i', $destination)){
                //echo 'GIF save';
                imageGif($dst, $destination);
            }else{
                //echo 'JPG save';
                imageJpeg($dst, $destination, 90);
            }
        }
        //echo '<BR>';
        chmod($destination, 0666);
        return 0;
    }

    /**
     * Транслитим имя файла, если не хотим обращаться по id
     * @param string $name
     * @return string
     */
    private static function prepareImageName($name) {
        // отрезаем расширение
        $parts = explode('.', $name);
        $lastdot = array_pop($parts);
        $name = basename($name, '.' . $lastdot);
        // конвертируем в транслит
        $name = Translit::UrlTranslit($name);
        return $name;
    }

    /**
     * Прогуливаемся по тамбам, и удаляем\заменяем их в зависимости от переданных параметров
     * @param string $img_path
     * @param int|bool $img_id
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @return boolean
     */
    public static function walkThumbnails($img_path, $img_id = false, \Symfony\Component\HttpFoundation\File\UploadedFile $FILE = null) {
        $image = static::getById($img_id);
        $img_name = $img_id . '.' . $image['ext'];
        $img_path = trim($img_path, '/');
        $thumb_path = Config::getRealDocumentRoot() . static::THUMBS_PATH;
        if ($fh = opendir($thumb_path)) {
            while (($dir = readdir($fh)) !== false) {
                if (strcmp($dir, '.') == 0 || strcmp($dir, '..') == 0)
                    continue;
                if (/*preg_match('/w(\d+)h(\d+)q\d+[A-Z]/i', $dir, $razmer) && */is_dir($thumb_path . '/' . $dir)) {
                    $file_name = $thumb_path . '/' . $dir . '/' . $img_path . '/' . $img_name;
//                    if (!empty($FILE)) {//если передан файл, то заменяем тамбы
//                        //файла может и не быть, т.к. не во всех папках есть тамбы, поэтому else не нужен
//                        if (file_exists($file_name) && is_file($file_name)) {
//                            unlink($file_name);
//                        }
//                        $result = $this->saveImage($thumb_path . '/' . $dir . '/' . $img_path . '/', $FILE, $img_id, $razmer[1], $razmer[2]);
//                    } else
                    if (!empty($img_id)) {//если передан id картинки, то удаляем все тамбы этой картинки
                        //файла может и не быть, т.к. не во всех папках есть тамбы, поэтому else не нужен
                        if (file_exists($file_name) && is_file($file_name)) {
                            unlink($thumb_path . '/' . $dir . '/' . $img_path . '/' . $img_name);
                        }
                    } else {//иначе удаляем все картинки в папке
                        $files = glob($thumb_path . '/' . $dir . '/' . $img_path . '/*.*');
                        foreach ($files as $filename) {
                            unlink($filename);
                        }
                    }
                }
            }
            closedir($fh);
        }
        return FALSE;
    }
	
	public function asArray(){
        $helper_fields = array();
        foreach(static::$dataProvidersByFields as $field=>$v){
            $helper_fields[$field] = $this[$field];
        }
		return array_merge($this->data, $helper_fields);
	}

    /*     * ***************************** ArrayAccess **************************** */

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->data[$offset]) || isset(static::$dataProvidersByFields[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->getData($offset);
    }

    /**
     * @param string $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value) {
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }

    /**
     * @param string $offset
     * @throws \Exception
     */
    public function offsetUnset($offset) {
        throw new \Exception(get_class($this) . ' has only immutable Array Access');
    }
    
    

/******************************* работа с iImageDataProvider *****************************/
    /**
     * @var iImageDataProvider[]
     */
    static protected $dataProviders = array();
    /**
     * @var iImageDataProvider[]
     */
    static protected $dataProvidersByFields = array();

    /**
     * @static
     * @param iImageDataProvider $provider
     */
    public static function addDataProvider($provider){
        static::$dataProviders[get_class($provider)] = $provider;
        foreach ($provider->fieldsList() as $field){
            static::$dataProvidersByFields[$field] = $provider;
        }
    }

    /**
     * @static
     * @param iImageDataProvider $provider
     */
    public static function delDataProvider($provider){
        unset(static::$dataProviders[get_class($provider)]);
    }
}
