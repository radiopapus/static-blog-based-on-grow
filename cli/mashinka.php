<?php
require_once 'autoload.php';

$commandInstance = Mashinka\Commands\CommandFactory::getInstance($argv[1], array_slice($argv, 2));
$commandInstance->run();
