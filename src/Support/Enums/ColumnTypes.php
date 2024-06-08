<?php

namespace PulsarLabs\Generators\Support\Enums;

enum ColumnTypes: string
{
    case BigInteger = 'bigInteger';
    case Boolean = 'boolean';
    case Date = 'date';
    case DateTime = 'dateTime';
    case Decimal = 'decimal';
    case Float = 'float';
    case Geography = 'geography';
    case Geometry = 'geometry';
    case Integer = 'integer';
    case Json = 'json';
    case String = 'string';
    case Text = 'text';
    case Time = 'time';
    case Timestamp = 'timestamp';
    case Uuid = 'uuid';


    public static function castTypes(): array
    {
        return [
            self::Boolean?->value  => 'boolean',
            self::Date?->value     => 'date',
            self::DateTime?->value => 'datetime',
            self::Decimal?->value  => 'decimal',
            self::Float?->value    => 'float',
            self::Integer?->value  => 'integer',
            self::Json?->value     => 'array',
        ];
    }

    public function isString(): bool
    {
        return in_array($this, [
            self::String,
            self::Text,
        ]);
    }

    public function isDate(): bool
    {
        return in_array($this, [
            self::Date,
            self::DateTime,
            self::Time,
            self::Timestamp,
        ]);
    }

    public function getCastType(): ?string
    {
        return self::castTypes()[$this->value] ?? null;
    }
}
