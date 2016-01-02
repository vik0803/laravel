<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;

class DashboardController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function dashboard()
    {
        return view('cms.' . \Config::get('app.defaultAuthRoute'));
    }

}
