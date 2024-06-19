<?php

namespace PulsarLabs\Generators\Features\Controllers\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\DataObjects\ReferencingTableData;

class PivotSyncProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $references = $command_data->getReferencingTableObjects();

        $pivot_sync_stub = file_get_contents(__DIR__ .'/../stubs/pivot_syncs.stub');
        $pivot_sync = "";

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

                $pivot_sync .= str_replace(
                    [
                        '{{ model_plural }}',
                        '{{ relationship }}'
                    ],
                    [
                        str($column->getReferencedModelName())->snake()->plural(),
                        $column->getPluralRelationshipName()
                    ],
                    $pivot_sync_stub
                );
            }

            $command_data->stub_contents = str_replace('{{ pivotSyncs }}', $pivot_sync, $command_data->stub_contents);

            return $next($command_data);
        }
    }
}
