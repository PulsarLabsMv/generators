<?php

namespace PulsarLabs\Generators\Contracts;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

interface IsProcessor
{
    public function handle(CommandData $command_data, Closure $next): CommandData;
}
