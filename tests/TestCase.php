<?php

namespace PulsarLabs\Generators\Tests;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PulsarLabs\Generators\GeneratorsServiceProvider;
use PulsarLabs\Generators\Tests\TestSupport\Providers\TestServiceProvider;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('app.key', 'base64:yWa/ByhLC/GUvfToOuaPD7zDwB64qkc/QkaQOrT5IpE=');

        $this->app['config']->set('session.serialization', 'php');

        $this->app['config']->set('database.default', 'mysql');

        $this->app['config']->set('database.connections.mysql', [
            'driver'   => 'mysql',
            'database' => env('DB_DATABASE'),
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '3306'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD', ''),
            'prefix'   => '',
        ]);

    }

    protected function getPackageProviders($app): array
    {
        return [
            GeneratorsServiceProvider::class,
            TestServiceProvider::class,
        ];
    }

    protected function deleteFile(string $path): void
    {
        /** @var Filesystem $files */
        $files = $this->app->make(Filesystem::class);
        $files->delete($path);
    }

    protected function getTestStubPath(string $name): string
    {
        return __DIR__ . '/stubs/' . $name;
    }

    protected function getTestStubContents(string $name): string
    {
        return file_get_contents($this->getTestStubPath($name));
    }

    protected function getGeneratedFileContents(string $file): string
    {
        return file_get_contents($file);
    }
}
