<?php

namespace PulsarLabs\Generators\Features\Requests\Commands;

use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Support\Processors\ModelVariableProcessor;
use PulsarLabs\Generators\Features\Requests\Processors\RulesProcessor;
use PulsarLabs\Generators\Support\Processors\RequestClassNameProcessor;
use PulsarLabs\Generators\Features\Requests\Generators\RequestGenerator;
use PulsarLabs\Generators\Support\Processors\ModelRouteParameterProcessor;
use PulsarLabs\Generators\Features\Requests\Processors\CustomRulesProcessor;

class GenerateRequestCommand extends Command
{
    protected $signature = 'generate:request {table}';

    protected $description = 'Generates a new request class.';

    protected array $processors = [
        ModelVariableProcessor::class,
        RequestClassNameProcessor::class,
        ModelRouteParameterProcessor::class,
        RulesProcessor::class,
        CustomRulesProcessor::class,
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
        ]);

        $processed_command_data = app(Pipeline::class)
            ->send($command_data)
            ->through($this->processors)
            ->thenReturn();

        $file_path = $this->getTargetFilePath($table_name);

        file_put_contents($file_path, $processed_command_data->stub_contents);
        $this->info('Request generated successfully');
    }

    private function getStub(): string
    {
        return file_get_contents(__DIR__ . '/../stubs/request.stub');
    }

    private function getTargetFilePath(string $table_name): string
    {
        $request_file_name = str($table_name)->studly()->singular() . 'Request';
        $path = app_path('/Http/Requests');
        $file_path = $path . '/' . $request_file_name . '.php';

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return $file_path;
    }
}
