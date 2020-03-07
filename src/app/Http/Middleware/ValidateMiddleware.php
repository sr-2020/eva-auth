<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class ValidateMiddleware
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
        $limit = $request->get('limit', 100);
        if ($limit < 0) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

//        $email = $request->get('email', null);
//        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
//            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
//        }

        return $next($request);
    }
}
