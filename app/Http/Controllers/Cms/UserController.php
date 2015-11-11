<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use Validator;
use Illuminate\Http\Request;
use App\Http\Requests\CreateUserRequest;

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
     * Get users Data.
     *
     * @return JSON Users Data
     */
    public function getData(User $user, Role $role, Request $request, $group = null)
    {
        $userRole = $group ? $role->select('id')->where('slug', $group)->get()->first()->id : false;

        $count = $userRole ? $user->where('role_id', $userRole)->count() : $user->count();
        if ($count <= \Config::get('datatables.clientSideLimit')) {
            $sql = $user->select('name', 'email');
            $sql = $userRole ? $sql->where('role_id', $userRole) : $sql;
            $datatables['data'] = $sql->get();
        } else {
            $datatables['ajax'] = \Locales::route('users', $request->session()->get('usersRouteParameters'));
        }

        return $datatables;
    }

    /**
     * Show users.
     *
     * @return View
     */
    public function index(User $user, Role $role, Request $request, $group = null)
    {
        $userRole = $group ? $role->select('id')->where('slug', $group)->get()->first()->id : false;

        $request->session()->put('usersRouteName', \Slug::getRouteName());
        $routeParameter = \Locales::getDefaultParameter(\Slug::getRouteName(), $group);
        $request->session()->put('usersRouteParameters', $routeParameter);

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

            $column = $request->input('columns.' . $request->input('order.0.column') . '.data', 'name');
            $dir = $request->input('order.0.dir', 'asc');

            $count = $userRole ? $user->where('role_id', $userRole)->count() : $user->count();
            $datatables['draw'] = (int)$request->input('draw', 1);
            $datatables['recordsTotal'] = $count;

            $sql = $user->select('name', 'email');
            $sql = $userRole ? $sql->where('role_id', $userRole) : $sql;
            $sql = $sql->orderBy($column, $dir);

            if ($request->input('search.value')) {
                $datatables['search'] = true;
                $datatables['data'] = $sql->where('name', 'like', '%' . $request->input('search.value') . '%')->orWhere('email', 'like', '%' . $request->input('search.value') . '%')->skip($request->input('start'))->take($request->input('length'))->get();
                $sql = $userRole ? $user->where('role_id', $userRole) : $user;
                $datatables['recordsFiltered'] = $sql->where('name', 'like', '%' . $request->input('search.value') . '%')->orWhere('email', 'like', '%' . $request->input('search.value') . '%')->count();
            } else {
                $datatables['recordsFiltered'] = $count;
                if ($request->input('length') > 0) { // All = -1
                    if ($request->input('start') > 0) {
                        $sql = $sql->skip($request->input('start'));
                    }

                    $sql = $sql->take($request->input('length'));
                }

                $datatables['data'] = $sql->get();
            }

            return response()->json($datatables);
        } else {
            $count = $userRole ? $user->where('role_id', $userRole)->count() : $user->count();
            $datatables = ['count' => $count];

            if ($count <= \Config::get('datatables.clientSideLimit')) {
                $sql = $user->select('name', 'email');
                $sql = $userRole ? $sql->where('role_id', $userRole) : $sql;
                $datatables['data'] = $sql->get()->toJson();
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
    public function create(Request $request, Role $role)
    {
        $group = $request->session()->get('usersRouteParameters');
        $roles = ['' => ''];
        foreach ($role::all() as $value) {
            $roles[$value->slug] = $value->name;
        }

        $view = \View::make('cms.users.create', compact('roles', 'group'));
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
    public function store(User $user, Role $role, CreateUserRequest $request)
    {
        $userRole = $request->input('group') ? $role->select('id')->where('slug', $request->input('group'))->get()->first()->id : false;

        User::create([
            'role_id' => $userRole,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        $successMessage = trans('cms/forms.createdSuccessfully', ['entity' => trans('cms/forms.entityUsers' . ucfirst($request->session()->get('usersRouteParameters')))]);

        $redirect = redirect(\Locales::route('users', $request->session()->get('usersRouteParameters')))->withSuccess([$successMessage]);
        if ($request->ajax()) {
            $datatables = $this->getData($user, $role, $request, $request->session()->get('usersRouteParameters'));
            return response()->json(['reloadTable' => true, 'data' => (isset($datatables['data']) ? $datatables['data'] : ''), 'ajaxURL' => (isset($datatables['ajax']) ? $datatables['ajax'] : ''), 'success' => $successMessage]);
        } else {
            return $redirect;
        }
    }
}
