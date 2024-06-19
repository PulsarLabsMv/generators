<?php

namespace PulsarLabs\Generators\Tests\Feature\Commands;

use PulsarLabs\Generators\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @group generate.factory
 */
class GenerateFactoryCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->deleteFile($this->app->path('database/factories/PostFactory.php'));
    }

    protected function tearDown(): void
    {
        $this->deleteFile($this->app->path('database/factories/PostFactory.php'));
        parent::tearDown();
    }

    /** @test */
    public function it_can_generate_factory_file(): void
    {
        $model_path = base_path('database/factories/PostFactory.php');
        $expected_output = $this->getTestStubContents('Factory.php');

        $this->artisan('generate:factory', ['table' => 'posts'])
             ->assertSuccessful();
        $this->assertFileExists($model_path);

        $actual_content = $this->getGeneratedFileContents($model_path);

        $actual_content = str_replace(["\r", "\n", "\t", " "], '', $actual_content);
        $expected_output = str_replace(["\r", "\n", "\t", " "], '', $expected_output);

        $this->assertEquals($expected_output, $actual_content);
    }

}
