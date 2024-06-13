<?php

namespace PulsarLabs\Generators\Features\Controllers\Commands;

use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Support\Processors\TableNameProcessor;
use PulsarLabs\Generators\Support\Processors\ModelVariableProcessor;
use PulsarLabs\Generators\Support\Processors\ModelClassNameProcessor;
use PulsarLabs\Generators\Support\Processors\RequestClassNameProcessor;
use PulsarLabs\Generators\Support\Processors\ModelPluralVariableProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\PivotSyncProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\QueryWhereHasManyRelationsProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\BelongsToRelationAssocationProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\QueryWhereBelongsToRelationsProcessor;
use PulsarLabs\Generators\Features\Controllers\Processors\OptionalBelongsToRelationAssocationProcessor;

class GenerateControllerCommand extends Command
{
    protected $signature = 'generate:controller {table}';
    protected $description = 'Generates an admin controller';

    protected array $processors = [
        QueryWhereBelongsToRelationsProcessor::class,
        QueryWhereHasManyRelationsProcessor::class,
        BelongsToRelationAssocationProcessor::class,
        OptionalBelongsToRelationAssocationProcessor::class,
        PivotSyncProcessor::class,
        ModelClassNameProcessor::class,
        ModelPluralVariableProcessor::class,
        RequestClassNameProcessor::class,
        ModelVariableProcessor::class,
        TableNameProcessor::class,
    ];

    public function handle(): void
    {
        $table_name = $this->argument('table');
        $stub = $this->getStub();

        $databaseReaderClass = config('generators.database_reader');
        $databaseReader = new $databaseReaderClass();

        $command_data = CommandData::fromArray([
            'stub_contents'   => $stub,
            'table_name'      => $table_name,
            'database_reader' => $databaseReader,
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
        return file_get_contents(__DIR__ . '/../stubs/controller.stub');
    }

    private function getTargetFilePath(string $table_name): string
    {
        $model_class_name = str($table_name)->studly()->singular();
        return app_path('Models/' . $model_class_name . '.php');
    }
}
