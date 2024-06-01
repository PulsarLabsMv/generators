<?php

namespace PulsarLabs\Generators;

use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        // declare publishes
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/generators.php' => config_path('generators.php'),
            ], 'generators-config');
        }

        $this->commands(config('generators.generators'));

    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/generators.php', 'generators');
    }
}
