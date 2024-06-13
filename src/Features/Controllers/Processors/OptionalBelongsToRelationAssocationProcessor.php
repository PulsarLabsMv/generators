<?php

namespace PulsarLabs\Generators\Features\Controllers\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;

class OptionalBelongsToRelationAssocationProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $columns = $command_data->getColumnObjects();
        $belongs_to_stub = file_get_contents(__DIR__ . '/../stubs/belongs_to_associate_optionally.stub');
        $belongs_to = "";

        /** @var ColumnData $column */
        foreach ($columns as $column) {
            if (! $column->is_foreign_key) {
                continue;
            }

            $belongs_to .= "\n" .
                str_replace(
                    [
                        '{{ model }}',
                        '{{ relationship }}',
                        '{{ model_key }}',
                    ],
                    [
                        str($column->getName())->snake()->singular(),
                        $column->getRelationshipName(),
                        $column->getName(),
                    ],
                    $belongs_to_stub
                );
        }

        $command_data->stub_contents = str_replace('{{ belongsToOptionalAssociations }}', $belongs_to, $command_data->stub_contents);

        return $next($command_data);
    }
}
