<?php
namespace exceptionHandler;
/**
 * require php 5.2+
 * @package ExceptionHandler
 */
class OutputCli extends Output{

    /**
     * @var string
     */
    protected static $fileLinkFormat;

    /**
     * This setting determines the format of the filepath that are made in the
     * display of stack traces where file names are used.
     * ': in %f on line %l' (Netbeans)<br>
     * The possible format specifiers are:<br>
     * %f - the filename<br>
     * %l - the line number<br>
     *
     * @param string $fileLinkFormat
     */
    public static function setFileLinkFormat($fileLinkFormat){
        self::$fileLinkFormat = $fileLinkFormat;
    }

    /**
     * @var string
     */
    protected $_fileLinkFormat;

    /**
     * @param string $fileLinkFormat
     */
    public function  __construct($fileLinkFormat = null) {
        if(is_null($fileLinkFormat)){
            $this->_fileLinkFormat = self::$fileLinkFormat;
        } else {
            $this->_fileLinkFormat = $fileLinkFormat;
        }
    }

    /**
     * @param Exception $exception
     * @param bool $debug
     */
    public function output($exception, $debug){
        if($debug){
            exit($this->format($exception));
        } else {
            exit(self::$productionMessage);
        }
    }

    /**
     * @param string $file
     * @param int $line
     * @return string
     */
    protected function getFileLink($file, $line){
        if(is_null($file) || !strlen($this->_fileLinkFormat)){
            return parent::getFileLink($file, $line);
        }
        $fileLink = str_replace(array('%f','%l'),
                array($file,$line),
                $this->_fileLinkFormat);
        return '    '.$fileLink;
    }

    /**
     * @param Exception $exception
     * @return string
     */
    public function format($exception, $log = false){
        return $this->prepareError($exception, false, $log);
    }
}