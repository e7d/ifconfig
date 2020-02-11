<?php

use IfConfig\Application;

if ($_ENV['mode'] !== 'DEBUG') {
    ini_set("display_errors", 0);
    ini_set("log_errors", 1);
}

require __DIR__ . '/../vendor/autoload.php';

new Application();
