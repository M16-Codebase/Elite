<?php
namespace MysqlSimple;
use mysqli_result;
use mysqli;
/**
 * Класс для работы с результатами MySQL
 */
class Result{

    /**
     * Указатель на результат мускуля
     * @var mysqli_result
     */
    protected $result = NULL;

    /**
     * @var Controller
     */
    private $simpleMySQLi = NULL;

    /**
     * Флаг, что указатель результата находится в начале
     * @var bool
     */
    private $reset = FALSE;

    /**
     * Высвобождать ли автоматически ресурсы после одного прохода по ним?
     * @var bool
     */
    private $autoFree = FALSE;

    /**
     * Освобождена ли память
     * @var bool
     */
    private $isFree = FALSE;

    /**
     * Создание объекта результата
     * @param Controller $simpleMySQLi соединение в котором конкретно был получен результат
     * @param mysqli_result $r
     * @return Result
     */
    public static function factory(Controller $simpleMySQLi, mysqli_result $r){
        if (!empty($simpleMySQLi) && !empty($r))
            return new Result($simpleMySQLi, $r);
        return null;
    }

    /**
     *
     * @param Controller $simpleMySQLi соединение в котором конкретно был получен результат
     * @param mysqli_result $r
     */
    private function __construct(Controller $simpleMySQLi, mysqli_result $r) {
        $this->result = $r;
        $this->simpleMySQLi = $simpleMySQLi;
        $this->reset = true;
    }

    public function __destruct(){
        if (!$this->isFree){
            $this->result->free();
        }
    }

    /**
     * Возвращает ссылку на ресурс результата, для ручной обработки средствами функций для работы с MySQL
     * @return Result
     */
    public function res(){
        $this->reset = false;
        return $this->result;
    }

    /**
     * Возвращает следующую строку, которая идет в результатах или null если строки кончились
     * @return mixed(array|null)
     */
    public function getRow(){
        $this->reset = false;
        $row = $this->result->fetch_assoc();
        if ($row === false){
            $this->checkFreeOrReset();
            return NULL;
        }
        return $row;
    }

    /**
     * Сдвиг указателя в результате на нужную позицию
     * @param int $num The desired row number of the new result pointer.
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function seek($num){
        if($num ==0){
            $this->reset = true;
        }
        return $this->result->data_seek($num);
    }

    /**
     * allias for seek(0)
     * @see $this->seek
     * @return void
     */
    public function reset(){
        if (!$this->reset){
            $this->seek(0);
        }
    }

    /**
     * Возвращает ячейку следующей строки, которая идет в результатах или null если строки кончились, проверить реальный конец набора результатов или просто null значение можно через второй параметр
     * @param string $name
     * @param bool $is_end
     * @throws Exceptions\InvalidArgumentException
     * @return mixed
     */
    public function getCell($name = null, &$is_end = null){
        $row = $this->getRow();
        if (!is_array($row)){
            $is_end = true;
            return FALSE;
        }
        $is_end = false;
        if (empty ($name)){
            reset($row);
            return current($row);
        }
        if (!isset ($row[$name])){
            throw new Exceptions\InvalidArgumentException('Key '.$name.' not found, possible keys:'.explode(',', array_keys($row)));
        }
        return $row[$name];
    }

    /**
     * Возвращает количество строк, которое имеется в результсете
     * @return int
     */
    public function count(){
        return $this->result->num_rows;
    }

    /**
     * В случае окончания пакетной обработки установить указатель на начало или отчистить результат
     */
    private function checkFreeOrReset(){
        if ($this->autoFree && !$this->isFree){
            $this->result->free();
            $this->isFree = TRUE;
        }else{
            $this->reset();
        }
    }

    /**
     * Применяет вызов переданной функции к каждой строке результата
     * Если применяемая функция возвращает FALSE, то обработка остальных строк прекращается
     * @example $r->map(function($row){print_f($row);});
     * @param callback $callback
     * @throws Exceptions\InvalidArgumentException
     * @return bool return TRUE on success all calls.
     */
    public function map($callback){
        if (!is_callable($callback)){
            throw new Exceptions\InvalidArgumentException('$callback is not callable');
        }
        $result = TRUE;
        $this->reset();
        while ($row = $this->getRow()){
            $result = call_user_func($callback, $row);
            if (FALSE === $result){
                break;
            }
        }
        $this->checkFreeOrReset();
        return FALSE === $result ? FALSE : TRUE;
    }

    /**
     * Позволяет сделать выбор всех строк с группировкой в многоуровневый массив
     * @example $r->select('group_id', 'id'); Получится на выходе многоуровенвый массив
     * @return array
     */
    public function select(){
        if (func_num_args()>0){
            $keys = func_get_args();
        }
        $result = array();
        $this->reset();
        if (!empty($keys)){
            while ($row = $this->getRow()){
                $current = &$result;
                foreach ($keys as $ak) {
                    $cell = $row[$ak];
                    //unset($row[$ak]);
                    $current = &$current[$cell];
                }
                $current = $row;
            }
        }else{
            while ($row = $this->getRow()){
                $result[]=$row;
            }
        }
        $this->checkFreeOrReset();
        return $result;
    }

    /**
     * Позволяет сделать выбор всех строк с группировкой в многоуровневый массив
     * @example $r->getCol(array('group_id', 'id'), 'count'); Получится на выходе многоуровенвый массив
     * @param array|string $index_key
     * @param string $value_key
     * @return array
     */
    public function getCol($index_key = NULL, $value_key){
        $result = array();
        $this->reset();
        if (is_array($index_key) && count($index_key)>1){
            $keys = $index_key;
            while ($row = $this->getRow()){
                $current = &$result;
                foreach ($keys as $ak) {
                    $cell = $row[$ak];
                    $current = &$current[$cell];
                }
                $current = $row[$value_key];
            }
        }else{
            if (is_array($index_key)){
                $index_key = end($index_key);
            }
            if (is_null($index_key)){
                while ($row = $this->getRow()){
                    $result[]=$row[$value_key];
                }
            }else{
                while ($row = $this->getRow()){
                    $result[$row[$index_key]]=$row[$value_key];
                }
            }
        }
        $this->checkFreeOrReset();
        return $result;
    }

    /**
     * Высвобождать ли автоматически ресурсы после использования?
     * @param bool $autoFree
     * @return \MysqlSimple\Result
     */
    public function setAutoFree($autoFree) {
        $this->autoFree = $autoFree;
        return $this;
    }

    /**
     * Вернуть объект mysqli_result более низкого уровня
     * @return \mysqli_result
     */
    public function getMySQLiResult(){
        return $this->result;
    }
}