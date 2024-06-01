<?php

namespace Abunooh\Generators\Features\Models\Updaters;

use Abunooh\Generators\Contracts\IsStubUpdater;

class ClassNameUpdater implements IsStubUpdater
{

    public function __construct(
        protected string $stub,
        protected string $table_name,
    )
    {
    }

    public function handle(): string
    {
        $class_name = str($this->table_name)->studly()->singular();
        return str_replace('{{ ModelClassName }}', $class_name, $this->stub);
    }
}
