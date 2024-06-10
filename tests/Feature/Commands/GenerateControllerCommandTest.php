<?php

namespace PulsarLabs\Generators\Tests\Feature\Commands;

use PulsarLabs\Generators\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenerateControllerCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @group generate.model.file
     */
    public function it_can_generate_model_file(): void
    {
        $this->artisan('generate:controller',
            ['table' => 'categories', 'namespace' => 'admin'])
             ->assertSuccessful();
    }
}
