<?php

namespace Abunooh\Generators\Support\GlobalUpdaters;

use Abunooh\Generators\Contracts\IsStubUpdater;

class RemovePlaceholdersUpdater implements IsStubUpdater
{

    public function __construct(
        protected string $stub,
        protected array $placeholders,
    )
    {
    }

    public function handle(): string
    {
        foreach ($this->placeholders as $placeholder) {
            $this->stub = str_replace($placeholder, '', $this->stub);
        }

        // Remove empty lines
        $pattern = '/(\n\s*\n)(?=\s*\})/';
        return preg_replace($pattern, "\n", $this->stub);
    }
}
