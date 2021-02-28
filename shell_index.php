<?php
if (empty($GLOBALS['argv'][1]) && empty($GLOBALS['argv'][2])){
    $GLOBALS['argv'][1]='work';
    $GLOBALS['argv'][2]='test';
}
	require_once('src/index.php');