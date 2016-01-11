<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Services\DataTable;
use Illuminate\Http\Request;
use App\Page;
use App\Http\Requests\PageRequest;

class PageController extends Controller {

    protected $route = 'pages';
    protected $datatables;

    public function __construct()
    {
        $this->datatables = [
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
                        'link' => [
                            'selector' => $this->route . '.is_category',
                            'rules' => [
                                'is_category' => 1,
                            ],
                            'route' => $this->route,
                            'routeParameter' => 'slug',
                            'icon' => 'folder-open',
                        ],
                    ],
                    [
                        'selector' => $this->route . '.slug',
                        'id' => 'slug',
                        'name' => trans('cms/datatables.slug'),
                        'search' => true,
                    ],
                ],
                'orderByColumn' => 'order',
                'order' => 'asc',
                'buttons' => [
                    [
                        'url' => \Locales::route($this->route . '/create-category'),
                        'class' => 'btn-primary js-create',
                        'icon' => 'plus',
                        'name' => trans('cms/forms.createCategoryButton'),
                    ],
                    [
                        'url' => \Locales::route($this->route . '/create'),
                        'class' => 'btn-primary js-create',
                        'icon' => 'plus',
                        'name' => trans('cms/forms.createPageButton'),
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

    public function index(DataTable $datatable, Page $page, Request $request, $categories = null)
    {
        if ($categories) {
            $categoriesArray = explode('/', $categories);
            $count = count($categoriesArray);

            $category = $page->where('slug', last($categoriesArray));
            $category = $category->first();

            if ($category) { // the last category slug is valid
                $parents = \DB::select('select slug, parent from (select slug, @r := (select parent from pages where id = @r) as parent from (select @r := ?) tmp1, pages where @r is not null) tmp2 where parent is not null', [$category->id]);
                if (count($parents) === ($count - 1)) { // if there are parents they must be equal to the number of slugs in the URL
                    $valid = true;
                    foreach ($parents as $key => $parent) { // check if parent slugs are also valid and in the correct order
                        if ($parent->slug !== $categoriesArray[$key]) {
                            $valid = false;
                            break;
                        }
                    }

                    if ($valid) {
                        $request->session()->put($page->getTable() . 'Parent', $category->id); // save current category for proper store/update/destroy actions
                        $page = $page->where('parent', $category->id);
                    } else {
                        abort(404);
                    }
                } else {
                    abort(404);
                }
            } else {
                abort(404);
            }
        } else {
            $request->session()->put($page->getTable() . 'Parent', null); // save current category for proper store/update/destroy actions
            $page = $page->where('parent', null);
        }

        $datatable->setup($page, $this->route, $this->datatables[$this->route]);

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

        $view = \View::make('cms.' . $this->route . '.create', compact('table'));
        $sections = $view->renderSections();
        return $sections['content'];
    }

    public function createCategory(Request $request)
    {
        $table = $request->input('table') ?: $this->route;

        $view = \View::make('cms.' . $this->route . '.create-category', compact('table'));
        $sections = $view->renderSections();
        return $sections['content'];
    }

    public function store(DataTable $datatable, Page $page, PageRequest $request)
    {
        $parent = $request->session()->get($page->getTable() . 'Parent', null);

        $page = $page->where('parent', $parent);

        $order = $request->input('order');
        $maxOrder = $page->max('order') + 1;

        if (!$order || $order > $maxOrder) {
            $order = $maxOrder;
        } else { // re-order all higher order rows
            $page->where('order', '>=', $order)->increment('order');
        }

        $request->merge([
            'parent' => $parent,
            'order' => $order,
        ]);

        $newPage = Page::create($request->all());

        if ($newPage->id) {
            $successMessage = trans('cms/forms.storedSuccessfully', ['entity' => trans_choice('cms/forms.entityPages', 1)]);

            $datatable->setup($page, $request->input('table'), $this->datatables[$request->input('table')], true);
            $datatable->setOption('url', \Locales::route($this->route, true));
            $datatables = $datatable->getTables();

            return response()->json($datatables + [
                'success' => $successMessage,
                'reset' => true,
            ]);
        } else {
            $errorMessage = trans('cms/forms.createError', ['entity' => trans_choice('cms/forms.entityPages', 1)]);
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

    public function destroy(DataTable $datatable, Page $page, Request $request)
    {
        $count = count($request->input('id'));

        if ($count > 0 && $page->destroy($request->input('id'))) {
            $parent = $request->session()->get($page->getTable() . 'Parent', null);
            $page = $page->where('parent', $parent);

            $datatable->setup($page, $request->input('table'), $this->datatables[$request->input('table')], true);
            $datatable->setOption('url', \Locales::route($this->route, true));
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
        $page = Page::findOrFail($id);

        $table = $request->input('table') ?: $this->route;

        $view = \View::make('cms.' . $this->route . '.create', compact('page', 'table'));
        $sections = $view->renderSections();
        return $sections['content'];
    }

    public function update(DataTable $datatable, PageRequest $request)
    {
        $page = Page::findOrFail($request->input('id'))->first();

        if ($page->update($request->all())) {
            $successMessage = trans('cms/forms.updatedSuccessfully', ['entity' => trans_choice('cms/forms.entityPages', 1)]);

            $page = $page->where('parent', $page->parent);

            $datatable->setup($page, $request->input('table'), $this->datatables[$request->input('table')], true);
            $datatable->setOption('url', \Locales::route($this->route, true));
            $datatables = $datatable->getTables();

            return response()->json($datatables + [
                'success' => $successMessage,
                'closePopup' => true
            ]);
        } else {
            $errorMessage = trans('cms/forms.editError', ['entity' => trans_choice('cms/forms.entityPages', 1)]);
            return response()->json(['errors' => [$errorMessage]]);
        }
    }

}
