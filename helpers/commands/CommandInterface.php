<?php

namespace Mashinka\Commands;

interface CommandInterface
{
    public function setParams(CommandParamsInterface $params): self;
    public function run(): bool;
}
