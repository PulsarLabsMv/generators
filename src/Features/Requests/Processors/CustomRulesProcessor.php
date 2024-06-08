<?php

namespace PulsarLabs\Generators\Features\Requests\Processors;

use Closure;
use PulsarLabs\Generators\Contracts\IsProcessor;
use PulsarLabs\Generators\DataObjects\IndexData;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Support\Enums\ColumnTypes;

class CustomRulesProcessor implements IsProcessor
{

    public function handle(CommandData $command_data, Closure $next): CommandData
    {
        $indexes = $command_data->getIndexObjects();

        $rules = "";
        /* @var IndexData $index */
        foreach ($indexes as $index) {
            if (
                $index->isUnique()
                && ! $index->isPrimary()
                && count($index->getColumns()) === 1
            ) {
                $column = $index->getColumns()[0];
                $rules .= "\t\t\t$" . $column . "_unique = Rule::unique('$command_data->table_name', '" . $column . "');\n";
            }
        }

        $rules = substr($rules, 3);

        $command_data->stub_contents = str_replace('{{ CustomRules }}', $rules, $command_data->stub_contents);

        return $next($command_data);
    }
}

