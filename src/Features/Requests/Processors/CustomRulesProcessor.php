<?php

namespace PulsarLabs\Generators\Features\Requests\Processors;

use Closure;
use PulsarLabs\Generators\Contracts\IsProcessor;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Support\Enums\ColumnTypes;

class CustomRulesProcessor implements IsProcessor
{

    public function handle(CommandData $command_data, Closure $next): CommandData
    {

        return $next($command_data);
    }
}

