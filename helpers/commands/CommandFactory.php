<?php

namespace Mashinka\Commands;

use Exception;

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
        if ($command === '--publish') {
            return new Publish($params);
        }

        $availableCommands = ['--publish', '--draft'];

        if (!in_array($command, $availableCommands, true)) {
            die("Command does not exist.");
        }

        throw new Exception('Undefined command. See $availableCommands for details.');
    }
}
