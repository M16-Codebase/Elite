<?php
namespace LPS;
/**
 * Автозагрузчик.
 * Внимание! автзагрузчик регистро чувствительный, нужно не забывать, что все имена классов и пространств имен, а так же их аналоги в FS пишутся в стиле "Кемел"
 * 
 * @example
    include_once(__DIR__.'/Autoload.php');
    $map = array( // маппинг маршрутов на FS настраивать здесь!
        '__NAMESPACE__' => 'lpscore', //папка в которой искать классы, из корня текущего пространства имен, должна быть ВСЕГДА
        'Config' => 'config.php', //класс корневого пространства имен
        'Route\' => 'ext/route/', // относительный путь в пространстве имен
        '\exceptionHandler\Controler' => 'include/exeption/controller.php', //абсолютный путь к классу
        '\exception\' => 'includes/classes/exeption/', //абсолютный путь к пространуству имен
    );
    LPS\Autoload::init($map, Config::getLogFolder().'/autoload.log');
 * 
 */
class Autoload {
    const USE_REQUIRE = TRUE;
    const WRITE_LOG = FALSE;
    static protected $instance = null;
    /**
     * @return Autoload 
     */
    static protected function factory(){
        if (empty(self::$instance)){
            self::$instance = new Autoload();
        }
        return self::$instance;
    }
    protected function __construct(){
        /* Бдение */
        //spl_autoload_extension('.php'); //как мера защиты если будет установлен стандартный загрузчик 
    }
    
    static protected $inited = FALSE;
    /**
     * инициализация автозагрузчика
     * @param array $fsMap
     * @param string $logFile
     */
    public static function init($fsMap = null, $logFile = ''){
        krsort($fsMap, SORT_STRING);
        $autoloader = self::factory();
        $autoloader->logFile = $logFile;
        $autoloader->fsMap = $fsMap;
        self::$inited = TRUE;
        self::register();
        
        $m = print_r($fsMap, TRUE);
        $m = substr($m, 8, -3);
        $m = "-------- ROUTE MAP --------\n".
            $m.
            "\n---------------------------\n";
        $autoloader->log($m);
    }
    
    static protected $registered = FALSE;
    /**
     * Регистрирование функции автозагрузки
     * @throws \Exception
     */
    public static function register() {
        if (self::$registered){
            return TRUE;
        }
        if (!self::$inited){
            throw new \LogicException('Autoload not inited');
        }elseif (!spl_autoload_register(array(self::$instance, 'load'))) {
            throw new \Exception('Could not register '.__NAMESPACE__.'\'s class autoload function');
        }else{
            return TRUE;
        }
    }
    /**
     * Отмена регистрации данного автозагрузчика
     * @throws \Exception
     * @return bool
     */
    public static function unregister() {
        if (!self::$registered){
            return TRUE;
        }
        if (!spl_autoload_unregister(array(self::$instance, 'load'))) {
            throw new \Exception('Could not unregister '.__NAMESPACE__.'\'s class autoload function');
        }
        return TRUE;
    }
    
    /**
     * файл отладки
     * @var string
     */
    protected $logFile = null;
    /**
     * Таблица маршрутов, позволяет настроить специальным образом мапинг пространств имен в файловую систему
     * Формат: 
     *  пространство имен (слеши левые) => путь к папке (слеши правые)
     *  Отстутствие в конце пространства имен слеша означает путь к Классу
     *  Отстутствие в начале пространства имен слеша означает относительный путь относительно __NAMESPACE__
     * @var array
     */
    protected $fsMap = null;
    protected $strongInclude = null;

    protected function log($m){
        if (!self::WRITE_LOG){
            return FALSE;
        }
        if (empty($this->logFile)){
            return FALSE;
        }else{
            if (!file_exists($this->logFile)){
                $r = fopen($this->logFile, 'w');
                if ($r === false){
                    trigger_error('Can`t create autoload log file ' . $this->logFile);
                    exit('Can`t create autoload log file ' . $this->logFile);
                }
                chmod($this->logFile, 0777);
                fclose($r);
            }
            $m = $m ."\n";
            file_put_contents($this->logFile, $m, FILE_APPEND);
            return TRUE;
        }
    }

    public static function findClassFileInPath($class, $path){
        $fileName = $path.'/'.$class.'.php';
        $fileName = str_replace('\\', '/', $fileName);
        $fileName = str_replace('//', '/', $fileName);
        $fileName = str_replace('/', DIRECTORY_SEPARATOR, $fileName);
        return $fileName;
    }

    /**
     *
     * @param string $fullClassName
     * @throws \InvalidArgumentException
     * @return bool
     */
    public function load($fullClassName) {
        $this->log(''.$fullClassName);
        if (!preg_match('~^([a-z][a-z0-9_]*)(\\\\[a-z][a-z0-9_]*)*$~i', $fullClassName)){
            $this->log("\tIncorrrect class format");
            return FALSE;
            //Короче автолоадер не должен кидать эксепшинов, в него могут передать любую чушь, ведь он может быть вызван и функцией is_callable
            //throw new \InvalidArgumentException('Incorrrect class format (class is "'.$fullClassName.'")');
        }
        $relativeNameSpace = FALSE;
        $namespacePieces = explode('\\', $fullClassName);
        $className = array_pop($namespacePieces);
        $localClassName = '';
        if(!empty($namespacePieces) && $namespacePieces[0] == __NAMESPACE__){ //родное пространство имен
            array_shift($namespacePieces);
            $localNameSpace = implode('\\', $namespacePieces);
            $localClassName = $localNameSpace.'\\'.$className;
            $relativeNameSpace = TRUE; //все задано в локальном пространстве имен
            $this->log("\tRelative at ".__NAMESPACE__.' as "'.$localClassName.'"');
        }
        $classFile = null;        
        $map = $this->fsMap;
        // поиск исключений в правилах
        foreach ($map as $nsRule => $fsRoute){
            $originRule = $nsRule;
            if ($nsRule{0} === '\\' and $relativeNameSpace){ 
                //Маска пространства имен описывает относительно глобального пространства имен, а запрос внутри __NAMESPACE__
                continue; 
            }
            if ($nsRule{0} === '\\'){
                //ведущий слеш просто отрезается, он нужен для указания того, пространство имен задано абсолютное 
                $nsRule = substr($nsRule, 1);
            }else{
                //Если ведущего слеша нет, то пространство имен дополняется корневым"
                $nsRule = __NAMESPACE__.'\\'.$nsRule;
            }
            if ($nsRule{strlen($nsRule)-1} === '\\'){ //класс или пространство имен?
                if (strncmp($fullClassName, $nsRule, strlen($nsRule)) === 0){
                    //дальнейшая машрутизация произойдет на основе правила $originRule
                    $this->log("\tFind namespace route: \"$originRule\" as \"$nsRule\"");
                    $includePath = $fsRoute;
                    $rule = substr($fullClassName, strlen($nsRule));
                    $classFile = self::findClassFileInPath($rule, $includePath);
                    break;
                }
            }else{
                if ($fullClassName === $nsRule){
                    //дальнейшая машрутизация произойдет на основе правила $originRule
                    $this->log("\tFind class route: \"$originRule\" as \"$nsRule\"");
                    $classFile = $fsRoute;
                    break;
                }
            }
        }
        if (empty($classFile) and $relativeNameSpace){ //штатная обработка
            $includePath = $this->fsMap['__NAMESPACE__'];
            $this->log("\tDefault route  \"$localClassName\" at \"$includePath\"");
            $classFile = self::findClassFileInPath($localClassName, $includePath);
        }
        if (!file_exists($classFile)){
            return false;
        }
        if (!empty($classFile)){ // подключение
            $this->log("\tFile: \"$classFile\"");
            if (self::USE_REQUIRE){
                require_once ($classFile);
            }else{
                if (!include_once ($classFile)){
                    $this->log("\t".'Can`t load file "'.$classFile.'"');
                }
            }
            return TRUE;
        }
        $this->log("\t Skip");
        return FALSE;
    }
}