<?php

namespace PulsarLabs\Generators\Tests\Feature\Commands;

use PulsarLabs\Generators\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenerateModelCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_generate_model_file(): void
    {
        $model_path = $this->app->path('Models/Category.php');

        $this->artisan('generate:model', ['table' => 'categories'])
            ->assertSuccessful();

        $this->assertFileExists($model_path);
    }

}
