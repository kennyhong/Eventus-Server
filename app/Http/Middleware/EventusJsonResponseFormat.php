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
          // This looks dumb, and it is... if getData() is "{}" then it converts to an empty stdClass
          // So I have to cast to an array and get the count of elements, if there are no
          // elements, it's empty and should be NULL
          $data = $response->getData();
          $data = count((array) $data) == 0 ? NULL : $data;

          $response->setData([
              'data' => $data,
              'error' => NULL,
            ]);
        }

        return $response;
    }
}
