<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\EventusException;
use App\Event;
use App\Service;

class EventController extends Controller
{
    public function index(){
      return response()->json([
        'data' => Event::with(['services','services.serviceTags'])->get(),
      ]);
    }

    public function store(Request $request){
      $event = Event::create($request->all());

      return response()->json([
        'data' => $event->with(['services','services.serviceTags'])->where('id', '=', $event->getKey())->get()->first(),
      ]);
    }

    public function show($id){
      return response()->json([
        'data' => Event::with(['services','services.serviceTags'])->where('id', '=', $id)->get()->first(),
      ]);
    }

    public function update(Request $request, $id){
      $event = Event::findOrFail($id);
      $event->update($request->all());

      return response()->json([
        'data' => $event,
      ]);
    }

    public function destroy($id){
      $event = Event::findOrFail($id);
      $success = false;
      if($event !== null){
        $success = $event->delete();
      }

      return response()->json([
        'meta' => [
          'success' => $success,
        ],
        'data' => NULL,
      ]);
    }

    public function getServices($id){
      return response()->json([
        'data' => Event::findOrFail($id)->services()->with('serviceTags')->get(),
      ]);
    }

    public function addService($id, $serviceId){
      $thingEvent = null;
      try {
        Event::findOrFail($id)->services()->attach($serviceId);

      } catch(\Exception $e) {
        if(!Service::find($serviceId)){
          throw new EventusException("Failed to add Service to Event. No such Service exists.");

        } else if(Event::findOrFail($id)->services()->find($serviceId)){
          throw new EventusException("Failed to add Service to Event. Event already has Service.");

        }
        throw new EventusException("Failed to add Service to Event.");
      }
      return response()->json([
        'data' => Event::findOrFail($id)->services()->with('serviceTags')->get(),
      ]);
    }

    public function removeService($id, $serviceId){
      Event::findOrFail($id)->services()->detach($serviceId);
      return response()->json([
        'data' => Event::findOrFail($id)->services()->with('serviceTags')->get(),
      ]);
    }
}
