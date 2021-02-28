<?php
namespace exceptionHandler;
/**
 * require php 5.2+
 * @package ExceptionHandler
 */
final class Controller{

    /**
     * @var bool
     */
    private static $debug = false;

    /**
     * if true ignores @-operator and generates exception
     * if false donesn't generate exceptions for errors slashed with @-operator, but logs it
     * @var bool
     */
    private static $scream = false;

    /**
     * Which type of error convert to exception
     * @var int
     */
    private static $errorTypesException = E_ALL;

    /**
     * @var bool
     */
    private static $assertionThrowException = true;

    /**
     * @var int
     */
    private static $assertionErrorType = E_USER_ERROR;

    /**
     * If seted to instanse of ExceptionHandlerLog, will log
     * If seted to null will not log
     * @var Log
     */
    private static $exceptionHandlerLog = null;

    /**
     * @var Output
     */
    private static $exceptionHandlerOutput;

    /**
     * @var array
     */
    private static $previousAssertOptions;

    /**
     * @var bool
     */
    private static $setupFlag = false;
    /**
     * Установка начальных параметров
     * @param bool $isCli //консоль или нет?
     * @param string $error_log //путь к файлам логов
     * @param bool $debug_enable //выводить ошибки или только в лог писать
     */
    public static function setup($isCli, $error_log, $debug_enable = false){
        self::$debug = $debug_enable;
        if ($isCli)
            OutputCli::setFileLinkFormat(': in %f on line %l');
        else
            OutputWeb::setFileLinkFormat('nb://?%f:%l');
        self::setupEnvironment($error_log);
        self::setupHandlers(!$isCli ? new OutputWeb() : new OutputCli());
    }
    /**
     * @param string $error_log
     */
    public static function setupEnvironment($error_log){
        self::$errorTypesException = E_ALL | E_STRICT;
        ini_set('display_errors', 'On');
        ini_set('display_startup_errors', 'On');
        ini_set('error_reporting', self::$errorTypesException);
        ini_set('html_errors', 'Off');
        ini_set('docref_root', '');
        ini_set('docref_ext', '');

        ini_set('log_errors', 'On');
        ini_set('log_errors_max_len', 0);
        ini_set('ignore_repeated_errors', 'Off');
        ini_set('ignore_repeated_source', 'Off');
        ini_set('report_memleaks', 'Off');
        ini_set('track_errors', 'On');
        ini_set('xmlrpc_errors', 'Off');
        ini_set('xmlrpc_error_number', 'Off');
        ini_set('error_prepend_string', '');
        ini_set('error_append_string', '');
        //ini_set('error_log', $error_log);
        if (!empty($error_log)){
            self::$exceptionHandlerLog = new Log($error_log, new OutputCli(': in %f on line %l'));
        }
    }
    
    /**
     * setup handlers
     *
     * @param Output $exceptionHandlerOutput
     * @param int $errorTypesHandle wich errors will be converted to exceptions
     */
    public static function setupHandlers($exceptionHandlerOutput = null, $errorTypesHandle = null){
        if(is_null($errorTypesHandle)){
            $errorTypesHandle = E_ALL | E_STRICT;
        }

        if(is_null($exceptionHandlerOutput)){
            $exceptionHandlerOutput = new OutputCli();
        }
        self::$exceptionHandlerOutput = $exceptionHandlerOutput;
        Output::$exceptionHandlerClass = __CLASS__;
        Output::setUtf();
        if(!self::$setupFlag){
            set_error_handler(__CLASS__.'::errorHandler', $errorTypesHandle);
            set_exception_handler(__CLASS__.'::exceptionHandler');
            self::$previousAssertOptions[ASSERT_ACTIVE] = assert_options(ASSERT_ACTIVE);
            self::$previousAssertOptions[ASSERT_WARNING] = assert_options(ASSERT_ACTIVE);
            self::$previousAssertOptions[ASSERT_BAIL] = assert_options(ASSERT_BAIL);
            self::$previousAssertOptions[ASSERT_QUIET_EVAL] = assert_options(ASSERT_QUIET_EVAL);
            self::$previousAssertOptions[ASSERT_CALLBACK] = assert_options(ASSERT_CALLBACK);
            assert_options(ASSERT_ACTIVE, 1);
            assert_options(ASSERT_WARNING, 0);
            assert_options(ASSERT_BAIL, 0);
            assert_options(ASSERT_QUIET_EVAL, 0);
            assert_options(ASSERT_CALLBACK, __CLASS__.'::assertionHandler');
            self::$setupFlag = true;
        }
    }

    /**
     * Restores error, exception and assertion handlers
     */
    public static function restoreHandlers(){
        if(self::$setupFlag){
            restore_error_handler();
            restore_exception_handler();
            assert_options(ASSERT_ACTIVE, self::$previousAssertOptions[ASSERT_ACTIVE]);
            assert_options(ASSERT_WARNING, self::$previousAssertOptions[ASSERT_WARNING]);
            assert_options(ASSERT_BAIL, self::$previousAssertOptions[ASSERT_BAIL]);
            assert_options(ASSERT_QUIET_EVAL, self::$previousAssertOptions[ASSERT_QUIET_EVAL]);
            assert_options(ASSERT_CALLBACK, self::$previousAssertOptions[ASSERT_CALLBACK]);
            self::$setupFlag = false;
        }
    }

    /**
     * Handles uncaught exceptions
     *
     * @param Exception $exception
     */
    public static function exceptionHandler($exception){
        self::exceptionLog($exception, Log::uncaughtException);
        self::$exceptionHandlerOutput->output($exception, self::$debug);
    }

    /**
     * Convert error to exception
     *
     * @param int $severity The severity level of the exception
     * @param string $message The Exception message to throw
     * @param string $file The filename where the exception is thrown
     * @param int $line The filename where the exception is thrown
     * @throws ErrorException
     */
    public static function errorHandler($severity, $message, $file, $line ) {
        $exception = new \ErrorException($message, 0, $severity, $file, $line);
        /**
         * don't throw exception if '@' operator used
         */
        if(error_reporting() === 0 && !self::$scream){
            self::exceptionLog($exception, Log::ignoredError);
            return;
        } else if( !($severity & self::$errorTypesException) ){
            self::exceptionLog($exception, Log::lowPriorityError);
            return;
        }else{
            self::exceptionHandler($exception);
        }
    }

    /**
     * Convert assertion fail to exception
     *
     * @param string $file The filename where the exception is thrown
     * @param int $line The filename where the exception is thrown
     * @param string $message The Exception message to throw
     * @throws ErrorException
     */
    public static function assertionHandler($file, $line, $message) {
        $exception = new ErrorException($message, 0, self::$assertionErrorType, $file, $line);
        if(!self::$assertionThrowException){
            self::exceptionLog($exception, Log::assertion);
            return;
        }
        throw $exception;
    }
    
    /**
     * @param Exception $exception
     * @param int $logPriority
     */
    public static function exceptionLog($exception, $logPriority = null){
        if(!is_null(self::$exceptionHandlerLog)){
            self::$exceptionHandlerLog->log($exception, $logPriority);
        }
    }

    /**
     * @param string $class
     * @return bool
     */
    public static function autoload($class){
        $pieces = explode('\\', $class);
        if($pieces[0] != __NAMESPACE__)
            return FALSE;
        $class = $pieces[1];
        $filePath = dirname(__FILE__).DIRECTORY_SEPARATOR.$class.'.php';
        if(!is_readable($filePath)){
            return FALSE;
        }
        require $filePath;
        return FALSE;
    }
    
    public static function autoloadRegisterate(){
        spl_autoload_register(__CLASS__.'::autoload');
    }
}

Controller::autoloadRegisterate();