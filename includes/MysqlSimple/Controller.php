<?php
namespace MysqlSimple;
use mysqli_result;
use mysqli;

/**
 * Класс для работы с MySQL
 *
 * @author Alexander
 */
class Controller{
    const SKIP = '/*SKIP=s8d39i@e%5nw($)w=SKIP*/';
    /**
     * @var Controller[]
     */
    static private $connects = array();

    // приводит флоат к нужному формату
    protected $fpp = 4; //float placeholder precition
    protected $all_work_time = 0;

    /**
     * @param string $str
     * @return string
     */
    static protected function parseDNS($str){
        $str_orig = $str;
        $ms = NULL;
        $sql = array();
        if (preg_match('~\?(.*)$~im', $str, $ms)){
            $sql = explode(';', $ms[1]);
            $str = str_replace($ms[0], '', $str);
        }
        $parsed = parse_url($str);
        if (!$parsed){
            return NULL;
        }
        if (!empty($str)) {
            foreach ($sql as $s){
                if (trim($s)!=''){
                    $parsed['sql'][] = $s;
                }
            }
        }
        $parsed['dbname'] = trim($parsed['path'],'/\\ ');
        unset($parsed['path']);
        $parsed['dns'] = $str_orig;
        return $parsed;
    }

    /**
     * Подключение к базе по строке
     * @param string $dns
     * @param $dns
     * @throws Exceptions\RuntimeException
     * @return Controller
     */
    public static function factory($dns){
        if (!class_exists('mysqli', false)) {
            throw new Exceptions\RuntimeException('MySQLi extension is not loaded (mysqli class not found!)');
        }
        if (!isset (self::$connects[$dns])){
            self::$connects[$dns] = new Controller($dns);
        }
        return self::$connects[$dns];
    }

    /**
     * Строка подключения
     * @var string
     */
    private $connect_string = '';
    /**
     * @var \mysqli
     */
    private $mysqli = NULL;
    /**
     * объект, который будет использоваться каждый раз при логировании
     * @var LoggerInterface
     */
    private $logger = NULL;
    /**
     *
     * @param LoggerInterface $logger
     * @return LoggerInterface логгер, который был установлен ранее
     */
    public function setLogger(LoggerInterface $logger){
        $old = $this->logger;
        $this->logger = $logger;
        return $old;
    }
    /**
     *
     * @param LoggerInterface $logger
     * @return LoggerInterface логгер, который был установлен ранее
     */
    public function unsetLogger(){
        $this->logger = NULL;
    }

    /**
     * @todo Сделать ленивые подключения (не подключаться до первого запроса)
     * @throws Exceptions\ConnectionException
     * @param string $connect_string format: mysql://user:password@host[:port]/dataBaseName?sql_query1;sql_query2
     */
    protected function __construct($connect_string){
        $this->db_error_reset();
        $this->connect_string = $connect_string;
        $params = self::parseDNS($connect_string);
        $this->mysqli = new mysqli(
            $params['host'],
            $params['user'],
            $params['pass'],
            $params['dbname'],
            (empty($params['port'])? ini_get("mysqli.default_port") : $params['port'])
        );
        $charset = \LPS\Config::getParametr('site','charset'); /*@TODO убрать связь*/
        if ($charset=='utf-8'){
            $charset='utf8';
        }
        $this->mysqli->query('SET NAMES "'. $charset .'"');
        $this->mysqli->query('SET MODE ""');
        if ($this->mysqli->connect_errno){
            throw new Exceptions\ConnectionException('mysqli '.$this->mysqli->connect_errno.'', $this->mysqli->connect_error);
        }
        if (!empty($params['sql'])){
            foreach ($params['sql'] as $sql) {
                $this->query($sql);
            }
        }
    }

    /**
     * Возвращает строку подключения
     * @return string
     */
    public function getConnectString(){
        return $this->connect_string;
    }

    public function beginTransaction(){
        $this->mysqli->begin_transaction();
    }

    public function commitTransaction(){
        $this->mysqli->commit();
    }

    public function rollbackTransaction(){
        $this->mysqli->rollback();
    }

    /*********************   Обработка ошибок    ******************************/
    /**
     * Информация о последнем сообщении об ошибке при работе с БД
     * @var array keys: code - error code, error - error message, sql - sql code
     */
    private $last_error_info = null;
    /**
     * Сброс внутренней ошибки
     */
    protected function db_error_reset(){
        $this->last_error_info = null;
    }

    /**
     * Ошибка при работе с базой данных
     * @param string $query
     * @param string $sql_source
     * @param array $data_values
     * @throws Exceptions\MySQLQueryException
     */
    protected function db_error($query, $sql_source = NULL, $data_values = NULL){
        $this->last_error_info = array(
            'code' => $this->mysqli->errno,
            'error' => $this->mysqli->error,
            'sql' => $query
        );
        throw new Exceptions\MySQLQueryException($this->last_error_info['error'], $this->mysqli->errno, NULL, $query, $sql_source, $data_values);
    }

    /*********************   Функции экранирования    ******************************/
    /**
     * Валидирует значения, переданные в качестве идентификаторов. Например: столбцы, таблицы, базы и проч.
     * placeholder: ?# or keys of ?a
     * @param string $var
     * @param $var
     * @throws Exceptions\InvalidArgumentException
     * @return string
     */
    protected function escape_name($var){
        $reg = '(?>[a-z]+[0-9a-z\-_]*)(?>\.[a-z]+[0-9a-z\-_]*)*';
        if (!is_string($var) || !preg_match('~^'.$reg.'$~i', $var)){
            $msg = 'Incorrect values for identificator placeholder (?# or keys of ?a), mask:' . $reg . ' var:' . $var;
            throw new Exceptions\InvalidArgumentException($msg);
        }
        return '`'.str_replace('.', '`.`', $var).'`';
    }

    /**
     * Валидирует значения, переданные в качестве строк.
     * using im placeholder: ?, values of ?a, ?l
     * @param string|int $var
     * @throws Exceptions\InvalidArgumentException
     * @return string|int
     */
    function escape_value($var){
        if (is_null($var)){
            return 'NULL';
        }elseif (is_array($var)){
            $msg = 'Incorrect value: value is array';
            throw new Exceptions\InvalidArgumentException($msg);
        }elseif (is_int($var) || is_numeric($var) && intval($var) == $var){
            return intval($var);
        }elseif (is_float($var)) {
            return $this->escape_float($var);
        } else {
            $tmp_var = str_replace(',', '.', $var);
            if (is_numeric($tmp_var) && floatval($tmp_var) == $tmp_var) {
                return $this->escape_float($var);
            } else {
                return '"'.$this->mysqli->real_escape_string($var).'"';
            }
        }
    }

    /**
     * Валидирует значения, переданные в качестве числа.
     * using im placeholder: ?f
     * @param mixed $var
     * @param null $precision
     * @throws Exceptions\InvalidArgumentException
     * @return float
     */
    function escape_float($var, $precision = NULL){
        if (is_null($var)){
            return 'NULL';
        }elseif (is_array($var)){
            $msg = 'Placeholder ?f. Incorrect value: value is array.';
            throw new Exceptions\InvalidArgumentException($msg);
        }
        if (empty($precision)){
            $precision = $this->fpp;
        }
        $var = floatval(str_replace(',', '.', $var));
        return str_replace(',', '.', $precision ? round($var, $precision) : $var);
    }

    /**
     * Валидирует значения, переданные в качестве числа.
     * using im placeholder: ?d
     * @param mixed $var
     * @throws Exceptions\InvalidArgumentException
     * @return int
     */
    function escape_int($var){
        if (is_null($var)){
            return 'NULL';
        }elseif (is_array($var)){
            $msg = 'Placeholder ?d. Incorrect value: value is array.';
            throw new Exceptions\InvalidArgumentException($msg);
        }else{
            $var = intval($var);
            if (strlen($var.'') > strlen(PHP_INT_MAX.'')){
                $var = $var.'';
                if (!preg_match('~^[0-9]+$~', $var)){
                    $msg = 'Placeholder ?d. Incorrect value: value is not int('.$var.')';
                    throw new Exceptions\InvalidArgumentException($msg);
                }
                return $var;
            }else{
                return intval($var);
            }
        }
    }
    /**
     * значения для плейсхолдеров
     * @var array
     */
    private $values = array();
    /**
     * количество найденных плайсхолдеров
     * @var int
     */
    private $ps_counter = 0;
    /**
     * Пропускать ли текущий условный блок
     * @var bool
     */
    private $have_skip  = false;
    /**
     * счетчик глубины рекурсии
     * @var int
     */
    private $deep  = 0;

    /**
     * Получает плайсхолдер или не разбираемый кусочек SQL и возвращающет результат для подстановки
     * @param array $matches
     * @throws Exceptions\InvalidArgumentException
     * @return string
     */
    private function compile_tocken($matches){
        $sqlTocken = $matches[0];
        $innerSqlTocken = isset($matches[1]) ? $matches[1] : null;
        switch ($sqlTocken{0}) {
            case '"': // строковая константа, они выделяются только для того чтобы в них не было поиска плайсхолдера
            case "'":
            case '`': // константа имени, они выделяются только для того чтобы в них не было поиска плайсхолдера
            case "-": // комментарий, они выделяются только для того чтобы в них не было поиска плайсхолдера
            case "#":
            case "/":
                return $sqlTocken;
            case "?": // плайсхолдер
                $this->ps_counter++;
                if (!count($this->values)){
                    $msg = 'Have not value for placeholder near: '."\n".$sqlTocken{0};
                    throw new Exceptions\InvalidArgumentException($msg);
                }
                //Забрать значение из стека подстановки
                $var = array_pop($this->values);
                if ($var === self::SKIP) {
                    $this->have_skip = true;
                    return '/* SQL_TOKEN_SKIP */';
                }

                if ($this->have_skip && $this->deep){ //если это не самый верхний уровень блока и уже принято решение его о пропуске, то смысла рассчитывать плайсхолдеры нет, достаточно того что их изъяли из стека
                    return '/* PARENT_BLOCK_SKIPED */';
                }
                $placeHolderType = strlen($sqlTocken)>1 ?  '?'.$sqlTocken{1} : '?';
                switch ($placeHolderType) {
                    case '?':
                    case '?s':
                        return $this->escape_value($var);
                    case '?d':
                        return $this->escape_int($var);
                    case '?#':
                        return $this->escape_name($var);
                    case '?f':
                        return $this->escape_float($var);
                    case '?l':
                        if (!is_array($var)){
                            $msg = 'Incorrect values for list ("?l") placeholder. Data is not array';
                            throw new Exceptions\InvalidArgumentException($msg);
                        }else{
                            return implode(', ', array_map(array($this, 'escape_value'), $var));
                        }
                    case '?i':
                    if (!is_array($var)){
                        $msg = 'Incorrect values for int list ("?i") placeholder. Data is not array';
                        throw new Exceptions\InvalidArgumentException($msg);
                    }else{
                        return implode(', ', array_map(array($this, 'escape_int'), $var));
                    }
                    case '?a':
                        if (!is_array($var)){
                            $msg = 'Incorrect values for array ("?a") placeholder. Data is not assoative array';
                            throw new Exceptions\InvalidArgumentException($msg);
                        }elseif (array_key_exists(0, $var)){
                            $msg = 'Incorrect values for array ("?a") placeholder. Data is list, but not assoative array. Use ?l placeholder.';
                            throw new Exceptions\InvalidArgumentException($msg);
                        }else{
                            $ans = array();
                            foreach ($var as $k => $v) {
                                $ans[] = $this->escape_name($k) .' = '. $this->escape_value($v);
                            }
                            return implode(', ', $ans);
                        }
                    default:
                        return 'ERROR:UNKNOWN_PLACEHOLDER';
                }
            case '{':
                $have_skip_save  = $this->have_skip;  //будет рекурсивный вызов и нужно сохранить значение, чтобы его потом восстановить
                /* сначала подумал: зачем компилировать внутренние значения, если и так понятно что весь текущий блок должен быть пропущен (верно только для не самого верхнего уровня)
                 * но потом оказалось, что иначе никак не посчитать сколько внутрях плайсхолдеров, а значит нужное количество их не выкинуть из стека обработки.
                 * Поэтому приходится спускаться чтобы обойти все вложенные плайсхолдеры
                 */
                $this->deep++;
                $option_block = $this->sql_block_compile($innerSqlTocken);
                $this->deep--;
                if ($this->have_skip){
                   $option_block = '/* SKIPED */';
                   // $option_block = '/* SKIPED: '.str_replace('*/', '* /', $sqlTocken).' */';
                }
                $this->have_skip = $have_skip_save;
                return $option_block;
            default:
                return 'ERROR:UNKNOWN_TOCKEN';
        }
    }

    /**
     * Функция производит подстановку плайсхолдеров в SQL
     * @param string $sql
     * @param array $values
     * @return string
     */
    protected function sql_compile($sql, $values){
        // все работает по принципу стека подсткановки. инициализация стека.
        $this->ps_counter = 0;
        $this->values = array_reverse($values); //(http://kb.ucla.edu/articles/performance-of-array_shift-and-array_pop-in-php)
        $this->deep = 0;
        $this->have_skip = false;
        return $this->sql_block_compile($sql);
    }

    /**
     * Реализация поиска и вызов функции подстканови в нужном порядке
     * @param string $sql
     * @return string
     */
    protected function sql_block_compile($sql){
        if (empty($sql)){
            return $sql;
        }
        // эта маска ищет плайсхолдеры, строковые константы и все виды SQL коментариев
        // строковые константы и коментарии ищутся только для того чтобы внутри их не учитывать плайсхолдеры,
        // обработка фигурных скобок оформлена с рекурсивным вызовом, чтобы корректно соблюсти вложенность и последовательность плейсхолдеров.
        $reg_mask = '(?>(?>\#|--)[^\n\r]*) |        # one line comments
                     (?>/\*(?>[^*]|\*[^/])*\*/) |   # multiline comments
                     (?>                            # string const
                        \'(?>\\\\.|[^\'\\\\])*\'|       # in quotes
                        "(?>\\\\.|[^"\\\\])*" |         # in double quotes
                        `(?>[^`]+ | ``)*`               # in backticks (for table names)
                      )|
                     (?>\{((?>[^{}]+|(?R))*)\}) |   # optional blocks \1
                     (?>\?[#adfils]?)                # placeholders';
        $sql = preg_replace_callback(
            '~'.$reg_mask.'~ismx',  //Пояснения к модификаторам: s - нужно чтобы точка соответствовала переносу строк, например в строковых константах, x - игнорирование пробелов в регулярке
            array($this, 'compile_tocken'),
            $sql
        );
        return $sql;
    }

    /**
     * Простая подстановка плайсхолдеров исходя из типа передаваемых переменных
     * @param string $sql
     * @param array $values
     * @param bool $strict
     * @throws Exceptions\InvalidArgumentException
     * @return string
     */
    protected function simple_sql_compile($sql, $values, $strict = TRUE){
        $sql  = str_replace(array('%','?'), array('%%','%s'), $sql);
        foreach ($values as $i => $var) {
            if (is_array($var)){
                if ($strict){
                    $msg = 'Incorrect value: value is array (use unstrict mode)';
                    throw new Exceptions\InvalidArgumentException($msg);
                    //$values[$i] =  'NULL /*'.$msg.'*/';
                    //continue;
                }
                $ans = array();
                if (isset($var[0])){ //проверка того, что массив является списком
                    //if ($var === array_values($var)){ //проверка того, что массив является списком
                    foreach ($var as $v) {
                        $ans[] = $this->escape_value($v);
                    }
                    $values[$i] = implode(', ', $ans);
                }else{
                    foreach ($var as $k => $v) {
                        $ans[] = $this->escape_name($k) .' = '. $this->escape_value($v);
                    }
                    $values[$i] = implode(', ', $ans);
                }
            }else{
                $values[$i] = $this->escape_value($var);
            }
        }
        $values[] = 'UNSET_PLACEHOLDER_VALUE';
        array_unshift($values, $sql);
        return call_user_func_array('sprintf', $values);
    }

    /**
     * Placeholders list:<ul>
     * <li><b>?s</b> string value
     * <li><b>?d</b> int value
     * <li><b>?f</b> float value (any delimiter: . or ,)
     * <li><b>?l</b> list of strings. Use example: `f` IN (?l)
     * <li><b>?i</b> list of int
     * <li><b>?a</b> key-value parts of string. Use example: SET ?a
     * </ul>
     * { } - conditional blocks be escaped, if one of inner placeholder have value as $this->skipIt();
     * @param string $sql
     * @return Result|int|FALSE
     */
    function query($sql){
        $start_time = microtime(1);
        if (empty($sql)){
           return FALSE;
        }
        $sql_source = $sql;
        $values = func_get_args();
        if (count($values)> 1){
            array_shift($values);
        }else{
            $values = array();
        }
//        $proccess = 'none';
        if (strpos($sql, '?') !== false){ //если используется хоть один плейсхолдер
            $tokens = array('"','\'','/*','--','#','?s','?d','?f','?a','?l','?#', '?i');
            //array_map('preg_quote', $tokens);
            //foreach(array('"','\'','/*','--','#','?d','?f','?a','?l','?#'))
            if (preg_match('~'.  implode('|', array_map('preg_quote', $tokens)).'~im', $sql, $matches)
            ){
                $sql   = $this->sql_compile($sql, $values);
//                $proccess = 'hard';
            }else{
                $sql  = $this->simple_sql_compile($sql, $values);
//                $proccess = 'simple';
            }
        }
        return $this->getAns($sql, $sql_source, $start_time, $values);
    }
    /**
     * Запрос без плейсходеров (когда они нам не нужны, тогда и не надо обрабатывать регулярками)
     * Не забываем прогонять нужные значения через $this->escape_value
     * @param string $sql
     * @return bool|int|Result
     */
    public function nakedQuery($sql){
        if (empty($sql)){
           return FALSE;
        }
        return $this->getAns($sql, $sql, microtime(1), array());
    }
    /**
     * Выполняем запрос, получаем результат
     * @param string $sql
     * @param string $sql_source исходный sql, в процессе мог меняться
     * @param string $start_time засели время начала парсинга sql запроса
     * @param array $values переменные плейсхолдеров (для полноты логов)
     */
    private function getAns($sql, $sql_source, $start_time, $values){
        $this->db_error_reset();
        $start_sql = microtime(1);
        $result = $this->mysqli->query($sql);
        $commentsStack = array();
        if ($result === FALSE){
            $this->db_error($sql, $sql_source, $values);
            $commentsStack[] = '# Query is failed';
            $ans = false;
        }elseif (is_object ($result) && $result instanceof mysqli_result) {
            $ans = Result::factory($this, $result);
            $commentsStack[] = '# Returned '.$ans->count().' row(s)';
        }else{
            if (preg_match('~^(?>
                (?>\s+) |  # spaces
                (?>(\#|--)[^\n\r]*) |        # one line comments
                (?>/\*(?>[^*]|\*[^/])*\*/)  # multiline comments
              )*INSERT \s+~six', $sql)
            ){// INSERT queries return generated ID
                $ans = $this->mysqli->insert_id;
                $commentsStack[] = '# Auto increment start at "'.$ans.'"'."\n";
            }else{// Non SELECT queries return number of affected rows
                $ans = $this->mysqli->affected_rows;
                $commentsStack[] = '# Affected '.$ans.' row(s) ';
            }
        }
        $work_time = microtime(1)-$start_sql;
        $this->all_work_time += $work_time;
        if (!is_null($this->logger)){
            $comment = implode("\n", $commentsStack);
            $this->logger->log($sql, $start_time, $work_time, $comment, $ans, $sql_source, $values);
        }
        return $ans;
    }

    /**
     * Выполнение серии запросов
     * @param string $sql
     * @return bool
     */
    public function multi_query($sql){
        $res = $this->mysqli->multi_query($sql);
        if ($res){
            $this->mysqli->use_result();
            do {
                if ($result = $this->mysqli->store_result()){
                    $result->free();
                }
            } while ($this->mysqli->more_results() && $this->mysqli->next_result());
        }
        return $res;
    }

    /**
     * при передаче в качестве значения в плайсхолдер приводит к "пропусканию" условного блока, содержащего данный плайсхолдер
     * @return self::SKIP
     */
    public function skipIt(){
        return self::SKIP;
    }
    public function getAllTime(){
        return $this->all_work_time;
    }
}