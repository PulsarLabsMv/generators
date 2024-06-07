<?php

namespace PulsarLabs\Generators\Features\Policies\Generators;

use Illuminate\Console\Command;
use PulsarLabs\Generators\Features\Models\Updaters\ClassNameUpdater;
use PulsarLabs\Generators\Features\Policies\Updaters\ModelVariableUpdater;
use PulsarLabs\Generators\Features\Policies\Updaters\ModelPluralLowercaseUpdater;

class PolicyGenerator
{
    public function handle(Command $command, string $table_name): void
    {
        $stub = $this->getStub();
        $replacedStub = $this->updateStubContent($stub, $table_name);
        $policy_classname = str($table_name)->studly()->singular() . 'Policy';
        $file_path = app_path('Policies/' . $policy_classname . '.php');

        file_put_contents($file_path, $replacedStub);
        $command->info('Policy generated successfully');
    }

    private function getStub(): false|string
    {
        return file_get_contents(__DIR__ . '/../stubs/policy.stub');
    }

    private function updateStubContent(false|string $stub, string $table_name): string
    {
        $stub = (new ClassNameUpdater($stub, $table_name))->handle();
        $stub = (new ModelVariableUpdater($stub, $table_name))->handle();
        $stub = (new ModelPluralLowercaseUpdater($stub, $table_name))->handle();

        return $stub;
    }
}
