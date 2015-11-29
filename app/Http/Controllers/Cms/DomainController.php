<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain;

class DomainController extends Controller {

	public $datatables;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->datatables = [
            'domains' => [
                'class' => 'table-checkbox table-striped table-bordered table-hover',
                'checkbox' => true,
                'columns' => [
                    ['id' => 'name', 'name' => trans('cms/datatables.name')],
                    ['id' => 'slug', 'name' => trans('cms/datatables.slug')],
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
                        'url' => \Locales::route('users/edit'),
                        'class' => 'btn-warning disabled js-edit',
                        'icon' => 'edit',
                        'name' => trans('cms/forms.editButton')
                    ],
                    [
                        'url' => \Locales::route('users/delete'),
                        'class' => 'btn-danger disabled js-destroy',
                        'icon' => 'trash',
                        'name' => trans('cms/forms.deleteButton')
                    ],
                ],
            ],
        ];
    }

    public function index(Domain $domain, Request $request)
    {
        $datatables = $this->datatables;
        $datatables['domains']['url'] = \Locales::route('settings/domains');
        $count = $domain->count();

        if ($request->ajax()) {
            $datatables['domains']['reloadTable'] = true;

            $datatables['domains']['draw'] = (int)$request->input('draw', 1);
            $datatables['domains']['recordsTotal'] = $count;

            $columns = array_column($datatables['domains']['columns'], 'id');
            if ($datatables['domains']['checkbox']) {
                array_unshift($columns, 'id');
            }
            $sql = $domain->select($columns);

            $column = $request->input('columns.' . $request->input('order.0.column') . '.data', $datatables['domains']['columns'][$datatables['domains']['orderByColumn']]['id']);
            $dir = $request->input('order.0.dir', $datatables['domains']['order']);
            $sql = $sql->orderBy($column, $dir);

            if ($request->input('search.value')) {
                $sql = $sql->where(function($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->input('search.value') . '%')->orWhere('slug', 'like', '%' . $request->input('search.value') . '%');
                });

                $sql2 = $sql;

                if ($request->input('length') > 0) { // All = -1
                    if ($request->input('start') > 0) {
                        $sql = $sql->skip($request->input('start'));
                    }

                    $sql = $sql->take($request->input('length'));
                }

                $datatables['domains']['search'] = true;
                $datatables['domains']['data'] = $sql->get();
                $datatables['domains']['recordsFiltered'] = $sql2->count();
            } else {
                $datatables['domains']['recordsFiltered'] = $count;

                if ($request->input('length') > 0) { // All = -1
                    if ($request->input('start') > 0) {
                        $sql = $sql->skip($request->input('start'));
                    }

                    $sql = $sql->take($request->input('length'));
                }

                $datatables['domains']['data'] = $sql->get();
            }

            return response()->json($datatables);
        } else {
            $datatables['domains']['count'] = $count;

            $size = ($count <= 100 ? 'small' : ($count <= 1000 ? 'medium' : 'large'));
            $datatables['domains']['size'] = $size;

            if ($count < \Config::get('datatables.clientSideLimit')) {
                $datatables['domains']['ajax'] = false;

                $columns = array_column($datatables['domains']['columns'], 'id');
                if ($datatables['domains']['checkbox']) {
                    array_unshift($columns, 'id');
                }
                $sql = $domain->select($columns);

                $sql = $sql->orderBy($datatables['domains']['columns'][$datatables['domains']['orderByColumn']]['id'], $datatables['domains']['order']);

                $datatables['domains']['data'] = $sql->get();
            } else {
                $datatables['domains']['ajax'] = true;
            }
        }

        return view('cms.domains.index', compact('datatables'));
    }

}
