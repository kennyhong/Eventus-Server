<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ServiceAPITest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_create_a_new_service()
    {
      $service = factory(App\Service::class)->make();

      // Validate general format and attributes, also validates plurality in the JsonStructure section
      $this->json('POST', '/api/services', $service->getAttributes())
        ->seeJson([
          'error' => null,
        ])->seeJson(
          $service->getAttributes()
        )->seeJsonStructure([
          'data' => [
            'id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags'
          ],
          'meta'
        ]);
      // Validate attributes that should not be present are not present
      $jsonResponse = json_decode($this->response->content());
      $this->assertObjectNotHasAttribute('pivot', $jsonResponse->data);
    }

    /** @test */
    public function can_filter_service_by_id()
    {
        // Creating 3 services with 1 service tags each
        factory(App\Service::class, 3)->create()->each(function($service){
          $service->serviceTags()->save(factory(App\ServiceTag::class, 1)->make());
        });
        // Get a single service by id
        $this->json('GET', '/api/services?filter-ids=2')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of services were retrieved
        $this->assertCount(1, $jsonResponse->data);
        // Validate that the correct service was retrieved
        $this->assertEquals(2, $jsonResponse->data[0]->id);

        // Get multiple services by id
        $this->json('GET', '/api/services?filter-ids=1,2&order-by=id')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of services were retrieved
        $this->assertCount(2, $jsonResponse->data);
        // Validate that the correct service was retrieved
        $this->assertEquals(1, $jsonResponse->data[0]->id);
        $this->assertEquals(2, $jsonResponse->data[1]->id);
    }

    /** @test */
    public function can_filter_service_empty()
    {
        // Creating 3 services with 1 service tags each
        factory(App\Service::class, 3)->create()->each(function($service){
          $service->serviceTags()->save(factory(App\ServiceTag::class, 1)->make());
        });
        // empty request
        $this->json('GET', '/api/services?filter-ids=')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of services were retrieved
        $this->assertCount(3, $jsonResponse->data);
    }

    /** @test */
    public function can_filter_service_by_except_id()
    {
        // Creating 3 services with 1 service tags each
        factory(App\Service::class, 3)->create()->each(function($service){
          $service->serviceTags()->save(factory(App\ServiceTag::class, 1)->make());
        });
        // Filter out a single service by id
        $this->json('GET', '/api/services?filter-except-ids=2&order-by=id')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of services were retrieved
        $this->assertCount(2, $jsonResponse->data);
        // validate the correct data is returned
        $this->assertEquals(1, $jsonResponse->data[0]->id);
        $this->assertEquals(3, $jsonResponse->data[1]->id);

        // Filter out multiple services by id
        $this->json('GET', '/api/services?filter-except-ids=1,2&order-by=id')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of services were retrieved
        $this->assertCount(1, $jsonResponse->data);
        // Validate the correct data is returned
        $this->assertEquals(3, $jsonResponse->data[0]->id);
    }

    /** @test */
    public function can_filter_except_service_empty()
    {
        // Creating 3 services with 1 service tags each
        factory(App\Service::class, 3)->create()->each(function($service){
          $service->serviceTags()->save(factory(App\ServiceTag::class, 1)->make());
        });
        // empty request
        $this->json('GET', '/api/services?filter-except-ids=')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of services were retrieved
        $this->assertCount(3, $jsonResponse->data);
    }

    /** @test */
    public function can_filter_service_multi_filter()
    {
        // Creating 3 services with 1 service tags each
        factory(App\Service::class, 3)->create()->each(function($service){
          $service->serviceTags()->save(factory(App\ServiceTag::class, 1)->make());
        });
        // request 2 services and then filter one of them out
        $this->json('GET', '/api/services?filter-ids=1,2&filter-except-ids=2')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of services were retrieved
        $this->assertCount(1, $jsonResponse->data);
        // validate the correct data is returned
        $this->assertEquals(1 ,$jsonResponse->data[0]->id);
    }

    /** @test */
    public function can_filter_service_by_tag_id()
    {
        // Creating 3 services with 1 service tags each
        factory(App\Service::class, 3)->create()->each(function($service){
          $service->serviceTags()->save(factory(App\ServiceTag::class, 1)->make());
        });
        // Get a single service by id
        $this->json('GET', '/api/services?filter-tag-ids=2')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of services were retrieved
        $this->assertCount(1, $jsonResponse->data);
        // Validate that the correct service was retrieved
        $this->assertEquals(2, $jsonResponse->data[0]->id);

        // Get a multiple services by id
        $this->json('GET', '/api/services?filter-tag-ids=1,2&order-by=id')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of services were retrieved
        $this->assertCount(2, $jsonResponse->data);
        // Validate that the correct service was retrieved
        $this->assertEquals(1, $jsonResponse->data[0]->id);
        $this->assertEquals(2, $jsonResponse->data[1]->id);
    }

    /** @test */
    public function can_filter_service_tags_empty()
    {
        // Creating 3 services with 1 service tags each
        factory(App\Service::class, 3)->create()->each(function($service){
          $service->serviceTags()->save(factory(App\ServiceTag::class, 1)->make());
        });
        // empty request
        $this->json('GET', '/api/services?filter-tags-ids=')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of services were retrieved
        $this->assertCount(3, $jsonResponse->data);
    }

    /** @test */
    public function can_list_all_services()
    {
      // Creating 3 services with 3 service tags each
      factory(App\Service::class, 3)->create()->each(function($service){
        factory(App\ServiceTag::class, 3)->make()->each(function($serviceTag) use ($service){
          $service->serviceTags()->save($serviceTag);
        });
      });
      // Use the index endpoint to retrieve, validate format and attributes, also validates plurality in the JsonStructure section
      $this->json('GET', '/api/services')
      ->seeJson([
        'error' => null,
      ])->seeJsonStructure([
        'data' => [
          '*' => ['id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags']
        ],
        'meta'
      ]);

      $jsonResponse = json_decode($this->response->content());
      // Validate that the correct number of services were retrieved
      $this->assertCount(3, $jsonResponse->data);

      foreach($jsonResponse->data as $service){
        // Validate attributes that should not be present are not present
        $this->assertObjectNotHasAttribute('pivot', $service);
        // Validate that each service has the correct number of serviceTags
        $this->assertCount(3, $service->service_tags);
      }
    }

    /** @test */
    public function can_list_single_service()
    {
      // Create 3 services to make sure when we retrieve our desired service that we don't retrieve the wrong one
      $services = factory(App\Service::class, 3)->create();
      // 1 service with 3 servicetags
      factory(App\ServiceTag::class, 3)->make()->each(function($serviceTag) use ($services){
        $services[1]->serviceTags()->save($serviceTag);
      });
      // Use the show endpoint to retrieve service ID 2, validate format and attributes, also validates plurality in the JsonStructure section
      $this->json('GET', '/api/services/2')
        ->seeJson([
          'error' => null,
        ])->seeJsonStructure([
          'data' => [
            'id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags'
          ],
          'meta'
        ]);
      $jsonResponse = json_decode($this->response->content());
      // Validate attributes that should not be present are not present
      $this->assertObjectNotHasAttribute('pivot', $jsonResponse->data);
      // Validate that the service has the correct number of serviceTags
      $this->assertCount(3, $jsonResponse->data->service_tags);
    }

    /** @test */
    public function can_update_a_service()
    {
      // The static test text we will use to update our service
      $newName = "Eventus Test 01";
      $newCost = 125.00;
      $newPropArray = [
        'name' => $newName,
        'cost' => $newCost,
      ];
      $idToRetrieve = 2;
      // Create 3 services to make sure when we retrieve our desired service that we don't retrieve the wrong one
      $services = factory(App\Service::class, 3)->create();
      // 1 service with 3 servicetags
      factory(App\ServiceTag::class, 3)->make()->each(function($serviceTag) use ($services){
        $services[1]->serviceTags()->save($serviceTag);
      });
      // Use the update endpoint to update service ID 2, validate the format and attributes and updated values,
      // also validates plurality in the JsonStructure section
      $this->json('PUT', '/api/services/'.$idToRetrieve, $newPropArray)
        ->seeJson([
          'error' => null,
        ])->seeJson(
          $newPropArray
        )->seeJsonStructure([
          'data' => [
            'id', 'name', 'cost', 'created_at', 'updated_at', 'service_tags'
          ],
          'meta'
        ])->seeJson([
          'id' => $idToRetrieve
        ]);
      $jsonResponse = json_decode($this->response->content());
      // Validate attributes that should not be present are not present
      $this->assertObjectNotHasAttribute('pivot', $jsonResponse->data);
      // Validate that the service has the correct number of serviceTags
      $this->assertCount(3, $jsonResponse->data->service_tags);
    }

    /** @test */
    public function can_delete_a_service()
    {
      // Create 3 services to make sure when we retrieve our desired service that we don't retrieve the wrong one
      $services = factory(App\Service::class, 3)->create();
      // 1 service with 3 servicetags
      factory(App\ServiceTag::class, 3)->make()->each(function($serviceTag) use ($services){
        $services[1]->serviceTags()->save($serviceTag);
      });
      // Use the delete endpoint to delete service ID 2, validate that the item is deleted on return ( data is null )
      // and that the meta data returns success = true
      $this->json('DELETE', '/api/services/2')
        ->seeJson([
          'error' => null,
          'success' => true,
          'data' => null
        ])->seeJsonStructure([
          'meta' => ['success']
        ]);
      // Verify that there are now only 2 services
      $this->assertCount(2, App\Service::all());
      // Verify that no service tags are missing
      $this->assertCount(3, App\ServiceTag::all());
    }

    /** @test */
    public function can_retrieve_related_service_tags()
    {
      // Create a service
      $service = factory(App\Service::class)->create();
      // Create 2 serviceTags and attach them to the service ( ids 1 and 2 )
      factory(App\ServiceTag::class, 2)->make()->each(function($serviceTag) use ($service){
        $service->serviceTags()->save($serviceTag);
      });
      // Create 2 serviceTags and DON'T attach them to the service ( ids 3 and 4 )
      factory(App\ServiceTag::class, 2)->create();
      // Use the getServiceTags endpoint to retrieve all serviceTags for our known event, validate that they are the right ones by ID
      // And that the underlying structure is correct, also validates plurality in the JsonStructure section
      $this->json('GET', '/api/services/1/service_tags')
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
      // Verify that there are the correct number of serviceTags attached to the service
      $this->assertCount(2, $service->serviceTags()->get());
    }

    /** @test */
    public function can_add_service_tag()
    {
      // Create a service
      $service = factory(App\Service::class)->create();
      // Create 2 serviceTags
      $serviceTags = factory(App\ServiceTag::class, 2)->create();
      // Using the addServiceTag endpoint, attach one of the serviceTags to the service,
      // validate that the structure is correct, also validates the plurality in the JsonStructure section
      $this->json('POST', '/api/services/1/service_tags/2')
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
        // Validate that the correct serviceTag was actually attached and not just returned, as above
        $this->assertEquals(2, $service->serviceTags()->get()->first()->getKey());
        // Verify that there are the correct number of services attached to the event
        $this->assertCount(1, $service->serviceTags()->get());
    }

    /** @test */
    public function can_remove_service_tag()
    {
      // Create a service
      $service = factory(App\Service::class)->create();
      // Create 2 serviceTags and attach them to the service ( ids 1 and 2 )
      factory(App\ServiceTag::class, 2)->make()->each(function($serviceTag) use ($service){
        $service->serviceTags()->save($serviceTag);
      });
      // Use the removeServiceTag endpoint, remove one of the serviceTags from the service, validate the structure is correct
      // also validates the plurality in the JsonStructure section
      $this->json('DELETE', '/api/services/1/service_tags/2')
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
        // Validate that the correct serviceTag was actually removed and not just returned that way, as above
        $this->assertEquals(1, $service->serviceTags()->get()->first()->getKey());
        // Verify that there are the correct number of serviceTags attached to the service
        $this->assertCount(1, $service->serviceTags()->get());
    }
}
