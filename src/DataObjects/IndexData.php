<?php

namespace PulsarLabs\Generators\DataObjects;

use Doctrine\DBAL\Schema\Index;
use PulsarLabs\Generators\Support\Enums\ColumnTypes;

readonly class IndexData
{

    public function __construct(
        private string $name,
        private array  $columns,
        private bool   $is_unique,
        private bool   $is_primary,
        private array  $flags,
        private array  $options,
        private ?string $namespace,
    )
    {
    }

    public static function fromDoctrineIndex(Index $index): IndexData
    {
        return new self(
            $index->getName(),
            $index->getColumns(),
            $index->isUnique(),
            $index->isPrimary(),
            $index->getFlags(),
            $index->getOptions(),
            $index->getNamespaceName(),
        );
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function isUnique(): bool
    {
        return $this->is_unique;
    }

    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    public function getFlags(): array
    {
        return $this->flags;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
