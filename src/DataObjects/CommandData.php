<?php

namespace PulsarLabs\Generators\DataObjects;

use PulsarLabs\Generators\Contracts\DatabaseReader;

class CommandData
{

    public function __construct(
        public string $table_name,
        public string $stub_contents,
        public ?array $arguments = [],
        public ?DatabaseReader $database_reader = null,
        public array $guarded_properties = [],
    )
    {
    }

    public static function fromArray(array $data): CommandData
    {
        return new CommandData(
            $data['table_name'],
            $data['stub_contents'],
            $data['arguments'] ?? null,
            $data['database_reader'] ?? null,
            $data['guarded_properties'] ?? [],
        );
    }

    public function getColumnObjects(): array
    {
        return $this->database_reader->getColumnObjects($this->table_name);
    }

    public function getIndexObjects(): array
    {
        return $this->database_reader->getIndexObjects($this->table_name);
    }

    public function getReferencingTableObjects(): array
    {
        return $this->database_reader->getReferencingTableObjects($this->table_name);
    }

    public function getGuardedProperties(): array
    {
        return $this->guarded_properties;
    }

}
