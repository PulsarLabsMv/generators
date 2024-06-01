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
        public bool        $is_foreign_key = false,
        public ?string     $referenced_table_name = null,
        public ?string     $referenced_column_name = null,
    )
    {
    }
}
