<?php

namespace PulsarLabs\Generators;

use Illuminate\Console\Command;
use PulsarLabs\Generators\Features\Models\Generators\ModelGenerator;

class GenerateCrudCommand extends Command
{
    protected $signature = 'generate:crud {table}';

    protected $description = 'Generates crud for a given table';


    public function handle(): void
    {
        $table_name = $this->argument('table');
        $generators = config('generators.generators');
        foreach ($generators as $generator) {
            $this->call($generator, ['table' => $table_name]);
        }
    }
}
