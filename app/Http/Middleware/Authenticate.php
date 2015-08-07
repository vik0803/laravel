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

                if ($request->ajax()) {
                    \Session::flash('errors', new \Illuminate\Support\MessageBag([trans('messages.sessionExpired')]));
                    return response()->json(['redirect' => \Locales::getLocalizedURL()]);
                } else {
                    return redirect()->to(\Locales::getLocalizedURL())->withErrors([trans('messages.sessionExpired')]);
                }
            }
        }*/

        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(\Locales::getLocalizedURL());
            }
        }

        // \Session::put('lastActivityTime', time());

        return $next($request);
    }
}
