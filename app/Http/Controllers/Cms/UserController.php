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
     * Single or Multiple DataTables on one page.
     *
     * @var string
     */
    protected $multipleDatatables = true;

    protected $route = 'users';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->datatables = [
            $this->route => [
                'class' => 'table-checkbox table-striped table-bordered table-hover',
                'checkbox' => true,
                'columns' => [
                    ['id' => 'name', 'name' => trans('cms/datatables.name')],
                    ['id' => 'email', 'name' => trans('cms/datatables.email')],
                ],
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
            'admins' => [
                'class' => 'table-checkbox table-striped table-bordered table-hover',
                'checkbox' => true,
                'columns' => [
                    ['id' => 'name', 'name' => trans('cms/datatables.name')],
                    ['id' => 'email', 'name' => trans('cms/datatables.email')],
                ],
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
                'checkbox' => false,
                'columns' => [
                    ['id' => 'email', 'name' => trans('cms/datatables.email')],
                    ['id' => 'name', 'name' => trans('cms/datatables.name')],
                ],
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
            if ($this->multipleDatatables) {
                return array_except($this->datatables, [$this->route]);
            } else {
                return [$this->route => $this->datatables[$this->route]];
            }
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
            $routeParameter = \Locales::getDefaultParameter($this->route, $group);
            $request->session()->put('usersRouteParameters', $routeParameter);

            $datatables = $this->getDataTables($routeParameter);
        }

        if (!$this->multipleDatatables) {
            $datatables[$group ?: $this->route]['url'] = \Locales::route($this->route, $group);
            $userRole = $group ? $role->select('id')->where('slug', $group)->get()->first()->id : false;
            $count = $userRole ? $user->where('role_id', $userRole)->count() : $user->count();
        }

        foreach ($datatables as $key => $value) {
            if ($this->multipleDatatables) {
                $datatables[$key]['url'] = \Locales::route($this->route, $key);
                $userRole = $role->select('id')->where('slug', $key)->get()->first()->id;
                $count = $userRole ? $user->where('role_id', $userRole)->count() : $user->count();
            }

            if (!$internal && $request->ajax()) {
                $datatables[$key]['reloadTable'] = true;

                $datatables[$key]['draw'] = (int)$request->input('draw', 1);
                $datatables[$key]['recordsTotal'] = $count;

                $columns = array_column($datatables[$key]['columns'], 'id');
                if ($datatables[$key]['checkbox']) {
                    array_unshift($columns, 'id');
                }
                $sql = $user->select($columns);
                $sql = $userRole ? $sql->where('role_id', $userRole) : $sql;

                $column = $request->input('columns.' . $request->input('order.0.column') . '.data', $datatables[$key]['columns'][$datatables[$key]['orderByColumn']]['id']);
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
                if (!$internal) {
                    $datatables[$key]['count'] = $count;

                    $size = ($count <= 100 ? 'small' : ($count <= 1000 ? 'medium' : 'large'));
                    $datatables[$key]['size'] = $size;
                }

                if ($count < \Config::get('datatables.clientSideLimit')) {
                    $datatables[$key]['ajax'] = false;

                    $columns = array_column($datatables[$key]['columns'], 'id');
                    if ($datatables[$key]['checkbox']) {
                        array_unshift($columns, 'id');
                    }
                    $sql = $user->select($columns);
                    $sql = $userRole ? $sql->where('role_id', $userRole) : $sql;

                    if (!$internal) {
                        $sql = $sql->orderBy($datatables[$key]['columns'][$datatables[$key]['orderByColumn']]['id'], $datatables[$key]['order']);
                    }

                    $datatables[$key]['data'] = $sql->get();
                } else {
                    $datatables[$key]['ajax'] = true;
                }
            }
        }

        if ($internal) {
            return $datatables;
        } else {
            $multipleDatatables = $this->multipleDatatables;
            return view('cms.users.index', compact('datatables', 'multipleDatatables'));
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

        $successMessage = trans('cms/forms.createdSuccessfully', ['entity' => trans('cms/forms.entityUsers' . ucfirst($request->input('group')))]);

        $redirect = redirect(\Locales::route($this->route, $group))->withSuccess([$successMessage]);
        if ($request->ajax()) {
            $datatables = $this->index($user, $role, $request, $group, true);
            $table = $group ?: $this->route;
            return response()->json([$table => ['updateTable' => true, 'data' => (isset($datatables[$table]['data']) ? $datatables[$table]['data'] : false), 'ajax' => $datatables[$table]['ajax']], 'success' => $successMessage, 'resetExcept' => ['group']]);
        } else {
            return $redirect;
        }
    }
}
