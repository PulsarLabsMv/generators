<?php

namespace PulsarLabs\Generators\Features\Controllers\Commands;

use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\File;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Support\Processors\TableNameProcessor;
use PulsarLabs\Generators\Support\Processors\ModelVariableProcessor;
use PulsarLabs\Generators\Support\Processors\ModelClassNameProcessor;
use PulsarLabs\Generators\Support\Processors\RequestClassNameProcessor;
use PulsarLabs\Generators\Support\Processors\ParentRelationshipProcessor;
use PulsarLabs\Generators\Support\Processors\ModelPluralVariableProcessor;
use PulsarLabs\Generators\Support\Processors\ModelRouteParameterProcessor;
use PulsarLabs\Generators\Support\Processors\ParentModelVariableProcessor;
use PulsarLabs\Generators\Support\Processors\ModelClassPluralNameProcessor;
use PulsarLabs\Generators\Support\Processors\ParentModelClassNameProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\PivotSyncProcessor;
use PulsarLabs\Generators\Support\Processors\ModelPluralLowercaseSpacesProcessor;
use PulsarLabs\Generators\Support\Processors\ModelRoutePluralResourceNameProcessor;
use PulsarLabs\Generators\Support\Processors\ParentRoutePluralResourceNameProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\IncludedRelationshipsProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\QueryWhereHasManyRelationsProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\BelongsToRelationAssocationProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\QueryWhereBelongsToRelationsProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\OptionalBelongsToRelationAssocationProcessor;

class GenerateNestedControllerCommand extends Command
{
    protected $signature = 'generate:controller {table} {--parent=}';

    protected $description = 'Generate an admin controller';

    protected array $processors = [
        QueryWhereBelongsToRelationsProcessor::class,
        QueryWhereHasManyRelationsProcessor::class,
        IncludedRelationshipsProcessor::class,
        BelongsToRelationAssocationProcessor::class,
        OptionalBelongsToRelationAssocationProcessor::class,
        PivotSyncProcessor::class,
        ParentModelClassNameProcessor::class,
        ModelClassNameProcessor::class,
        ModelClassPluralNameProcessor::class,
        ModelPluralVariableProcessor::class,
        ModelPluralLowercaseSpacesProcessor::class,
        RequestClassNameProcessor::class,
        ParentModelVariableProcessor::class,
        ModelVariableProcessor::class,
        ModelRouteParameterProcessor::class,
        TableNameProcessor::class,
        ParentRoutePluralResourceNameProcessor::class,
        ModelRoutePluralResourceNameProcessor::class,
        IncludedRelationshipsProcessor::class,
        ParentRelationshipProcessor::class,
    ];

    public function handle(): void
    {
        $table_name = $this->argument('table');
        $parent_name = $this->option('parent');
        $stub = $this->getStub();

        $databaseReaderClass = config('generators.database_reader');
        $databaseReader = new $databaseReaderClass();

        $command_data = CommandData::fromArray([
            'stub_contents'   => $stub,
            'table_name'      => $table_name,
            'database_reader' => $databaseReader,
            'arguments'       => [
                'parent_name' => $parent_name,
            ],
        ]);

        $processed_command_data = app(Pipeline::class)
            ->send($command_data)
            ->through($this->processors)
            ->thenReturn();

        $file_path = $this->getTargetFilePath($parent_name, $table_name);

        file_put_contents($file_path, $processed_command_data->stub_contents);

        $this->info('Controller generated successfully');
    }

    protected function getStub(): string
    {
        return file_get_contents(__DIR__ . '/../stubs/nested_controller.stub');
    }

    private function getTargetFilePath(string $parent_name, string $table_name): string
    {
        $parent_class_name = str($parent_name)->studly()->singular();
        $controller_class_name = str($table_name)->studly()->plural();

        $namespace_folder = app_path('Http/Controllers/Admin');
        if (! File::exists($namespace_folder)) {
            File::makeDirectory($namespace_folder);
        }

        return app_path('Http/Controllers/Admin/' . $parent_class_name . $controller_class_name . 'Controller.php');
    }


}
