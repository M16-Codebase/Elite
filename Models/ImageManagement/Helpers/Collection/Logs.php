<?php
namespace Models\ImageManagement\Helpers\Collection;

use Models\ImageManagement\Collection;
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
	  /**
     * событие перед изменением
     */
    public function preUpdate(Collection $collection, &$params, &$errors){
        $this->old_data[$collection['id']] = $collection->asArray();
    }
     /**
     * событие после изменения Collection
     */
    public function onUpdate(Collection $collection){
        if (!empty($this->old_data[$collection['id']])){
            return;
        }
        $old_data = $this->old_data[$collection['id']];
        $new_data = $collection->asArray();
        foreach (self::$logged_fields as $f => $n){
            if ($old_data[$f] == $new_data[$f]){
                continue;
            }
            \Models\Logger::add(
                array(
                    'type' => \Models\Logger::LOG_TYPE_EDIT,
                    'entity_type' => self::LOG_TYPE,
                    'entity_id' => $collection['id'],
                    'attr_id' => $f,
                    'additional_data' => array(
                        'v' => $new_data[$f]
                    )
                )
            );
        }
        //с инфо надо отдельно разбираться
        if ($this->infoExists && (!empty($old_data['data']) || !empty($new_data['data']))){
			$data_fields = Info::factory()->fieldsList();
			foreach ($data_fields as $if){
				//если какого-то поля нет, заменяем его на NULL
				//для того чтобы удобно было сравнивать и не городить огромные условия
				if (!array_key_exists($if, $old_data['data'])){
					$old_data['data'][$if] = NULL;
				}
				if (!array_key_exists($if, $new_data['data'])){
					$new_data['data'][$if] = NULL;
				}
				if ($old_data['data'][$if] == $new_data['data'][$if]){
					continue;
				}
                \Models\Logger::add(
					array(
						'type' => \Models\Logger::LOG_TYPE_EDIT,
						'entity_type' => self::LOG_TYPE,
						'entity_id' => $collection['id'],
						'attr_id' => $if,
						'additional_data' => array(
							'v' => $new_data['data'][$if]
						)
					)
				);
			}
        }
        unset($this->old_data[$collection['id']]);
    }
    /**
     *
     * @param Collection $collection
     */
    public function onDelete(Collection $collection){
        \Models\Logger::add(
            array(
                'type' => \Models\Logger::LOG_TYPE_DEL,
                'entity_type' => self::LOG_TYPE,
                'entity_id' => $collection['id'],
                'additional_data' => array(
                    
                )
            )
        );
    }
}
