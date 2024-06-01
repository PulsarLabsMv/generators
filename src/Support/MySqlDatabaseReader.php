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
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\Contracts\DatabaseReader;
use PulsarLabs\Generators\Support\Enums\ColumnTypes;
use PulsarLabs\Generators\Exceptions\InvalidTableException;

class MySqlDatabaseReader implements DatabaseReader
{
    protected Connection $connection;

    public function __construct()
    {
        $this->connection = DriverManager::getConnection($this->getConnectionParameters());
    }

    public function getConnectionParameters(): array
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

    public function getColumnDataObject($column): ColumnData
    {
        /* @var Column $column */
        return new ColumnData(
            $column->getName(),
            $this->getColumnType($column->getType()),
            $column->getDefault(),
            ! $column->getNotnull(),
            $column->getAutoincrement(),
            $column->getUnsigned(),
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
        /* @var Column $column */
        foreach ($columns as $column) {
            $columnObjects[] = $this->getColumnDataObject($column);
        }

        return $columnObjects;
    }

    public function getForeignKeys(string $table)
    {
        $foreignKeys = DB::select(
            "
            SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$table]
        );

        dd($foreignKeys);
        if (empty($foreignKeys)) {
            return [];
        }

        foreach ($foreignKeys as $foreignKey) {
            $this->info("Constraint: {$foreignKey->CONSTRAINT_NAME}, Column: {$foreignKey->COLUMN_NAME},
                         Referenced Table: {$foreignKey->REFERENCED_TABLE_NAME},
                         Referenced Column: {$foreignKey->REFERENCED_COLUMN_NAME}");
        }
    }

    public function getReferencingTables(string $table, string $schema = null)
    {
        $schema ??= config('database.connections.mysql.database');

        $referencingTables = DB::select("SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_COLUMN_NAME
                                          FROM information_schema.KEY_COLUMN_USAGE
                                          WHERE REFERENCED_TABLE_NAME = ? AND CONSTRAINT_SCHEMA = ?", [$table, $schema]);

        dd($referencingTables);
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
