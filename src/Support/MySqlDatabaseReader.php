<?php

namespace PulsarLabs\Generators\Support;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\GuidType;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\DBAL\Types\TimeType;
use Doctrine\DBAL\Types\FloatType;
use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\DateTimeType;
use Illuminate\Support\Facades\Schema;
use PulsarLabs\Generators\DataObjects\IndexData;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\Contracts\DatabaseReader;
use PulsarLabs\Generators\Support\Enums\ColumnTypes;
use PulsarLabs\Generators\DataObjects\ReferencingTableData;
use PulsarLabs\Generators\Exceptions\InvalidTableException;

class MySqlDatabaseReader implements DatabaseReader
{
    protected Connection $connection;

    public function __construct(
        protected ?string $schema = null,
    )
    {
        $this->connection = DriverManager::getConnection($this->getConnectionParameters());
        $this->schema = $schema ?? config('database.connections.mysql.database');
    }

    protected function getConnectionParameters(): array
    {
        return [
            'dbname'   => config('database.connections.mysql.database'),
            'user'     => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
            'host'     => config('database.connections.mysql.host'),
            'driver'   => 'pdo_mysql',
        ];
    }

    /**
     * @throws InvalidTableException
     * @throws Exception
     */
    public function listColumns(string $table): array
    {
        if (! Schema::hasTable($table)) {
            throw new InvalidTableException($table);
        }
        $schemaManager = $this->connection->createSchemaManager();
        return $schemaManager->listTableColumns($table);
    }

    protected function getColumnDataObject($column, array $foreign_keys): ColumnData
    {
        $foreign_key_column_names = array_column($foreign_keys, 'COLUMN_NAME');
        $is_foreign_key = in_array($column->getName(), $foreign_key_column_names);
        $referenced_table_name = null;
        $referenced_column_name = null;

        if ($is_foreign_key) {
            $foreign_key = $foreign_keys[array_search($column->getName(), $foreign_key_column_names)];
            $referenced_table_name = $foreign_key->REFERENCED_TABLE_NAME;
            $referenced_column_name = $foreign_key->REFERENCED_COLUMN_NAME;
        }

        /* @var Column $column */
        return new ColumnData(
            $column->getName(),
            $this->getColumnType($column->getType()),
            $column->getDefault(),
            ! $column->getNotnull(),
            $column->getAutoincrement(),
            $column->getUnsigned(),
            $column->getComment(),
            $is_foreign_key,
            $referenced_table_name,
            $referenced_column_name
        );
    }

    /**
     * @throws InvalidTableException
     * @throws Exception
     */
    public function getColumnObjects(string $table_name): array
    {
        $columns = $this->listColumns($table_name);
        $columnObjects = [];
        $foreign_keys = $this->getForeignKeys($table_name);

        /* @var Column $column */
        foreach ($columns as $column) {
            $columnObjects[] = $this->getColumnDataObject($column, $foreign_keys);
        }

        return $columnObjects;
    }

    /**
     * @throws InvalidTableException
     */
    public function listIndexes(string $table): array
    {
        if (! Schema::hasTable($table)) {
            throw new InvalidTableException($table);
        }

        try {
            $schemaManager = $this->connection->createSchemaManager();
        } catch (Exception $e) {
            throw new InvalidTableException($table);
        }

        try {
            return $schemaManager->listTableIndexes($table);
        } catch (Exception $e) {
            throw new InvalidTableException($table);
        }
    }


    public function getIndexObjects(string $table): array
    {
        try {
            $indexes = $this->listIndexes($table);
        } catch (InvalidTableException $e) {
            return [];
        }

        $indexObjects = [];

        foreach ($indexes as $index) {
            $indexObjects[] = IndexData::fromDoctrineIndex($index);
        }

        return $indexObjects;
    }


    protected function getForeignKeys(string $table): array
    {
        $foreignKeys = DB::select(
            "
            SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL AND CONSTRAINT_SCHEMA = ?",
            [$table, $this->schema]
        );

        return $foreignKeys;
    }

    protected function getReferencingTables(string $table): array
    {
        $referencingTables = DB::select("SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_COLUMN_NAME
                                          FROM information_schema.KEY_COLUMN_USAGE
                                          WHERE REFERENCED_TABLE_NAME = ? AND CONSTRAINT_SCHEMA = ?", [$table, $this->schema]);

        return $referencingTables;
    }

    protected function getReferencingTableData($referencing_table): ReferencingTableData
    {
        return new ReferencingTableData(
            $referencing_table->TABLE_NAME,
            $referencing_table->COLUMN_NAME,
            $referencing_table->CONSTRAINT_NAME,
            $referencing_table->REFERENCED_COLUMN_NAME
        );
    }

    public function getReferencingTableObjects(string $table): array
    {
        $referencingTables = [];
        foreach ($this->getReferencingTables($table) as $referencing_table) {
            $referencingTables[] = $this->getReferencingTableData($referencing_table);
        }

        return $referencingTables;
    }

    protected function getColumnType($type): ColumnTypes
    {
        $type_mappings = [
            BigIntType::class   => ColumnTypes::BigInteger,
            BooleanType::class  => ColumnTypes::Boolean,
            DateType::class     => ColumnTypes::Date,
            DateTimeType::class => ColumnTypes::DateTime,
            DecimalType::class  => ColumnTypes::Decimal,
            FloatType::class    => ColumnTypes::Float,
            IntegerType::class  => ColumnTypes::Integer,
            JsonType::class     => ColumnTypes::Json,
            StringType::class   => ColumnTypes::String,
            TextType::class     => ColumnTypes::Text,
            TimeType::class     => ColumnTypes::Time,
            GuidType::class     => ColumnTypes::Uuid,
        ];

        foreach ($type_mappings as $doctrine_type => $column_type) {
            if ($type instanceof $doctrine_type) {
                return $column_type;
            }
        }

        return ColumnTypes::String;
    }
}
