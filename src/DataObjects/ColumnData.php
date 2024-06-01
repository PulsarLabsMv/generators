<?php

namespace Abunooh\Generators\DataObjects;

use Abunooh\Generators\Support\Enums\ColumnTypes;

class ColumnData
{

    public function __construct(
        public string      $name,
        public ColumnTypes $type,
        public ?string      $default,
        public bool        $nullable,
        public bool        $autoIncrement,
        public bool        $unsigned,
    )
    {
    }


}
