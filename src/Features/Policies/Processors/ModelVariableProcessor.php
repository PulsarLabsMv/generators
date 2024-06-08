<?php

namespace PulsarLabs\Generators\Features\Policies\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

class ModelVariableProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $model_variable_name = str($command_data->table_name)->lower()->slug('_')->singular();
        $command_data->stub_contents = str_replace('{{ ModelVariable }}', $model_variable_name, $command_data->stub_contents);

        return $next($command_data);
    }
}
