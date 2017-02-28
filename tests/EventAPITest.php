<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EventAPITest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_create_a_new_event()
    {
      $event = factory(App\Event::class)->make();

      // Validate general format and attributes
      $this->json('POST', '/api/events', $event->getAttributes())
        ->seeJson([
          'error' => null,
        ])->seeJson(
          $event->getAttributes()
        )->seeJsonStructure([
          'data' => [
            'id', 'name', 'description', 'date', 'created_at', 'updated_at', 'services'
          ],
          'meta'
        ]);
      // Validate attributes that should not be present are not present
      $jsonResponse = json_decode($this->response->content());
      $this->assertObjectNotHasAttribute('pivot', $jsonResponse->data);
      // Validate the plurality of the data
      $this->assertEquals(false, is_array($jsonResponse->data));
      $this->assertEquals(true, is_array($jsonResponse->data->services));
    }

    /** @test */
    public function can_list_all_events()
    {
      // The ol' triple cubed - Creating 3 events with 3 services with 3 service tags each
      $events = factory(App\Event::class, 3)->create()->each(function($event){
        factory(App\Service::class, 3)->make()->each(function($service) use ($event){
          $event->services()->save($service);
          factory(App\ServiceTag::class, 3)->make()->each(function($serviceTag) use ($service){
            $service->serviceTags()->save($serviceTag);
          });
        });
      });
      //dd(json_decode(json_encode(App\Event::with(['services','services.serviceTags'])->get())));
    }

}
