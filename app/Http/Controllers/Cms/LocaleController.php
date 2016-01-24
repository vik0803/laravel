<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Services\DataTable;
use Illuminate\Http\Request;
use App\Locale;
use App\Http\Requests\LocaleRequest;

class LocaleController extends Controller {

    protected $route = 'locales';
    protected $datatables;

    public function __construct()
    {
        $this->datatables = [
            $this->route => [
                'url' => \Locales::route('settings/' . $this->route),
                'class' => 'table-checkbox table-striped table-bordered table-hover',
                'columns' => [
                    [
                        'selector' => $this->route . '.id',
                        'id' => 'id',
                        'checkbox' => true,
                        'order' => false,
                        'class' => 'text-center',
                    ],
                    [
                        'selector' => $this->route . '.name',
                        'id' => 'name',
                        'name' => trans('cms/datatables.name'),
                        'search' => true,
                    ],
                    [
                        'selector' => $this->route . '.native',
                        'id' => 'native',
                        'name' => trans('cms/datatables.native'),
                        'search' => true,
                    ],
                    [
                        'selector' => $this->route . '.locale',
                        'id' => 'locale',
                        'name' => trans('cms/datatables.locale'),
                        'search' => true,
                    ],
                    [
                        'selector' => $this->route . '.script',
                        'id' => 'script',
                        'name' => trans('cms/datatables.script'),
                        'search' => true,
                    ],
                ],
                'orderByColumn' => 1,
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
    }

    public function index(DataTable $datatable, Locale $locale, Request $request)
    {
        $datatable->setup($locale, $this->route, $this->datatables[$this->route]);

        $datatables = $datatable->getTables();

        if ($request->ajax()) {
            return response()->json($datatables);
        } else {
            return view('cms.' . $this->route . '.index', compact('datatables'));
        }
    }

    public function create(Request $request)
    {
        $table = $request->input('table') ?: $this->route;

        $script = null;
        $scripts = ['' => ''] + trans('cms/forms.localeScripts');

        $view = \View::make('cms.' . $this->route . '.create', compact('table', 'scripts', 'script'));
        $sections = $view->renderSections();
        return $sections['content'];
    }

    public function store(DataTable $datatable, Locale $locale, LocaleRequest $request)
    {
        $newLocale = Locale::create($request->all());

        if ($newLocale->id) {
            $successMessage = trans('cms/forms.storedSuccessfully', ['entity' => trans_choice('cms/forms.entityLocales', 1)]);

            $datatable->setup($locale, $request->input('table'), $this->datatables[$request->input('table')], true);
            $datatables = $datatable->getTables();

            return response()->json($datatables + [
                'success' => $successMessage,
                'reset' => true,
            ]);
        } else {
            $errorMessage = trans('cms/forms.createError', ['entity' => trans_choice('cms/forms.entityLocales', 1)]);
            return response()->json(['errors' => [$errorMessage]]);
        }
    }

    public function delete(Request $request)
    {
        $table = $request->input('table') ?: $this->route;

        $view = \View::make('cms.' . $this->route . '.delete', compact('table'));
        $sections = $view->renderSections();
        return $sections['content'];
    }

    public function destroy(DataTable $datatable, Locale $locale, Request $request)
    {
        $count = count($request->input('id'));

        if ($count > 0 && $locale->destroy($request->input('id'))) {
            $datatable->setup($locale, $request->input('table'), $this->datatables[$request->input('table')], true);
            $datatables = $datatable->getTables();

            return response()->json($datatables + [
                'success' => trans('cms/forms.destroyedSuccessfully'),
                'closePopup' => true
            ]);
        } else {
            if ($count > 0) {
                $errorMessage = trans('cms/forms.deleteError');
            } else {
                $errorMessage = trans('cms/forms.countError');
            }

            return response()->json(['errors' => [$errorMessage]]);
        }
    }

    public function edit(Request $request, $id = null)
    {
        $locale = Locale::findOrFail($id);

        $table = $request->input('table') ?: $this->route;
        $script = $request->input('script');
        $scripts = trans('cms/forms.localeScripts');

        $view = \View::make('cms.' . $this->route . '.create', compact('locale', 'table', 'scripts', 'script'));
        $sections = $view->renderSections();
        return $sections['content'];
    }

    public function update(DataTable $datatable, LocaleRequest $request)
    {
        $locale = Locale::findOrFail($request->input('id'))->first();

        if ($locale->update($request->all())) {
            $successMessage = trans('cms/forms.updatedSuccessfully', ['entity' => trans_choice('cms/forms.entityLocales', 1)]);

            $datatable->setup($locale, $request->input('table'), $this->datatables[$request->input('table')], true);
            $datatables = $datatable->getTables();

            return response()->json($datatables + [
                'success' => $successMessage,
                'closePopup' => true
            ]);
        } else {
            $errorMessage = trans('cms/forms.editError', ['entity' => trans_choice('cms/forms.entityLocales', 1)]);
            return response()->json(['errors' => [$errorMessage]]);
        }
    }

}
