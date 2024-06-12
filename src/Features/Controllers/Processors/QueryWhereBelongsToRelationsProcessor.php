<?php

namespace PulsarLabs\Generators\Features\Controllers\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;

class QueryWhereBelongsToRelationsProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $columns = $command_data->getColumnObjects();
        $query_belongs_to_stub = file_get_contents(__DIR__ . '/../stubs/query_belongs_to.stub');
        $query_belongs_to = "";

        /** @var ColumnData $column */
        foreach ($columns as $column) {
            if (! $column->is_foreign_key) {
                continue;
            }

            $query_belongs_to .= "\n" .
                str_replace(
                    [
                    '{{ model }}',
                    '{{ model_key }}',
                ],
                    [
                        str($column->getReferencedModelName())->singular()->snake(),
                        $column->getName(),
                    ],
                    $query_belongs_to_stub
                );
        }

        $command_data->stub_contents = str_replace('{{ belongsToQueries }}', $query_belongs_to_stub, $command_data->stub_contents);

        return $next($command_data);
    }
}
