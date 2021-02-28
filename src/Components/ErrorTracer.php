<?php
namespace LPS\Components;
/**
 * ErrorTracer is class for create correct debug messages
 *
 * This is port debug part from Dk Lab DbSimple project (Dmitry Koterov & Konstantin Zhinko)
 * @see http://en.dklab.ru (DbSimple > DbSimple_Generic_LastError part)
 *  
 * @author Alexander Shulman
 * @link http://wiki.lpscms.ru
 */
class ErrorTracer{
	protected $ignoresInTraceRe =  '';
	/**
     * void addIgnoreInTrace($reName)
     * Add regular expression matching ClassName::functionName or functionName.
     * Matched stack frames will be ignored in stack traces passed to query logger. 
     * @param string $reName
     */    
	function __construct($reName=null){
		$this->ignoresInTraceRe =  __CLASS__.'::.*|call_user_func.*';
		if (!empty($reName)){
			$this->addIgnoreInTrace($reName);
		}
	}
	/**
     * void addIgnoreInTrace($reName)
     * Add regular expression matching ClassName::functionName or functionName.
     * Matched stack frames will be ignored in stack traces passed to query logger. 
     * @param string $reName
     */    
    function addIgnoreInTrace($reName)
    {
        $this->ignoresInTraceRe .= '|' . $reName;
    }
    
    /**
     * array of array findLibraryCaller()
     * Return part of stacktrace before calling first library method.
     * Used in debug purposes (query logging etc.).
     */
    function findLibraryCaller()
    {
        $caller = $this->debug_backtrace_smart(
            $this->ignoresInTraceRe,
            true
        );
        return $caller;
    }
    
    /**
     * array debug_backtrace_smart($ignoresRe=null, $returnCaller=false)
     * 
     * Return stacktrace. Correctly work with call_user_func*
     * (totally skip them correcting caller references).
     * If $returnCaller is true, return only first matched caller,
     * not all stacktrace.
     */
    function debug_backtrace_smart($ignoresRe=null, $returnCaller=false)
    {
        if (!is_callable($tracer='debug_backtrace'))
        	return array();
        $trace = $tracer();
        //exit ($ignoresRe);
        if ($ignoresRe !== null)
        	$ignoresRe = '~^(?>'.str_replace ('\\', '\\\\', $ignoresRe).')$~six';
        $smart = array();
        $framesSeen = 0;
        for ($i=0, $n=count($trace); $i<$n; $i++) {
            $t = $trace[$i];
            if (!$t)
            	continue;
                
            // Next frame.
            $next = isset($trace[$i+1])? $trace[$i+1] : null;
            
            // Dummy frame before call_user_func* frames.
            if (!isset($t['file'])) {
                $t['over_function'] = $trace[$i+1]['function'];
                $t = $t + $trace[$i+1];
                $trace[$i+1] = null; // skip call_user_func on next iteration
            }
            
            // Skip myself frame.
            if (++$framesSeen < 2)
            	continue;

            // 'class' and 'function' field of next frame define where
            // this frame function situated. Skip frames for functions
            // situated in ignored places.
            if ($ignoresRe && $next) {
                // Name of function "inside which" frame was generated.
                $frameCaller = 
                	(isset($next['class'])    ? $next['class'].'::' : '').
                	(isset($next['function']) ? $next['function'] : '');
                if (preg_match($ignoresRe, $frameCaller)){
                	continue;
                }
            }

            // On each iteration we consider ability to add PREVIOUS frame
            // to $smart stack.
            $ans=array('line'=>$t['line'], 'file'=>$t['file'], 'from'=>isset($frameCaller)?$frameCaller:'--');
            if (isset($next['class']))
            	$ans['class']=$next['class'];
            if (isset($next['function']))
            	$ans['function']=$next['function'];
            	
            if ($returnCaller){
            	return $ans;
            }

            $smart[] = $t;
        }
        return $smart;
    }
}
?>