<?php

namespace PulsarLabs\Generators\Features\Models\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;

class BelongsToRelationsProcessor
{
    protected string $casts_prefix = 'protected $casts = [';
    protected string $casts_suffix = "\t];";
    protected string $casts = '';

    public function handle(CommandData $command_data, Closure $next)
    {
        $columns = $command_data->getColumnObjects();
        $belongs_to_stub = file_get_contents(__DIR__ . '/../stubs/belongs_to.stub');
        $belongs_to = "";
        foreach ($columns as $column) {
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

        $command_data->stub_contents = str_replace('{{ belongsToRelations }}', $belongs_to, $command_data->stub_contents);

        return $next($command_data);
    }
}
