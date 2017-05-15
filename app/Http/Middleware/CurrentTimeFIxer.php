<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\PinHelper;

/**
 * Used to modify current_time I get from mobile into PHP readable format
 * Class CurrentTimeFixer
 * @package App\Http\Middleware
 */
class CurrentTimeFixer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(isset($request->current_time))
        {
            $request['current_time'] = PinHelper::formatCurrentTime($request->current_time, true);
        }

        return $next($request);
    }
}
