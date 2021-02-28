<?php
namespace Modules\Site;
/**
 * Description of SharedMemory
 *
 * @author olya
 */
class SharedMemory extends \LPS\AdminModule{
    public function index() {
        $shm = \App\Builder::getInstance()->getSharedMemory();
        $shm_data = array();
        if (!empty($shm)){
            foreach (\App\Configs\SharedMemoryConfig::getEntityKey() as $k => $v){
                $data = $shm->get($v);
                if (!empty($data)){
                    $shm_data[$k] = $data;
                }
            }
        }
        $this->getAns()
            ->add('shm_limit', \App\Configs\SharedMemoryConfig::MEMORY_LIMIT)
            ->add('shm_data', $shm_data)
            ->add('shm_length', empty($shm_data) ? 0 : mb_strlen(serialize($shm_data)))
            ->add('shm_id', dechex($shm->getId()));
    }
    /**
     * проверяем что как с памятью и отдаем json. для внешних систем.
     */
    public function check(){
        $secret_key = $this->request->request->get('secret_key');
        if ($secret_key != \App\Configs\SharedMemoryConfig::SECRET_KEY_VIEW_DATA){
            return 'key failed';
        }
        $shm = \App\Builder::getInstance()->getSharedMemory();
        $shm_data = array();
        if (!empty($shm)){
            foreach (\App\Configs\SharedMemoryConfig::getEntityKey() as $k => $v){
                $data = $shm->get($v);
                if (!empty($data)){
                    $shm_data[$k] = $data;
                }
            }
        }
        return json_encode(array(
            'shm_limit' => \App\Configs\SharedMemoryConfig::MEMORY_LIMIT,
            'shm_length' => empty($shm_data) ? 0 : mb_strlen(serialize($shm_data)),
            'shm_id' => dechex($shm->getId())
        ));
    }
    public function delete(){
        $shm = \App\Builder::getInstance()->getSharedMemory();
        $shm->delete();
        return $this->redirect('/shared-memory/');
    }
}
