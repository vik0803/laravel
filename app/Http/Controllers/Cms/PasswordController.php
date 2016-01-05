<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    public $redirectPath;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectPath = \Locales::route();
        $this->subject = trans('passwords.reset_link');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('cms.auth.password');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject($this->getEmailSubject());
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                if ($request->ajax()) {
                    return response()->json(['success' => trans($response)]);
                } else {
                    return redirect()->back()->withSuccess([trans($response)]);
                }

            case Password::INVALID_USER:
                if ($request->ajax()) {
                    return response()->json(['errors' => [trans($response)], 'ids' => ['email']]);
                } else {
                    return redirect()->back()->withErrors(['email' => trans($response)]);
                }
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Http\Response
     */
    public function showResetForm(Request $request, $token = null)
    {
        if (is_null($token)) {
            return $this->getEmail();
        }

        $email = $request->input('email');

        return view('auth.reset')->with(compact('token', 'email'));
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                $redirect = redirect($this->redirectPath());
                if ($request->ajax()) {
                    return response()->json(['redirect' => $redirect->getTargetUrl()]);
                } else {
                    return $redirect;
                }

            default:
                if ($request->ajax()) {
                    return response()->json(['errors' => [trans($response)], 'ids' => ['email'], 'resetExcept' => ['email']]);
                } else {
                    return redirect()->back()
                            ->withInput($request->only('email'))
                            ->withErrors(['email' => trans($response)]);
                }
        }
    }
}
