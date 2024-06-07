<?php

namespace PulsarLabs\Generators\Features\Requests\Commands;

use Illuminate\Console\Command;
use PulsarLabs\Generators\Features\Models\Generators\ModelGenerator;
use PulsarLabs\Generators\Features\Requests\Generators\RequestGenerator;

class GenerateRequestCommand extends Command
{
    protected $signature = 'generate:request {table}';

    protected $description = 'Generates a new request class.';


    public function handle(): void
    {
        $table_name = $this->argument('table');
        (new RequestGenerator())->handle($this, $table_name);
    }
}
