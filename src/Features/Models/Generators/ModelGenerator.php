<?php

namespace PulsarLabs\Generators\Features\Models\Generators;

use Illuminate\Console\Command;
use PulsarLabs\Generators\Contracts\DatabaseReader;
use PulsarLabs\Generators\Support\Traits\HasGuardedProperties;
use PulsarLabs\Generators\Support\Traits\HasAttributesProperty;
use PulsarLabs\Generators\Features\Models\Updaters\ClassNameUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\CastsPropertyUpdater;
use PulsarLabs\Generators\Features\Models\Updaters\FillablePropertyUpdater;
use PulsarLabs\Generators\Support\GlobalUpdaters\RemovePlaceholdersUpdater;

class ModelGenerator
{
    use HasAttributesProperty;
    use HasGuardedProperties;

    protected DatabaseReader $databaseReader;
    protected array $placeholders = [
        '{{ imports }}',
        '{{ interfaces }}',
        '{{ traits }}',
        '{{ attributes }}',
        '{{ fillable }}',
        '{{ casts }}',
        '{{ relations }}',
        '{{ mutators }}',
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
        $stub = (new ClassNameUpdater($stub, $table_name))->handle();
        $stub = (new FillablePropertyUpdater($stub, $columns, $this->getGuardedProperties($columns)))->handle();
        $stub = (new CastsPropertyUpdater($stub, $columns))->handle();
        //        $stub = str_replace('{{attributes}}', $this->getAttributesProperty($columns), $stub);
        //        $stub = str_replace('{{relations}}', $this->getRelations($columns), $stub);
        //        $stub = str_replace('{{methods}}', $this->getMethods($columns), $stub);
        $stub = (new RemovePlaceholdersUpdater($stub, $this->placeholders))->handle();
        return $stub;
    }
}
