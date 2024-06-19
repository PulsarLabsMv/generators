<?php

namespace PulsarLabs\Generators\Features\Controllers\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;

class OptionalBelongsToRelationAssocationProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        /** @var string|null $argument_parent_name */
        $argument_parent_name = data_get($command_data->arguments, 'parent_name');

        $columns = $command_data->getColumnObjects();
        $belongs_to_stub = file_get_contents(__DIR__ . '/../stubs/belongs_to_associate_optionally.stub');
        $belongs_to = "";

        /**
         * Closure to manage stub selection
         * @param string $table_name
         * @return ?string
         */
        $selectedStub = function (string $table_name) use ($belongs_to_stub, $argument_parent_name) {
            if (! $argument_parent_name) {
                return $belongs_to_stub;
            }

            if ($table_name !== $argument_parent_name) {
                return $belongs_to_stub;
            }

            return file_get_contents(__DIR__ . '/../stubs/nested_belongs_to_associate.stub');
        };

        /** @var ColumnData $column */
        foreach ($columns as $column) {
            if (! $column->is_foreign_key) {
                continue;
            }

            $table_name = str($column->referenced_table_name)->singular()->snake()->toString();
            $belongs_to .= "\n" .
                str_replace(
                    [
                        '{{ model }}',
                        '{{ relationship }}',
                        '{{ model_key }}',
                    ],
                    [
                        str($column->getReferencedModelName())->snake()->singular(),
                        $column->getRelationshipName(),
                        $column->getName(),
                    ],
                    $selectedStub($table_name)
                );
        }

        $command_data->stub_contents =
            str_replace(
                '{{ belongsToOptionalAssociations }}',
                $belongs_to,
                $command_data->stub_contents
            );

        return $next($command_data);
    }
}
