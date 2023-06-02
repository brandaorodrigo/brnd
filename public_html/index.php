<?php

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

// config

include 'config.php';

// classes

require 'classes/DB.php';
require 'classes/Normalize.php';
require 'classes/Route.php';
require 'classes/Validate.php';

// app

require 'app.php';

// route

function route()
{
    @include Route::execute(__DIR__ . DIRECTORY_SEPARATOR . 'routes');
}

route();
