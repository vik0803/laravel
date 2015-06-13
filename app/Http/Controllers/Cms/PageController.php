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
        $this->middleware('auth', ['except' => 'showSubpage']);
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function show()
    {
        return view('cms.page');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function showSubpage()
    {
        return view('cms.subpage');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function home()
    {
        return view('cms.home');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function getReset()
    {
        return view('cms.subpage');
    }

}
