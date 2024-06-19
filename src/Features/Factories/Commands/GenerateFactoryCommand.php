<?php

namespace PulsarLabs\Generators\Features\Factories\Commands;

use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\File;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Contracts\DatabaseReader;
use PulsarLabs\Generators\Contracts\GeneratorCommand;
use PulsarLabs\Generators\Support\Processors\ModelClassNameProcessor;
use PulsarLabs\Generators\Features\Factories\Processors\FakerRowProcessor;

class GenerateFactoryCommand extends Command
{
    protected $signature = 'generate:factory {table}';

    protected $description = 'Generates a factory';

    protected array $processors = [
        ModelClassNameProcessor::class,
        FakerRowProcessor::class
    ];

    public function handle(): void
    {
        $table_name = $this->argument('table');
        $stub = $this->getStub();

        $databaseReaderClass = config('generators.database_reader');
        /** @var DatabaseReader $databaseReader */
        $databaseReader = new $databaseReaderClass();

        $command_data = CommandData::fromArray([
            'stub_contents'      => $stub,
            'table_name'         => $table_name,
            'database_reader'    => $databaseReader,
        ]);

        $processed_command_data = app(Pipeline::class)
            ->send($command_data)
            ->through($this->processors)
            ->thenReturn();

        $file_path = $this->getTargetFilePath($table_name);

        file_put_contents($file_path, $processed_command_data->stub_contents);
        $this->info('Factory generated successfully');
    }

    public function getStub(): string
    {
        return file_get_contents(__DIR__ . '/../stubs/factory.stub');
    }

    private function getTargetFilePath(string $table_name): string
    {
        $model_class_name = str($table_name)->studly()->singular();
        $namespace_folder = base_path('database/factories');
        if (! File::exists($namespace_folder)) {
            File::makeDirectory($namespace_folder);
        }
        return base_path('database/factories/' . $model_class_name . 'Factory.php');
    }
}
