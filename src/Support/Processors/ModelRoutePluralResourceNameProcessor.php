<?php

namespace PulsarLabs\Generators\Support\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

class ModelRoutePluralResourceNameProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $class_name = str($command_data->table_name)->kebab()->plural();
        $command_data->stub_contents = str_replace('{{ ModelRoutePluralResourceName }}', $class_name, $command_data->stub_contents);

        return $next($command_data);
    }
}
