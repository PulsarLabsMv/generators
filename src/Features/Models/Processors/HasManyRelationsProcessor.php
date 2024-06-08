<?php

namespace PulsarLabs\Generators\Features\Models\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\DataObjects\ReferencingTableData;

class HasManyRelationsProcessor
{

    public function handle(CommandData $command_data, Closure $next)
    {
        $references = $command_data->getReferencingTableObjects();
        $belongs_to_stub = file_get_contents(__DIR__ . '/../stubs/has_many.stub');
        $has_many = "\n";

        /* @var ReferencingTableData $reference*/
        foreach ($references as $reference) {
            if ($reference->referencingTableIsPivot()) {
                continue;
            }

            $has_many .= str_replace(
                [
                    '{{ method }}',
                    '{{ model }}',
                    '{{ foreign_key }}',
                    '{{ local_key }}'
                ],
                [
                    $reference->getMethodName(),
                    $reference->getModelName(),
                    $reference->getReferencingColumnName(),
                    $reference->getLocalKey()
                ],
                $belongs_to_stub
            );
        }

        $command_data->stub_contents = str_replace('{{ hasManyRelations }}', $has_many, $command_data->stub_contents);

        return $next($command_data);
    }
}
