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
        public ?string     $comment,
        public bool        $is_foreign_key = false,
        public ?string     $referenced_table_name = null,
        public ?string     $referenced_column_name = null,
    ) {
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

    public function isEnum(): bool
    {
        return str($this->getComment())->contains('enum:');
    }

    public function getEnum(): ?string
    {
        if (! $this->isEnum()) {
            return null;
        }

        return str($this->getComment())->after('enum:')->before(']')->trim()->toString();
    }

    public function isForeignKey(): bool
    {
        return $this->is_foreign_key;
    }

    public function autoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    public function getStatusClassFromComment(): false|string
    {
        $comment_parts = explode("\\", $this->getComment());
        return end($comment_parts);
    }

    public function getReferencedColumnName(): ?string
    {
        return $this->referenced_column_name;
    }

    public function getReferencedTableName(): ?string
    {
        return $this->referenced_table_name;
    }

    public function getReferencedModelName(): ?string
    {
        if ($this->referenced_table_name === null) {
            return null;
        }

        return str($this->referenced_table_name)->singular()->studly()->toString();
    }

    public function getRelationshipName(): ?string
    {
        if ($this->referenced_table_name === null) {
            return null;
        }

        return str($this->referenced_table_name)->singular()->camel()->toString();
    }

    public function getPluralRelationshipName(): ?string
    {
        if ($this->referenced_table_name === null) {
            return null;
        }

        return str($this->referenced_table_name)->plural()->camel()->toString();
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

}
