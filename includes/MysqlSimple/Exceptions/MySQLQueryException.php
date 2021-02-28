<?php
namespace MysqlSimple\Exceptions;
/**
 * Description of ConnectionException
 *
 * @author Alexander
 */
class MySQLQueryException extends Exception{
    protected $query;
    protected $sql_source;
    protected $data_values;

    /**
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     * @param string $query
     * @param string $sql_source
     * @param array $data_values
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null, $query = '', $sql_source = '', array $data_values = array()){
        parent::__construct($message, $code, $previous);
        $this->query = $query;
        $this->sql_source = $sql_source;
        $this->data_values = $data_values;
    }
    public function getQuery() {
        return $this->query;
    }

    public function getSql_source() {
        return $this->sql_source;
    }

    public function getData_values() {
        return $this->data_values;
    }
}
