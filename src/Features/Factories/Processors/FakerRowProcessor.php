<?php

namespace PulsarLabs\Generators\Features\Factories\Processors;

use Closure;
use PulsarLabs\Generators\DataObjects\IndexData;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Support\Enums\ColumnTypes;

class FakerRowProcessor
{
    public function handle(CommandData $command_data, Closure $next)
    {
        /** @var array<IndexData> $indexes */
        $indexes = $command_data->getIndexObjects();
        $faker_rows = "";

        $columns = $command_data->getColumnObjects();
        /** @var ColumnData $column */
        foreach ($columns as $column) {

            if (in_array($column->getName(), ['created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            // Retrieve an enum instance
            if ($column->isEnum()) {
                $faker_rows .= "'{$column->getName()}' => \$this->faker->randomElement(\\{$column->getEnum()}::class)->value,\n";
                continue;
            }

            // Create a factory instance
            if ($column->isForeignKey()) {
                $referencing_class = $column->getReferencedModelName();
                $faker_rows .= "'{$column->getName()}' => \\App\\Models\\{$referencing_class}::factory()->create(), \n";
                continue;
            }

            foreach ($indexes as $index) {
                if (count($index->getColumns()) !== 1) {
                    continue;
                }

                $column_name = $index->getColumns()[0];
                if ($column->getName() !== $column_name) {
                    continue;
                }

                if (! $index->isUnique()) {
                    continue;
                }

                // Skip primary indexes
                if ($index->isPrimary() && $column->autoIncrement()) {
                    continue 2;
                }

                $faker_rows .= "'{$column_name}' => \$this->faker->unique()->{$this->fakerMethods($column->getType())},\n";
                continue 2;
            }

            $faker_rows .= "'{$column->getName()}' => \$this->faker->{$this->fakerMethods($column->getType())},\n";
        }

        $command_data->stub_contents = str_replace('{{ FakerRows }}', $faker_rows, $command_data->stub_contents);
        return $next($command_data);
    }

    private function fakerMethods(ColumnTypes $type): string
    {
        return match ($type) {
            ColumnTypes::BigInteger => 'numberBetween()',
            ColumnTypes::Integer => 'numberBetween(1,10000)',
            ColumnTypes::Boolean => 'boolean()',
            ColumnTypes::Decimal => 'randomFloat()',
            ColumnTypes::Float => 'randomFloat()',
            ColumnTypes::Date => 'date()',
            ColumnTypes::DateTime => 'dateTime()',
            ColumnTypes::Time => 'unixTime()',
            ColumnTypes::Timestamp => 'dateTime()',
            ColumnTypes::String => 'lexify(\'?????????\')',
            ColumnTypes::Text => 'text()',
            ColumnTypes::Uuid => 'uuid()',
            ColumnTypes::Geography => throw new \Exception('To be implemented'),
            ColumnTypes::Geometry => throw new \Exception('To be implemented'),
            ColumnTypes::Json => throw new \Exception('To be implemented')
        };
    }
}
