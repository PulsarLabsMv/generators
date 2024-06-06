<?php

namespace Abunooh\Generators\Tests\Feature\Commands;

use PulsarLabs\Generators\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GeneratePolicyCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->deleteFile($this->app->path('Policies/CategoryPolicy.php'));
    }

    protected function tearDown(): void
    {
        $this->deleteFile($this->app->path('Policies/CategoryPolicy.php'));
        parent::tearDown();
    }

    /** @test */
    public function it_can_generate_policy_file(): void
    {
        $file_path = $this->app->path('Policies/CategoryPolicy.php');
        $expected_output = $this->getTestStubContents('Policy.stub');

        $this->artisan('generate:policy', ['table' => 'categories'])
            ->assertSuccessful();

        $this->assertFileExists($file_path);

        $actual_content = $this->getGeneratedFileContents($file_path);
        $actual_content = str_replace(["\r", "\n", "\t", " "], '', $actual_content);
        $expected_output = str_replace(["\r", "\n", "\t", " "], '', $expected_output);

        $this->assertEquals($expected_output, $actual_content);
    }

}
