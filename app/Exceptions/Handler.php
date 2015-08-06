<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            if ($request->ajax()) {
                $request->flashExcept('_token');
                $request->session()->flash('errors', new \Illuminate\Support\MessageBag([trans('validation.tokenMismatchException')]));
                return response()->json(['redirect' => $request->fullUrl()]);
            } else {
                return redirect()->back()->withInput(\Input::except('_token'))->withErrors([trans('validation.tokenMismatchException')]);
            }
        }

        return parent::render($request, $e);
    }
}
