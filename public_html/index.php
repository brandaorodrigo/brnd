<?php

// config

require 'config.php';

// locale

setlocale(LC_ALL, 'pt_BR');

// timezone

date_default_timezone_set('America/Fortaleza');

// timeout

ini_set('max_execution_time', 30);

// debug

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);

// index

define('__INDEX__', __DIR__);

// header

header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Origin: *');

// includes

include 'config.php';

include 'classes/DB.php';
include 'classes/Normalize.php';
include 'classes/Route.php';
include 'classes/Validate.php';

// app

include 'app.php';

// route

Route::execute(__DIR__ . DIRECTORY_SEPARATOR . 'routes');
