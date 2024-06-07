<?php

namespace PulsarLabs\Generators\Features\Policies\Updaters;

use PulsarLabs\Generators\Contracts\IsStubUpdater;

class ModelVariableUpdater implements IsStubUpdater
{
    public function __construct(
        protected string $stub,
        protected string $table_name,
    ) {
    }

    public function handle(): string
    {
        $model_variable_name = str($this->table_name)->lower()->slug('_')->singular();
        return str_replace('{{ ModelVariable }}', $model_variable_name, $this->stub);
    }
}
