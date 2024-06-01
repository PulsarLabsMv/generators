<?php

namespace Abunooh\Generators\Features\Models\Commands;

use Illuminate\Console\Command;
use Abunooh\Generators\Features\Models\Generators\ModelGenerator;

class GenerateModelCommand extends Command
{
    protected $signature = 'generate:model {table}';

    protected $description = 'Generates a model';


    public function handle(): void
    {
        $table_name = $this->argument('table');
        (new ModelGenerator())->handle($this, $table_name);


    }
}
