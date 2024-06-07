<?php

namespace PulsarLabs\Generators\Features\Policies\Generators;

use Illuminate\Console\Command;
use PulsarLabs\Generators\Support\Updaters\ModelClassNameUpdater;
use PulsarLabs\Generators\Features\Policies\Updaters\ModelVariableUpdater;
use PulsarLabs\Generators\Features\Policies\Updaters\ModelPluralLowercaseUpdater;

class PolicyGenerator
{
    public function handle(Command $command, string $table_name): void
    {
        $stub = $this->getStub();
        $replacedStub = $this->updateStubContent($stub, $table_name);
        $policy_classname = str($table_name)->studly()->singular() . 'Policy';

        $path = app_path('/Policies');
        $file_path = $path . '/' . $policy_classname . '.php';

        // Check if the directory exists
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        file_put_contents($file_path, $replacedStub);
        $command->info('Policy generated successfully');
    }

    private function getStub(): false|string
    {
        return file_get_contents(__DIR__ . '/../stubs/policy.stub');
    }

    private function updateStubContent(false|string $stub, string $table_name): string
    {
        $stub = (new ModelClassNameUpdater($stub, $table_name))->handle();
        $stub = (new ModelVariableUpdater($stub, $table_name))->handle();
        $stub = (new ModelPluralLowercaseUpdater($stub, $table_name))->handle();

        return $stub;
    }
}
