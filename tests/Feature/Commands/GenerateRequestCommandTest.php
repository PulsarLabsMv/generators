<?php

namespace PulsarLabs\Generators\Tests\Feature\Commands;

use PulsarLabs\Generators\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenerateRequestCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
//        $this->markTestIncomplete();
        parent::setUp();
        $this->deleteFile($this->app->path('Http/Requests/PostRequest.php'));
    }

    protected function tearDown(): void
    {
//        $this->deleteFile($this->app->path('Http/Requests/PostRequest.php'));
        parent::tearDown();
    }

    /** @test */
    public function it_can_generate_request_file(): void
    {
        $file_path = $this->app->path('Http/Requests/PostRequest.php');
        $expected_output = $this->getTestStubContents('Request.php');

        $this->artisan('generate:request', ['table' => 'posts'])
            ->assertSuccessful();

        $this->assertFileExists($file_path);

        $actual_content = $this->getGeneratedFileContents($file_path);
        $actual_content = str_replace(["\r", "\n", "\t", " "], '', $actual_content);
        $expected_output = str_replace(["\r", "\n", "\t", " "], '', $expected_output);

        $this->assertEquals($expected_output, $actual_content);
    }

}
