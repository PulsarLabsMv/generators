<?php

namespace PulsarLabs\Generators\Features\Models\Updaters;

use PulsarLabs\Generators\Contracts\IsStubUpdater;

class FillablePropertyUpdater implements IsStubUpdater
{
    protected string $fillable_prefix = 'protected $fillable = [';
    protected string $fillable_suffix = "\t];";
    protected string $fillable = '';

    public function __construct(
        protected string $stub,
        protected array $columns,
        protected array $guarded_properties,
    ) {

    }

    public function handle(): string
    {
        foreach ($this->columns as $column) {
            if (in_array($column->name, $this->guarded_properties)) {
                continue;
            }

            $this->fillable .= "\t\t'$column->name',\n";
        }

        $fillable = $this->fillable_prefix . "\n" . $this->fillable . $this->fillable_suffix;

        return str_replace('{{fillable}}', $fillable, $this->stub);
    }
}
