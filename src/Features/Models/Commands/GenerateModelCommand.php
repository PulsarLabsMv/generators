<?php

namespace PulsarLabs\Generators\Features\Models\Commands;

use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Support\Traits\HasGuardedProperties;
use PulsarLabs\Generators\Features\Models\Processors\ImportsProcessor;
use PulsarLabs\Generators\Support\Processors\ModelClassNameProcessor;
use PulsarLabs\Generators\Features\Models\Processors\CastsPropertyProcessor;
use PulsarLabs\Generators\Features\Models\Processors\FillablePropertyProcessor;
use PulsarLabs\Generators\Features\Models\Processors\HasManyRelationsProcessor;
use PulsarLabs\Generators\Features\Models\Processors\BelongsToRelationsProcessor;
use PulsarLabs\Generators\Features\Models\Processors\BelongsToManyRelationsProcessor;

class GenerateModelCommand extends Command
{
    use HasGuardedProperties;

    protected $signature = 'generate:model {table}';

    protected $description = 'Generates a model';

    protected array $processors = [
        ModelClassNameProcessor::class,
        FillablePropertyProcessor::class,
        CastsPropertyProcessor::class,
        BelongsToRelationsProcessor::class,
        HasManyRelationsProcessor::class,
        BelongsToManyRelationsProcessor::class,
        ImportsProcessor::class,
    ];

    public function handle(): void
    {
        $table_name = $this->argument('table');
        $stub = $this->getStub();

        $databaseReaderClass = config('generators.database_reader');
        $databaseReader = new $databaseReaderClass();

        $command_data = CommandData::fromArray([
            'stub_contents'      => $stub,
            'table_name'         => $table_name,
            'database_reader'    => $databaseReader,
            'guarded_properties' => $this->getGuardedProperties($databaseReader->getColumnObjects($table_name)),
        ]);

        $processed_command_data = app(Pipeline::class)
            ->send($command_data)
            ->through($this->processors)
            ->thenReturn();

        $file_path = $this->getTargetFilePath($table_name);

        file_put_contents($file_path, $processed_command_data->stub_contents);
        $this->info('Model generated successfully');
    }

    protected function getStub(): string
    {
        return file_get_contents(__DIR__ . '/../stubs/model.stub');
    }

    private function getTargetFilePath(string $table_name): string
    {
        $model_class_name = str($table_name)->studly()->singular();
        return app_path('Models/' . $model_class_name . '.php');
    }
}
