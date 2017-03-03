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
    public function can_update_an_event()
    {
      // The static test text we will use to update our event
      $newName = "Eventus Test 01";
      $newDescription = "Eventus description test of the first";
      $newDate = "1000-01-01 00:00:00";
      $newPropArray = [
        'name' => $newName,
        'description' => $newDescription,
        'date' => $newDate
      ];
      $idToRetrieve = 2;
      // Create 3 events to make sure when we retrieve our desired event that we don't retrieve the wrong one
      $events = factory(App\Event::class, 3)->create();
      // 1 event with 3 services and 1 servicetag for each service
      factory(App\Service::class, 3)->make()->each(function($service) use ($events){
        $events[1]->services()->save($service);
        $service->serviceTags()->save(factory(App\ServiceTag::class)->make());
      });
      // Use the update endpoint to update event ID 2, validate the format and attributes and updated values,
      // also validates plurality in the JsonStructure section
      $this->json('PUT', '/api/events/'.$idToRetrieve, $newPropArray)
        ->seeJson([
          'error' => null,
        ])->seeJson(
          $newPropArray
        )->seeJsonStructure([
          'data' => [
            'id', 'name', 'description', 'date', 'created_at', 'updated_at', 'services'
          ],
          'meta'
        ])->seeJson([
          'id' => $idToRetrieve
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
    public function can_delete_an_event()
    {
      // Create 3 events to make sure when we delete our desired event that we don't delete the wrong one
      $events = factory(App\Event::class, 3)->create();
      // 1 event with 3 services and 1 servicetag for each service
      factory(App\Service::class, 3)->make()->each(function($service) use ($events){
        $events[1]->services()->save($service);
        $service->serviceTags()->save(factory(App\ServiceTag::class)->make());
      });
      // Use the delete endpoint to delete event ID 2, validate that the item is deleted on return ( data is null )
      // and that the meta data returns success = true
      $this->json('DELETE', '/api/events/2')
        ->seeJson([
          'error' => null,
          'success' => true,
          'data' => null
        ])->seeJsonStructure([
          'meta' => ['success']
        ]);
      // Verify that there are now only 2 events
      $this->assertCount(2, App\Event::all());
      // Verify that no services are missing
      $this->assertCount(3, App\Service::all());
      // Verify that no service tags are missing
      $this->assertCount(3, App\ServiceTag::all());
    }

    /** @test */
    public function can_retrieve_related_services()
    {
      // Create an event
      $event = factory(App\Event::class)->create();
      // Create 2 services with serviceTags and attach them to the event ( ids 1 and 2 )
      factory(App\Service::class, 2)->make()->each(function($service) use ($event){
        $event->services()->save($service);
        $service->serviceTags()->save(factory(App\ServiceTag::class)->make());
      });
      // Create 2 services with serviceTags and DON'T attach them to the event ( ids 3 and 4 )
      factory(App\Service::class, 2)->create()->each(function($service){
        $service->serviceTags()->save(factory(App\ServiceTag::class)->make());
      });
      // Use the getServices endpoint to retrieve all services for our known event, validate that they are the right ones by ID
      // And that the underlying structure is correct, also validates plurality in the JsonStructure section
      $this->json('GET', '/api/events/1/services')
        ->seeJson([
          'error' => null,
        ])->seeJson([
          'id' => 1,
          'id' => 2,
        ])->dontSeeJson([
          'id' => 3,
          'id' => 4,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id']
          ],
          'meta'
        ]);
      // Verify that there are the correct number of services attached to the event
      $this->assertCount(2, $event->services()->get());
    }

    /** @test */
    public function can_add_service()
    {
      // Create an event
      $event = factory(App\Event::class)->create();
      // Create 2 services with serviceTags
      $services = factory(App\Service::class, 2)->create()->each(function($service){
        $service->serviceTags()->save(factory(App\ServiceTag::class)->make());
      });
      // Using the addService endpoint, attach one of the services to the event,
      // validate that the structure is correct, also validates the plurality in the JsonStructure section
      $this->json('POST', '/api/events/1/services/2')
        ->seeJson([
          'error' => null,
          'id' => 2,
        ])->dontSeeJson([
          'id' => 1,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id']
          ],
          'meta'
        ]);
        // Validate that the correct service was actually attached and not just returned, as above
        $this->assertEquals(2, $event->services()->get()->first()->getKey());
        // Verify that there are the correct number of services attached to the event
        $this->assertCount(1, $event->services()->get());
    }

    /** @test */
    public function can_remove_service()
    {
      // Create an event
      $event = factory(App\Event::class)->create();
      // Create 2 services with serviceTags and attach them to the event ( ids 1 and 2 )
      factory(App\Service::class, 2)->make()->each(function($service) use ($event){
        $event->services()->save($service);
        $service->serviceTags()->save(factory(App\ServiceTag::class)->make());
      });
      // Use the removeService endpoint, remove one of the services from the event, validate the structure is correct
      // also validates the plurality in the JsonStructure section
      $this->json('DELETE', '/api/events/1/services/2')
        ->seeJson([
          'error' => null,
          'id' => 1,
        ])->dontSeeJson([
          'id' => 2,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id']
          ],
          'meta'
        ]);
        // Validate that the correct service was actually removed and not just returned that way, as above
        $this->assertEquals(1, $event->services()->get()->first()->getKey());
        // Verify that there are the correct number of services attached to the event
        $this->assertCount(1, $event->services()->get());
    }
}
