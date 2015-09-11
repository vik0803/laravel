<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;

class PageController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Page Controller
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
     * Show a page to the user.
     *
     * @return Response
     */
    public function pages()
    {
        return view('cms.pages');
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
