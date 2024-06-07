<?php

namespace Abunooh\Generators\Tests\Feature\Commands;

use PulsarLabs\Generators\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenerateRequestCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->deleteFile($this->app->path('Http/Requests/CategoryRequest.php'));
    }

    protected function tearDown(): void
    {
        $this->deleteFile($this->app->path('Http/Requests/CategoryRequest.php'));
        parent::tearDown();
    }

    /** @test */
    public function it_can_generate_request_file(): void
    {
        $file_path = $this->app->path('Http/Requests/CategoryPolicy.php');
        $expected_output = $this->getTestStubContents('request.stub');

        $this->artisan('generate:request', ['table' => 'categories'])
            ->assertSuccessful();

        $this->assertFileExists($file_path);

        $actual_content = $this->getGeneratedFileContents($file_path);
        $actual_content = str_replace(["\r", "\n", "\t", " "], '', $actual_content);
        $expected_output = str_replace(["\r", "\n", "\t", " "], '', $expected_output);

        $this->assertEquals($expected_output, $actual_content);
    }

}
