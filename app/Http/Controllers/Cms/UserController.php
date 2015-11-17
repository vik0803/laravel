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
        $this->datatables = [
            'admins' => [
                'class' => 'table-striped table-bordered table-hover',
                'columns' => ['name', 'email'],
                'orderByColumn' => 0,
                'order' => 'asc',
                'buttons' => [
                    0 => [
                        'url' => \Locales::route('users/create'),
                        'class' => 'btn-primary js-create',
                        'icon' => 'plus',
                        'name' => trans('cms/forms.createUserButton')
                    ]
                ]
            ],
            'operators' => [
                'class' => '',
                'columns' => ['email', 'name'],
                'orderByColumn' => 0,
                'order' => 'asc',
                'buttons' => [
                    0 => [
                        'url' => \Locales::route('users/create'),
                        'class' => 'btn-primary js-create',
                        'icon' => 'plus',
                        'name' => trans('cms/forms.createUserButton')
                    ]
                ]
            ]
        ];
    }

    public function getDataTables($group)
    {
        $datatables = [];
        if ($group) {
            $datatables = [$group => $this->datatables[$group]];
        } else {
            $datatables = $this->datatables;
        }

        return $datatables;
    }

    /**
     * Show users.
     *
     * @return View
     */
    public function index(User $user, Role $role, Request $request, $group = null, $internal = false)
    {
        if ($internal) {
            $datatables = $this->getDataTables($group);
        } else {
            $request->session()->put('usersRouteName', \Slug::getRouteName());
            $routeParameter = \Locales::getDefaultParameter(\Slug::getRouteName(), $group);
            $request->session()->put('usersRouteParameters', $routeParameter);

            $datatables = $this->getDataTables($routeParameter);
        }

        //$userRole = $group ? $role->select('id')->where('slug', $group)->get()->first()->id : false;

        foreach ($datatables as $key => $value) {
            $datatables[$key]['url'] = \Locales::route($request->session()->get('usersRouteName', \Slug::getRouteName()), $key);

            $userRole = $role->select('id')->where('slug', $key)->get()->first()->id;

            if (!$internal && $request->ajax()) {
                $datatables[$key]['reloadTable'] = true;

                $count = $userRole ? $user->where('role_id', $userRole)->count() : $user->count();
                $datatables[$key]['draw'] = (int)$request->input('draw', 1);
                $datatables[$key]['recordsTotal'] = $count;

                $sql = $user->select($datatables[$key]['columns']);
                $sql = $userRole ? $sql->where('role_id', $userRole) : $sql;

                $column = $request->input('columns.' . $request->input('order.0.column') . '.data', $datatables[$key]['columns'][$datatables[$key]['orderByColumn']]);
                $dir = $request->input('order.0.dir', $datatables[$key]['order']);
                $sql = $sql->orderBy($column, $dir);

                if ($request->input('search.value')) {
                    $sql = $sql->where(function($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->input('search.value') . '%')->orWhere('email', 'like', '%' . $request->input('search.value') . '%');
                    });

                    $sql2 = $sql;

                    if ($request->input('length') > 0) { // All = -1
                        if ($request->input('start') > 0) {
                            $sql = $sql->skip($request->input('start'));
                        }

                        $sql = $sql->take($request->input('length'));
                    }

                    $datatables[$key]['search'] = true;
                    $datatables[$key]['data'] = $sql->get();
                    $datatables[$key]['recordsFiltered'] = $sql2->count();
                } else {
                    $datatables[$key]['recordsFiltered'] = $count;

                    if ($request->input('length') > 0) { // All = -1
                        if ($request->input('start') > 0) {
                            $sql = $sql->skip($request->input('start'));
                        }

                        $sql = $sql->take($request->input('length'));
                    }

                    $datatables[$key]['data'] = $sql->get();
                }

                return response()->json($datatables);
            } else {
                $count = $userRole ? $user->where('role_id', $userRole)->count() : $user->count();

                if (!$internal) {
                    $datatables[$key]['count'] = $count;

                    $size = ($count <= 100 ? 'Small' : ($count <= 1000 ? 'Medium' : 'Large'));
                    $datatables[$key]['size'] = $size;
                }

                if ($count < \Config::get('datatables.clientSideLimit')) {
                    $datatables[$key]['ajax'] = false;

                    $sql = $user->select($datatables[$key]['columns']);
                    $sql = $userRole ? $sql->where('role_id', $userRole) : $sql;

                    if ($internal) {
                        $datatables[$key]['data'] = $sql->get();
                    } else {
                        $sql = $sql->orderBy($datatables[$key]['columns'][$datatables[$key]['orderByColumn']], $datatables[$key]['order']);
                        $datatables[$key]['data'] = $sql->get()->toJson();
                    }
                } else {
                    $datatables[$key]['ajax'] = true;
                }
            }
        }

        if ($internal) {
            return $datatables;
        } else {
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
        $roles = ['' => ''];
        foreach ($role::all() as $value) {
            $roles[$value->slug] = $value->name;
        }

        $table = false;
        if ($request->input('table')) { // magnific popup request
            $table = $request->input('table');
            $group = $table;
        } else {
            $group = $request->session()->get('usersRouteParameters');
        }

        $view = \View::make('cms.users.create', compact('roles', 'group', 'table'));
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

        $group = $request->session()->get('usersRouteParameters') ?: $request->input('table');

        $successMessage = trans('cms/forms.createdSuccessfully', ['entity' => trans('cms/forms.entityUsers' . ucfirst($group))]);

        $redirect = redirect(\Locales::route($request->session()->get('usersRouteName'), $group))->withSuccess([$successMessage]);
        if ($request->ajax()) {
            $datatables = $this->index($user, $role, $request, $group, true);
            return response()->json([$group => ['updateTable' => true, 'data' => (isset($datatables[$group]['data']) ? $datatables[$group]['data'] : false), 'ajax' => $datatables[$group]['ajax']], 'success' => $successMessage, 'resetExcept' => ['group']]);
        } else {
            return $redirect;
        }
    }
}
