<?php

namespace PulsarLabs\Generators\Features\Requests\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;

class RulesProcessor
{

    protected array $exclude_columns = ['id', 'created_at', 'updated_at'];

    public function handle(CommandData $command_data, Closure $next)
    {
        $columns = $command_data->getColumnObjects();
        $columns = $this->removeExcludeColumns($columns);
        $rules = '';

        foreach ($columns as $column) {
            $rules .= $this->getRules($column);
        }

        $rules = substr($rules, 0, -1);
        $rules = substr($rules, 3);

        $command_data->stub_contents = str_replace('{{ Rules }}', $rules, $command_data->stub_contents);

        return $next($command_data);
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
