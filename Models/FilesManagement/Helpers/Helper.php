<?php
namespace Models\FilesManagement\Helpers;
use Models\FilesManagement\File;
/**
 * Description of Helper
 *
 * @author olya
 */
class Helper implements iFileDataProvider{
    protected static $i = NULL;
	protected static $fieldList = array();
    /**
     * @return static
     */
    public static function factory(){
        if (empty (static::$i)){
            static::$i = new static();
        }
        return static::$i;
    }
    protected function __construct(){
        File::addDataProvider($this);
    }
    public function fieldsList() {
        return static::$fieldList;
    }

    public function get(File $file, $field, $segment_id = NULL) {

    }

    public function onLoad(File $file) {
        
    }

    public function preCreate($title, $FILE, $error) {
        
    }

    public function onCreate($file_id, $title, $FILE) {
        
    }

    public function onDelete($ids, $error_ids) {
        
    }

    public function preUpdate(File $file, &$params) {
        
    }

    public function onUpdate(File $file) {
        
    }

    public function onCoverUpload($FILE, $new_cover, $error) {
        
    }
    
    public function onReload(File $f, $FILE){}

}
