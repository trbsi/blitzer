<?php

namespace App\Http\Middleware;

use Closure;
use Dingo\Api\Http\Response;

class DebugDingoHttpResponse
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

        if (
            $response instanceof Response &&
            app()->bound('debugbar') &&
            app('debugbar')->isEnabled()
        ) {
            $response->setContent(json_decode($response->morph()->getContent(), true) + [
                '_debugbar' => app('debugbar')->getData(),
            ]);
        }

        return $response;
    }
}