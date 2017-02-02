<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ServiceTag;

class ServiceTagController extends Controller
{
    public function index(){
      return response()->json(ServiceTag::all());
    }

    public function store(Request $request){
      $serviceTag = ServiceTag::create($request->all());
      // Return the created object for now
      return response()->json($serviceTag);
    }

    public function show($id){
      return response()->json(ServiceTag::find($id));
    }

    public function update(Request $request, $id){
      $serviceTag = ServiceTag::find($id);
      $serviceTag->update($request->all());
      // Return the object updated for now
      return response()->json($serviceTag);
    }

    public function destroy($id){
      $serviceTag = ServiceTag::find($id);
      $success = false;
      if($serviceTag !== null){
        $success = $serviceTag->delete();
      }
      // Return a json object with confirmation
      return response()->json(["success" => $success]);
    }
}
