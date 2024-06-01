<?php

namespace PulsarLabs\Generators\Features\Models\Updaters;

use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\Contracts\IsStubUpdater;

class ImportsUpdater implements IsStubUpdater
{

    public function __construct(
        protected string $stub,
        protected array $columns,
    ) {
    }

    public function handle(): string
    {
        $imports = "";

        // Check if there is a foreign key in the columns
        $has_foreign_key = collect($this->columns)->contains('is_foreign_key', true);

        if ($has_foreign_key) {
            $imports .= "use Illuminate\Database\Eloquent\Relations\BelongsTo;\n";
        }

        // Import status classes
        foreach ($this->columns as $column) {
            if ($column->getComment()) {
                $imports .= "use " . $column->getComment() . ";\n";
            }
        }

        return str_replace('{{ imports }}', $imports, $this->stub);
    }
}
