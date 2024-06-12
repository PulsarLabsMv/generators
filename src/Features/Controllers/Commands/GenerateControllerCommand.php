<?php

namespace PulsarLabs\Generators\Features\Controllers\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use PulsarLabs\Generators\DataObjects\ColumnData;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Support\Processors\ModelVariableProcessor;
use PulsarLabs\Generators\Support\Processors\ModelClassNameProcessor;
use PulsarLabs\Generators\Support\Processors\RequestClassNameProcessor;
use PulsarLabs\Generators\Support\Processors\ModelPluralVariableProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\ModelPluralLowercaseSpacesProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\QueryWhereHasManyRelationsProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\QueryWhereBelongsToRelationsProcessor;

class GenerateControllerCommand extends Command
{
    protected $signature = 'generate:controller {table}';
    protected $description = 'Generates an admin controller';

    protected array $processors = [
        QueryWhereBelongsToRelationsProcessor::class,
        QueryWhereHasManyRelationsProcessor::class,
        ModelClassNameProcessor::class,
        ModelPluralVariableProcessor::class,
        RequestClassNameProcessor::class,
        ModelVariableProcessor::class
    ];

    public function handle(): void
    {
        $table_name = $this->argument('table');
        // Namespace
        $namespace = $this->argument('namespace', null);
        // Scratch
        $databaseReaderClass = config('generators.database_reader');
        $databaseReader = new $databaseReaderClass();
        $command_data = CommandData::fromArray([
            'table_name'      => $table_name,
            'stub_contents'   => '',
            'database_reader' => $databaseReader,
        ]);

        $processed_command_data = app(Pipeline::class)
            ->send($command_data)
            ->through($this->processors)
            ->thenReturn();

        dd($processed_command_data->stub_contents);

        // main resource
        //        $namespace_folder_name = '\\' . Str::studly($namespace)  ?? '';
        //        $namespace_resource_class = Str::kebab($namespace);
        //        $controller_import = $namespace_folder_name ?
        //            'use App\Http\Controllers\Controller;' : '';
        //
        //        $class_name = str($command_data->table_name)->studly()->singular();
        //        $model_variable_name = str($command_data->table_name)->snake()->singular();
        //        $model_resource_name = str($command_data->table_name)->kebab()->plural();
        //        // Fill everything out
        //        $fillable = [];
        //        $columns = $command_data->getColumnObjects();
        //        // store:
        //        // guarded set
        //        $guarded_column_sets = '';
        //        $associate_relations = '';
        //        $column_set_stub = file_get_contents(__DIR__ . '/../stubs/column_set.stub');
        //        $nullable_associate_relations_stub = file_get_contents(__DIR__ . '/../stubs/nullable_associate_relations.stub');
        //        $associate_relations_stub = file_get_contents(__DIR__ . '/../stubs/associate_relations.stub');
        //
        //        $update_associate_sets = '';
        //        // ModelColumnSet
        //        /** @var ColumnData $column */
        //        foreach ($columns as $column) {
        //            if (in_array($column->name, $command_data->getGuardedProperties())) {
        //                $guarded_column_sets .= str_replace(
        //                    [
        //                        '{{ model_variable }}',
        //                        '{{ column_name }}',
        //                    ],
        //                    [
        //                        $model_variable_name,
        //                        $column->name,
        //                    ],
        //                    $column_set_stub
        //                );
        //            }
        //
        //            if ($column->is_foreign_key) {
        //                $associate_relations .= str_replace(
        //                    [
        //                        '{{ model_variable }}',
        //                        '{{ relationship }}',
        //                        '{{ column_name }}',
        //                    ],
        //                    [
        //                        $model_variable_name,
        //                        $column->getRelationshipName(),
        //                        $column->getName(),
        //                    ],
        //                    $column->isNullable() ?
        //                        $nullable_associate_relations_stub :
        //                        $associate_relations_stub
        //                );
        //
        //                $update_associate_sets .= str_replace(
        //                    [
        //                        '{{ model_variable }}',
        //                        '{{ relationship }}',
        //                        '{{ column_name }}',
        //                    ],
        //                    [
        //                        $model_variable_name,
        //                        $column->getRelationshipName(),
        //                        $column->getName(),
        //                    ],
        //                    $nullable_associate_relations_stub
        //                );
        //            }
        //
        //        }
        //
        //        $attach_stub = file_get_contents(__DIR__ . '/../stubs/pivot_relations.stub');
        //
        //        $store_stub = file_get_contents(__DIR__ . '/../stubs/store.stub');
        //        $store_method = str_replace([
        //            '{{ ModelClassName }}',
        //            '{{ ModelVariable }}',
        //            '{{ ModelColumnSet }}',
        //            '{{ ModelAssociateRelations }}',
        //            '{{ ModelResourceName }}',
        //        ], [
        //            $class_name,
        //            $model_variable_name,
        //            $guarded_column_sets,
        //            $associate_relations,
        //            $model_resource_name,
        //        ], $store_stub);
        //
        //        $update_stub = file_get_contents(__DIR__ . '/../stubs/update.stub');
        //        $update_method = str_replace([
        //            '{{ ModelClassName }}',
        //            '{{ ModelVariable }}',
        //            '{{ ModelColumnSet }}',
        //            '{{ ModelAssociateRelations }}',
        //            '{{ ModelResourceName }}',
        //        ], [
        //            $class_name,
        //            $model_variable_name,
        //            $guarded_column_sets,
        //            $update_associate_sets,
        //            $model_resource_name,
        //        ], $update_stub);
        //
        //        $controller_stub = file_get_contents(__DIR__ . '/../stubs/controller.stub');
        //
        //        $command_data->stub_contents = str_replace([
        //            '{{ ModelClassName }}',
        //            '{{ ModelVariableName }}',
        //            '{{ ModelPluralVariable }}',
        //            '{{ store_method }}',
        //            '{{ update_method }}',
        //            '{{ ModelResourceName }}',
        //            '{{ ModelSingularLowercaseSpaces }}',
        //            '{{ NamespaceClass }}',
        //            '{{ NamespaceResourceName }}',
        //            '{{ ControllerImport }}'
        //        ], [
        //            $class_name,
        //            $model_variable_name,
        //            Str::plural($model_variable_name),
        //            $store_method,
        //            $update_method,
        //            $model_resource_name,
        //            str($command_data->table_name)->lower()->replace('_', ' ')->singular()->toString(),
        //            $namespace_folder_name ?? '',
        //            $namespace_resource_class ? $namespace_resource_class .'.' : '',
        //            $controller_import
        //        ], $controller_stub);
        //        dd($command_data->stub_contents);
    }
}
