<?php

namespace PulsarLabs\Generators\Tests\Feature\Commands;

use PulsarLabs\Generators\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @group generate.controllers
 */
class GenerateControllerCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->deleteFile($this->app->path('Http/Controllers/Admin/PostsController.php'));
    }

    protected function tearDown(): void
    {
        $this->deleteFile($this->app->path('Http/Controllers/Admin/PostsController.php'));
        parent::tearDown();
    }

    /**
     * @test
     * @group generate.controllers.primary
     */
    public function it_can_generate_controller_file(): void
    {
        $controller_path = $this->app->path('Http/Controllers/Admin/PostsController.php');
        $expected_output = $this->getTestStubContents('Controller.php');

        $this->artisan('generate:controller', ['table' => 'posts'])
            ->assertSuccessful();

        $this->assertFileExists($controller_path);

        $actual_content = $this->getGeneratedFileContents($controller_path);

        $actual_content = str_replace(["\r", "\n", "\t", " "], '', $actual_content);
        $expected_output = str_replace(["\r", "\n", "\t", " "], '', $expected_output);

        $this->assertEquals($expected_output, $actual_content);
    }

    /**
     * @test
     * @group generate.controllers.nested
     */
    public function it_can_generate_nested_controller_file()
    {
        $controller_path = $this->app->path('Http/Controllers/Admin/CategoryPostsController.php');
        $expected_output = $this->getTestStubContents('NestedController.php');

        $this->artisan('generate:controller', ['table' => 'posts', '--parent' => 'category'])
             ->assertSuccessful();

        $this->assertFileExists($controller_path);

        $actual_content = $this->getGeneratedFileContents($controller_path);

        $actual_content = str_replace(["\r", "\n", "\t", " "], '', $actual_content);
        $expected_output = str_replace(["\r", "\n", "\t", " "], '', $expected_output);

        $this->assertEquals($expected_output, $actual_content);
    }

}
