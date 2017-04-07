<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FilterAPITest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_order_events(){
      // Creating 3 events
      factory(App\Event::class)->create(['date' => '2017-05-30 00:33:39']);
      factory(App\Event::class)->create(['date' => '2017-03-30 00:33:39']);
      factory(App\Event::class)->create(['date' => '2017-04-30 00:33:39']);

      // Get multiple events, default order ( DATE : ASC )
      $this->json('GET', '/api/events')
        ->seeJson([
          'error' => null,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id', 'name', 'date', 'created_at', 'updated_at', 'services']
          ],
          'meta'
        ]);

      $jsonResponse = json_decode($this->response->content());
      // Validate that the correct number of services were retrieved
      $this->assertCount(3, $jsonResponse->data);
      // Validate that the correct service was retrieved
      $this->assertEquals(2, $jsonResponse->data[0]->id);
      $this->assertEquals(3, $jsonResponse->data[1]->id);
      $this->assertEquals(1, $jsonResponse->data[2]->id);

      // Get multiple events alternate order ( ID : DESC )
      $this->json('GET', '/api/events?order-by=id&order=DESC')
        ->seeJson([
          'error' => null,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id', 'name', 'date', 'created_at', 'updated_at', 'services']
          ],
          'meta'
        ]);

      $jsonResponse = json_decode($this->response->content());
      // Validate that the correct number of services were retrieved
      $this->assertCount(3, $jsonResponse->data);
      // Validate that the correct service was retrieved
      $this->assertEquals(3, $jsonResponse->data[0]->id);
      $this->assertEquals(2, $jsonResponse->data[1]->id);
      $this->assertEquals(1, $jsonResponse->data[2]->id);
    }

    /** @test */
    public function can_order_services(){
      // Creating 3 services
      factory(App\Service::class)->create(['name' => 'XYZ']);
      factory(App\Service::class)->create(['name' => 'ABC']);
      factory(App\Service::class)->create(['name' => 'UVW']);

      // Get multiple services, default order ( NAME : ASC )
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
      // Validate that the correct service was retrieved
      $this->assertEquals(2, $jsonResponse->data[0]->id);
      $this->assertEquals(3, $jsonResponse->data[1]->id);
      $this->assertEquals(1, $jsonResponse->data[2]->id);

      // Get multiple services alternate order ( ID : DESC )
      $this->json('GET', '/api/services?order-by=id&order=DESC')
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
      // Validate that the correct service was retrieved
      $this->assertEquals(3, $jsonResponse->data[0]->id);
      $this->assertEquals(2, $jsonResponse->data[1]->id);
      $this->assertEquals(1, $jsonResponse->data[2]->id);
    }

    /** @test */
    public function can_order_service_tags(){
      // Creating 3 services
      factory(App\ServiceTag::class)->create(['name' => 'XYZ']);
      factory(App\ServiceTag::class)->create(['name' => 'ABC']);
      factory(App\ServiceTag::class)->create(['name' => 'UVW']);

      // Get multiple serviceTags, default order ( NAME : ASC )
      $this->json('GET', '/api/service_tags')
        ->seeJson([
          'error' => null,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id', 'name', 'created_at', 'updated_at']
          ],
          'meta'
        ]);

      $jsonResponse = json_decode($this->response->content());
      // Validate that the correct number of services were retrieved
      $this->assertCount(3, $jsonResponse->data);
      // Validate that the correct service was retrieved
      $this->assertEquals(2, $jsonResponse->data[0]->id);
      $this->assertEquals(3, $jsonResponse->data[1]->id);
      $this->assertEquals(1, $jsonResponse->data[2]->id);

      // Get multiple serviceTags alternate order ( ID : DESC )
      $this->json('GET', '/api/service_tags?order-by=id&order=DESC')
        ->seeJson([
          'error' => null,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id', 'name', 'created_at', 'updated_at']
          ],
          'meta'
        ]);

      $jsonResponse = json_decode($this->response->content());
      // Validate that the correct number of services were retrieved
      $this->assertCount(3, $jsonResponse->data);
      // Validate that the correct service was retrieved
      $this->assertEquals(3, $jsonResponse->data[0]->id);
      $this->assertEquals(2, $jsonResponse->data[1]->id);
      $this->assertEquals(1, $jsonResponse->data[2]->id);
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
    public function can_filter_service_tag_by_id()
    {
        // Creating 3 serviceTags
        factory(App\ServiceTag::class, 3)->create();
        // Get a single serviceTag by id
        $this->json('GET', '/api/service_tags?filter-ids=2')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'created_at', 'updated_at']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of serviceTags were retrieved
        $this->assertCount(1, $jsonResponse->data);
        // Validate that the correct serviceTag was retrieved
        $this->assertEquals(2, $jsonResponse->data[0]->id);

        // Get multiple serviceTags by id
        $this->json('GET', '/api/service_tags?filter-ids=1,2&order-by=id')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'created_at', 'updated_at']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of serviceTags were retrieved
        $this->assertCount(2, $jsonResponse->data);
        // Validate that the correct serviceTag was retrieved
        $this->assertEquals(1, $jsonResponse->data[0]->id);
        $this->assertEquals(2, $jsonResponse->data[1]->id);

        // empty request
        $this->json('GET', '/api/service_tags?filter-ids=')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'created_at', 'updated_at']
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
    public function can_filter_service_tag_by_except_id()
    {
        // Creating 3 servicesTags
        factory(App\ServiceTag::class, 3)->create();
        // Filter out a single serviceTag by id
        $this->json('GET', '/api/service_tags?filter-except-ids=2&order-by=id')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'created_at', 'updated_at']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of serviceTags were retrieved
        $this->assertCount(2, $jsonResponse->data);
        // validate the correct data is returned
        $this->assertEquals(1, $jsonResponse->data[0]->id);
        $this->assertEquals(3, $jsonResponse->data[1]->id);

        // Filter out multiple serviceTags by id
        $this->json('GET', '/api/service_tags?filter-except-ids=1,2&order-by=id')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'created_at', 'updated_at']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of serviceTags were retrieved
        $this->assertCount(1, $jsonResponse->data);
        // Validate the correct data is returned
        $this->assertEquals(3, $jsonResponse->data[0]->id);

        // empty request
        $this->json('GET', '/api/service_tags?filter-except-ids=')
          ->seeJson([
            'error' => null,
          ])->seeJsonStructure([
            'data' => [
              '*' => ['id', 'name', 'created_at', 'updated_at']
            ],
            'meta'
          ]);

        $jsonResponse = json_decode($this->response->content());
        // Validate that the correct number of serviceTags were retrieved
        $this->assertCount(3, $jsonResponse->data);
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
    public function can_filter_services_on_event()
    {
      // Create an event
      $event = factory(App\Event::class)->create();
      // Create 2 Services and attach them to the event ( ids 1 and 2 )
      factory(App\Service::class, 2)->make()->each(function($service) use ($event){
        $event->services()->save($service);
        // Create 2 ServiceTags for each Service
        factory(App\ServiceTag::class, 2)->make()->each(function($serviceTag) use ($service){
          $service->serviceTags()->save($serviceTag);
        });
      });
      // Create 2 Services and DON'T attach them to the event ( ids 3 and 4 )
      factory(App\Service::class, 2)->create();

      // In total we have:
      // 1 Event
      // 4 Services ( 1,2 attached to Event and 3,4 not attached )
      // 4 ServiceTags ( 1,2 attached to Service 1 and 3,4 attached to Service 2 )

      // Filter with
      $this->json('GET', '/api/events/1/services?filter-ids=2')
        ->seeJson([
          'error' => null,
        ])->seeJson([
          'id' => 2,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id']
          ],
          'meta'
        ]);

      $jsonResponse = json_decode($this->response->content());
      // Validate that the correct number of services were retrieved
      $this->assertCount(1, $jsonResponse->data);

      // Filter except
      $this->json('GET', '/api/events/1/services?filter-except-ids=2')
        ->seeJson([
          'error' => null,
        ])->seeJson([
          'id' => 1,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id']
          ],
          'meta'
        ]);

      $jsonResponse = json_decode($this->response->content());
      // Validate that the correct number of services were retrieved
      $this->assertCount(1, $jsonResponse->data);

      // Filter service tag
      $this->json('GET', '/api/events/1/services?filter-tag-ids=3')
        ->seeJson([
          'error' => null,
        ])->seeJson([
          'id' => 2,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id']
          ],
          'meta'
        ]);

      $jsonResponse = json_decode($this->response->content());
      // Validate that the correct number of services were retrieved
      $this->assertCount(1, $jsonResponse->data);

      // Order
      $this->json('GET', '/api/events/1/services?order-by=id&order=ASC')
        ->seeJson([
          'error' => null,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id']
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
    public function can_filter_service_tags_on_service()
    {
      // Create a service
      $service = factory(App\Service::class)->create();
      // Create 2 serviceTags and attach them to the service ( ids 1 and 2 )
      factory(App\ServiceTag::class, 2)->make()->each(function($serviceTag) use ($service){
        $service->serviceTags()->save($serviceTag);
      });
      // Create 2 serviceTags and DON'T attach them to the service ( ids 3 and 4 )
      factory(App\ServiceTag::class, 2)->create();

      // Filter with
      $this->json('GET', '/api/services/1/service_tags?filter-ids=2')
        ->seeJson([
          'error' => null,
        ])->seeJson([
          'id' => 2,
        ])->dontSeeJson([
          'id' => 1,
          'id' => 3,
          'id' => 4,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id']
          ],
          'meta'
        ]);

      // Filter except
      $this->json('GET', '/api/services/1/service_tags?filter-except-ids=2')
        ->seeJson([
          'error' => null,
        ])->seeJson([
          'id' => 1,
        ])->dontSeeJson([
          'id' => 2,
          'id' => 3,
          'id' => 4,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id']
          ],
          'meta'
        ]);

      // Order
      $this->json('GET', '/api/services/1/service_tags?order-by=id&order=asc')
        ->seeJson([
          'error' => null,
        ])->seeJsonStructure([
          'data' => [
            '*' => ['id']
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
}
