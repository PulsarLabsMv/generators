<?php

namespace Abunooh\Generators\Support\Enums;

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


}
