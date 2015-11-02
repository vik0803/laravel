<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\User;
use Validator;
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
     * Show users.
     *
     * @return View
     */
    public function index(User $user, Request $request, $group = null)
    {
        // group ?

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

            return view('cms.users.index', compact('datatables'));
        }
    }

    /**
     * Show create new users view.
     *
     * @return View
     */
    public function create(Request $request)
    {
        $view = \View::make('cms.users.create');
        if ($request->ajax()) {
            $sections = $view->renderSections();
            return $sections['content'];
        }
        return $view;
    }

    /**
     * Store new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => array('required', 'confirmed', 'regex:/^(?=.*\p{Ll})(?=.*\p{Lu})(?=.*[\p{N}\p{P}]).{6,}$/u'), // /^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d\W]).{6,}$/ // http://www.zorched.net/2009/05/08/password-strength-validation-with-regular-expressions/
        ]);

        dd(User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]));

        /*$redirect = redirect($this->redirectPath());
        if ($request->ajax()) {
            return response()->json(['redirect' => $redirect->getTargetUrl()]);
        } else {
            return $redirect;
        }*/
    }

}
