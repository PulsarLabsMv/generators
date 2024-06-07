<?php

namespace PulsarLabs\Generators\Features\Requests\Updaters;

use PulsarLabs\Generators\DataObjects\ColumnData;

class RulesUpdater
{

    protected array $exclude_columns = ['id', 'created_at', 'updated_at'];
    protected string $rules = '';

    public function __construct(
        protected string $stub,
        protected string $table_name,
        protected array $columns
    ) {
    }

    public function handle(): string
    {
        // remove exclude columns
        $columns = $this->removeExcludeColumns($this->columns);

        dd($columns);
    }

    private function removeExcludeColumns(array $columns): array
    {
        return array_filter($columns, function (ColumnData $column) {
            return ! in_array($column->name, $this->exclude_columns);
        });
    }
}
