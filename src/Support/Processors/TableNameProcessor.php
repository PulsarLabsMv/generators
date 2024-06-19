<?php

namespace PulsarLabs\Generators\Support\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

class TableNameProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $table_name = $command_data->table_name;
        $command_data->stub_contents = str_replace('{{ TableName }}', $table_name, $command_data->stub_contents);

        return $next($command_data);
    }
}
