<?php  return '$value = (int) $modx->getOption(\'value\', $scriptProperties, 0);

if ($value == 0) {
    return;
}

$output = \'\';

$index = 1;
while($index <= 5) {
    $className = ($index <= $value) ? \'active\' : \'\';
    $output .= \'<li class="\'.$className.\'"></li>\';
    $index++;
}

$output = \'<ul class="rating-stars">\' . $output . \'</ul>\';

return $output;
return;
';