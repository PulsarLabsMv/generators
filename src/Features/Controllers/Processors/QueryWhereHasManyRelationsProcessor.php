<?php

namespace PulsarLabs\Generators\Features\Controllers\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\DataObjects\ReferencingTableData;

class QueryWhereHasManyRelationsProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $references = $command_data->getReferencingTableObjects();
        $query_has_many_stub = file_get_contents(__DIR__ . '/../stubs/query_has_many.stub');
        $query_has_many = "";

        /** @var ReferencingTableData $reference */
        foreach ($references as $reference) {
            if ($reference->referencingTableIsPivot()) {
                continue;
            }

            $query_has_many .= "\n" . str_replace([
                    '{{ model }}',
                    '{{ model_key }}',
                    '{{ model_name }}',
                ], [
                    str($reference->getModelName())->snake()->singular(),
                    $reference->getReferencingColumnName(),
                    str($reference->getModelName())->studly(),
                ], $query_has_many_stub);
        }


        $command_data->stub_contents = str_replace(
            '{{ hasManyQueries }}',
            $query_has_many,
            $command_data->stub_contents
        );

        return $next($command_data);
    }
}
