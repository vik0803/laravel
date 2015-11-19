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
    protected $multipleDatatables = false;

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
                    [
                        'url' => \Locales::route('users/create'),
                        'class' => 'btn-primary js-create',
                        'icon' => 'plus',
                        'name' => trans('cms/forms.createButton')
                    ],
                    [
                        'url' => \Locales::route('users/destroy'),
                        'class' => 'btn-danger disabled js-destroy',
                        'icon' => 'trash',
                        'name' => trans('cms/forms.destroyButton')
                    ],
                ],
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
                    [
                        'url' => \Locales::route('users/create'),
                        'class' => 'btn-primary js-create',
                        'icon' => 'plus',
                        'name' => trans('cms/forms.createButton')
                    ],
                    [
                        'url' => \Locales::route('users/destroy'),
                        'class' => 'btn-danger disabled js-destroy',
                        'icon' => 'trash',
                        'name' => trans('cms/forms.destroyButton')
                    ],
                ],
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
                    [
                        'url' => \Locales::route('users/create'),
                        'class' => 'btn-primary js-create',
                        'icon' => 'plus',
                        'name' => trans('cms/forms.createButton')
                    ],
                ],
            ],
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
        $group = null;
        $table = $this->route;
        if ($request->input('table')) { // magnific popup request
            $table = $request->input('table');
            $group = $request->input('table');
        }

        if ($table == $this->route) {
            $roles = ['' => ''];
            foreach ($role::all() as $value) {
                $roles[$value->slug] = $value->name;
            }
        } else {
            $roles = [$table => $role::where('slug', $table)->get()->first()->name];
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

        $group = ($request->input('table') == $this->route ? null : $request->input('table'));
        $redirect = redirect(\Locales::route($this->route, $group));

        $newUser = User::create([
            'role_id' => $userRole,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        if ($newUser->id) {
            $successMessage = trans('cms/forms.createdSuccessfully', ['entity' => trans_choice('cms/forms.entityUsers' . ucfirst($request->input('group')), 1)]);
            $redirect->withSuccess([$successMessage]);

            if ($request->ajax()) {
                $datatables = $this->index($user, $role, $request, $group, true);
                $table = $request->input('table');
                return response()->json([
                    $table => [
                        'updateTable' => true,
                        'data' => (isset($datatables[$table]['data']) ? $datatables[$table]['data'] : false),
                        'ajax' => $datatables[$table]['ajax']
                    ],
                    'success' => $successMessage,
                    'resetExcept' => ['group']
                ]);
            }
        } else {
            $errorMessage = trans('cms/forms.createdError', ['entity' => trans_choice('cms/forms.entityUsers' . ucfirst($request->input('group')), 1)]);
            $redirect->withError([$errorMessage]);

            if ($request->ajax()) {
                return response()->json(['errors' => [$errorMessage]]);
            }
        }

        return $redirect;
    }

    /**
     * Show destroy users confirmation.
     *
     * @return View
     */
    public function confirm(Request $request)
    {
        $table = $this->route;
        if ($request->input('table')) { // magnific popup request
            $table = $request->input('table');
        }

        $view = \View::make('cms.users.destroy', compact('table'));
        if ($request->ajax()) {
            $sections = $view->renderSections();
            return $sections['content'];
        }
        return $view;
    }

    /**
     * Destroy users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Role $role, Request $request)
    {
        $group = ($request->input('table') == $this->route ? null : $request->input('table'));
        $redirect = redirect(\Locales::route($this->route, $group));

        if ($user->destroy($request->input('id'))) {
            $successMessage = trans('cms/forms.deletedSuccessfully', ['entity' => trans_choice('cms/forms.entityUsers' . ucfirst($group), count($request->input('id')))]);
            $redirect->withSuccess([$successMessage]);

            if ($request->ajax()) {
                $datatables = $this->index($user, $role, $request, $group, true);
                $table = $request->input('table');
                return response()->json([
                    $table => [
                        'updateTable' => true,
                        'data' => (isset($datatables[$table]['data']) ? $datatables[$table]['data'] : false),
                        'ajax' => $datatables[$table]['ajax']
                    ],
                    'success' => $successMessage,
                    'closePopup' => true
                ]);
            }
        } else {
            $errorMessage = trans('cms/forms.deletedError', ['entity' => trans_choice('cms/forms.entityUsers' . ucfirst($group), count($request->input('id')))]);
            $redirect->withError([$errorMessage]);

            if ($request->ajax()) {
                return response()->json(['errors' => [$errorMessage]]);
            }
        }

        return $redirect;
    }
}
