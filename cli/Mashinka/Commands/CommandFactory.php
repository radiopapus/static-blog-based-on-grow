<?php

namespace Mashinka\Commands;

use Exception;
use Mashinka\Util\Template;

class CommandFactory
{
    /**
     * @param string $command
     *
     * @param array  $params
     *
     * @return CommandInterface
     * @throws \Exception
     */
    public static function getInstance(string $command, array $params): CommandInterface
    {
        if ($command === 'publish') {
            return new Publish(new Template(), $params);
        }

        $availableCommands = ['publish', 'draft'];

        if (!in_array($command, $availableCommands, true)) {
            die("Command does not exist.");
        }

        throw new Exception('Undefined command. See $availableCommands for details.');
    }
}
