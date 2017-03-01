<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->visit('/')
             ->see('Laravel');
    }

    // PAUL: This just tests that the models created in ModelFactory.php actually work
    public function test_models_in_factory()
    {
        $event = factory(App\Event::class)->create();
        $service = factory(App\Service::class)->create();
        $serviceTag = factory(App\ServiceTag::class)->create();
    }
}
