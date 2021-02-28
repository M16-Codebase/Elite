<?php
$output = '';
$input = $modx->getOption('input', $scriptProperties, '');
if (trim($input) != '') {
    $output = str_replace(array('-', '(', ')', ' '), '', $input);
}

return $output;
return;
