<?php

require_once __DIR__ . '/vendor/autoload.php';

use salpay\controllers\Controller;
use salpay\helpers\CommandLineHelper;

if ($argc === 1) {
    fwrite(STDOUT, "Please, enter a filename \n");
} else {
    $controller = new Controller();
    $controller->render();
}
