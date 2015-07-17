<?php

namespace App\Services\Cms\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait AuthenticatesUsers
{
    use RedirectsUsers;

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        return view('cms.auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $this->getCredentials($request);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            if ($request->ajax()) {
                return response()->json(['redirect' => $this->redirectPath()]);
            } else {
                return redirect()->intended($this->redirectPath());
            }
        }

        if ($request->ajax()) {
            return response()->json(['errors' => [trans('cms/auth.failedLogin')], 'ids' => ['email'], 'resetOnly' => ['password']]);
        } else {
            return redirect($this->loginPath())
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => trans('cms/auth.failedLogin'),
                ]);
        }
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        return $request->only('email', 'password');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        Auth::logout();

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : \Locales::getLocalizedURL());
    }

    /**
     * Get the path to the login route.
     *
     * @return string
     */
    public function loginPath()
    {
        return property_exists($this, 'loginPath') ? $this->loginPath : \Locales::getLocalizedURL();
    }
}
