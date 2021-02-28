<?php
header("Cache-control: public");

header("Expires: " . gmdate("D, d M Y H:i:s", time() + 7*60*60*24) . " GMT");

require 'webp-on-demand.inc';

use WebPOnDemand\WebPOnDemand;

// $source = str_replace('scripts/tmpl/','',rtrim(dirname(__FILE__), '/').$_GET['source']);
// Absolute file path to source file. Comes from the .htaccess
$source = $_GET['source'];

$destination = $source . '.webp';     // Store the converted images besides the original images (other options are available!)

$options = [
    // Tell where to find the webp-convert-and-serve library, which will

    // be dynamically loaded, if need be.

    'require-for-conversion' => 'webp-convert-and-serve.inc',

    // UNCOMMENT NEXT LINE, WHEN YOU ARE UP AND RUNNING!    

    'show-report' => false             // Show a conversion report instead of serving the converted image.

    // More options available!
];



WebPOnDemand::serve($source, $destination, $options);