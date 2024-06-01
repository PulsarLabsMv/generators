<?php

namespace PulsarLabs\Generators\DataObjects;


class ReferencingTableData
{
    public function __construct(
        public string $referencing_table_name,
        public string $referencing_column_name,
        public string $constraint_name,
        public string $local_key,
    ) {
    }

    public function getMethodName(): string
    {
        return str($this->referencing_table_name)->camel()->plural()->toString();
    }

    public function getModelName(): string
    {
        return str($this->referencing_table_name)->studly()->singular()->toString();
    }

    public function getReferencingColumnName(): string
    {
        return $this->referencing_column_name;
    }

    public function getLocalKey(): string
    {
        return $this->local_key;
    }
}
