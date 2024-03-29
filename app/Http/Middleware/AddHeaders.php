<?php

namespace App\Http\Middleware;

use Closure;

class AddHeaders
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

        $response->header('Cache-Control', 'private, no-cache, max-age=0, must-revalidate, no-store, post-check=0, pre-check=0');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');

        return $response;
    }
}
