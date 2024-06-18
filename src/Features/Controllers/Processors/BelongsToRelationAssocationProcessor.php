<?php

namespace PulsarLabs\Generators\Features\Controllers\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Exceptions\MissingArgumentException;

class BelongsToRelationAssocationProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        if (! $argument_parent_name = data_get($command_data->arguments, 'parent_name')) {
            throw new MissingArgumentException('parent_name');
        }

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
                        '{{ model }}',
                    ],
                    [
                        $column->getRelationshipName(),
                        str($column->getReferencedModelName())->snake()->singular(),
                    ],
                    str($column->referenced_table_name)->singular()->snake()->toString() == $argument_parent_name ?
                        file_get_contents(__DIR__ . '/../stubs/nested_belongs_to_associate.stub')
                        : $belongs_to_stub
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
