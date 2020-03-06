<?php

namespace Mashinka\Commands;

class CommandFactory
{
    /**
     * @param string $command
     *
     * @return \Mashinka\Commands\CommandInterface
     * @throws \Exception
     */
    public static function getInstance(string $command): CommandInterface
    {
        if ($command == '--publish') {
            return new Publish();
        }

        $availableCommands = ['--publish', '--draft'];

        if (!in_array($command, $availableCommands, true)) {
            die ("Command does not exist. see ");
        }

        throw new \Exception('Undefined command. See $availableCommands for details.');
    }

    /**
     * @param array $params
     *
     * @return \Mashinka\Commands\CommandParamsInterface
     * @throws \Exception
     */
    public static function getParamsInstance(array $params): CommandParamsInterface
    {
        throw new \Exception('Undefined command. See $availableCommands for details.');
    }
}
