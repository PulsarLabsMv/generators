<?php

namespace Abunooh\Generators\Features\Models\Updaters;

use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\Contracts\IsStubUpdater;

class HasManyRelationsUpdater implements IsStubUpdater
{

    public function __construct(
        protected string $stub,
        protected array $columns,
    ) {
    }

    public function handle(): string
    {
        $belongs_to_stub = file_get_contents(__DIR__ . '/../stubs/belongs_to.stub');
        $belongs_to = "";
        foreach ($this->columns as $column) {
            if (! $column->is_foreign_key) {
                continue;
            }

            $belongs_to .= "\n" . str_replace([
                '{{ method }}',
                '{{ model }}',
                '{{ foreign_key }}',
                '{{ owner_key }}',
            ], [
                $column->getRelationshipName(),
                $column->getReferencedModelName(),
                $column->getName(),
                $column->getReferencedColumnName(),
            ], $belongs_to_stub);
        }

        return str_replace('{{ relations }}', $belongs_to, $this->stub);
    }
}
