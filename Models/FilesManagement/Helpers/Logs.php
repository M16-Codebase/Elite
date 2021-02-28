<?php
namespace Models\FilesManagement\Helpers;

use Models\Logger AS MainLogger;
use Models\FilesManagement\File;
/**
 * Description of Logs
 *
 * @author olya
 */
class Logs extends Helper{
    const LOG_TYPE = 'file';
    protected static $i = NULL;
    private $oldDataCache = array();
    public function onCreate($file_id, $title, $FILE) {
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE){
            $title = json_decode($title, TRUE);
            $title = reset($title);
        }
        MainLogger::add(
            array(
                'type' => MainLogger::LOG_TYPE_CREATE,
                'entity_type' => self::LOG_TYPE,
                'entity_id' => $file_id,
                'additional_data' => array(
                    'f_n' => $FILE->getClientOriginalName(),
                    't' => $title
                )
            )
        );
    }

    public function onDelete($ids, $error_ids) {
        foreach ($ids as $id){
            if (!in_array($id, $error_ids)){
                MainLogger::add(
                    array(
                        'type' => MainLogger::LOG_TYPE_DEL,
                        'entity_type' => self::LOG_TYPE,
                        'entity_id' => $id,
                        'additional_data' => array(
                        )
                    )
                );
            }
        }
    }

    public function preUpdate(File $file, &$params) {
        $this->oldDataCache[$file['id']] = $file->asArray();
    }

    public function onUpdate(File $file) {
        $old_data = $this->oldDataCache[$file['id']];
        unset($this->oldDataCache[$file['id']]);
        $new_data = $file->asArray();
        $diffs = array();
        foreach ($old_data as $k => $f){                
            if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE && is_array($f) && in_array($k, File::getSegmentFields())){
                foreach ($f as $sid => $sv){
                    if ($new_data[$k][$sid] != $sv){
                        $diffs[$k][$sid] = $new_data[$k][$sid];
                    }
                }
            }else{
                if ($f != $new_data[$k]){
                    $diffs[$k] = $new_data[$k];
                }
            }
        }
        if (empty($diffs)){
            return;
        }
        $logged_fields = \App\Configs\FileConfig::getFields();
        foreach ($diffs as $field => $d){
            if (!array_key_exists($field, $logged_fields)){
                continue;
            }
            if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE && is_array($d) && in_array($field, File::getSegmentFields())){
                foreach ($d as $s_id => $v){
                    MainLogger::add(
                        array(
                            'type' => MainLogger::LOG_TYPE_EDIT,
                            'entity_type' => self::LOG_TYPE,
                            'entity_id' => $file['id'],
                            'attr_id' => $field,
                            'segment_id' => $s_id,
                            'additional_data' => array(
                                't' => \LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE ? reset($old_data['title']) : $old_data['title'],
                                'v' => $v
                            )
                        )
                    );
                }
            }else{
                MainLogger::add(
                    array(
                        'type' => MainLogger::LOG_TYPE_EDIT,
                        'entity_type' => self::LOG_TYPE,
                        'entity_id' => $file['id'],
                        'attr_id' => $field,
                        'additional_data' => array(
                            't' => \LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE ? reset($old_data['title']) : $old_data['title'],
                            'v' => $d
                        )
                    )
                );
            }
        }
    }
    public function onReload(File $f, $FILE){
        MainLogger::add(
            array(
                'type' => MainLogger::LOG_TYPE_EDIT,
                'entity_type' => self::LOG_TYPE,
                'entity_id' => $f['id'],
                'attr_id' => 'file',
                'additional_data' => array(
                    'f_n' => $FILE->getClientOriginalName(),
                    't' => $f['title']
                )
            )
        );
    }
    public function onCoverUpload($FILE, $new_cover, $error) {
        
    }
}
