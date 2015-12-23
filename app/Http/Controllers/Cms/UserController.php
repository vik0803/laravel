<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use Validator;
use App\Services\DataTable;
use Illuminate\Http\Request;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\EditUserRequest;

class UserController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| User Controller
	|--------------------------------------------------------------------------
	*/

    protected $route = 'users';
    protected $datatables;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->datatables = [
            'admins' => [
                'url' => \Locales::route($this->route, true),
                'class' => 'table-checkbox table-striped table-bordered table-hover',
                'checkbox' => [
                    'selector' => $this->route . '.id',
                    'id' => 'id',
                ],
                'columns' => [
                    [
                        'selector' => $this->route . '.name',
                        'id' => 'name',
                        'name' => trans('cms/datatables.name'),
                        'search' => true,
                    ],
                    [
                        'selector' => $this->route . '.email',
                        'id' => 'email',
                        'name' => trans('cms/datatables.email'),
                        'search' => true,
                    ],
                ],
                'orderByColumn' => 0,
                'order' => 'asc',
                'buttons' => [
                    [
                        'url' => \Locales::route($this->route . '/create'),
                        'class' => 'btn-primary js-create',
                        'icon' => 'plus',
                        'name' => trans('cms/forms.createButton'),
                    ],
                    [
                        'url' => \Locales::route($this->route . '/edit'),
                        'class' => 'btn-warning disabled js-edit',
                        'icon' => 'edit',
                        'name' => trans('cms/forms.editButton'),
                    ],
                    [
                        'url' => \Locales::route($this->route . '/delete'),
                        'class' => 'btn-danger disabled js-destroy',
                        'icon' => 'trash',
                        'name' => trans('cms/forms.deleteButton'),
                    ],
                ],
            ],
            'operators' => [
                'url' => \Locales::route($this->route, true),
                'class' => 'table-checkbox table-striped table-bordered table-hover',
                'checkbox' => [
                    'selector' => $this->route . '.id',
                    'id' => 'id',
                ],
                'columns' => [
                    [
                        'selector' => $this->route . '.name',
                        'id' => 'name',
                        'name' => trans('cms/datatables.name'),
                        'search' => true,
                    ],
                    [
                        'selector' => $this->route . '.email',
                        'id' => 'email',
                        'name' => trans('cms/datatables.email'),
                        'search' => true,
                    ],
                ],
                'orderByColumn' => 0,
                'order' => 'asc',
                'buttons' => [
                    [
                        'url' => \Locales::route($this->route . '/create'),
                        'class' => 'btn-primary js-create',
                        'icon' => 'plus',
                        'name' => trans('cms/forms.createButton'),
                    ],
                    [
                        'url' => \Locales::route($this->route . '/edit'),
                        'class' => 'btn-warning disabled js-edit',
                        'icon' => 'edit',
                        'name' => trans('cms/forms.editButton'),
                    ],
                    [
                        'url' => \Locales::route($this->route . '/delete'),
                        'class' => 'btn-danger disabled js-destroy',
                        'icon' => 'trash',
                        'name' => trans('cms/forms.deleteButton'),
                    ],
                ],
            ],
            $this->route => [
                'url' => \Locales::route($this->route),
                'class' => 'table-checkbox table-striped table-bordered table-hover',
                'checkbox' => [
                    'selector' => $this->route . '.id',
                    'id' => 'id',
                ],
                'columns' => [
                    [
                        'selector' => $this->route . '.name',
                        'id' => 'name',
                        'name' => trans('cms/datatables.name'),
                        'search' => true,
                    ],
                    [
                        'selector' => $this->route . '.email',
                        'id' => 'email',
                        'name' => trans('cms/datatables.email'),
                        'search' => true,
                    ],
                ],
                'orderByColumn' => 0,
                'order' => 'asc',
                'buttons' => [
                    [
                        'url' => \Locales::route($this->route . '/create'),
                        'class' => 'btn-primary js-create',
                        'icon' => 'plus',
                        'name' => trans('cms/forms.createButton'),
                    ],
                    [
                        'url' => \Locales::route($this->route . '/edit'),
                        'class' => 'btn-warning disabled js-edit',
                        'icon' => 'edit',
                        'name' => trans('cms/forms.editButton'),
                    ],
                    [
                        'url' => \Locales::route($this->route . '/delete'),
                        'class' => 'btn-danger disabled js-destroy',
                        'icon' => 'trash',
                        'name' => trans('cms/forms.deleteButton'),
                    ],
                ],
            ],
        ];
    }

    /**
     * Show users.
     *
     * @return View
     */
    public function index(DataTable $datatable, User $user, Role $role, Request $request, $group = null)
    {
        $routeParameter = \Locales::getDefaultParameter($this->route, $group);
        if ($routeParameter) {
            $user = $user->where('role_id', $role->select('id')->where('slug', $routeParameter)->firstOrFail()->id);
        } else {
            $routeParameter = $this->route;
        }

        $datatable->setup($user, $routeParameter, $this->datatables[$routeParameter]);

        $datatables = $datatable->getTables();

        if ($request->ajax()) {
            return response()->json($datatables);
        } else {
            return view('cms.' . $this->route . '.index', compact('datatables'));
        }
    }

    /**
     * Show create new users view.
     *
     * @return View
     */
    public function create(Role $role, Request $request)
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
            $roles = [$table => $role::where('slug', $table)->firstOrFail()->name];
        }

        $view = \View::make('cms.' . $this->route . '.create', compact('roles', 'group', 'table'));
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
    public function store(DataTable $datatable, User $user, Role $role, CreateUserRequest $request)
    {
        $userRole = $role->select('id')->where('slug', $request->input('group'))->firstOrFail()->id;
        $group = ($request->input('table') == $this->route ? null : $request->input('table'));
        $param = $group ? \Locales::getRouteParameters($this->route)[$group] : true;
        $redirect = redirect(\Locales::route($this->route, $param));

        $newUser = User::create([
            'role_id' => $userRole,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        if ($newUser->id) {
            $successMessage = trans('cms/forms.storedSuccessfully', ['entity' => trans_choice('cms/forms.entityUsers' . ucfirst($request->input('group')), 1)]);
            $redirect->withSuccess([$successMessage]);

            if ($request->ajax()) {
                $user = $user->where('role_id', $userRole);
                $datatable->setup($user, $request->input('table'), $this->datatables[$request->input('table')], true);
                $datatable->setOption('url', \Locales::route($this->route, $param));
                $datatables = $datatable->getTables();

                return response()->json($datatables + [
                    'success' => $successMessage,
                    'resetExcept' => ['group']
                ]);
            }
        } else {
            $errorMessage = trans('cms/forms.createError', ['entity' => trans_choice('cms/forms.entityUsers' . ucfirst($request->input('group')), 1)]);
            $redirect->withError([$errorMessage]);

            if ($request->ajax()) {
                return response()->json(['errors' => [$errorMessage]]);
            }
        }

        return $redirect;
    }

    /**
     * Show delete users confirmation.
     *
     * @return View
     */
    public function delete(Request $request)
    {
        $table = $this->route;
        if ($request->input('table')) { // magnific popup request
            $table = $request->input('table');
        }

        $view = \View::make('cms.' . $this->route . '.delete', compact('table'));
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
    public function destroy(DataTable $datatable, User $user, Role $role, Request $request)
    {
        $group = ($request->input('table') == $this->route ? null : $request->input('table'));
        $param = true;
        if ($group) {
            $param = \Locales::getRouteParameters($this->route)[$group];
        }
        $redirect = redirect(\Locales::route($this->route, $param));
        $count = count($request->input('id'));

        if ($count > 0 && $user->destroy($request->input('id'))) {
            $successMessage = trans('cms/forms.destroyedSuccessfully', ['entity' => trans_choice('cms/forms.entityUsers' . ucfirst($group), $count)]);
            $redirect->withSuccess([$successMessage]);

            if ($request->ajax()) {
                if ($group) {
                    $user = $user->where('role_id', $role->select('id')->where('slug', $group)->firstOrFail()->id);
                }
                $datatable->setup($user, $request->input('table'), $this->datatables[$request->input('table')], true);
                $datatable->setOption('url', \Locales::route($this->route, $param));
                $datatables = $datatable->getTables();

                return response()->json($datatables + [
                    'success' => $successMessage,
                    'closePopup' => true
                ]);
            }
        } else {
            if ($count > 0) {
                $errorMessage = trans('cms/forms.deleteError', ['entity' => trans_choice('cms/forms.entityUsers' . ucfirst($group), $count)]);
            } else {
                $errorMessage = trans('cms/forms.countError', ['entity' => trans_choice('cms/forms.entityUsers' . ucfirst($group), 1)]);
            }

            $redirect->withError([$errorMessage]);

            if ($request->ajax()) {
                return response()->json(['errors' => [$errorMessage]]);
            }
        }

        return $redirect;
    }

    /**
     * Edit user.
     *
     * @return View
     */
    public function edit(Role $role, Request $request, $id = null)
    {
        $user = User::findOrFail($id);

        $group = $user->role->slug;
        $table = $this->route;
        if ($request->input('table')) { // magnific popup request
            $table = $request->input('table');
        }

        $roles = ['' => ''];
        foreach ($role::all() as $value) {
            $roles[$value->slug] = $value->name;
        }

        $view = \View::make('cms.' . $this->route . '.create', compact('user', 'roles', 'group', 'table'));
        if ($request->ajax()) {
            $sections = $view->renderSections();
            return $sections['content'];
        }
        return $view;
    }

    /**
     * Update user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(DataTable $datatable, Role $role, EditUserRequest $request)
    {
        $user = User::findOrFail($request->input('id'))->first();

        $userRole = $role->select('id')->where('slug', $request->input('group'))->firstOrFail()->id;

        $group = ($request->input('table') == $this->route ? null : $request->input('table'));
        $param = $group ? \Locales::getRouteParameters($this->route)[$group] : true;
        $redirect = redirect(\Locales::route($this->route, $param));

        if ($user->update([
            'role_id' => $userRole,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ])) {
            $successMessage = trans('cms/forms.updatedSuccessfully', ['entity' => trans_choice('cms/forms.entityUsers' . ucfirst($group), 1)]);
            $redirect->withSuccess([$successMessage]);

            if ($request->ajax()) {
                $datatable->setup(($group ? $user->where('role_id', $role->select('id')->where('slug', $group)->firstOrFail()->id) : $user), $request->input('table'), $this->datatables[$request->input('table')], true);
                $datatable->setOption('url', \Locales::route($this->route, $param));
                $datatables = $datatable->getTables();

                return response()->json($datatables + [
                    'success' => $successMessage,
                    'closePopup' => true
                ]);
            }
        } else {
            $errorMessage = trans('cms/forms.editError', ['entity' => trans_choice('cms/forms.entityUsers' . ucfirst($request->input('group')), 1)]);
            $redirect->withError([$errorMessage]);

            if ($request->ajax()) {
                return response()->json(['errors' => [$errorMessage]]);
            }
        }

        return $redirect;
    }
}
