<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;
use App\ServiceTag;

class ServiceController extends Controller
{
    public function index(){
      return response()->json(Service::with(['serviceTags'])->get());
    }

    public function store(Request $request){
      $service = Service::create($request->all());
      // Return the created object for now
      return response()->json($service);
    }

    public function show($id){
      return response()->json(Service::with(['serviceTags'])->where('id', '=', $id)->get());
    }

    public function update(Request $request, $id){
      $service = Service::find($id);
      $service->update($request->all());
      // Return the object updated for now
      return response()->json($service);
    }

    public function destroy($id){
      $service = Service::find($id);
      $success = false;
      if($service !== null){
        $success = $service->delete();
      }
      // Return a json object with confirmation
      return response()->json(["success" => $success]);
    }

    public function getServiceTags($id){
      return response()->json(Service::findOrFail($id)->serviceTags()->get());
    }

    public function addServiceTag($id, $serviceTagId){
      Service::findOrFail($id)->serviceTags()->attach($serviceTagId);
      return ServiceTag::findOrFail($serviceTagId);
    }

    public function removeServiceTag($id, $serviceTagId){
      Service::findOrFail($id)->serviceTags()->detach($serviceTagId);
    }
}
