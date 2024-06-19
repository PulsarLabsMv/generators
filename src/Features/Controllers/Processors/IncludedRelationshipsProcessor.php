<?php

namespace PulsarLabs\Generators\Features\Controllers\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\DataObjects\ReferencingTableData;

class IncludedRelationshipsProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $included_relationships = [];
        $columns = $command_data->getColumnObjects();
        // Belongs to relationships
        /** @var ColumnData $column */
        foreach ($columns as $column) {
            if (! $column->is_foreign_key) {
                continue;
            }

            $relationship_name = $column->getRelationshipName();
            $included_relationships [] = "'$relationship_name'";
        }

        // Pivot relationships
        $references = $command_data->getReferencingTableObjects();
        /** @var ReferencingTableData $reference */
        foreach ($references as $reference) {
            if (! $reference->referencingTableIsPivot()) {
                continue;
            }

            $columns = $command_data->database_reader->getColumnObjects($reference->getReferencingTableName());
            /** @var ColumnData $column */
            foreach ($columns as $column) {
                if (! $column->isForeignKey() || $column->getName() == $reference->getReferencingColumnName()) {
                    continue;
                }
                $relationship_name = str($column->getRelationshipName())->plural();
                $included_relationships [] = "'$relationship_name'";
            }
        }

        $included_relationships = implode(",", $included_relationships);
        $command_data->stub_contents =
            str_replace(
                '{{ IncludedRelationships }}',
                $included_relationships,
                $command_data->stub_contents
            );

        return $next($command_data);
    }
}
