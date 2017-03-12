<?php

use Illuminate\Http\Request;
use App\Event;
use App\Service;
use App\ServiceTag;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/', function (Request $request) {
  return response()->json(['data' => null, 'error' => null, 'meta' => ['message' => 'You\'ve successfully connected to the Eventus API!', 'success' => true]]);
});

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:api');

// Events
Route::group(['prefix' => '/events/{event}'], function(){
    Route::get('/invoice', 'EventController@getInvoice')->name('events.invoice.show');
  Route::get('/services', 'EventController@getServices')->name('events.services.show');
  Route::post('/services/{service}', 'EventController@addService')->name('events.services.add');
  Route::delete('/services/{service}', 'EventController@removeService')->name('events.services.remove');
});
Route::resource('/events', 'EventController', ['except' => ['create', 'edit']]);

// Services
Route::group(['prefix' => '/services/{service}'], function(){
  Route::get('/service_tags', 'ServiceController@getServiceTags')->name('services.service_tags.show');
  Route::post('/service_tags/{service_tag}', 'ServiceController@addServiceTag')->name('services.service_tags.add');
  Route::delete('/service_tags/{service_tag}', 'ServiceController@removeServiceTag')->name('services.service_tags.remove');
});
Route::resource('/services', 'ServiceController', ['except' => ['create', 'edit']]);

// Service Tags
Route::resource('/service_tags', 'ServiceTagController', ['except' => ['create', 'edit']]);
