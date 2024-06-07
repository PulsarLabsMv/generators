<?php

namespace PulsarLabs\Generators\Features\Policies\Commands;

use Illuminate\Console\Command;
use PulsarLabs\Generators\Features\Policies\Generators\PolicyGenerator;
use PulsarLabs\Generators\Features\Permissions\Generators\PermissionGenerator;

class GeneratePolicyCommand extends Command
{
    protected $signature = 'generate:policy {table}';

    protected $description = 'Generates policies for the model.';


    public function handle(): void
    {
        $table_name = $this->argument('table');
        (new PolicyGenerator())->handle($this, $table_name);
    }
}
