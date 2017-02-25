<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;
use App\ServiceTag;

class ServiceController extends Controller
{
    public function index(){
      return response()->json([
        'data' => Service::with(['serviceTags'])->get(),
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
        'data' => $service,
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
      Service::findOrFail($id)->serviceTags()->attach($serviceTagId);
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
