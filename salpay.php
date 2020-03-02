<?php

require_once __DIR__ . '/vendor/autoload.php';

use salpay\controllers\Controller;

$controller = new Controller($argv);
$controller->resolve();
