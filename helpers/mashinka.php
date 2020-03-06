<?php

namespace Mashinka;

use Mashinka\Commands\CommandFactory;

function main(array $argv)
{
    // check $argv
    // from env
    $commandInstance = CommandFactory::getInstance($argv[1]);
    //$commandParamsInstance = CommandFactory::getInstance($argv[2::]);
    $commandInstance->run();
}
