<?php

namespace PulsarLabs\Generators\Support\Traits;

use PulsarLabs\Generators\DataObjects\ColumnData;

trait HasGuardedProperties
{
    protected function getGuardedProperties(array $columns): array
    {
        $default = [
            'id',
            'created_at',
            'updated_at',
        ];

        // get all foreign keys
        $foreign_keys = array_filter($columns, fn (ColumnData $column) => $column->is_foreign_key);

        return array_merge($default, array_column($foreign_keys, 'name'));
    }

}
