<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ServiceTagAPITest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_create_a_new_service_tag()
    {
      $serviceTag = factory(App\ServiceTag::class)->make();

      // Validate general format and attributes, also validates plurality in the JsonStructure section
      $this->json('POST', '/api/service_tags', $serviceTag->getAttributes())
        ->seeJson([
          'error' => null,
        ])->seeJson(
          $serviceTag->getAttributes()
        )->seeJsonStructure([
          'data' => [
            'id', 'name', 'created_at', 'updated_at'
          ],
          'meta'
        ]);
      // Validate attributes that should not be present are not present
      $jsonResponse = json_decode($this->response->content());
      $this->assertObjectNotHasAttribute('pivot', $jsonResponse->data);
    }

    /** @test */
    public function can_list_all_service_tags()
    {
      // Creating 3 serviceTags
      factory(App\ServiceTag::class, 3)->create();
      // Use the index endpoint to retrieve, validate format and attributes, also validates plurality in the JsonStructure section
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
      // Validate that the correct number of service tags were retrieved
      $this->assertCount(3, $jsonResponse->data);

      foreach($jsonResponse->data as $service){
        // Validate attributes that should not be present are not present
        $this->assertObjectNotHasAttribute('pivot', $service);
      }
    }

    /** @test */
    public function can_list_single_service_tag()
    {
      // Create 3 serviceTags to make sure when we retrieve our desired serviceTag that we don't retrieve the wrong one
      $serviceTags = factory(App\ServiceTag::class, 3)->create();
      // Use the show endpoint to retrieve serviceTag ID 2, validate format and attributes, also validates plurality in the JsonStructure section
      $this->json('GET', '/api/service_tags/2')
        ->seeJson([
          'error' => null,
        ])->seeJsonStructure([
          'data' => [
            'id', 'name', 'created_at', 'updated_at'
          ],
          'meta'
        ]);
      $jsonResponse = json_decode($this->response->content());
      // Validate attributes that should not be present are not present
      $this->assertObjectNotHasAttribute('pivot', $jsonResponse->data);
    }

    /** @test */
    public function can_update_a_service_tag()
    {
      // The static test text we will use to update our serviceTag
      $newName = "Eventus Test 01";
      $newPropArray = [
        'name' => $newName,
      ];
      $idToRetrieve = 2;
      // Create 3 serviceTags to make sure when we retrieve our desired serviceTag that we don't retrieve the wrong one
      $serviceTags = factory(App\ServiceTag::class, 3)->create();
      // Use the update endpoint to update serviceTag ID 2, validate the format and attributes and updated values,
      // also validates plurality in the JsonStructure section
      $this->json('PUT', '/api/service_tags/'.$idToRetrieve, $newPropArray)
        ->seeJson([
          'error' => null,
        ])->seeJson(
          $newPropArray
        )->seeJsonStructure([
          'data' => [
            'id', 'name', 'created_at', 'updated_at'
          ],
          'meta'
        ])->seeJson([
          'id' => $idToRetrieve
        ]);
      $jsonResponse = json_decode($this->response->content());
      // Validate attributes that should not be present are not present
      $this->assertObjectNotHasAttribute('pivot', $jsonResponse->data);
    }

    /** @test */
    public function can_delete_a_service_tag()
    {
      // Create 3 serviceTags to make sure when we retrieve our desired serviceTag that we don't retrieve the wrong one
      $serviceTags = factory(App\ServiceTag::class, 3)->create();
      // Use the delete endpoint to delete serviceTag ID 2, validate that the item is deleted on return ( data is null )
      // and that the meta data returns success = true
      $this->json('DELETE', '/api/service_tags/2')
        ->seeJson([
          'error' => null,
          'success' => true,
          'data' => null
        ])->seeJsonStructure([
          'meta' => ['success']
        ]);
      // Verify that there are now only 2 serviceTags
      $this->assertCount(2, App\ServiceTag::all());
    }
}
