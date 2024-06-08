<?php

namespace PulsarLabs\Generators\DataObjects;

class CommandData
{

    public function __construct(
        public string $table_name,
        public string $stub_contents,
        public ?array $arguments = [],
    )
    {
    }

    public static function fromArray(array $data): CommandData
    {
        return new CommandData(
            $data['table_name'],
            $data['stub_contents'],
            $data['arguments'] ?? null,
        );
    }

}
