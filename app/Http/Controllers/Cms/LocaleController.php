<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;

class LocaleController extends Controller {

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function index()
    {
        return view('cms.locales.index');
    }

}
