<?php

require_once __DIR__ . '/vendor/autoload.php';

use Geekbrains\Application\Application\Application;
use Geekbrains\Application\Application\Render;

try {
    $app = new Application();
    echo $app->run();
} catch (Exception $e) {
    echo Render::renderExceptionPage($e);
}
