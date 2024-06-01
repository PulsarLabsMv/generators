<?php

namespace PulsarLabs\Generators\DataObjects;

use PulsarLabs\Generators\Support\Enums\ColumnTypes;

class ColumnData
{
    public function __construct(
        public string      $name,
        public ColumnTypes $type,
        public ?string     $default,
        public bool        $nullable,
        public bool        $autoIncrement,
        public bool        $unsigned,
        public ?string      $comment,
        public bool        $is_foreign_key = false,
        public ?string     $referenced_table_name = null,
        public ?string     $referenced_column_name = null,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ColumnTypes
    {
        return $this->type;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
