<?php

namespace PulsarLabs\Generators\Features\Models\Updaters;

use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\Contracts\IsStubUpdater;

class CastsPropertyUpdater implements IsStubUpdater
{
    protected string $casts_prefix = 'protected $casts = [';
    protected string $casts_suffix = "\t];";
    protected string $casts = '';

    public function __construct(
        protected string $stub,
        protected array $columns,
    ) {
    }

    public function handle(): string
    {
        $casts = "";

        /* @var ColumnData $column */
        foreach ($this->columns as $column) {
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

        return str_replace('{{ casts }}', $casts, $this->stub);
    }

    public function excludedColumns(): array
    {
        return [
            'created_at',
            'updated_at',
        ];
    }


}
