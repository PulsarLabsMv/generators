<?php

namespace PulsarLabs\Generators\Features\Permissions\Commands;

use Illuminate\Console\Command;
use PulsarLabs\Generators\Features\Models\Generators\ModelGenerator;
use PulsarLabs\Generators\Features\Permissions\Generators\PermissionGenerator;

class GeneratePermissionCommand extends Command
{
    protected $signature = 'generate:permissions {table}';

    protected $description = 'Generates permissions for the model.';


    public function handle(): void
    {
        $table_name = $this->argument('table');
        (new PermissionGenerator())->handle($this, $table_name);
    }
}
