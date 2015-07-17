<?php

namespace App\Services\Cms\Auth;

use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ResetsPasswords
{
    use RedirectsUsers;

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEmail()
    {
        return view('cms.auth.password');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject(trans('passwords.reset_link'));
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                if ($request->ajax()) {
                    return response()->json(['success' => trans($response)]);
                } else {
                    return redirect()->back()->with('success', trans($response));
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
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function getReset($token = null)
    {
        if (is_null($token)) {
            throw new NotFoundHttpException;
        }

        return view('cms.auth.reset')->with('token', $token);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

        $response = Password::reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                if ($request->ajax()) {
                    return response()->json(['redirect' => $this->redirectPath()]);
                } else {
                    return redirect($this->redirectPath());
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

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = bcrypt($password);

        $user->save();

        Auth::login($user);
    }
}
