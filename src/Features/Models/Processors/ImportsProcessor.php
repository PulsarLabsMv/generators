<?php

namespace PulsarLabs\Generators\Features\Models\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

class ImportsProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        $references = $command_data->getReferencingTableObjects();
        $columns = $command_data->getColumnObjects();

        $imports = "";

        // Check if there is a foreign key in the columns
        $has_foreign_key = collect($columns)->contains('is_foreign_key', true);

        if ($has_foreign_key) {
            $imports .= "use Illuminate\Database\Eloquent\Relations\BelongsTo;\n";
        }

        if (count($references) > 0) {
            $imports .= "use Illuminate\Database\Eloquent\Relations\HasMany;\n";
        }

        $has_belong_to_many = collect($references)->filter(function ($reference) {
            return $reference->referencingTableIsPivot();
        })->count() > 0;

        if ($has_belong_to_many) {
            $imports .= "use Illuminate\Database\Eloquent\Relations\BelongsToMany;\n";
        }

        // Import status classes
        foreach ($columns as $column) {
            if ($column->getComment()) {
                $imports .= "use " . $column->getComment() . ";\n";
            }
        }

        $command_data->stub_contents = str_replace('{{ imports }}', $imports, $command_data->stub_contents);

        return $next($command_data);
    }
}
