<?php
/** 
 * Check PHP version.
 */
if (version_compare(PHP_VERSION, '5.4', '<')) {
    throw new Exception('PHP version >= 5.4 required');
}

// Check PHP Curl & json decode capabilities.
if (!function_exists('curl_init') || !function_exists('curl_exec')) {
    throw new Exception('DANA needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('DANA needs the JSON PHP extension.');
}

// Configurations
require_once 'DANA/Config.php';

// DANA Resources
require_once 'DANA/Auth.php';
require_once 'DANA/Spi.php';
require_once 'DANA/Transaction.php';

// Utils
require_once 'DANA/Util.php';