<?php

namespace PulsarLabs\Generators\Support\Traits;

trait HasGuardedProperties
{
    protected function getGuardedProperties(): array
    {
        return [
            'id',
            'created_at',
            'updated_at',
        ];
    }

}
