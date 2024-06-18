<?php

namespace PulsarLabs\Generators\Features\Controllers\Commands;

use Illuminate\Console\Command;

class GenerateNestedControllerCommand extends Command
{
    protected $signature = 'generate:controller {table} --parent={parent}';

    protected $description = 'Generate an admin controller';

    public function handle(): void
    {
    }


}
