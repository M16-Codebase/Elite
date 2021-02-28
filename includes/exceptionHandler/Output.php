<?php
namespace exceptionHandler;
/**
 * require php 5.2+
 * @package ExceptionHandler
 */
abstract class Output{
    /**
     * @var string
     */
    public static $exceptionHandlerClass;

    /**
     * @var string
     */
    public static $productionMessage = 'Internal Server Error';

    /**
     * @var int
     */
    public static $maxchars = 40;

    /**
     * @var bool
     */
    protected static $utf = false;

    /**
     * @param int $severity
     */
    public static function severityToString($severity) {
        switch ($severity) {
            case 1: return 'E_ERROR';
                break;
            case 2: return 'E_WARNING';
                break;
            case 4: return 'E_PARSE';
                break;
            case 8: return 'E_NOTICE';
                break;
            case 16: return 'E_CORE_ERROR';
                break;
            case 32: return 'E_CORE_WARNING';
                break;
            case 64: return 'E_COMPILE_ERROR';
                break;
            case 128: return 'E_COMPILE_WARNING';
                break;
            case 256: return 'E_USER_ERROR';
                break;
            case 512: return 'E_USER_WARNING';
                break;
            case 1024: return 'E_USER_NOTICE';
                break;
            case 2048: return 'E_STRICT';
                break;
            case 4096: return 'E_RECOVERABLE_ERROR';
                break;
            case 8192: return 'E_DEPRECATED';
                break;
            case 16384: return 'E_USER_DEPRECATED';
                break;
            case 30719: return 'E_ALL';
                break;
        }
    }

    /**
     * @return bool
     */
    public static function setUtf(){
        if(extension_loaded('mbstring')){
            Output::$utf = (ini_get('mbstring.internal_encoding') == 'UTF-8');
        }
        return Output::$utf;
    }
    
    /**
     * @param Exception $exception
     * @return string
     */
    protected function prepareError($exception, $html, $log = false){
        $aTrace = self::getTrace($exception);
        $sTrace = '['.get_class($exception).']: ';
        $sTrace .= self::getMessage($exception)."\n";
        if($html){
            if(self::$utf){
                $sTrace = htmlspecialchars($sTrace, ENT_COMPAT, 'UTF-8');
            } else {
                $sTrace = htmlspecialchars($sTrace);
            }
        }
        $prevArg = null;
        if ($log)
            $sTrace .= "\t: ".base64_encode (serialize(array('server' => $_SERVER, 'cookie' => $_COOKIE, 'session' => isset($_SESSION) ? $_SESSION : array(), 'post' => $_POST, 'get' => $_GET, 'file' => $_FILES))) . "\n";
        foreach ($aTrace as $aTraceNo => $aTraceLine){
            $sTraceLine = '';
            $sTraceLine .= '#'.$aTraceNo.': ';
            if(array_key_exists('class', $aTraceLine)){
                $sTraceLine .= $aTraceLine['class'].$aTraceLine['type'];
            }
            if(array_key_exists('function', $aTraceLine)){
                $sTraceLine .= $aTraceLine['function'];
                if (!empty($aTraceLine['args'])){
                    $sTraceLine .= self::formatArguments($aTraceLine['args'], 'string');
                }
            }
            if($html){
                if(self::$utf){
                    $sTraceLine = htmlspecialchars($sTraceLine, ENT_COMPAT, 'UTF-8');
                } else {
                    $sTraceLine = htmlspecialchars($sTraceLine);
                }
            }
            if(array_key_exists('file', $aTraceLine)){
                $sTraceLine .= "\n".  $this->getFileLink(
                    $aTraceLine['file'], $aTraceLine['line']);
            } else {
                $sTraceLine .= "\n".$this->getFileLink(null, null);
            }
            if ($log && !empty($aTraceLine['args'])){
                $data4pack = self::formatArguments($aTraceLine['args'], 'array');
                //var_dump($data4pack);
                $sTraceLine .= "\n\t: ".base64_encode (serialize($data4pack));
            }
            $sTrace .= $sTraceLine. "\n";
        }
        if($html){
            $sTrace = '<pre>'.$sTrace.'</pre>';
        }
        return $sTrace;
    }

    /**
     * @param Exception $exception
     */
    abstract public function format($exception);

    /**
     * @param Exception $exception
     */
    public static function getMessage($exception){
        $result = '';
        if($exception instanceof ErrorException){
            $result = self::severityToString($exception->getSeverity());
            if(strlen($exception->getMessage())){
                $result .= ' - ';
            }
        }
        $result .= $exception->getMessage();
        return $result;
    }

    /**
     * @param Exception $exception
     * @return array
     */
    public static function getTrace($exception) {
        $backtrace = $exception->getTrace();
        $count = count($backtrace);
        /**
         * bug
         * @see http://www.php.net/manual/en/class.errorexception.php#86985
         */
        if(strpos(phpversion(), '5.2') === 0 && $exception instanceof ErrorException){
            for ($i = $count - 1; $i > 0; --$i) {
                $backtrace[$i]['args'] = $backtrace[$i - 1]['args'];
            }
            $backtrace[0]['args'] = null;
        }
        $result = array();
        for ($i = 0; $i < $count; $i++) {
            if(array_key_exists('file', $backtrace[$i]) && dirname($backtrace[$i]['file']) == dirname(__FILE__)){
                continue;
            }
            $result[] = $backtrace[$i];
        }
        return $result;
    }

    /**
     * @param string $str
     * @return string
     */
    public static function formatString($str){
        if(self::stringLength($str) > self::$maxchars){
            if(self::$utf){
                $hellip = '…';
                $str = trim(mb_substr($str, 0, self::$maxchars/2)).$hellip.trim(mb_substr($str, -self::$maxchars/2));
            } else {
                $hellip = '...';
                $str = substr($str, 0, self::$maxchars/2).$hellip.substr($str, -self::$maxchars/2);
            }
        }
        return $str;
    }

    /**
     * @param string $str
     * @return int
     */
    public static function stringLength($str){
        if(self::$utf){
            $strlen = mb_strlen($str);
        } else {
            $strlen = strlen($str);
        }
        return $strlen;
    }

    /**
     * @param array $args
     * @return string
     */
    /*public static function argumentsToString($args) {
        if (!is_null($args)) {
            foreach ($args as $iArg => $arg) {
                $args[$iArg] = self::argToString($arg);
            }
            return '(' . implode(', ', $args) . ')';
        }
        return '()';
    }*/

    /**
     * @param string $file
     * @param int $line
     * @return string
     */
    protected function getFileLink($file, $line){
        if (is_null($file)) {
            return 'unknown file';
        }
        return $file.':'.$line;
    }
    /**
     *
     * @param array $args
     * @param string $type return type string|array
     * @return string|array 
     */
    public static function formatArguments($args, $type='string') {
        if (!is_null($args)) {
            //$args_array = array();
            foreach ($args as $iArg => $arg) {
                $f_args[$iArg] = self::argFormat($arg, $type);
                //$args_array[$iArg] = self::argFormat($arg, 'array');
            }
            return $type == 'string' ? ('(' . implode(', ', $f_args) . ')') : $f_args;
        }
        return $type == 'string' ? '()' : array();
    }
    /**
     * 
     * @param mixed $arg что требуется запаковать
     * @param string $type как паковать string|array
     * @return string|array в зависимости от $type
     */
    public static function argFormat($arg, $type){
        switch (gettype($arg)){
            case 'boolean':
                $arg = $arg ? 'true' : 'false';
                break;
            case 'NULL':
                $arg = 'null';
                break;
            case 'integer':
            case 'double':
            case 'float':
                $arg = (string) $arg;
                if(self::$utf){
                    $arg = str_replace('INF', '∞', $arg); //is_infinite($arg)
                }
                break;
            case 'string':
                if(is_callable($arg, false, $callable_name)){
                    $arg = 'fs:'.$callable_name;
                } else if( class_exists($arg, false) ){
                    $arg = 'c:'.$arg;
                } else if( interface_exists($arg, false) ){
                    $arg = 'i:'.$arg;
                } else {
                    $strlen = self::stringLength($arg);
                    if ($type == 'string')
                        $arg = self::formatString($arg);
                    if($strlen <= self::$maxchars){
                        $arg = '"'.$arg.'"';
                    } else {
                        $arg = '"'.$arg.'"('.$strlen.')';
                    }
                    return $arg = str_replace("\n", '\n', $arg);
                }
                break;
            case 'array':
                if(is_callable($arg, false, $callable_name)){
                    $arg = 'fa:'.$callable_name;
                } elseif ($type == 'string') {
                    $arg = 'array('.count($arg).')';
                } else {
                    foreach ($arg as $k => $v){
                        $arg[$k] = self::argFormat($v, 'string');
                    }
                }
                break;
            case 'object':
                if ($type == 'string')
                    $arg = get_class($arg).'()';//.':'.spl_object_hash($arg);
                else
                    $arg = self::formatObject($arg);
                break;
            case 'resource':
                // @see http://php.net/manual/en/resource.php
                $arg = 'r:'.get_resource_type($arg);
                break;
            default:
                $arg = 'unknown type';
                break;
        }
        return $arg;
    }
    
    public static function formatObject($obj){
        $obj_data = array();
        $reflector = new \ReflectionObject($obj);
        $obj_data['class_name'] = get_class($obj);
        $properties = $reflector->getProperties();
        foreach ($properties as $prop){
            $prop->setAccessible(true);
            $obj_data['properties'][$prop->getName()] = self::argFormat($prop->getValue($obj), 'string');
        }
        return $obj_data;
    }
    /**
     * @param Exception $exception
     * @param bool $debug
     */
    abstract public function output($exception, $debug);
}