<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;

class PasswordController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests.
	|
	*/

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The password broker implementation.
     *
     * @var PasswordBroker
     */
    protected $passwords;

	/**
	 * Create a new password controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\PasswordBroker  $passwords
	 * @return void
	 */
	public function __construct(Guard $auth, PasswordBroker $passwords)
	{
		$this->auth = $auth;
		$this->passwords = $passwords;

        $this->middleware('guest');
	}

    /**
     * Display the form to request a password reset link.
     *
     * @return Response
     */
    public function show()
    {
        return view('cms.password');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function pf(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $response = $this->passwords->sendResetLink($request->only('email'), function($m)
        {
            $m->subject($this->getEmailSubject());
        });

        switch ($response)
        {
            case PasswordBroker::RESET_LINK_SENT:
                return redirect()->back()->with('status', trans($response));

            case PasswordBroker::INVALID_USER:
                return redirect()->back()->withErrors(['email' => trans($response)]);
        }
    }

    /**
     * Get the e-mail subject line to be used for the reset link email.
     *
     * @return string
     */
    protected function getEmailSubject()
    {
        return isset($this->subject) ? $this->subject : 'Your Password Reset Link';
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return Response
     */
    public function getReset($token = null)
    {
        if (is_null($token))
        {
            abort(404);
        }

        return view('cms.reset')->with('token', $token);
    }

    /**
     * Reset the given user's password.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postReset(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = $this->passwords->reset($credentials, function($user, $password)
        {
            $user->password = bcrypt($password);

            $user->save();

            $this->auth->login($user);
        });

        switch ($response)
        {
            case PasswordBroker::PASSWORD_RESET:
                return redirect($this->redirectPath());

            default:
                return redirect()->back()
                            ->withInput($request->only('email'))
                            ->withErrors(['email' => trans($response)]);
        }
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (property_exists($this, 'redirectPath'))
        {
            return $this->redirectPath;
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
    }

}
