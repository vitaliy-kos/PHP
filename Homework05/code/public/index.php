<?php
// $memory_start = memory_get_usage();

require_once '../vendor/autoload.php';

use Geekbrains\Application\Application\Application;
use Geekbrains\Application\Application\Render;

try {
    $app = new Application();
    echo $app->run();
} catch (Exception $e) {
    echo Render::renderExceptionPage($e);
}

// echo 'Потреблено памяти ' . round((memory_get_usage() - $memory_start) / 1024 / 1024, 2) . "МБ памяти";