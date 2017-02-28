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

      // Validate general format and attributes, also validates plurality in the JsonStructure section
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
      // Use the index endpoint to retrieve, validate format and attributes, also validates plurality in the JsonStructure section
      $this->json('GET', '/api/events')
      ->seeJson([
        'error' => null,
      ])->seeJsonStructure([
        'data' => [
          '*' => ['id', 'name', 'description', 'date', 'created_at', 'updated_at', 'services']
        ],
        'meta'
      ]);

      $jsonResponse = json_decode($this->response->content());
      // Validate that the correct number of events were retrieved
      $this->assertCount(3, $jsonResponse->data);

      foreach($jsonResponse->data as $event){
        // Validate attributes that should not be present are not present
        $this->assertObjectNotHasAttribute('pivot', $event);
        // Validate that each event has the correct number of services
        $this->assertCount(3, $event->services);
        // Validate that each service has the correct number of serviceTags
        foreach($event->services as $service){
          $this->assertCount(3, $service->service_tags);
        }
      }
    }

    /** @test */
    public function can_list_single_event()
    {
      // Create 3 events to make sure when we retrieve our desired event that we don't retrieve the wrong one
      $events = factory(App\Event::class, 3)->create();
      // 1 event with 3 services and 1 servicetag for each service
      factory(App\Service::class, 3)->make()->each(function($service) use ($events){
        $events[1]->services()->save($service);
        $service->serviceTags()->save(factory(App\ServiceTag::class)->make());
      });
      // Use the show endpoint to retrieve event ID 2, validate format and attributes, also validates plurality in the JsonStructure section
      $this->json('GET', '/api/events/2')
        ->seeJson([
          'error' => null,
        ])->seeJsonStructure([
          'data' => [
            'id', 'name', 'description', 'date', 'created_at', 'updated_at', 'services'
          ],
          'meta'
        ]);
      $jsonResponse = json_decode($this->response->content());
      // Validate attributes that should not be present are not present
      $this->assertObjectNotHasAttribute('pivot', $jsonResponse->data);
      // Validate that the event has the correct number of services
      $this->assertCount(3, $jsonResponse->data->services);
      // Validate that each service has the correct number of serviceTags
      foreach($jsonResponse->data->services as $service){
        $this->assertCount(1, $service->service_tags);
      }
    }

    /** @test */
    public function can_update_an_event(){
      // The static test text we will use to update our event
      $newName = "Eventus Test 01";
      $newDescription = "Eventus description test of the first";
      $newDate = "1000-01-01 00:00:00";
      $newPropArray = [
        'name' => $newName,
        'description' => $newDescription,
        'date' => $newDate
      ];
      // Create 3 events to make sure when we retrieve our desired event that we don't retrieve the wrong one
      $events = factory(App\Event::class, 3)->create();
      // 1 event with 3 services and 1 servicetag for each service
      factory(App\Service::class, 3)->make()->each(function($service) use ($events){
        $events[1]->services()->save($service);
        $service->serviceTags()->save(factory(App\ServiceTag::class)->make());
      });
      // Use the update endpoint to update event ID 2, validate the format and attributes and new value,
      // also validates plurality in the JsonStructure section
      $this->json('PUT', '/api/events/2', $newPropArray)
        ->seeJson([
          'error' => null,
        ])->seeJson(
          $newPropArray
        )->seeJsonStructure([
          'data' => [
            'id', 'name', 'description', 'date', 'created_at', 'updated_at', 'services'
          ],
          'meta'
        ]);
    }

}
