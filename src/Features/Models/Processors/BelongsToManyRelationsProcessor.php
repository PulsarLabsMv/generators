<?php

namespace PulsarLabs\Generators\Features\Models\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\DataObjects\ReferencingTableData;

class BelongsToManyRelationsProcessor
{

    public function handle(CommandData $command_data, Closure $next)
    {
        $references = $command_data->getReferencingTableObjects();

        $belongs_to_stub = file_get_contents(__DIR__ . '/../stubs/belongs_to_many.stub');
        $belongs_to_many = "";

        /* @var ReferencingTableData $reference*/
        foreach ($references as $reference) {
            if (! $reference->referencingTableIsPivot()) {
                continue;
            }

            $columns = $command_data->database_reader->getColumnObjects($reference->getReferencingTableName());

            /* @var ColumnData $column */
            foreach ($columns as $column) {
                if ($column->isForeignKey() && $column->getName() != $reference->getReferencingColumnName()) {
                    $belongs_to_many .= str_replace(
                        [
                            '{{ method }}',
                            '{{ model }}',
                            '{{ table_name }}',
                        ],
                        [
                            $column->getPluralRelationshipName(),
                            $column->getReferencedModelName(),
                            $reference->getReferencingTableName(),
                        ],
                        $belongs_to_stub
                    );
                }
            }
        }

        $command_data->stub_contents = str_replace('{{ manyToManyRelations }}', $belongs_to_many, $command_data->stub_contents);

        return $next($command_data);
    }
}
