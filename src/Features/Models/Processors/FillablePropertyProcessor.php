<?php

namespace PulsarLabs\Generators\Features\Models\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\CommandData;

class FillablePropertyProcessor
{
    protected string $fillable_prefix = 'protected $fillable = [';
    protected string $fillable_suffix = "\t];";
    protected string $fillable = '';

    public function handle(CommandData $command_data, Closure $next)
    {
        $columns = $command_data->getColumnObjects();
        foreach ($columns as $column) {
            if (in_array($column->name, $command_data->getGuardedProperties())) {
                continue;
            }

            $this->fillable .= "\t\t'$column->name',\n";
        }

        $fillable = $this->fillable_prefix . "\n" . $this->fillable . $this->fillable_suffix;

        $command_data->stub_contents = str_replace('{{ Fillable }}', $fillable, $command_data->stub_contents);

        return $next($command_data);
    }
}
