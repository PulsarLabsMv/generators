<?php

namespace PulsarLabs\Generators\Features\Models\Updaters;

use PulsarLabs\Generators\Contracts\IsStubUpdater;
use PulsarLabs\Generators\DataObjects\ReferencingTableData;

class HasManyRelationsUpdater implements IsStubUpdater
{
    public function __construct(
        protected string $stub,
        protected array $references,
    ) {
    }

    public function handle(): string
    {
        $belongs_to_stub = file_get_contents(__DIR__ . '/../stubs/has_many.stub');
        $has_many = "\n";

        /* @var ReferencingTableData $reference*/
        foreach ($this->references as $reference) {
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

        return str_replace('{{ hasManyRelations }}', $has_many, $this->stub);
    }
}
