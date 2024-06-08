<?php

namespace PulsarLabs\Generators\Features\Requests\Processors;

use Closure;
use Doctrine\DBAL\Schema\Column;
use PulsarLabs\Generators\Contracts\IsProcessor;
use PulsarLabs\Generators\DataObjects\IndexData;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Support\Enums\ColumnTypes;

class ImportsProcessor implements IsProcessor
{
    public function handle(CommandData $command_data, Closure $next): CommandData
    {
        $columns = $command_data->getColumnObjects();
        $imports = "";
        $has_enum = false;

        /* @var ColumnData $column */
        foreach ($columns as $column) {
            if ($column->getEnum()) {
                $has_enum = true;
                $imports .= "use " . $column->getEnum() . ";\n";
            }
        }

        if ($has_enum) {
            $imports .= "use Illuminate\Validation\Rule;\n";
        }

        $command_data->stub_contents = str_replace('{{ Imports }}', $imports, $command_data->stub_contents);

        return $next($command_data);
    }
}
