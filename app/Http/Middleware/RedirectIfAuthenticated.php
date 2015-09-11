<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class RedirectIfAuthenticated
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->check()) {
            if (redirect()->back()->getTargetUrl() != $request->fullUrl()) {
                return redirect()->back();
            } else { // this happens when session is expired ad there is no HTTP_REFFERER URL
                return redirect(\Locales::getLocalizedURL(\Config::get('app.defaultAuthRoute')));
            }
        }

        return $next($request);
    }
}
