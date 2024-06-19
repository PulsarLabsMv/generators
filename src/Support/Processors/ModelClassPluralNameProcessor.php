<?php

namespace PulsarLabs\Generators\Support\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

class ModelClassPluralNameProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $class_name = str($command_data->table_name)->studly()->plural();
        $command_data->stub_contents = str_replace('{{ ModelClassPluralName }}', $class_name, $command_data->stub_contents);

        return $next($command_data);
    }
}
