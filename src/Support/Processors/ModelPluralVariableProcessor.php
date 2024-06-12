<?php

namespace PulsarLabs\Generators\Support\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

class ModelPluralVariableProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $model_variable_name = str($command_data->table_name)->lower()->slug('_')->plural();
        $command_data->stub_contents = str_replace('{{ ModelPluralVariable }}', $model_variable_name, $command_data->stub_contents);

        return $next($command_data);
    }
}
