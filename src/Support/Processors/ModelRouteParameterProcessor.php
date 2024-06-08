<?php

namespace PulsarLabs\Generators\Support\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

class ModelRouteParameterProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $model_route_parameter = str($command_data->table_name)->slug('_')->singular()->toString();
        $command_data->stub_contents = str_replace('{{ ModelRouteParameter }}', $model_route_parameter, $command_data->stub_contents);

        return $next($command_data);
    }
}
