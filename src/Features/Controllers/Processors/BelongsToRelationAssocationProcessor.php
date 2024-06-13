<?php

namespace PulsarLabs\Generators\Features\Controllers\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;

class BelongsToRelationAssocationProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $columns = $command_data->getColumnObjects();
        $belongs_to_stub = file_get_contents(__DIR__ . '/../stubs/belongs_to_associate.stub');
        $belongs_to = "";

        /** @var ColumnData $column */
        foreach ($columns as $column) {
            if (! $column->is_foreign_key) {
                continue;
            }

            $belongs_to .= "\n" .
                str_replace(
                    [
                    '{{ relationship }}',
                    '{{ model_key }}',
                ],
                    [
                        $column->getRelationshipName(),
                        $column->getName(),
                    ],
                    $belongs_to_stub
                );
        }

        $command_data->stub_contents =
            str_replace(
                '{{ belongsToAssociations }}',
                $belongs_to,
                $command_data->stub_contents
            );

        return $next($command_data);
    }
}
