<?php

namespace PulsarLabs\Generators\Features\Policies\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

class ModuleClassNameProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $class_name = str($command_data->table_name)->studly()->singular();
        $command_data->stub_contents = str_replace('{{ ModelClassName }}', $class_name, $command_data->stub_contents);

        return $next($command_data);
    }
}
