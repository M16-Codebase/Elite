<?php
namespace LPS\Components\SharedMemory;

/* 
 * Класс для работы с разделяемой памятью, используя модуль Semaphore
 */
class Sem implements iSharedMemory{
    const FILE_PATH = '/logs/shm_error.log';
    /**
     * Системный id блока памяти
     *
     * @var int
     */
    protected $id;
    /**
     * Указатель на блок памяти
     *
     * @var int
     */
    protected $shm;
    /**
     * Экземпляр класса
     * @var static
     */
    private static $instance = NULL;
    /**
     * 
     * @return static
     */
    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }
    private function __construct(){
        $this->id = ftok(__FILE__, 'a');//т.к. название файла даст нам уникальный id, то не надо запариваться по поводу литеры
        if (empty($this->id)){
            throw new \Exception('Невозможно обратиться к блоку памяти, пустой id');
        }
        $this->shm = shm_attach($this->id, \App\Configs\SharedMemoryConfig::MEMORY_LIMIT, \App\Configs\SharedMemoryConfig::PERMISSIONS);
    }
    /**
     * взять позырить id блока памяти
     * @return type
     */
    public function getId(){
        return $this->id;
    }
    public function get($var_key, $id = NULL){
        if (empty($var_key) || !is_int($var_key)){
            throw new \Exception('Идентификатор переменной должен быть целым числом (0 тоже нельзя использовать)');
        }
        if (is_null($id)){
            return shm_has_var($this->shm, $var_key) ? shm_get_var($this->shm, $var_key) : NULL;
        }else{
            $data = shm_has_var($this->shm, $var_key) ? shm_get_var($this->shm, $var_key) : NULL;
            return array_key_exists($data[$id]) ? $data[$id] : NULL;
        }
    }
    public function set($var_key, $id = NULL, $value){
        if (empty($var_key) || !is_int($var_key)){
            throw new \Exception('Идентификатор переменной должен быть целым числом (0 тоже нельзя использовать)');
        }
        $shm = @shmop_open($this->id, 'a', 0, 0);
        $current_size = shmop_size($shm);
        if ($current_size != \App\Configs\SharedMemoryConfig::MEMORY_LIMIT){
            //самопочинка при изменении лимита памяти
            shmop_close($shm);
            $this->delete();
            return;
        }
        //надо проверить, влезают ли данные
        $old_size = mb_strlen(trim(shmop_read($shm, 0, $current_size)));
        shmop_close($shm);
        $data = $this->get($var_key);
        $data_size = mb_strlen(serialize($data));
        if (!is_null($id)){
            $data[$id] = $value;
        }else{
            $data = $value;
        }
        $new_data_size = mb_strlen(serialize($data));
        if ($old_size + $new_data_size - $data_size > \App\Configs\SharedMemoryConfig::MEMORY_LIMIT){
            $this->error_log('Превышен лимит разделяемой памяти. old_size: ' . $old_size . ', new_data_size: ', $new_data_size . ', data_size: ' . $data_size);
            return;
        }
        if (!@shm_put_var($this->shm, $var_key, $data)){
            $this->error_log('Не получается положить данные в разделяемую память.');
            return;
        }
    }
    public function remove($var_key, $id = NULL){
        if (empty($var_key) || !is_int($var_key)){
            throw new \Exception('Идентификатор переменной должен быть целым числом (0 тоже нельзя использовать)');
        }
        if (is_null($id)){
            if (shm_has_var($this->shm, $var_key)){
                shm_remove_var($this->shm, $var_key);
            }
        }else{
            $data = $this->get($var_key);
            if (isset($data[$id])){
                unset($data[$id]);
            }
            $this->set($var_key, NULL, $data);
        }
    }
    /**
     * Удаляем из памяти зарезервированный блок
     */
    public function delete(){
        if (empty($this->shm)){
            return;
        }
        shm_remove($this->shm);
    }
    public function __destruct(){
        shm_detach($this->shm);
    }
    /**
     * 
     */
    private function error_log($message){
        $file = str_replace('//', '/', \LPS\Config::getRealDocumentRoot() . self::FILE_PATH);
        $h = fopen($file, 'w');
        fwrite($h, date('d.m.Y H:i:s') . ' - ' . $message);
        fclose($h);
        chmod($file, 0660);
    }
}