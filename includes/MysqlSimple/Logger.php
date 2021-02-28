<?php
namespace MysqlSimple;
/**
 * 
 */
class Logger implements LoggerInterface{
    /**
     * Singeltone
     * @var Logger
     */
    protected static $i = NULL;
    
    /**
     * Маркер всего что относится к выводу в файл
     */
    const USE2FILE = 2;
    /**
     * Маркер всего что относится к выводу в stdout
     */
    const USE2PRINT = 4;
    /**
     * USE2FILE|USE2PRINT
     */
    const USE_ANY = 6;
    /**
     * Массив плейсхолдеров, которые можно использовать
     * @var array
     */
    protected $placeholders = array('%sql%', '%start_time%', '%start_time_formatted%', '%time%', '%comment%', '%ans%', '%sql_source%', '%values%', '%file%', '%line%');
    
    /**
     * Маска для вывода в stdout
     * @var string
     */
    protected $out_mask = '';
    
    /**
     * Маска для вывода в файл
     * @var string
     */
    protected $file_out_mask = NULL;
    
    /**
     * Путь к лог файлу
     * @var string 
     */
    protected $file_path = '';
    
    /**
     * Флаг выводить ли в файл
     * @var bool 
     */
    protected $log2file = FALSE;
    
    /**
     * Флаг выводить ли в stdout
     * @var bool 
     */
    protected $log2print = TRUE;
    
    /**
     * Массив функций, которые форматируют данные перед выводом, сгруппирован по 2 направлениям
     * @var array[] 
     */
    protected $formaters = array(
        self::USE2PRINT => array(),
        self::USE2FILE => array(),
    );
    
    /**
     * Фильтры позволяют поставить ограничение на логирование
     * @var callable[] 
     */
    protected $filters = array(
        self::USE2PRINT => NULL,
        self::USE2FILE => NULL,
    );


    /**
     * возвращает экземпляр
	 * @return Logger
	 */
    public static function factory(){
        if (empty (self::$i)){
            self::$i = new static();
        }
        return self::$i;
    }
    
    /**
     * конструктор
     */
    protected function __construct() {
        $this->addFormatter('ans', function ($v){return '';});
        $this->addFormatter('values', function ($v){return print_r($v, true);});
        $this->setMask("\ntime:%start_time%---------------------------------\n%sql%\n%comment%\n-- %time% s\n    source----------------\n%sql_source%", self::USE2FILE);
        $this->setMask("<pre>%sql%\n%comment%\n-- %time% ms\n   %start_time_formatted%\n\n</pre>", self::USE2PRINT);
    }

    /**
     * @see LoggerInterface
     * @param string $sql
     * @param float $start_time
     * @param float $time
     * @param string $comment
     * @param mixed $ans
     * @param string $sql_source
     * @param mixed $values
     */
    public function log($sql, $start_time, $time, $comment, $ans, $sql_source, $values){
        $data = array(
            'sql' => $sql,
            'start_time' => $start_time,
            'start_time_formatted' => date('H:i:s', $start_time) . '.' . substr($start_time - floor($start_time), 2),
            'time' => $time,
            'comment' => $comment,
            'ans' => '$ans',
            'sql_source' => $sql_source,
            'values' => $values
        );
        if ($this->log2file and !empty($this->file_path)){
            $this->fileLog($data);
        }
        if ($this->log2print){
            $this->output($data);
        }
    }
    
    /**
     * Обеспечивает вывод в stdout
     * @param array $data
     */
    protected function output($data){
        if (!empty($this->filters[self::USE2PRINT])){
            $filter = $this->filters[self::USE2PRINT];
            if (!$filter($data, $this)){
                return;
            }
        }
        foreach ($data as $placeholder => &$value) {
            if (isset($this->formaters[self::USE2PRINT][$placeholder])){
                $formatter = $this->formaters[self::USE2PRINT][$placeholder];
                $value = $formatter($value);
            }
        }
        echo str_replace($this->placeholders, $data, $this->out_mask);
    }
    
    /**
     * Обеспечивает печать в файл
     * @param array $data
     */
    protected function fileLog($data){
        if (!empty($this->filters[self::USE2FILE])){
            $filter = $this->filters[self::USE2FILE];
            if (!$filter($data, $this)){
                return;
            }
        }
        foreach ($data as $placeholder => &$value) {
            if (isset($this->formaters[self::USE2FILE][$placeholder])){
                $formatter = $this->formaters[self::USE2FILE][$placeholder];
                $value = $formatter($value);
            }
        }
        if ($this->log2file and !empty($this->file_path)){
            $data = str_replace($this->placeholders, $data, $this->out_mask);
            file_put_contents($this->file_path, $data, FILE_APPEND);
        }
    }
    
    /**
     * Устанавливает файл, в который делать лог запросов
     * @param string $file
     */
    public function setLogFile($file = NULL){
        $this->file_path = strval($file);
    }

    /**
     * включать или выключать печать в файл
     * @param bool $enable
     */
    public function log2File($enable = TRUE){
        $this->log2file = $enable ? TRUE : FALSE;
    }
    
    /**
     * включать или выключать печать в stdout
     * @param bool $enable
     */
    public function log2print($enable = TRUE){
        $this->log2print = $enable ? TRUE : FALSE;
    }
    
    /**
     * Устанавливает форматтер, позволяет тонко настроить вывод
     * @staticvar array $allowed_placeholders
     * @param string $placeholder
     * @param callback $formatter функция одного параметра, должна возвращать строку
     * @param int $use
     * @throws \InvalidArgumentException
     */
    public function addFormatter($placeholder, $formatter, $use = self::USE_ANY) {
        static $allowed_placeholders = array();
        if (empty($allowed_placeholders)){
            $allowed_placeholders = array_map(function($v){return trim($v, '%');}, $this->placeholders);
        }
        if (!is_callable($formatter)){
            throw new \InvalidArgumentException('$formatter is not callback');
        }
        if ($use & self::USE2PRINT){
            $this->formaters[self::USE2PRINT][$placeholder] = $formatter;
        }
        if ($use & self::USE2FILE){
            $this->formaters[self::USE2FILE][$placeholder] = $formatter;
        }
    }
    
    /**
     * Позволяет задавать маски вывода
     * @param string $mask
     * @param int $use
     */
    public function setMask($mask, $use = self::USE_ANY){
        if ($use & self::USE2PRINT){
            $this->out_mask = $mask;
        }
        if ($use & self::USE2FILE){
            $this->file_out_mask = $mask;
        }
    }
    
    /**
     * Позволяет задавать фильтры для того чтобы не логировать все подряд
     * @param callback $filter функция от двух аргументов, в первом ассоативный массив данных для логирования, в втором ссылка на этот логер, возвращает bool логировать/не логировать
     * @param int $use
     */
    public function setFilter($filter, $use = self::USE_ANY){
        if ($use & self::USE2PRINT){
            $this->filters[self::USE2PRINT] = $filter;
        }
        if ($use & self::USE2FILE){
           $this->filters[self::USE2FILE] = $filter;
        }
    }
}
