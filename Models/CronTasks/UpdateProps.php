<?php
namespace Models\CronTasks;
/**
 * Description of UpdateProps
 *
 * @author olya
 */
class UpdateProps extends Task{
    const MANUAL = TRUE;
    const STOPPABLE = FALSE;
    const CANCELABLE = TRUE;
    const TITLE = 'Групповое изменение свойств';
    /**
     * создать задачу
     * 
     * @param array $params
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|string $FILE
     * @param string $error
     */
    public static function create($params = array(), $FILE = NULL, &$error = NULL){
        
    }
    public static function createAndStart(){
        return;
    }
    public function start(){
        $this->setStart();
        if (empty($this['data']['catalog_id'])){
            $this->setCancel(array('errors' => 'Не передан catalog_id'));
        }
        $catalogManager = \Models\CatalogManagement\Catalog::factory($this['data']['catalog_id'], $this['segment_id']);
        $catalogManager->updateProperties($this['data']['type_id'], $this['data']['request_data'], $this['data']['properties_values'], $errors, $this['segment_id']);
        if (!empty($errors)){
            foreach ($errors as $e_t => $ed){
                foreach ($ed as $e_id => $er){
                    $this->addError($e_t . $e_id, $er);
                }
            }
        }
        $this->setComplete();
    }
}
