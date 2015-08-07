<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * I could have used the App\Exceptions\Handler.php render method, but...
     *
     * ...redirect back with error message does not work when session is expired,
     * because the session is regenrated 2 times...
     *
     * ...so I overwrite the BaseVerifier handle method instead.
     *
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed or redirects back with error (TokenMismatchException)
     *
     */
    public function handle($request, \Closure $next)
    {
        if ($this->isReading($request) || $this->shouldPassThrough($request) || $this->tokensMatch($request)) {
            return $this->addCookieToResponse($request, $next($request));
        }

        // throw new TokenMismatchException;

        if ($request->ajax()) {
            $request->flashExcept('_token');
            $request->session()->flash('errors', new \Illuminate\Support\MessageBag([trans('validation.tokenMismatchException')]));
            return response()->json(['redirect' => $request->fullUrl()]);
        } else {
            return redirect()->to($request->fullUrl())->withInput(\Input::except('_token'))->withErrors([trans('validation.tokenMismatchException')]);
        }
    }
}
