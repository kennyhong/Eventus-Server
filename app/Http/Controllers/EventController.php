<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\Service;

class EventController extends Controller
{
    public function index(){
      return response()->json(Event::with(['services','services.serviceTags'])->get());
    }

    public function store(Request $request){
      $event = Event::create($request->all());
      // Return the created object for now
      return response()->json($event);
    }

    public function show($id){
      return response()->json(Event::with(['services','services.serviceTags'])->where('id', '=', $id)->get());
    }

    public function update(Request $request, $id){
      $event = Event::findOrFail($id);
      $event->update($request->all());
      // Return the object updated for now
      return response()->json($event);
    }

    public function destroy($id){
      $event = Event::findOrFail($id);
      $success = false;
      if($event !== null){
        $success = $event->delete();
      }
      // Return a json object with confirmation
      return response()->json(["success" => $success]);
    }

    public function getServices($id){
      return response()->json(Event::findOrFail($id)->services()->get());
    }

    public function addService($id, $serviceId){
      Event::findOrFail($id)->services()->attach($serviceId);
      return Service::with('serviceTags')->where('id', '=', $serviceId)->get();
    }

    public function removeService($id, $serviceId){
      Event::findOrFail($id)->services()->detach($serviceId);
    }
}
