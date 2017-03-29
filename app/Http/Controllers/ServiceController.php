<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\EventusException;
use App\Service;
use App\ServiceTag;

class ServiceController extends Controller
{
    public function index(Request $request){
      $data = Service::with(['serviceTags'])->
        filterByIds($request)->
        filterByTagIds($request)->
        get();

      return response()->json([
          'data' => $data,
      ]);
    }

    public function store(Request $request){
      $service = Service::create($request->all());

      return response()->json([
        'data' => $service->with(['serviceTags'])->where('id', '=', $service->getKey())->get()->first(),
      ]);
    }

    public function show($id){
      return response()->json([
        'data' => Service::with(['serviceTags'])->where('id', '=', $id)->get()->first(),
      ]);

    }

    public function update(Request $request, $id){
      $service = Service::find($id);
      $service->update($request->all());

      return response()->json([
        'data' => $service->with(['serviceTags'])->where('id', '=', $service->getKey())->get()->first(),
      ]);
    }

    public function destroy($id){
      $service = Service::find($id);
      $success = false;
      if($service !== null){
        $success = $service->delete();
      }

      return response()->json([
        'meta' => [
          'success' => $success,
        ],
        'data' => NULL,
      ]);
    }

    public function getServiceTags($id){
      return response()->json([
        'data' => Service::findOrFail($id)->serviceTags()->get(),
      ]);
    }

    public function addServiceTag($id, $serviceTagId){
      try {
        Service::findOrFail($id)->serviceTags()->attach($serviceTagId);
      } catch(\Exception $e) {
        if(!ServiceTag::find($serviceTagId)){
          throw new EventusException("Failed to add ServiceTag to Service. No such ServiceTag exists.");

        } else if(Service::findOrFail($id)->serviceTags()->find($serviceTagId)){
          throw new EventusException("Failed to add ServiceTag to Service. Service already has ServiceTag.");

        }
        throw new EventusException("Failed to add ServiceTag to Service.");
      }
      return response()->json([
        'data' => Service::findOrFail($id)->serviceTags()->get(),
      ]);
    }

    public function removeServiceTag($id, $serviceTagId){
      Service::findOrFail($id)->serviceTags()->detach($serviceTagId);
      return response()->json([
        'data' => Service::findOrFail($id)->serviceTags()->get(),
      ]);
    }
}
