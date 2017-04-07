<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ServiceTag;

class ServiceTagController extends Controller
{
    public function index(Request $request){
      $data = ServiceTag::filterByIds($request)->
      filterExceptIds($request)->
      order($request)->get();

      return response()->json([
        'data' => $data,
      ]);
    }

    public function store(Request $request){
      $serviceTag = ServiceTag::create($request->all());

      return response()->json([
        'data' => $serviceTag,
      ]);
    }

    public function show($id){
      return response()->json([
        'data' => ServiceTag::where('id', '=', $id)->get()->first(),
      ]);
    }

    public function update(Request $request, $id){
      $serviceTag = ServiceTag::find($id);
      $serviceTag->update($request->all());

      return response()->json([
        'data' => $serviceTag,
      ]);
    }

    public function destroy($id){
      $serviceTag = ServiceTag::find($id);
      $success = false;
      if($serviceTag !== null){
        $success = $serviceTag->delete();
      }

      return response()->json([
        'meta' => [
          'success' => $success,
        ],
        'data' => NULL,
      ]);
    }
}
