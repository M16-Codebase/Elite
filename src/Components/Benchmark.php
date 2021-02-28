<?php
namespace LPS\Components;
/**
 * simple benchmark class for log systems events
 * @author Alexander Shulman
 * @link http://wiki.lpscms.ru
 */
class Benchmark {
    const DEF_NAME = 'global';
    private $name = '';
    private $enable = FALSE;
    /**
     * @var float
     */
    protected $lastTime;
    /**
     * @var float
     */
    protected $startTime;

    /**
     * return current time in seconds with miliseconds
     * @return float
     */
    protected function getTime() {
        return microtime(1);
    }
    /**
     * return last time interval
     * @return float
     */
    protected function getInterval() {
        $ans = $this->getTime() - $this->lastTime;
        $this->lastTime = $this->getTime();
        return $ans;
    }

    protected function __construct($name) {
        $this->name = $name;
        $this->lastTime = $this->getTime();
        $this->startTime = $this->getTime();
        $this->log('Benchmark create');
    }
    /**
     * @var Benchmark
     */
    static protected $instances = array();
    /**
     * Benchmark's factory. Garanty one instance for one name
     * @param string $name
     * @return Benchmark
     */
    static function factory($name='') {
        if (empty($name)) {
            $name = self::DEF_NAME;
        }
        if (empty(self::$instances[$name]))
            self::$instances[$name] = new Benchmark($name);
        return self::$instances[$name];
    }
	/**
     * Benchmark's factory. Garanty one instance for one name
     * @param string $name
     * @return Benchmark
     */
    static function get($name='') {
        return self::factory($name);
    }
    /**
     * @var array any position have structure: array('time'=>float,'reason'=>string, 'interval'=>float);
     */
    protected $log = array();
    /**
     *
     * @staticvar array $fromPlacesCounter
     * @param string $reason
     * @param string $info_0
     * @param string $info_1
     * @param string $info_2
     * @return array log item info
     */
    public function log($reason='') {
        if (!$this->enable) {
            return null;
        }
        static $fromPlacesCounter = array(); //учет количества итераций из одного места
        $errorTracer = new ErrorTracer();
        $errorTracer->addIgnoreInTrace(__CLASS__ . '::' . __FUNCTION__);
        $caller = $errorTracer->findLibraryCaller();
		$caller['file'] = preg_replace('~^'.preg_quote(\LPS\Config::getRealDocumentRoot(), '~').'~', '', $caller['file']);
        $from = $caller['from'] . ';' . $caller['file'] . ':' . $caller['line'];
        if (!isset($fromPlacesCounter[$from])) {
            $fromPlacesCounter[$from] = 0;
        } else {
            $fromPlacesCounter[$from]++;
        }
        $logRec = array(
            'interval' => round($this->getInterval(), 3),
            'reason' => $reason,
            'from' => $caller['from'],
            'file' => $caller['file'] . ' on line ' . $caller['line'],
            'time' => round($this->getTime() - $this->startTime, 3)
        );
        $args = func_get_args();
        if(count($args) > 1){
            $args_count = count($args)-1;
            for($i=0; $i < $args_count; $i++){
                $logRec['info_'.$i] = $args[$i];
            }
        }
        $this->log[!isset($fromPlacesCounter[$from]) ? $caller['from'] : ($from . '#' . $fromPlacesCounter[$from])] = $logRec;
        if (!empty($this->handler)) {
            call_user_func($this->handler, $logRec);
        }
        return $logRec;
    }
    /**
     *
     * @var callback
     */
    protected $handler = null;
    /**
     * Create Handler on log() call
     * @param callback $function
     */
    public function setHandler($callback, $recall = FALSE) {
		if (is_callable($callback)){
			$this->handler = $callback;
			if ($recall){
				foreach ($this->log as $logRec){
					call_user_func($this->handler, $logRec);
				}
			}
		}else{
			trigger_error ('incorrect param $callback');
		}
    }
    public function getLog() {
        return $this->log;
    }
    public function enable($enable){
        $this->enable = $enable ? TRUE : FALSE;
    }
}
