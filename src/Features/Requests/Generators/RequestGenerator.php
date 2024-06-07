<?php

namespace PulsarLabs\Generators\Features\Requests\Generators;

use Illuminate\Console\Command;
use PulsarLabs\Generators\Contracts\DatabaseReader;
use PulsarLabs\Generators\Features\Requests\Updaters\RulesUpdater;
use PulsarLabs\Generators\Features\Policies\Updaters\ModelVariableUpdater;

class RequestGenerator
{
    protected DatabaseReader $databaseReader;
    protected array $placeholders = [
        '{{ imports }}',
        '{{ methods }}',
    ];

    public function __construct()
    {
        $databaseReaderClass = config('generators.database_reader');
        $this->databaseReader = new $databaseReaderClass();
    }

    public function handle(Command $command, string $table_name): void
    {
        $stub = $this->getStub();
        $replacedStub = $this->updateStubContent($stub, $table_name);
        $request_file_name = str($table_name)->studly()->singular() . 'Request';
        $path = app_path('/Http/Requests');
        $file_path = $path . '/' . $request_file_name . '.php';

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        file_put_contents($file_path, $replacedStub);
        $command->info('Model generated successfully');
    }


    protected function getStub(): string
    {
        return file_get_contents(__DIR__ . '/../stubs/request.stub');
    }

    protected function updateStubContent(string $stub, string $table_name): string
    {
        $references = $this->databaseReader->getReferencingTableObjects($table_name);
        $columns = $this->databaseReader->getColumnObjects($table_name);
        $stub = (new ModelVariableUpdater($stub, $table_name))->handle();
        $stub = $this->updateRequestFileName($stub, $table_name);
        $stub = $this->updateModelRouteParameter($stub, $table_name);
        $stub = (new RulesUpdater($stub, $table_name, $columns))->handle();
        return $stub;
    }

    private function updateRequestFileName(string $stub, string $table_name): string
    {
        $request_file_name = str($table_name)->studly()->singular()->toString() . 'Request';
        return str_replace('{{ RequestFileName }}', $request_file_name, $stub);
    }

    private function updateModelRouteParameter(string $stub, string $table_name): string
    {
        $model_route_parameter = str($table_name)->slug('_')->singular()->toString();
        return str_replace('{{ ModelRouteParameter }}', $model_route_parameter, $stub);
    }
}
