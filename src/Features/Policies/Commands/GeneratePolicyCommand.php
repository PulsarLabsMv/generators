<?php

namespace PulsarLabs\Generators\Features\Policies\Commands;

use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use PulsarLabs\Generators\DataObjects\CommandData;
use PulsarLabs\Generators\Features\Policies\Generators\PolicyGenerator;
use PulsarLabs\Generators\Features\Permissions\Generators\PermissionGenerator;
use PulsarLabs\Generators\Features\Policies\Processors\ModelVariableProcessor;
use PulsarLabs\Generators\Features\Policies\Processors\ModuleClassNameProcessor;
use PulsarLabs\Generators\Features\Policies\Processors\ModelPluralLowercaseSpacesProcessor;

class GeneratePolicyCommand extends Command
{
    protected $signature = 'generate:policy {table}';

    protected $description = 'Generates policies for the model.';

    protected array $processors = [
        ModuleClassNameProcessor::class,
        ModelVariableProcessor::class,
        ModelPluralLowercaseSpacesProcessor::class,
    ];

    public function handle(): void
    {
        $table_name = $this->argument('table');
        $stub = $this->getStub();

        $command_data = CommandData::fromArray(
            [
                'stub_contents' => $stub,
                'table_name' => $table_name,
            ]
        );

        $processed_command_data = app(Pipeline::class)
            ->send($command_data)
            ->through($this->processors)
            ->thenReturn();

        $file_path = $this->getTargetFilePath($table_name);

        file_put_contents($file_path, $processed_command_data->stub_contents);
        $this->info('Policy generated successfully');
    }

    private function getStub(): false|string
    {
        return file_get_contents(__DIR__ . '/../stubs/policy.stub');
    }

    private function getTargetFilename(string $table_name): string
    {
        return str($table_name)->studly()->singular() . 'Policy';
    }

    private function getTargetFilePath(string $table_name): string
    {
        $policy_classname = $this->getTargetFilename($table_name);
        $path = app_path('/Policies');
        $file_path = $path . '/' . $policy_classname . '.php';

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return $file_path;
    }
}
