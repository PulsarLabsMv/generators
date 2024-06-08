<?php

namespace PulsarLabs\Generators\Features\Requests\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Support\Enums\ColumnTypes;

class RulesProcessor
{

    protected array $exclude_columns = ['id', 'created_at', 'updated_at'];

    public function handle(CommandData $command_data, Closure $next)
    {
        $columns = $command_data->getColumnObjects();
        $columns = $this->removeExcludeColumns($columns);
        $table_references = $command_data->getReferencingTableObjects();
        $rules = '';

        foreach ($columns as $column) {
            $rules .= $this->getRules($column);
        }

        foreach ($table_references as $table_reference) {
            $referenced_table_columns = $command_data->database_reader->getColumnObjects($table_reference->getReferencingTableName());
            foreach ($referenced_table_columns as $referenced_table_column) {
                if ($referenced_table_column->isForeignKey()
                    && $referenced_table_column->getName() != $table_reference->getReferencingColumnName()) {
                    $rules .= $this->getBelongsToManyRules($referenced_table_column);
                }
            }
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
        if ($column->isForeignKey()) {
            return $this->getForeignKeyRules($column);
        }

        $column_name = $column->name;
        $column_rule = "\t\t\t'{$column_name}' => [";

        if ($column->isNullable()) {
            $column_rule .= "'nullable', ";
        }

        if ($column->getType()->isString()) {
            $column_rule .= "'string', ";
        }

        if ($column->getType() == ColumnTypes::String) {
            $column_rule .= "'max:255', ";
        }

        if ($column->getEnum()) {
            // get classname from string after last backslash
            $enum_class_name = substr($column->getEnum(), strrpos($column->getEnum(), '\\') + 1);
            $column_rule .= "Rule::enum($enum_class_name::class), ";
        }

        if ($column->getType()->isDate()) {
            $column_rule .= "'date', ";
        }

        $column_rule .= "],\n";
        return $column_rule;
    }

    private function getForeignKeyRules(ColumnData $column): string
    {
        $column_name = substr($column->name, 0, -3);
        $column_rule = "\t\t\t'{$column_name}' => [";

        if ($column->isNullable()) {
            $column_rule .= "'nullable', ";
        }

        $column_rule .= "'exists:{$column->getReferencedTableName()},{$column->getReferencedColumnName()}', ";

        $column_rule .= "],\n";
        return $column_rule;
    }

    private function getBelongsToManyRules(ColumnData $referenced_table_column): string
    {
        $rules = "\t\t\t'{$referenced_table_column->getReferencedTableName()}' => ['nullable', 'array',],\n";
        $rules .= "\t\t\t'{$referenced_table_column->getReferencedTableName()}.*' => [";
        $rules .= "'exists:{$referenced_table_column->getReferencedTableName()},{$referenced_table_column->getReferencedColumnName()}', ";
        $rules .= "],\n";

        return $rules;
    }
}
