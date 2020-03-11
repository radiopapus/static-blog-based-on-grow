<?php

namespace Mashinka;

use Mashinka\Commands\CommandFactory;

/**
 * @param array $argv
 *
 * @throws \Exception
 */
function main(array $argv)
{
    // check $argv
    // from env
    $commandInstance = CommandFactory::getInstance($argv[1], array_slice($argv, 1));
    $commandInstance->run();
}
