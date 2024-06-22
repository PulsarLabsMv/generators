<?php

namespace PulsarLabs\Generators\Contracts;

interface DatabaseReader
{
    public function listTables(): array;

    public function listColumns(string $table): array;

    public function listIndexes(string $table): array;

    public function getIndexObjects(string $table): array;

    public function getColumnObjects(string $table_name): array;

    public function getReferencingTableObjects(string $table): array;
}
