<?php
namespace MysqlSimple;
/**
 * proxy class for DbSimple_Mysql
 * @see DbSimple_Generic: universal database connected by DSN. (C) Dk Lab, http://en.dklab.ru
 * @author Alexander
 */
define('DBSIMPLE_SKIP', log(0));
class DbSimple_Mysql {

    /**
     * @var Controller
     */
    private $simpleMySQLi = null;
    private $DBSIMPLE_SKIP = 0;
    private $DBSIMPLE_ARRAY_KEY = 'ARRAY_KEY';
    private $DBSIMPLE_PARENT_KEY = 'PARENT_KEY';

    /**
     * @param Controller $simpleMySQLi
     */
    public function __construct(Controller $simpleMySQLi){
        $this->simpleMySQLi = $simpleMySQLi;
        $this->DBSIMPLE_SKIP = log(0);
    }

    /**
     * @param string $sql
     * @param int|array|string $placeholder
     * @param int|array|string $placeholder2
     * @param int|array|string $placeholder3
     * @return int|array
     */
    public function select($sql){
        $sql = str_replace('(?a', '(?l', $sql);

        $args = func_get_args();
        $args[0] = $sql;
        foreach ($args as &$a){
            if ($a === $this->DBSIMPLE_SKIP){
                $a = $this->simpleMySQLi->skipIt();
            }
        }
            $result = call_user_func_array(array($this->simpleMySQLi, 'query'), $args);
        if (!is_object($result)){
            return $result;
        }else{
            /* @var $result Result */
            $fields = $result->getMySQLiResult()->fetch_fields();
            $field_names = array();
            foreach ($fields as $f) {
               $field_names[] =  $f->name;
            }
            $ak = array();
            foreach ($field_names as $fieldName) {
                if (0 == strncasecmp($fieldName, $this->DBSIMPLE_ARRAY_KEY, strlen($this->DBSIMPLE_ARRAY_KEY))) {
                    $ak[] = $fieldName;
                }
            }
            natsort($ak); // sort ARRAY_KEY* using natural comparision
            $ans = call_user_func_array(array($result, 'select'), $ak);
            foreach ($ans as &$row){
                foreach ($ak as $fieldName){
                    unset($row[$fieldName]);
                }
            }

            return $ans;
        }
    }

    /**
     * @return int|array
     */
    public function query(){
        return call_user_func_array(array($this, 'select'), func_get_args());
    }

    /**
     * @return array
     */
    public function selectRow(){
        $result = call_user_func_array(array($this, 'select'), func_get_args());
        if (empty($result)){
            return array();
        }else{
            reset($result);
            return current($result);
        }
    }

    /**
     * @return int|float|string
     */
    public function selectCell(){
        $result = call_user_func_array(array($this, 'selectRow'), func_get_args());
        if (empty($result)){
            return null;
        }else{
            reset($result);
            return current($result);
        }
    }

    /**
     * Replaces the last array in a multi-dimensional array $V by its first value.
     * Used for selectCol(), when we need to transform (N+1)d resulting array
     * to Nd array (column).
     */
     protected function shrinkLastArrayDimensionCallback(&$v){
        if (!$v) return;
        reset($v);
        if (!is_array($firstCell = current($v))) {
            $v = $firstCell;
        } else {
            array_walk($v, array($this, 'shrinkLastArrayDimensionCallback'));
        }
    }

    /**
     * @return array
     */
    public function selectCol(){
        $result = call_user_func_array(array($this, 'select'), func_get_args());
        if (empty($result)){
            return array();
        }else{
            reset($result);
            $this->shrinkLastArrayDimensionCallback($result);
            $ans = $result;
            return $ans;
        }

    }
}

?>
