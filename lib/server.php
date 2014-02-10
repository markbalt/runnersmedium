<?php
/*

Runner's Medium
http://www.runnersmedium.com/

server.php

host specific config

copyright 2009 Mark Baltrusaitis <http://josieprogramme.com>

*/

// log errors
ini_set('display_errors', 1);
ini_set('log_errors', 1);

//error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL);

ini_set('error_log', '../logs/errors.log');

define('DATABASE_HOST', 'localhost');
define('DATABASE_USER', 'runnersmedium');
define('DATABASE_PASSWORD', '**');
define('DATABASE_NAME', 'runnersmedium');

define('ROOT', '/runnersmedium/');
define('URL', 'http://localhost/runnersmedium');

// google maps api key
define('GMAP_KEY', '**');

// recaptcha public and private key
define('RECAPTCHA_PUBLIC_KEY', '**');
define('RECAPTCHA_PRIVATE_KEY', '**');
?>