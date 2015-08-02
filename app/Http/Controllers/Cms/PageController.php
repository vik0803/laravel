<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;

class PageController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the application's "dashboard" for users that
	| are authenticated.
	|
	*/

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function page()
    {
        return view('cms.page');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function dashboard()
    {
        return view('cms.dashboard');
    }

}
