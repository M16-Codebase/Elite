<?php
function ftok ($pathname, $proj) {
    if (empty($pathname) || !file_exists($pathname)) {  // an error occured
        return -1;
    }

    $pathname = $pathname . (string) $proj;
    $key = array();
    while (sizeof($key) < strlen($pathname)) {
        $key[] = ord(substr($pathname, sizeof($key), 1));
    }

    return dechex(array_sum($key));
}
function _ftok($project = ''){
    $s = stat(__FILE__);
    return sprintf("%u", (($s['ino'] & 0xffff) | (($s['dev'] & 0xff) << 16) | 
    (($project & 0xff) << 24))); 
}
vaR_dump(__FILE__);
vaR_dump(ftok(__FILE__, 'e'));
vaR_dump(ftok(__FILE__, 'eas'));
vaR_dump(_ftok('e'));
vaR_dump(_ftok('eas'));