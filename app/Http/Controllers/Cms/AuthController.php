<?php

namespace App\Http\Controllers\Cms;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    public $redirectPath;
    public $redirectAfterLogout;
    public $maxLoginAttempts = 5;
    public $lockoutTime = 60;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectPath = \Locales::route();
        $this->redirectAfterLogout = \Locales::route('/');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => array('required', 'confirmed', 'regex:/^(?=.*\p{Ll})(?=.*\p{Lu})(?=.*[\p{N}\p{P}]).{6,}$/u'), // /^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d\W]).{6,}$/ // http://www.zorched.net/2009/05/08/password-strength-validation-with-regular-expressions/
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('cms.auth.login');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $throttles
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request, $throttles)
    {
        if ($throttles) {
            $this->clearLoginAttempts($request);
        }

        if (method_exists($this, 'authenticated')) {
            return $this->authenticated($request, Auth::user());
        }

        $redirect = redirect()->intended($this->redirectPath());
        if ($request->ajax()) {
            return response()->json(['redirect' => $redirect->getTargetUrl()]);
        } else {
            return $redirect;
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param \Illuminate\Http\Request  $response
     * @return \Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(['errors' => [$this->getFailedLoginMessage()], 'ids' => [$this->loginUsername()], 'resetOnly' => ['password']]);
        } else {
            return redirect()->back()
                ->withInput($request->only($this->loginUsername(), 'remember'))
                ->withErrors([
                    $this->loginUsername() => $this->getFailedLoginMessage(),
                ]);
        }
    }

    /**
     * Get the failed login message.
     *
     * @return string
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('cms.auth.failed') ? Lang::get('cms.auth.failed') : 'These credentials do not match our records.';
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('cms.auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        Auth::login($this->create($request->all()));

        $redirect = redirect($this->redirectPath());
        if ($request->ajax()) {
            return response()->json(['redirect' => $redirect->getTargetUrl()]);
        } else {
            return $redirect;
        }
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = app(RateLimiter::class)->availableIn(
            $this->getThrottleKey($request)
        );

        if ($request->ajax()) {
            return response()->json(['errors' => [$this->getLockoutErrorMessage($seconds)], 'ids' => [$this->loginUsername()], 'resetOnly' => ['password']]);
        } else {
            return redirect()->back()
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getLockoutErrorMessage($seconds),
            ]);
        }
    }

    /**
     * Get the login lockout error message.
     *
     * @param  int  $seconds
     * @return string
     */
    protected function getLockoutErrorMessage($seconds)
    {
        return Lang::has('cms.auth.throttle') ? Lang::get('cms.auth.throttle', ['seconds' => $seconds]) : 'Too many login attempts. Please try again in ' . $seconds . ' seconds.';
    }
}
