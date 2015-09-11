<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
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
        /* This however doe not take into account the 'remember me' checkbox.
        if (\Session::has('lastActivityTime')) {
            if (time() - \Session::get('lastActivityTime') > (\Config::get('session.lifetime') * 60)) {
                \Session::forget('lastActivityTime');
                $this->auth->logout();

                $redirect = redirect()->to(\Locales::getLocalizedURL())->withErrors([trans('messages.sessionExpired')]);
                if ($request->ajax()) {
                    return response()->json(['redirect' => $redirect->getTargetUrl()]);
                } else {
                    return $redirect;
                }
            }
        }*/

        if ($this->auth->guest()) {
            $redirect = redirect()->guest(\Locales::getLocalizedURL())->withErrors([trans('messages.sessionExpired')])->with('session_expired', true);
            if ($request->ajax()) {
                // return response('Unauthorized.', 401);
                return response()->json(['redirect' => $redirect->getTargetUrl()]);
            } else {
                return $redirect;
            }
        }

        // \Session::put('lastActivityTime', time());

        return $next($request);
    }
}
