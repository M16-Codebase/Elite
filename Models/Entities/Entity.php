<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 20.11.17
 * Time: 1:21
 */

namespace Models\Entities;
use Dompdf\Exception;

/**
 * Главный класс сущностей
 * Имена таблиц должны совпадать с именами классов
 * @TODO продумать все методы
 *
 * Class Entity
 * @package Models\Entities
 */
abstract class Entity  implements \ArrayAccess
{
    const ID_FIELD = 'id';

    const TABLE_MODEL_METHOD = 'tableModel';

    protected $id;
    protected $table;
    protected $db;
    protected $tableModel;

    public function __construct()
    {
        if ( null === $this->tableModel = static::tableModel() ) {
            throw new Exception('Table model must be');
            return false;
        }
        $this->db = \App\Builder::getInstance()->getDB();

        $path = explode('\\', get_called_class());
        $this->table = mb_strtolower(array_pop($path));
    }

    public function getById($id)
    {
        if (is_null($id)) {
            return false;
        }
        return $this->_select($id);
    }

    public function getAll()
    {
        return $this->_select();
    }

    protected function save()
    {
        return;
    }

    private function _select($id = null)
    {
        try {
            if (is_null($id)) {
                $sql = "SELECT * FROM {$this->table} WHERE 1";
                return $this->db->query($sql)->select();
            } else {
                if (is_numeric($id)) {
                    $ph = '?d';
                } else {
                    $ph = '?s';
                }
                $sql = "SELECT * FROM {$this->table} WHERE " . static::ID_FIELD . " = {$ph}";
                return $this->db->query($sql, $id)->getRow();
            }
        }
        catch (Exception $e) {
            throw new Exception("Some process is killed all");
        }
    }

    public function insert($data)
    {
        $data = $this->cast($data);
        return $this->_insert($data);
    }

    public function update($data, $where)
    {
        $data = $this->cast($data);
        $where = $this->cast($where);
        return $this->_update($data, $where);
    }

    private function cast($data)
    {
        $return_data = [];
        foreach ($this->tableModel as $column => $columnInfo) {
            if (array_key_exists($column, $data)) {
                if ($columnInfo['type'] === 'string') {
                    $return_data[$column] = strval($data[$column]);
                } elseif ($columnInfo['type'] === 'int') {
                    $return_data[$column] = intval($data[$column]);
                }
            }
        }
        return $return_data;
    }

    private function _update($update, $where = null)
    {
        if (!is_array($update) || empty($update)) {
            return true;
        }

        $action = 'UPDATE ' . $this->table . ' SET ';
        $values = [];

        foreach ($this->tableModel as $column => $columnInfo) {
            if (array_key_exists($column, $update)) {
                if (is_string($update[$column])) {
                    $value = "'{$update[$column]}'";
                } else {
                    $value = $update[$column];
                }
                $action .= " `{$column}` = $value , ";
                $values[] = $update[$column];
            }
        }

        $action  = substr($action,0, strrpos($action, ','));
        $where_str = "";
        if (!is_null($where) && is_array($where)) {
            foreach ($where as $key=>$value) {
                if (is_string($value)) {
                    $value = "'{$value}'";
                }
                $where_str .= " `{$key}` = $value AND";
                //$values[] = $value;
            }
            $where_str  = substr($where_str,0, strrpos($where_str, 'AND'));
        } else {
            $where_str = " 1";
        }
        $action .= " WHERE" . $where_str;
        //dump($action);exit;
        try {
            $this->db->query($action);
        } catch (Exception $e) {
            $e->getMessage();
            return false;
        }
        return true;
    }

    private function _insert($insert)
    {
        if ( null === $tableModel = static::tableModel() ) {
            throw new Exception('Table model must be');
            return false;
        }

        if (!is_array($insert) || empty($insert)) {
            return true;
        }

        $action = 'INSERT INTO ' . $this->table;
        $values = [];
        $names_part = " (";
        $values_part = " (";
        $values_ph = [];
        foreach ($tableModel as $column => $columnInfo) {
            if (array_key_exists($column, $insert)) {
                $names_part .= " `{$column}`, ";
                $values_ph[] = "?";
                if (is_string($insert[$column])) {
                    $values_part .= " '{$insert[$column]}' , ";
                } elseif (is_integer($insert[$column])) {
                    $values_part .= " {$insert[$column]} , ";
                }
                $values[] = $insert[$column];
            }
        }
        $names_part  = substr($names_part,0, strrpos($names_part, ','));
        $values_part  = substr($values_part,0, strrpos($values_part, ','));
        $names_part .= ")";
        $values_part .= ")";
        $action  .= "{$names_part} VALUES {$values_part}";

        try {
            $insert_id = $this->db->query($action);
        } catch (Exception $e) {
            $e->getMessage();
            return false;
        }
        return $insert_id;
    }

    public function offsetExists($offset)
    {

    }

    public function offsetGet($offset)
    {

    }

    public function offsetSet($offset, $value)
    {

    }

    public function offsetUnset($offset)
    {

    }

    abstract protected function tableModel();
}
