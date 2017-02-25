<?php

namespace App\Http\Middleware;

use Closure;

class EventusJsonResponseFormat
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if($response instanceof \Illuminate\Http\JsonResponse && $response->exception === NULL){
          $data = $response->getData();

          $responseData = [];
          $responseData['meta'] = property_exists($data, 'meta') ? $data->meta : NULL;
          $responseData['data'] = property_exists($data, 'data') ? $data->data : NULL;
          $responseData['error'] = NULL;

          $response->setData($responseData);
        }

        return $response;
    }
}
