<?php

namespace PulsarLabs\Generators\Support\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

class RequestClassNameProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $request_file_name = str($command_data->table_name)->studly()->singular()->toString() . 'Request';
        $command_data->stub_contents = str_replace('{{ RequestFileName }}', $request_file_name, $command_data->stub_contents);

        return $next($command_data);
    }
}
