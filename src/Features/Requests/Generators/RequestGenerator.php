<?php

namespace PulsarLabs\Generators\Features\Requests\Generators;

use Illuminate\Console\Command;
use PulsarLabs\Generators\Contracts\DatabaseReader;
use PulsarLabs\Generators\Support\Traits\HasGuardedProperties;
use PulsarLabs\Generators\Support\Traits\HasAttributesProperty;
use PulsarLabs\Generators\Features\Models\Updaters\ImportsUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\ClassNameUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\CastsPropertyUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\FillablePropertyUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\HasManyRelationsUpdater;
use PulsarLabs\Generators\Support\GlobalUpdaters\RemovePlaceholdersUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\BelongsToRelationsUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\BelongsToManyRelationsUpdater;

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
        $columns = $this->databaseReader->getColumnObjects($table_name);
        $stub = $this->getStub();
        $replacedStub = $this->updateStubContent($stub, $table_name, $columns);
        $model_class_name = str($table_name)->studly()->singular();
        $file_path = app_path('Models/' . $model_class_name . '.php');

        file_put_contents($file_path, $replacedStub);
        $command->info('Model generated successfully');
    }


    protected function getStub(): string
    {
        return file_get_contents(__DIR__ . '/../stubs/model.stub');
    }

    protected function updateStubContent(string $stub, string $table_name, array $columns): string
    {
        $references = $this->databaseReader->getReferencingTableObjects($table_name);
        $stub = (new ClassNameUpdater($stub, $table_name))->handle();
        $stub = (new FillablePropertyUpdater($stub, $columns, $this->getGuardedProperties($columns)))->handle();
        $stub = (new CastsPropertyUpdater($stub, $columns))->handle();
        $stub = (new BelongsToRelationsUpdater($stub, $columns))->handle();
        $stub = (new HasManyRelationsUpdater($stub, $references))->handle();
        $stub = (new BelongsToManyRelationsUpdater($stub, $references, $this->databaseReader))->handle();
        $stub = (new ImportsUpdater($stub, $columns, $references))->handle();
        $stub = (new RemovePlaceholdersUpdater($stub, $this->placeholders))->handle();
        return $stub;
    }
}