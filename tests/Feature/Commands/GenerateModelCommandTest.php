<?php

namespace PulsarLabs\Generators\Tests\Feature\Commands;

use PulsarLabs\Generators\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenerateModelCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->deleteFile($this->app->path('Models/Category.php'));
    }

    protected function tearDown(): void
    {
        $this->deleteFile($this->app->path('Models/Category.php'));
        parent::tearDown();
    }

    /** @test */
    public function it_can_generate_model_file(): void
    {
        $model_path = $this->app->path('Models/Category.php');
        $expected_output = $this->getTestStubContents('Category.php');

        $this->artisan('generate:model', ['table' => 'categories'])
            ->assertSuccessful();

        $this->assertFileExists($model_path);

        $actual_content = $this->getGeneratedFileContents($model_path);

        $actual_content = str_replace(["\r", "\n", "\t", " "], '', $actual_content);
        $expected_output = str_replace(["\r", "\n", "\t", " "], '', $expected_output);

        $this->assertEquals($expected_output, $actual_content);
    }

}
