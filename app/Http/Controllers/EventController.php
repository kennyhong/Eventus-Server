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
        'data' => $event->with(['services','services.serviceTags'])->where('id', '=', $event->getKey())->get()->first(),
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

    //decode the json returned from the query and add up all the prices/taxes of the services within
    public function getInvoice($id){
        $tax_rate = 0.13;
        $temp = Event::with(['services','services.serviceTags'])->where('id', '=', $id)->get()->first()->toJson();
        $json = json_decode($temp,true);
        $price = 0;
        //add up the prices
        foreach($json['services'] as $service)
        {
            $price = $price + $service['cost'];
        }
        //add values to the json
        $json['sub_total'] = $price;
        $tax = $price * $tax_rate;//calculate taxes
        $tax = round($tax,2,PHP_ROUND_HALF_UP);
        $json['tax'] = $tax;
        $json['grand_total'] = $json['sub_total'] + $json['tax'];

        return response()->json($json);
    }

    public function getServices($id){
      return response()->json([
        'data' => Event::findOrFail($id)->services()->with('serviceTags')->get(),
      ]);
    }

    public function addService($id, $serviceId){
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
