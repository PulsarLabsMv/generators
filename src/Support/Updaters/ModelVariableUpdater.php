<?php

namespace Abunooh\Generators\Support\Updaters;

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
        $model_variable = str($this->table_name)->singular()->slug('_')->toString();
        return str_replace('{{ ModelVariable }}', $model_variable, $this->stub);
    }
}
