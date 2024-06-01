<?php

namespace Abunooh\Generators\Contracts;

use Abunooh\Generators\DataObjects\ColumnData;

interface DatabaseReader
{

    public function listColumns(string $table): array;

    public function getColumnDataObject($column): ColumnData;

    public function getColumnObjects(string $table_name): array;
}
