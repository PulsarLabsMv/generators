<?php

namespace PulsarLabs\Generators\Features\Models\Updaters;

use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\Contracts\IsStubUpdater;

class ImportsUpdater implements IsStubUpdater
{

    public function __construct(
        protected string $stub,
        protected array $columns,
        protected array $references,
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

        if (count($this->references) > 0) {
            $imports .= "use Illuminate\Database\Eloquent\Relations\HasMany;\n";
        }

        $has_belong_to_many = collect($this->references)->filter(function ($reference) {
            return $reference->referencingTableIsPivot();
        })->count() > 0;

        if ($has_belong_to_many) {
            $imports .= "use Illuminate\Database\Eloquent\Relations\BelongsToMany;\n";
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
