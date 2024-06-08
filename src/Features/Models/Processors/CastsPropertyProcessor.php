<?php

namespace PulsarLabs\Generators\Features\Models\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;

class CastsPropertyProcessor
{
    protected string $casts_prefix = 'protected $casts = [';
    protected string $casts_suffix = "\t];";
    protected string $casts = '';

    public function handle(CommandData $command_data, Closure $next)
    {
        $columns = $command_data->getColumnObjects();
        $casts = "";

        /* @var ColumnData $column */
        foreach ($columns as $column) {
            if (! $column->getComment() && in_array($column->getName(), $this->excludedColumns())) {
                continue;
            }

            $cast_type = $column->getType()->getCastType();

            if (!$column->getComment() && is_null($cast_type)) {
                continue;
            }

            $cast_type = "'$cast_type'";
            if ($column->getComment()) {
                $cast_type = $column->getStatusClassFromComment() . '::class';
            }

            $this->casts .= "\t\t'" . $column->getName() . "' => $cast_type,\n";
        }

        if ($this->casts) {
            $casts = "\n\n\t" . $this->casts_prefix . "\n" . $this->casts . $this->casts_suffix;
        }

        $command_data->stub_contents = str_replace('{{ casts }}', $casts, $command_data->stub_contents);

        return $next($command_data);
    }

    public function excludedColumns(): array
    {
        return [
            'created_at',
            'updated_at',
        ];
    }
}
