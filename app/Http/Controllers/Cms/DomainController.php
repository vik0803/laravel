<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Services\DataTable;
use Illuminate\Http\Request;
use App\Domain;
use App\Locale;
use App\Http\Requests\CreateDomainRequest;
use App\Http\Requests\EditDomainRequest;

class DomainController extends Controller {

    protected $route = 'domains';
    protected $datatables;
    protected $multiselect;

    public function __construct()
    {
        $this->datatables = [
            $this->route => [
                'url' => \Locales::route('settings/' . $this->route),
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
                        'selector' => $this->route . '.slug',
                        'id' => 'slug',
                        'name' => trans('cms/datatables.slug'),
                        'search' => true,
                    ],
                    [
                        'selector' => '',
                        'id' => 'locales',
                        'name' => trans('cms/datatables.locales'),
                        'aggregate' => 'localesCount',
                    ],
                    [
                        'selector' => 'locales.name as defaultLocale',
                        'id' => 'defaultLocale',
                        'name' => trans('cms/datatables.defaultLocale'),
                        'append' => [
                            'selector' => $this->route . '.hide_default_locale',
                            'column' => 'defaultLocale',
                            'rules' => [
                                'hide_default_locale' => 1,
                            ],
                            'text' => trans('cms/messages.localeIsHidden'),
                        ],
                        'join' => [
                            'table' => 'locales',
                            'localColumn' => 'locales.id',
                            'constrain' => '=',
                            'foreignColumn' => $this->route . '.default_locale_id',
                        ],
                    ],
                ],
                'orderByColumn' => 0,
                'order' => 'asc',
                'buttons' => [
                    [
                        'url' => \Locales::route('settings/' . $this->route . '/create'),
                        'class' => 'btn-primary js-create',
                        'icon' => 'plus',
                        'name' => trans('cms/forms.createButton'),
                    ],
                    [
                        'url' => \Locales::route('settings/' . $this->route . '/edit'),
                        'class' => 'btn-warning disabled js-edit',
                        'icon' => 'edit',
                        'name' => trans('cms/forms.editButton'),
                    ],
                    [
                        'url' => \Locales::route('settings/' . $this->route . '/delete'),
                        'class' => 'btn-danger disabled js-destroy',
                        'icon' => 'trash',
                        'name' => trans('cms/forms.deleteButton'),
                    ],
                ],
            ],
        ];

        $this->multiselect = [
            'locales' => [
                'id' => 'id',
                'name' => 'native',
                'subText' => 'name',
            ],
            'default_locale_id' => [
                'id' => 'id',
                'name' => 'native',
                'subText' => 'name',
            ],
        ];
    }

    public function index(DataTable $datatable, Domain $domain, Request $request)
    {
        $datatable->setup($domain, $this->route, $this->datatables[$this->route]);

        $datatables = $datatable->getTables();

        if ($request->ajax()) {
            return response()->json($datatables);
        } else {
            return view('cms.' . $this->route . '.index', compact('datatables'));
        }
    }

    public function create(Domain $domain, Locale $locale, Request $request)
    {
        $this->multiselect['locales']['options'] = $locale->select($this->multiselect['locales']['id'], $this->multiselect['locales']['name'], $this->multiselect['locales']['subText'])->get()->toArray();
        $this->multiselect['locales']['selected'] = $domain->locales->lists('id')->toArray();

        $this->multiselect['default_locale_id']['options'] = [];
        $this->multiselect['default_locale_id']['selected'] = [];

        $multiselect = $this->multiselect;

        $table = $request->input('table') ?: $this->route;

        $view = \View::make('cms.' . $this->route . '.create', compact('multiselect', 'table'));
        if ($request->ajax()) {
            $sections = $view->renderSections();
            return $sections['content'];
        }
        return $view;
    }

    public function store(DataTable $datatable, Domain $domain, CreateDomainRequest $request)
    {
        $redirect = redirect(\Locales::route($this->route));

        $newDomain = Domain::create($request->all());

        if ($newDomain->id) {
            $newDomain->locales()->sync($request->input('locales'));

            $successMessage = trans('cms/forms.storedSuccessfully', ['entity' => trans_choice('cms/forms.entityDomains', 1)]);
            $redirect->withSuccess([$successMessage]);

            if ($request->ajax()) {
                $datatable->setup($domain, $request->input('table'), $this->datatables[$request->input('table')], true);
                $datatables = $datatable->getTables();

                return response()->json($datatables + [
                    'success' => $successMessage,
                    'reset' => true,
                    'resetMultiselect' => [
                        'input-locales' => ['refresh'],
                        'input-default_locale_id' => ['empty', 'disable', 'refresh'],
                    ],
                ]);
            }
        } else {
            $errorMessage = trans('cms/forms.createError', ['entity' => trans_choice('cms/forms.entityDomains', 1)]);
            $redirect->withError([$errorMessage]);

            if ($request->ajax()) {
                return response()->json(['errors' => [$errorMessage]]);
            }
        }

        return $redirect;
    }

    public function delete(Request $request)
    {
        $table = $request->input('table') ?: $this->route;

        $view = \View::make('cms.' . $this->route . '.delete', compact('table'));
        if ($request->ajax()) {
            $sections = $view->renderSections();
            return $sections['content'];
        }
        return $view;
    }

    public function destroy(DataTable $datatable, Domain $domain, Request $request)
    {
        $redirect = redirect(\Locales::route($this->route));
        $count = count($request->input('id'));

        if ($count > 0 && $domain->destroy($request->input('id'))) {
            $successMessage = trans('cms/forms.destroyedSuccessfully', ['entity' => trans_choice('cms/forms.entityDomains', $count)]);
            $redirect->withSuccess([$successMessage]);

            if ($request->ajax()) {
                $datatable->setup($domain, $request->input('table'), $this->datatables[$request->input('table')], true);
                $datatables = $datatable->getTables();

                return response()->json($datatables + [
                    'success' => $successMessage,
                    'closePopup' => true
                ]);
            }
        } else {
            if ($count > 0) {
                $errorMessage = trans('cms/forms.deleteError', ['entity' => trans_choice('cms/forms.entityDomains', $count)]);
            } else {
                $errorMessage = trans('cms/forms.countError', ['entity' => trans_choice('cms/forms.entityDomains', 1)]);
            }

            $redirect->withError([$errorMessage]);

            if ($request->ajax()) {
                return response()->json(['errors' => [$errorMessage]]);
            }
        }

        return $redirect;
    }

    public function edit(Locale $locale, Request $request, $id = null)
    {
        $domain = Domain::findOrFail($id);

        $this->multiselect['locales']['options'] = $locale->select($this->multiselect['locales']['id'], $this->multiselect['locales']['name'], $this->multiselect['locales']['subText'])->get()->toArray();
        $this->multiselect['locales']['selected'] = $domain->locales->lists('id')->toArray();

        $this->multiselect['default_locale_id']['options'] = array_where($this->multiselect['locales']['options'], function($key, $value) {
            if (in_array($value['id'], $this->multiselect['locales']['selected'])) {
                return true;
            }

            return false;
        });
        $this->multiselect['default_locale_id']['selected'] = $domain->default_locale_id;

        $multiselect = $this->multiselect;

        $table = $request->input('table') ?: $this->route;

        $view = \View::make('cms.' . $this->route . '.create', compact('multiselect', 'domain', 'table'));
        if ($request->ajax()) {
            $sections = $view->renderSections();
            return $sections['content'];
        }
        return $view;
    }

    public function update(DataTable $datatable, EditDomainRequest $request)
    {
        $domain = Domain::findOrFail($request->input('id'))->first();

        $redirect = redirect(\Locales::route($this->route));

        if ($domain->update($request->all())) {
            $domain->locales()->sync($request->input('locales'));

            $successMessage = trans('cms/forms.updatedSuccessfully', ['entity' => trans_choice('cms/forms.entityDomains', 1)]);
            $redirect->withSuccess([$successMessage]);

            if ($request->ajax()) {
                $datatable->setup($domain, $request->input('table'), $this->datatables[$request->input('table')], true);
                $datatables = $datatable->getTables();

                return response()->json($datatables + [
                    'success' => $successMessage,
                    'closePopup' => true
                ]);
            }
        } else {
            $errorMessage = trans('cms/forms.editError', ['entity' => trans_choice('cms/forms.entityDomains', 1)]);
            $redirect->withError([$errorMessage]);

            if ($request->ajax()) {
                return response()->json(['errors' => [$errorMessage]]);
            }
        }

        return $redirect;
    }

}
