<?php

namespace PulsarLabs\Generators\Features\Controllers\Commands;

use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Storage;
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
use Illuminate\Support\Facades\File;

class GenerateControllerCommand extends Command
{
    protected $signature = 'generate:controller {table} {--parent=}';
    protected $description = 'Generates an admin controller';

    protected ?string $parent_name = null;

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
        $this->parent_name = $this->option('parent');
        $stub = $this->getStub();
        $databaseReaderClass = config('generators.database_reader');
        $databaseReader = new $databaseReaderClass();

        $command_data = CommandData::fromArray([
            'stub_contents'   => $stub,
            'table_name'      => $table_name,
            'database_reader' => $databaseReader,
            'arguments'       => $this->parent_name ? ['parent_name' => $this->parent_name] : [],
        ]);

        $processed_command_data = app(Pipeline::class)
            ->send($command_data)
            ->through($this->processors)
            ->thenReturn();

        $file_path = $this->getTargetFilePath($table_name);

        file_put_contents($file_path, $processed_command_data->stub_contents);

        $this->info('Controller generated successfully');

    }

    protected function getStub(): string
    {
        $stubFile = $this->parent_name ? '/../stubs/nested_controller.stub' : '/../stubs/controller.stub';
        return file_get_contents(__DIR__ . $stubFile);
    }

    private function getTargetFilePath(string $table_name): string
    {
        $model_class_name = str($table_name)->studly()->plural();
        $parent_class_name = $this->parent_name ? str($this->parent_name)->studly()->singular() : '';
        $controller_class_name = $parent_class_name . $model_class_name;

        $namespace_folder = app_path('Http/Controllers/Admin');
        if (! File::exists($namespace_folder)) {
            File::makeDirectory($namespace_folder);
        }

        return app_path('Http/Controllers/Admin/' . $controller_class_name . 'Controller.php');
    }
}
