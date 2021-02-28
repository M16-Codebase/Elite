<?php
/*/
    require_once('coverage.php');
    exit;
*/
if(!empty($_SERVER['REQUEST_URI'])&&strpos($_SERVER['REQUEST_URI'],'__')){
	header('HTTP/1.1 404 Not Found');
	header('Location: http://m16-elite.ru/404');
	exit;
}
if(!empty($_SERVER['REQUEST_URI'])&&strpos($_SERVER['REQUEST_URI'],'en/top-100/')){
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: http://m16-elite.ru/top-100');
	exit;
}

function dump($var, $info = false) {
    if (isset($_REQUEST['debug'])) {
        $bt = debug_backtrace();
        echo '<br />';
        echo "========= file : {$bt[0]['file']}, line: {$bt[0]['line']} ==========";
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
        if ($info) {
            foreach ($bt as $b) {
                if (isset($b['file'])) {
                    echo '<small>file : ' .$b['file'].', line: '. $b['line'].'</small>';
                } else {
                    echo '---';
                }
                echo '<br />';
            }
            exit;
        } else {
            if (isset($bt[1]['file'])) {
                echo 'file : ' .$bt[1]['file'].', line: '. $bt[1]['line'];
            }
        }
        echo '<br />'; echo '==================='; echo '<br />';
        }
    }


    ob_start ( "ob_gzhandler");
	require_once('src/index.php');
	
	