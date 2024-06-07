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

        foreach ($columns as $column) {
            $this->rules .= $this->getRules($column);
        }

        $this->rules = substr($this->rules, 0, -1);
        $this->rules = substr($this->rules, 3);

        return str_replace('{{ Rules }}', $this->rules, $this->stub);
    }

    private function removeExcludeColumns(array $columns): array
    {
        return array_filter($columns, function (ColumnData $column) {
            return ! in_array($column->name, $this->exclude_columns);
        });
    }

    private function getRules(ColumnData $column): string
    {
        return "\t\t\t'{$column->name}' => ['required', 'string'],\n";
    }
}
