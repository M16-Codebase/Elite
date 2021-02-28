<?php
$output = '';
$doc = $modx->resource;
$items = array(
    array(
        'tvId' => 44,
        'icon' => 'vk'
    ),
    array(
        'tvId' => 45,
        'icon' => 'fb'
    ),
    array(
        'tvId' => 46,
        'icon' => 'in',
    )
);

foreach($items as $item) {
    
    $value = '';
    $value = $doc->getTVValue($item['tvId']);
    if (trim($value) != '') {
        $output .= $modx->getChunk('socialItem', array(
            'icon' => $item['icon'],
            'url' => $value
        ));
    }
    
}

if ($output != '') {
    $output = $modx->getChunk('socialOuter', array(
        'output' => $output
    ));
}

return $output;
return;
