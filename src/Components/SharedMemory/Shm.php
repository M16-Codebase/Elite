<?php
namespace LPS\Components\SharedMemory;

/* 
 * Класс для работы с разделяемой памятью, используя только набор функций shmop
 */
class Shm implements iSharedMemory{
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
        $this->id = $this->_ftok('a');//т.к. название файла даст нам уникальный id, то не надо запариваться по поводу литеры
        if (empty($this->id)){
            throw new \Exception('Невозможно обратиться к блоку памяти, пустой id');
        }
        $this->shm = $this->exists() ? shmop_open($this->id, "w", 0, 0) : NULL;
    }
    public function getId(){
        return $this->id;
    }
    public function get($var_key, $id = NULL){
        if (empty($var_key)){
            throw new \Exception('Не передан идентификатор переменной (0 тоже нельзя использовать)');
        }
        if (!is_int($var_key)){
            throw new \Exception('Идентификатор переменной должен быть целым числом');
        }
        $data = $this->read();
        return isset($data[$var_key]) ? (is_null($id) ? $data[$var_key] : (array_key_exists($id, $data[$var_key]) ? $data[$var_key][$id] : NULL)) : NULL;
    }
    public function set($var_key, $id = NULL, $value){
        if (empty($var_key)){
            throw new \Exception('Не передан идентификатор переменной (0 тоже нельзя использовать)');
        }
        if (!is_int($var_key)){
            throw new \Exception('Идентификатор переменной должен быть целым числом');
        }
        $data = $this->read();
        if (!is_null($id)){
            $data[$var_key][$id] = $value;
        }else{
            $data[$var_key] = $value;
        }
        $this->write($data);
    }
    public function remove($var_key, $id = NULL){
        if (empty($var_key)){
            throw new \Exception('Не передан идентификатор переменной (0 тоже нельзя использовать)');
        }
        if (!is_int($var_key)){
            throw new \Exception('Идентификатор переменной должен быть целым числом');
        }
        $data = $this->read();
        if (is_null($id)){
            if (isset($data[$var_key])){
                unset($data[$var_key]);
            }
        }else{
            if (isset($data[$var_key]) && array_key_exists($id, $data[$var_key])){
                unset($data[$var_key][$id]);
            }
        }
        $this->write($data);
    }
    /**
     * Удаляем из памяти зарезервированный блок
     */
    public function delete(){
        if (empty($this->shm)){
            return;
        }
        $h = $this->lock();
        shmop_delete($this->shm);
        $this->unlock($h);
    }
    public function __destruct(){
        $this->close();
    }
    /**
     * Проверяем, существует ли блок памяти с нашим id
     */
    private function exists(){
        return @shmop_open($this->id, "a", 0, 0);
    }
    /**
     * записываем массив данных
     */
    private function write($data){
        if (!empty($this->shm)){
            $current_size = shmop_size($this->shm);
            if (empty($data) || $current_size != \App\Configs\SharedMemoryConfig::MEMORY_LIMIT){
                //самопочинка при изменении лимита памяти, либо если передали пустые данные
                $this->delete();
                return;
            }
        }
        //упаковываем
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $str_len = mb_strlen($data);
        if ($str_len >= \LPS\Config::SHM_MEMORY_LIMIT){
            $this->error_log('Превышен лимит разделяемой памяти.');
            return;
        }
        $this->shm = @shmop_open($this->id, "c", \App\Configs\SharedMemoryConfig::PERMISSIONS, \App\Configs\SharedMemoryConfig::MEMORY_LIMIT);
        if (empty($this->shm)){
            throw new \Exception('Невозможно открыть блок разделяемой памяти');
        }
        //Подсчитываем максимально используемое количество памяти, чтобы затирать лишнее, если прошлая запись была длиннее
        if ($str_len < \LPS\Config::SHM_MEMORY_LIMIT){
            $data .= str_repeat(' ', \LPS\Config::SHM_MEMORY_LIMIT-$str_len);
        }
        $h = $this->lock();
        shmop_write($this->shm, $data, 0);
        $this->unlock($h);
    }
    /**
     * Забрать всю информацию из памяти
     */
    private function read(){
        if (empty($this->shm)){
            return array();
        }
        $data = shmop_read($this->shm, 0, shmop_size($this->shm));
        return json_decode(trim($data), TRUE);
    }
    private function close(){
        if (!empty($this->shm)){
            shmop_close($this->shm);
            $this->shm = NULL;
        }
    }
    /**
     * Уникальный идентификатор
     * @param string $proj
     * @return int
     */
    private function _ftok($proj = ''){
        $st = @stat(\LPS\Config::getRealDocumentRoot());
        if (!$st) {
            return -1;
        }
        return sprintf("%u", (($st['ino'] & 0xffff) | (($st['dev'] & 0xff) << 16) | (($proj & 0xff) << 24)));
    }
    /**
     * Закрываем доступ на запись
     * @return resource указатель на локер
     */
    private function lock(){
        $h = fopen(\LPS\Config::getRealDocumentRoot() . '/data/shm_' . $this->id . '.shm', 'w');
        flock($h, LOCK_EX);
        return $h;
    }
    /** 
     * Разблокировать доступ на запись
     * @param resource $h указатель на локер
     * 
     */
    private function unlock($h){
        fclose($h);
    }
    /**
     * 
     */
    private function error_log($message){
        $file = \LPS\Config::getRealDocumentRoot() . self::FILE_PATH;
        $h = fopen($file, 'w');
        fwrite($h, date('d.m.Y H:i:s') . ' - ' . $message);
        fclose($h);
    }
}
