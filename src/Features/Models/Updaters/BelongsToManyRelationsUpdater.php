<?php

namespace PulsarLabs\Generators\Features\Models\Updaters;

use Doctrine\DBAL\Schema\Column;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\Contracts\IsStubUpdater;
use PulsarLabs\Generators\Contracts\DatabaseReader;
use PulsarLabs\Generators\DataObjects\ReferencingTableData;

class BelongsToManyRelationsUpdater implements IsStubUpdater
{

    public function __construct(
        protected string $stub,
        protected array $references,
        public DatabaseReader $databaseReader
    ) {
    }

    public function handle(): string
    {
        $belongs_to_stub = file_get_contents(__DIR__ . '/../stubs/belongs_to_many.stub');
        $belongs_to_many = "";

        /* @var ReferencingTableData $reference*/
        foreach ($this->references as $reference) {
            if (! $reference->referencingTableIsPivot()) {
                continue;
            }

            $columns = $this->databaseReader->getColumnObjects($reference->getReferencingTableName());

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

        return str_replace('{{ manyToManyRelations }}', $belongs_to_many, $this->stub);
    }
}
