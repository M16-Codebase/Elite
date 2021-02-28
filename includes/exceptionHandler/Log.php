<?php
namespace exceptionHandler;
/**
 * require php 5.2+
 * @package ExceptionHandler
 */
class Log{
    const uncaughtException = 0;

    const caughtException = 1;

    const ignoredError = 2;

    const lowPriorityError = 3;

    const assertion = 4;

    protected $log_file = '';

    protected $formatter;

    public function __construct($error_log, OutputCli $formatter){
        $this->log_file = $error_log;
        if (empty($error_log))
            trigger_error ('Empty log file');
        if (!file_exists($this->log_file)){
            $r = fopen($this->log_file, 'w');
            if ($r === false){
                trigger_error('Can`t create log file ' . $this->log_file);
                exit('Can`t create log file ' . $this->log_file);
            }
            chmod($this->log_file, 0777);
        }
        $this->formatter = $formatter;
    }

    /**
     * @param Exception $exception
     */
    public static function getUid($exception){
        return md5(get_class($exception).$exception->getFile()
            .$exception->getLine().$exception->getCode());
    }

    /**
     * @param Exception $exception
     * @param int $logType
     */
    public function log($exception, $logType){
//        switch ($logType){
//            case self::uncaughtException:
                //error_log($formatter->format($exception));
                $error_text = "\n\n" . date(DATE_RFC850) . "\n" . $this->formatter->format($exception, true) . "\n";

                file_put_contents($this->log_file, $error_text, FILE_APPEND);
//                break;
//        }
    }
}