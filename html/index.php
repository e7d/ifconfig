<?php

require_once __DIR__ . '/../vendor/autoload.php';

use IfConfig\Application;
use Utils\StopwatchService;

StopwatchService::get('total')->start();
new Application();
