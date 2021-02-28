<?php
namespace Models\ImageManagement\Helpers\CollectionImage;

use Models\Logger AS MainLogger;
use Models\ImageManagement\CollectionImage;
/**
 * Description of Logs
 *
 * @author olya
 */
class Logs extends Helper{
    const LOG_TYPE = 'collection';
    private $old_data = array();
	private $infoExists = FALSE;
    protected static $i = NULL;
	protected function __construct() {
		parent::__construct();
		$this->infoExists = class_exists('Info');
	}
    protected static $logged_fields = array(
        'width' => 'Ширина', 
        'height' => 'Высота', 
        'hidden' => 'Спрятать', 
        'gravity' => 'Центр',
        'ext' => 'Расширение'
    );
    /**
     * Событие после загрузки картинки (надо совместить с созданием)
     * @param CollectionImage $image
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     */
    public function onUpload(CollectionImage $image, \Symfony\Component\HttpFoundation\File\UploadedFile $FILE, $from_create){
        MainLogger::add(
            array(
                'type' => MainLogger::LOG_TYPE_IMG,
                'entity_type' => self::LOG_TYPE,
                'entity_id' => $image['collection_id'],
                'comment' => $image['id'],
                'attr_id' => 'file',
                'additional_data' => array(
                    'f_n' => $FILE->getClientOriginalName(),
                    'w' => $image['width'],
                    'h' => $image['height'],
                    'e' => $image['ext'],
                    'a' => $from_create ? MainLogger::LOG_TYPE_CREATE : MainLogger::LOG_TYPE_EDIT
                )
            )
        );
    }
     /**
     * событие перед изменением
     */
    public function preUpdate(CollectionImage $image, &$params, &$errors){
        $this->old_data[$image['id']] = $image->asArray();
    }
     /**
     * событие после изменения CollectionImage
     */
    public function onUpdate(CollectionImage $image){
        if (!empty($this->old_data[$image['id']])){
            return;
        }
        $old_data = $this->old_data[$image['id']];
        $new_data = $image->asArray();
        foreach (self::$logged_fields as $f => $n){
            if ($old_data[$f] == $new_data[$f]){
                continue;
            }
            MainLogger::add(
                array(
                    'type' => MainLogger::LOG_TYPE_IMG,
                    'entity_type' => self::LOG_TYPE,
                    'entity_id' => $image['collection_id'],
                    'comment' => $image['id'],
                    'attr_id' => $f,
                    'additional_data' => array(
                        'v' => $new_data[$f],
                        'a' => MainLogger::LOG_TYPE_EDIT
                    )
                )
            );
        }
        //с инфо надо отдельно разбираться
        if ($this->infoExists && (!empty($old_data['info']) || !empty($new_data['info']))){
			$info_fields = Info::factory()->fieldsList();
			foreach ($info_fields as $if){
				//если какого-то поля нет, заменяем его на NULL
				//для того чтобы удобно было сравнивать и не городить огромные условия
				if (!array_key_exists($if, $old_data['info'])){
					$old_data['info'][$if] = NULL;
				}
				if (!array_key_exists($if, $new_data['info'])){
					$new_data['info'][$if] = NULL;
				}
				if ($old_data['info'][$if] == $new_data['info'][$if]){
					continue;
				}
				MainLogger::add(
					array(
						'type' => MainLogger::LOG_TYPE_IMG,
						'entity_type' => self::LOG_TYPE,
						'entity_id' => $image['collection_id'],
						'comment' => $image['id'],
						'attr_id' => $if,
						'additional_data' => array(
							'v' => $new_data['info'][$if],
							'a' => MainLogger::LOG_TYPE_EDIT
						)
					)
				);
			}
        }
        unset($this->old_data[$image['id']]);
    }
    /**
     *
     * @param CollectionImage $image
     */
    public function onDelete(CollectionImage $image){
        MainLogger::add(
            array(
                'type' => MainLogger::LOG_TYPE_IMG,
                'entity_type' => self::LOG_TYPE,
                'entity_id' => $image['collection_id'],
                'comment' => $image['id'],
                'additional_data' => array(
                    'a' => MainLogger::LOG_TYPE_DEL
                )
            )
        );
    }
}