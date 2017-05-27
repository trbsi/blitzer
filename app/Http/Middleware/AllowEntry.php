<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Used to allow entry only to authorized devices
 * Class AllowEntry
 * @package App\Http\Middleware
 */
class AllowEntry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $header = $request->header("AllowEntry");
        if (isset($header) && !empty($header) && $header == base64_encode("mali:debeli")) {
            return $next($request);
        }

        throw new \Exception("Not allowed", 400);
    }
}
