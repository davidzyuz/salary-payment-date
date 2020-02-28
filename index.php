<?php

require_once __DIR__ . '/vendor/autoload.php';

use salpay\controllers\Controller;

$controller = new Controller();
$controller->foo();

fwrite(STDOUT, "Salary Payment Date Tool\n");

for ($i = 0; $i < 3; $i++) {
	$line = readline("> ");
	readline_add_history($line);
}
