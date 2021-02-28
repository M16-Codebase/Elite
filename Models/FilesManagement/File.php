<?php
/**
 * CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` tinytext NOT NULL,
  `ext` varchar(4) NOT NULL,
  `size` int(11) NOT NULL,
  `show_in` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 * ALTER TABLE `files` ADD `cover_id` INT UNSIGNED NULL DEFAULT NULL
 */
namespace Models\FilesManagement;
use LPS\Components\FS;
use LPS\Config;
use Models\InternalLinkManager;
use LPS\Components\Translit;
use Models\ImageManagement\Collection;
use Models\FilesManagement\FilesImageCollection;

class File implements \ArrayAccess{
    const TABLE = 'files';
	const TABLE_DOWNLOAD_LOG = 'files_download_log';
    const FILES_PATH = '/data/files/';
    const TYPE_NONE = 'none';
	const TYPE_ANALYTICS = 'analytics';
	const TYPE_ACT = 'act';
	const TYPE_CONTRACT = 'contract';
    protected static $segmentFields = array('title');
    protected static $mainFields = array('id', 'title', 'directory', 'ext', 'size', 'name', 'type', 'date', 'position', 'number_1C');
    protected static $allowFields = array('id', 'title', 'directory', 'ext', 'size', 'name', 'path', 'date', 'show_in', 'cover_id', 'cover', 'full_name', 'type', 'full_size', 'position', 'number_1C');
    protected static $updateFields = array('title', 'directory', 'ext', 'size', 'name', 'show_in', 'cover_id', 'type', 'date', 'position', 'number_1C');
    static protected $allow_ext = array(
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'djvu',
        'txt',
        'rar',
        'zip',
        'rtf',
        'ppt',
        'pps',
        'png',
        'jpg',
        'jpeg',
        'gif',
        'tiff',
        'tif',
		'xml',
		'csv',
        'swf'
    );
    protected static $registry = array();
    protected $data = NULL;
    protected $needSave = false;
    protected static $loadIds = array();
    private $segment_id;
    
    public static function getSegmentFields(){
        return static::$segmentFields;
    }

    public static function getAllowExt(){
        return self::$allow_ext;
    }
	public static function getLog($params = array(), &$count = 0, $start = 0, $limit = 10000000){
		$db = \App\Builder::getInstance()->getDB();
		$order_part = '';
		if (!empty($params['order'])){
            if (is_array($params['order'])){
                foreach ($params['order'] as $key => $desc){
                    $order[] = ('`dl`.`' . $key . '`') . (!empty($desc) ? ' DESC ' : ' ');
                }
                $order_part = implode(', ', $order);
            }else{
                throw new \LogicException('Order param must be an array("key" => "desc")');
            }
        }
		$logs = $db->query('SELECT SQL_CALC_FOUND_ROWS `dl`.*, '
			. ' CONCAT(`f`.`name`, ".", `f`.`ext`) AS `file_name`, `f`.`title` AS `file_title`, '
			. ' IF (`mn`.`email` IS NULL, 0, 1) AS `subscribe`, UNIX_TIMESTAMP(`dl`.`date`) AS `timestamp` '
			. ' FROM `'.self::TABLE_DOWNLOAD_LOG.'`  AS `dl`'
			. ' INNER JOIN `'.self::TABLE.'` AS `f` ON (`f`.`id` = `dl`.`file_id`)'
			. ' LEFT JOIN `mails_news` AS `mn` ON (`mn`.`email` = `dl`.`email`)'
			. ' WHERE 1{ AND `mn`.`email` LIKE ?s}'
			. '{ AND CONCAT(`f`.`name`, ".", `f`.`ext`) LIKE ?s}'
			. '{ AND CONCAT(`f`.`title`, ".", `f`.`ext`) LIKE ?s}'
			. '{ AND `mn`.`email` IS NULL AND ?d}'
			. '{ AND `mn`.`email` IS NOT NULL AND ?d}'
			. '{ AND `dl`.`date` >= ?s}'
			. '{ AND `dl`.`date` <= ?s}'
			. (!empty($order_part) ? (' ORDER BY ' . $order_part) : '') . ''
			. ' LIMIT ?d, ?d',
			!empty($params['email']) ? ('%' . $params['email'] . '%') : $db->skipIt(),
			!empty($params['file_name']) ? ('%' . $params['file_name'] . '%') : $db->skipIt(),
			!empty($params['file_title']) ? ('%' . $params['file_title'] . '%') : $db->skipIt(),
			isset($params['subscribe']) && empty($params['subscribe']) ? 1 : $db->skipIt(),
			isset($params['subscribe']) && !empty($params['subscribe']) ? 1 : $db->skipIt(),
			!empty($params['date_min']) ? date('Y-m-d H:i:s', strtotime($params['date_min'])) : $db->skipIt(),
			!empty($params['date_max']) ? date('Y-m-d H:i:s', strtotime($params['date_max'])) : $db->skipIt(),
			$start,
			$limit
		)->select('id');
		$count = $db->query('SELECT FOUND_ROWS()')->getCell();
		return $logs;
	}

	/**
     *
     * @param array $ids
     */
    public static function prepare(array $ids){
        if (!empty($ids)){
            $ids = array_diff($ids, array_keys(static::$registry), static::$loadIds);
            if (!empty($ids)){
                static::$loadIds = array_merge($ids, static::$loadIds);
            }
        }
    }

    /**
     *
     * @param array $ids
     * @param null $segment_id
     * @return File[]
     */
    public static function factory(array $ids, $segment_id = NULL){
        if (empty($ids)){
            return array();
        }
        $getIds = array_unique(array_merge($ids, static::$loadIds));
        if (!empty(static::$registry)){
            $getIds = array_diff($getIds, array_keys(static::$registry));
        }
        if (!empty($getIds)){
            $db = \App\Builder::getInstance()->getDB();
            $files = $db->query('
            SELECT
                `'.implode('`, `', static::$mainFields).'`,
                "'. static::FILES_PATH .'" AS `path`, `show_in`, `cover_id`
            FROM `'. static::TABLE .'`
            WHERE `id` IN (?i)',
                $getIds
            )->select('id');
            foreach ($getIds as $id){
                if (!empty($files[$id])){
                    static::$registry[$id] = !empty($files[$id]) ? new static($files[$id], $segment_id) : NULL;
                }else{
                    static::$registry[$id] = NULL;
                }
            }
        }
        $result = array();
        foreach ($ids as $id_result){
            $result[$id_result] = static::$registry[$id_result];
        }
        return $result;
    }
    /**
     *
     * @param int $id
     * @param int $segment_id
     * @return File
     */
    public static function getById($id, $segment_id = NULL){
        if (!empty(static::$registry[$id])){
            return static::$registry[$id];
        }
        $files = static::factory(array($id), $segment_id);
        return !empty($files[$id]) ? $files[$id] : NULL;
    }
    /**
     * вычистить информацию из реестра
     * @param type $ids
     */
    public static function clearRegistry($ids = array()) {
        if (empty($ids)) {
            $ids = !empty(static::$registry) ? array_keys(static::$registry) : array();
        }
        foreach ($ids as $id) {
            if (!empty(static::$registry[$id])) {
                $file = static::$registry[$id];
                /* @var $file Image */
                $file->save();
                unset(static::$registry[$id]);
            }
        }
    }
    /**
     * 
     * @param string $title
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param string $error
     * @param string $directory
     * @param boolean $translitName
     * @param boolean $onNameExistsRename
     * @return int
     */
    public static function add($title, \Symfony\Component\HttpFoundation\File\UploadedFile $FILE, &$error, $directory = '', $translitName = TRUE, $onNameExistsRename = TRUE){
        foreach (static::$dataProviders as $p){
            $p->preCreate($title, $FILE, $error);
        }
        $original_file_name = $FILE->getClientOriginalName();
        if (preg_match('~\.('.implode('|', static::$allow_ext).')$~i', $original_file_name, $regs)){
            $ext = strtolower($regs[1]);
            $name = static::prepareFileName($original_file_name, $translitName);
            $path = static::getFilePath() . (!empty($directory) ? $directory . '/' : '');
            FS::makeDirs($path);
            $path = realpath($path);
            $full_name = $path . $name . '.' . $ext;
            $count = 0;
            $db = \App\Builder::getInstance()->getDB();
            if ($onNameExistsRename){
                $tmp_name = $name;
                while(file_exists($full_name)){//при совпадении названия файла
                    $name = sprintf("%s_%d", $tmp_name, ++$count);
                    $full_name = $path . $name . '.' . $ext;
                }
            }elseif(file_exists($full_name) && !is_null($onNameExistsRename)){
				$error = 'Файл с таким названием уже существует';
				return;
            }
			$real_path = $FILE->getPath() . DIRECTORY_SEPARATOR . $FILE->getBasename();
            $save_title = empty($title) ? $name : $title;
            if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE){
                if (!is_array($save_title)) {
                    $segments = \App\Segment::getInstance()->getAll();
                    $res = array();
                    foreach ($segments as $s) {
                        $res[$s['id']] = $save_title;
                    }
                    $save_title = $res;
                }
                $save_title = json_encode($save_title);
            }
            if (!is_dir($real_path) && ($real_path == $full_name || move_uploaded_file($real_path, $full_name) || (copy($real_path, $full_name) && unlink($FILE->getRealPath())))){//копируем и записываем в таблицу
				$max_position = $db->query('SELECT MAX(`position`) FROM `'.static::TABLE.'`')->getCell();
                $file_id = $db->query('INSERT INTO `'.static::TABLE.'` SET `title`=?, `directory` = ?s, `name`=?, `ext`=?, `size`=?d, `position` = ?d, `date` = NOW()', $save_title, !empty($directory) ? $directory : NULL, $name, $ext, filesize($full_name), !empty($max_position) ? $max_position + 1 : 1);
                foreach (static::$dataProviders as $p){
                    $p->onCreate($file_id, $save_title, $FILE);
                }
                return $file_id;
            }else{
                $error = 'Не удалось загрузить файл';
                return;
            }
        }else{
            $error = 'Неверный формат файла';
            return;
        }
    }

    public function copy(&$errors = NULL){
        $path = static::getFilePath();
        $tmp_path = $path.'tmp/';
        if (!file_exists($tmp_path)){
            \LPS\Components\FS::makeDirs($tmp_path);
        }
        copy($path.$this['full_name'], $tmp_path.$this['full_name']);
        $FILE = new \Symfony\Component\HttpFoundation\File\UploadedFile($tmp_path.$this['full_name'], $this['title']);
        $file_id = static::add($this->data['title'], $FILE, $errors);
        $file = !empty($file_id) ? static::getById($file_id) : NULL;
        if (empty($file)){
            return FALSE;
        }
        $update_data = array();
        foreach(static::$updateFields as $field){
            if (!in_array($field, array('title', 'ext', 'size', 'name'))) {
                $update_data[$field] = $this[$field];
            }
        }
        if (!empty($update_data)){
            $file->edit($update_data);
        }
        return $file;
    }
    public static function del($ids){
        $db = \App\Builder::getInstance()->getDB();
        if (!is_array($ids)){
            $ids = array($ids);
        }
        $files_names = $db->query('SELECT `id`, CONCAT(`name`, ".", `ext`) AS `name` FROM '. static::TABLE .' WHERE `id` IN (?i)', $ids)->getCol('id', 'name');
        $error = '';
        $error_ids = array();
        static::clearRegistry(array_keys($files_names));
        foreach ($files_names as $id => $file_name){
            $full_file_name = static::getFilePath() . $file_name;
            if (!file_exists($full_file_name) || unlink($full_file_name)){
                $db->query('DELETE FROM `'.static::TABLE.'` WHERE `id`=?d', $id);
                $db->query('DELETE FROM `'. InternalLinkManager::TABLE.'` WHERE `obj_type`="file" AND `obj_id`=?d', $id); //удаляем все связи
            }else{
                $error .= 'Не удалось удалить файл ' . $file_name . '<br />';
                $error_ids[] = $id;
            }
        }
        foreach (static::$dataProviders as $p){
            $p->onDelete($ids, $error_ids);
        }
        return empty($error) ? TRUE : $error;
    }
    public static function search($params = array(), &$count = NULL){
        $db = \App\Builder::getInstance()->getDB();
        $ids = $db->query('
            SELECT SQL_CALC_FOUND_ROWS `id`
            FROM `'. static::TABLE .'`
            WHERE 1
                { AND `id` IN (?i)}
                { AND `show_in` = ?d}
                { AND `type` IN (?l)}
				{ AND CONCAT(`name`, ".", `ext`) = ?s}
				{ AND `segment_id` = ?d}
				ORDER BY `position`
            { LIMIT ?d, ?d}',
            !empty($params['ids']) ? $params['ids'] : $db->skipIt(),
            !empty($params['show_in']) ? 1 : $db->skipIt(),
            !empty($params['type']) ? (is_array($params['type']) ? $params['type'] : array($params['type'])) : $db->skipIt(),
			!empty($params['name']) ? $params['name'] : $db->skipIt(),
			!empty($params['segment_id']) ? $params['segment_id'] : $db->skipIt(),
            isset($params['start']) ? $params['start'] : $db->skipIt(),
            isset($params['limit']) ? $params['limit'] : $db->skipIt()
        )->getCol('id', 'id');
		$count = $db->query('SELECT FOUND_ROWS()')->getCell();
        return static::factory($ids);
    }
    protected static function prepareFileName($name, $translit = TRUE){
        // отрезаем расширение
        $parts = explode('.', $name);
        $lastdot = array_pop($parts);
        $name = basename($name, '.'.$lastdot);
        // конвертируем в транслит
        if ($translit){
            $name = Translit::UrlTranslit($name);
        }
        return $name;
    }
    public static function getFilePath(){
        return Config::getRealDocumentRoot() . static::FILES_PATH;
    }
    public static function f_bafsize($size, $segment_key = NULL) {
		if (empty($segment_key) || $segment_key == 'ru'){
			$iec = array('б', 'Кб', 'Мб', 'Гб');
		}elseif($segment_key == 'en'){
			$iec = array('b', 'Kb', 'Mb', 'Gb');
		}
        if (empty($size))
            return '0 '.$iec[0];
        $i = 0;
        while (($size / 1024) > 1) {
            $size = $size / 1024;
            $i++;
        }
        $size = round($size); //округляем
        $echo = $size . ' ' . $iec[$i]; //Формируем вывод
        return $echo;
    }
	/**
	 * Найти файл по имени (в системах, где только имена файлов не совпадают)
	 * @param string $name
	 * @param string $error
	 * @return File
	 */
	public static function getByName($name, &$error = NULL){
		$files = static::search(array('name' => $name));
		if (empty($files)){
			return NULL;
		}
		if (count($files) > 1){
//			echo 'Дубликаты файла ' . $name . ' => ' . implode(',', array_keys($files));
			$error = 'Дубликаты файла ' . $name . ' => ' . implode(',', array_keys($files));
			return NULL;
//			throw new \LogicException('Взять файл по имени можно только в системах с уникальными именами фалов');
		}
		return reset($files);
	}

    /**
     * @TODO заменить $allowFields на $mainFields
     * @param type $data
     * @param null $segment_id
     */
    protected function __construct($data, $segment_id = NULL){
        foreach (static::$allowFields as $field){
            $this->data[$field] = !empty($data[$field]) ? $data[$field] : NULL;
        }
        $this->segment_id = $segment_id;
        if (!empty($this->data['date'])) {
            $this->data['date'] = strtotime($this->data['date']);
        }
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE && !empty(static::$segmentFields)) {
            foreach(static::$segmentFields as $field) {
                $field_data = json_decode($this->data[$field], true);
                if (!empty($field_data)) {
                    $this->data[$field] = $field_data;
                }
            }
        }
    }
    public function __destruct() {
        $this->save();
    }
    protected function getData($key){
        if (!empty(static::$dataProvidersByFields[$key])) {
            return static::$dataProvidersByFields[$key]->get($this, $key, $this->data['segment_id']);
        } elseif (in_array($key, static::$allowFields)){
            if ($key == 'full_size'){
                return static::f_bafsize($this->data['size']);
            } elseif ($key == 'cover'){
                return \Models\ImageManagement\Image::getById($this->data['cover_id']);
            } elseif($key == 'full_name'){
                return $this->data['name'] . '.' . $this->data['ext'];
            } elseif (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE && in_array($key, static::$segmentFields)) {
                $segment_id = !empty($this->data['segment_id']) ? $this->segment_id : \App\Segment::getInstance()->getDefault()['id'];
                return !empty($this->data[$key][$segment_id]) ? $this->data[$key][$segment_id] : NULL;
            } else {
                return $this->data[$key];
            }
        }else{
            throw new \LogicException('No key ' . $key . ' in Files');
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
            throw new \LogicException('Key ' . $key . ' not allowed in ' . __CLASS__);
        }
        if (!in_array($key, static::$updateFields)){
            throw new \LogicException('Key '.$key.' unchangable');
        }
        if ($this->data[$key] == $value){
            return NULL;
        }
        $this->data[$key] = $value;
        $this->needSave = true;
        return TRUE;
    }
    /**
     * Редактирование
     * @param array $params
     * @return boolean
     */
    public function edit($params) {
        if (empty ($params))
            return NULL;
        foreach (static::$dataProviders as $p){
            $p->preUpdate($this, $params);
        }
        $updated = NULL;
        foreach (static::$updateFields as $field){
            if (array_key_exists ($field, $params)){
                if ($this->setData($field, $params[$field])){
                    $updated = TRUE;
                }
            }
        }
        foreach (static::$dataProviders as $p){
            $p->onUpdate($this);
        }
        return $updated;
    }
    public function setShowIn($show_in){
        return $this->edit(array('show_in' => $show_in));
    }
	public function setKnownDownloader($param){
		return $this->edit(array('known_downloader' => $param));
	}
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param boolean $translitName
     * @param boolean $onNameExistsRename
     * @return boolean|string
     */
    public function reload(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE, $translitName = TRUE, $onNameExistsRename = TRUE){
        $original_file_name = $FILE->getClientOriginalName();
        if (preg_match('~\.('.implode('|', static::$allow_ext).')$~i', $original_file_name, $regs)){
            $ext = strtolower($regs[1]);
            $name = static::prepareFileName($original_file_name, $translitName);
            $path_to_file = static::getFilePath() . (!empty($this['directory']) ? ($this['directory'] . '/') : '');
            if (!file_exists($path_to_file)){
                FS::makeDirs($path_to_file);
            }
            $path = realpath($path_to_file) . DIRECTORY_SEPARATOR;
            $full_name = $path . $name . '.' . $ext;
            $count = 0;
            if ($onNameExistsRename && $name.'.'.$ext != $this['full_name']){
                while(file_exists($full_name)){//при совпадении названия файла
                    $name = sprintf("%s_%d", $name, ++$count);
                    $full_name = $path . $name . '.' . $ext;
                }
            }elseif(!is_null($onNameExistsRename) && file_exists($full_name) && $full_name != $path . $this['full_name']){
                return 'У файла должно быть такое же название';
            }
            $new_file_path = $FILE->getPath() . DIRECTORY_SEPARATOR . $FILE->getBasename();
            if (!is_null($onNameExistsRename) && file_exists($path . $this['full_name'])){
                @unlink($path . $this['full_name']);
            }
            if ($new_file_path == $full_name || move_uploaded_file($FILE->getRealPath(), $full_name) || (copy($FILE->getRealPath(), $full_name) && unlink($FILE->getRealPath()))){//копируем и записываем в таблицу
                foreach (self::$dataProviders as $p){
                    $p->onReload($this, $FILE);
                }
                $this->edit(array('name' => $name, 'ext' => $ext, 'size' => filesize($full_name)));
                return true;
            }else{
                return 'Не удалось загрузить файл';
            }
        }else{
            return 'Неверный формат файла';
        }
    }
    /**
     * Загрузка обложки
     * @param array $FILE
	 * @param string $status
     * @return string
     */
    public function uploadCover(\Symfony\Component\HttpFoundation\File\UploadedFile $FILE = NULL, &$error = NULL){
        $new_cover = FALSE;
        if ($FILE){
            if (empty($this['cover'])){
                $collection = Collection::getById(FilesImageCollection::COLLECTION_ID);
                $cover = $collection->addImage($FILE, '', $error);
                $this->setData('cover_id', $cover['id']);
                $new_cover = TRUE;
            }else{
                $cover = $this['cover'];
                $error = $cover->reload($FILE);
            }
            $result = $cover;
        }else{
            if (!empty($this['cover_id'])){
                \Models\ImageManagement\Image::del($this['cover_id']);
                $this->setData('cover_id', NULL);
            }
            $result = NULL;
        }
        foreach (static::$dataProviders as $p){
            $p->onCoverUpload($FILE, $new_cover, $error);
        }
        return $result;
    }
    /**
     * Сохраняет все поля объекта в базу
     */
    public function save() {
        if ($this->needSave) {
            $update_fields = array();
            foreach (static::$updateFields as $field) {
                if (in_array($field, static::$segmentFields)
                    && \LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE
                    && is_array($this->data[$field]))
                {
                    $update_fields[$field] = json_encode($this->data[$field]);
                } else {
                    $update_fields[$field] = $this->data[$field];
                }
            }
            if (isset($update_fields['date'])){
                $update_fields['date'] = date('Y-m-d', $update_fields['date']);
            }
            \App\Builder::getInstance()->getDB()->query('UPDATE `' . static::TABLE . '` SET ?a WHERE `id` = ?d', $update_fields, $this['id']);
        }
        $this->needSave = false;
    }
    public function asArray(){
        $data = array(
            'full_size' => $this->getData('full_size'), 
            'full_name' => $this->getData('full_name')
            ) + $this->data;
        foreach (static::$dataProvidersByFields as $f){
            $data[$f] = $this[$f];
        }
        return $data;
    }
	
	public function getUrl($type = 'download'){
		if ($type == 'download'){
			return '/files/download/?file=' . $this['full_name'];
		}
		$short = (!empty($this['directory']) ? ($this['directory'] . '/') : '') . $this['full_name'];
		$relative = self::FILES_PATH . $short;
		if($type == 'relative'){
			return $relative;
		}elseif($type == 'absolute'){
			return Config::getRealDocumentRoot() . $relative;
		}
		throw new \Exception('Передан неверный тип получаемого пути к файлу. Передан: ' . $type . ' Возможные типы: ' . implode(', ', array('download','absolute','relative')));
	}
	/**
     * двигает на заданную позицию
     * @param int $move на какой номер менять позицию
     */
    public function move($move){
		$db = \App\Builder::getInstance()->getDB();
        $file_id = $this['id'];
        if ($file_id && !empty($move)){
            $old_position = $this['position'];
            if ($old_position > $move){
                $db->query('UPDATE `' . static::TABLE . '` SET `position`=`position`+1 WHERE `position`>=?d AND `position`<?d', $move, $old_position);
            }else{
                $db->query('UPDATE `' . static::TABLE . '` SET `position`=`position`-1 WHERE `position`<=?d AND `position`>?d', $move, $old_position);
            }
            $this->edit(array('position' => $move));
        }
    }

    /* ****************************** ArrayAccess **************************** */

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        if ($offset == 'cover'){
            return isset($this->data['cover_id']);
        }
        return array_key_exists($offset, $this->data);
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
    
    /* ********************* работа с iFileDataProvider ********************* */
    /**
     * @var Helpers\iFileDataProvider[]
     */
    static protected $dataProviders = array();
    /**
     * @var Helpers\iFileDataProvider[]
     */
    static protected $dataProvidersByFields = array();

    /**
     * @static
     * @param Helpers\iFileDataProvider $provider
     */
    static function addDataProvider($provider){
        static::$dataProviders[get_class($provider)] = $provider;
        foreach ($provider->fieldsList() as $field){
            static::$dataProvidersByFields[$field] = $provider;
        }
    }

    /**
     * @static
     * @param Helpers\iFileDataProvider $provider
     */
    static function delDataProvider($provider){
        unset(static::$dataProviders[get_class($provider)]);
    }
}