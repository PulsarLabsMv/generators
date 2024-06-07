<?php

namespace PulsarLabs\Generators\Features\Policies\Updaters;

use PulsarLabs\Generators\Contracts\IsStubUpdater;

class ModelPluralLowercaseUpdater implements IsStubUpdater
{
    public function __construct(
        protected string $stub,
        protected string $table_name,
    ) {
    }

    public function handle(): string
    {
        $model_plural_lowercase = str($this->table_name)->lower()->replace('_', ' ')->plural()->toString();
        return str_replace('{{ model plural lowercase }}', $model_plural_lowercase, $this->stub);
    }
}
