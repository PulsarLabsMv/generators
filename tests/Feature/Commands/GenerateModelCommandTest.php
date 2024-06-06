<?php

namespace PulsarLabs\Generators\Tests\Feature\Commands;

use PulsarLabs\Generators\Tests\TestCase;

class GenerateModelCommandTest extends TestCase
{
    /** @test */
    public function it_can_generate_model_file(): void
    {
        $model_path = $this->app->path('Models/Category.php');

        $this->artisan('generate:model', ['table' => 'categories'])
            ->assertSuccessful();

        $this->assertFileExists($model_path);
    }

}
