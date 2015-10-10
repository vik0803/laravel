<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| User Controller
	|--------------------------------------------------------------------------
	*/

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show users default view.
     *
     * @return View
     */
    public function users()
    {
        return view('cms.users');
    }

    /**
     * Show admins.
     *
     * @return View
     */
    public function admins(User $user, Request $request)
    {
        if ($request->ajax()) {
            /* State can't be saved with pipelining and deferLoading enabled and first result page in html
            $request->session()->put('datatablesStart', $request->input('start', 0));
            if ($request->input('length')) {
                $length = $request->input('length') / \Config::get('datatables.pipeline');
            } else {
                $length = \Config::get('datatables.pageLengthLarge');
            }
            $request->session()->put('datatablesLength', $length);
            $request->session()->put('datatablesSearch', $request->input('search.value', ''));*/

            $column = $request->input('columns.' . $request->input('order.0.column') . '.data');
            $dir = $request->input('order.0.dir');

            $count = $user->count();
            $datatables['draw'] = (int)$request->input('draw', 1);
            $datatables['recordsTotal'] = $count;

            $data = $user->select('name', 'email')->orderBy($column, $dir);

            if ($request->input('search.value')) {
                $datatables['data'] = $data->where('name', 'like', '%' . $request->input('search.value') . '%')->orWhere('email', 'like', '%' . $request->input('search.value') . '%')->skip($request->input('start'))->take($request->input('length'))->get();
                $datatables['recordsFiltered'] = $user->where('name', 'like', '%' . $request->input('search.value') . '%')->orWhere('email', 'like', '%' . $request->input('search.value') . '%')->count();
            } else {
                $datatables['recordsFiltered'] = $count;
                if ($request->input('length') > 0) { // All = -1
                    if ($request->input('start') > 0) {
                        $data = $data->skip($request->input('start'));
                    }

                    $data = $data->take($request->input('length'));
                }

                $datatables['data'] = $data->get();
            }

            return response()->json($datatables);
        } else {
            $count = $user->count();
            $datatables = ['count' => $count];

            if ($count <= \Config::get('datatables.clientSideLimit')) {
                $datatables['data'] = $user->select('name', 'email')->get()->toJson();
            } else {
                $datatables['ajax'] = $request->url();
                /* State can't be saved with pipelining and deferLoading enabled and first result page in html
                $data = $user->select('name', 'email');

                if ($request->session()->has('datatablesSearch') && !empty($request->session()->get('datatablesSearch'))) {
                    $data = $data->where('name', 'like', '%' . $request->session()->get('datatablesSearch') . '%')->orWhere('email', 'like', '%' . $request->session()->get('datatablesSearch') . '%');
                }

                $limit = $request->session()->get('datatablesLength', \Config::get('datatables.pageLengthLarge'));
                if ($limit > 0) { // All = -1
                    if ($request->session()->has('datatablesStart') && $request->session()->get('datatablesStart') > 0) {
                        $data = $data->skip($request->session()->get('datatablesStart'));
                    }

                    $data = $data->take($limit);
                }

                $datatables['data'] = $data->get();*/
            }

            return view('cms.users', compact('datatables'));
        }
    }

}
