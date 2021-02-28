<?php
namespace exceptionHandler;
/**
 * require php 5.2+
 * @package ExceptionHandler
 */
class OutputWeb extends Output {
    const ERROR_FILE = 'templates/base/error500.html';

    /**
     * @var string
     */
    protected static $fileLinkFormat;

    /**
     * This setting determines the format of the links that are made in the
     * display of stack traces where file names are used. This allows IDEs to
     * set up a link-protocol that makes it possible to go directly to a line
     * and file by clicking on the filenames that shows in stack traces.
     * An example format might look like:
     * 'txmt://open/?file://%f&line=%l' (TextMate)
     * 'gvim://%f@%l' (gVim - with additional hack)
     * 'nb://%f:%l' (NetBeans - with additional hack)
     * The possible format specifiers are:
     * %f - the filename
     * %l - the line number
     *
     * @see https://bugs.eclipse.org/bugs/show_bug.cgi?id=305345
     * @see http://code.google.com/p/coda-protocol/
     *
     * @param string $fileLinkFormat
     */
    public static function setFileLinkFormat($fileLinkFormat){
        ini_set('xdebug.file_link_format',  self::$fileLinkFormat);
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
     * @see http://bugs.php.net/bug.php?id=50921
     * @param Exception $exception
     * @param bool $debug
     */
    public function output($exception, $debug){
        header('HTTP/1.0 500 Internal Server Error', true, 500);
        header('Status: 500 Internal Server Error', true, 500);
        if($debug){
            exit($this->format($exception));
        } else {
            if (file_exists(self::ERROR_FILE)){
                readfile(self::ERROR_FILE);
            }else{
                echo '500: Internal Server Error. Template not found.';  
            }
            exit();
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
        $fileLink = str_replace(array('%f', '%l'),
                array($file, $line),
                $this->_fileLinkFormat);
        return '    <a href="'.$fileLink.'">'.self::formatString(parent::getFileLink($file, $line)).'</a>';
    }

    /**
     * @param Exception $exception
     * @return string
     */
    public function format($exception){
        return $this->prepareError($exception, true);
    }
}