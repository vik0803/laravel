<?php namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Services\DataTable;
use App\Services\FineUploader;
use Illuminate\Http\Request;
use App\Banner;
use App\BannerImage;
use Storage;
use App\Http\Requests\BannerRequest;
use App\Http\Requests\BannerImageRequest;

class BannerController extends Controller {

    protected $route = 'banners';
    protected $uploadDirectory = 'banners';
    protected $datatables;

    public function __construct()
    {
        $this->datatables = [
            $this->route => [
                'url' => \Locales::route($this->route, true),
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
                        'link' => [
                            'icon' => 'file',
                            'route' => $this->route,
                            'routeParameter' => 'slug',
                        ],
                    ],
                    [
                        'selector' => $this->route . '.slug',
                        'id' => 'slug',
                        'name' => trans('cms/datatables.slug'),
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
            'banner_images' => [
                'url' => \Locales::route($this->route, true),
                'class' => 'table-checkbox table-striped table-bordered table-hover table-thumbnails popup-gallery',
                'columns' => [
                    [
                        'selector' => 'banner_images.id',
                        'id' => 'id',
                        'checkbox' => true,
                        'order' => false,
                        'class' => 'text-center',
                    ],
                    [
                        'selector' => 'banner_images.name',
                        'id' => 'name',
                        'name' => trans('cms/datatables.name'),
                        'search' => true,
                        'link' => [
                            'selector' => ['banner_images.url'],
                            'url' => 'url',
                        ],
                    ],
                    [
                        'selector' => 'banner_images.file',
                        'id' => 'file',
                        'name' => trans('cms/datatables.image'),
                        'order' => false,
                        'class' => 'text-center',
                        'thumbnail' => [
                            'selector' => ['banner_images.uuid', 'banner_images.description'],
                            'title' => 'description',
                            'id' => 'uuid',
                            'directory' => 'banners',
                        ],
                    ],
                    [
                        'selector' => 'banner_images.size',
                        'id' => 'size',
                        'name' => trans('cms/datatables.size'),
                        'filesize' => true,
                    ],
                ],
                'orderByColumn' => 'order',
                'order' => 'asc',
                'buttons' => [
                    [
                        'upload' => true,
                        'id' => 'fine-uploader',
                        'url' => \Locales::route($this->route . '/upload'),
                        'class' => 'btn-primary js-upload',
                        'icon' => 'upload',
                        'name' => trans('cms/forms.uploadButton'),
                    ],
                    [
                        'url' => \Locales::route($this->route . '/edit-image'),
                        'class' => 'btn-warning disabled js-edit',
                        'icon' => 'edit',
                        'name' => trans('cms/forms.editButton'),
                    ],
                    [
                        'url' => \Locales::route($this->route . '/delete-image'),
                        'class' => 'btn-danger disabled js-destroy',
                        'icon' => 'trash',
                        'name' => trans('cms/forms.deleteButton'),
                    ],
                ],
            ],
        ];
    }

    public function index(DataTable $datatable, Banner $banner, Request $request, $slug = null)
    {
        $uploadDirectory = $this->uploadDirectory;
        if (!Storage::disk('local-public')->exists($uploadDirectory)) {
            Storage::disk('local-public')->makeDirectory($uploadDirectory);
        }

        $is_banner = false;
        $pageId = '';
        if ($slug) {
            $row = $banner->where('slug', $slug)->firstOrFail();
            $request->session()->put('routeSlugs', explode('/', $slug)); // save current slug for proper file upload actions
            $is_banner = true;
            $pageId = $row['id'];
        } else {
            $request->session()->put($banner->getTable() . 'Parent', null); // save current category for proper store/update/destroy actions
            $request->session()->put('routeSlugs', []); // save current slugs for proper file upload actions
        }

        if ($is_banner) {
            $datatable->setup(BannerImage::where('banner_id', $pageId), 'banner_images', $this->datatables['banner_images']);
        } else {
            $datatable->setup($banner, $this->route, $this->datatables[$this->route]);
        }

        $datatables = $datatable->getTables();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($datatables);
        } else {
            return view('cms.' . $this->route . '.index', compact('datatables', 'pageId'));
        }
    }

    public function create(Request $request)
    {
        $table = $request->input('table') ?: $this->route;

        $view = \View::make('cms.' . $this->route . '.create', compact('table'));
        $sections = $view->renderSections();
        return $sections['content'];
    }

    public function store(DataTable $datatable, Banner $banner, BannerRequest $request)
    {
        $newBanner = Banner::create($request->all());

        if ($newBanner->id) {
            $slugs = $request->session()->get('routeSlugs', []);

            $uploadDirectory = $this->uploadDirectory . DIRECTORY_SEPARATOR . trim(implode(DIRECTORY_SEPARATOR, $slugs), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newBanner->slug;
            if (!Storage::disk('local-public')->exists($uploadDirectory)) {
                Storage::disk('local-public')->makeDirectory($uploadDirectory);
            }

            $successMessage = trans('cms/forms.storedSuccessfully', ['entity' => trans_choice('cms/forms.entityPages', 1)]);

            $datatable->setup($banner, $request->input('table'), $this->datatables[$request->input('table')], true);
            $datatable->setOption('url', \Locales::route($this->route, implode('/', $slugs)));
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

    public function destroy(DataTable $datatable, Banner $banner, Request $request)
    {
        $count = count($request->input('id'));

        $directories = Banner::find($request->input('id'))->lists('slug');

        if ($count > 0 && $banner->destroy($request->input('id'))) {
            $slugs = $request->session()->get('routeSlugs', []);
            $path = DIRECTORY_SEPARATOR . trim(implode(DIRECTORY_SEPARATOR, $slugs), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            foreach ($directories as $directory) {
                Storage::disk('local-public')->deleteDirectory($this->uploadDirectory . $path . $directory);
            }

            $datatable->setup($banner, $request->input('table'), $this->datatables[$request->input('table')], true);
            $datatable->setOption('url', \Locales::route($this->route, implode('/', $slugs)));
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
        $banner = Banner::findOrFail($id);

        $table = $request->input('table') ?: $this->route;

        $view = \View::make('cms.' . $this->route . '.create', compact('banner', 'table'));
        $sections = $view->renderSections();
        return $sections['content'];
    }

    public function update(DataTable $datatable, BannerRequest $request)
    {
        $banner = Banner::findOrFail($request->input('id'))->first();
        $oldBanner = $banner->replicate();

        if ($banner->update($request->all())) {
            $slugs = $request->session()->get('routeSlugs', []);

            $uploadDirectory = $this->uploadDirectory . DIRECTORY_SEPARATOR . trim(implode(DIRECTORY_SEPARATOR, $slugs), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            if ($oldBanner->slug == $banner->slug) {
                if (!Storage::disk('local-public')->exists($uploadDirectory . $banner->slug)) {
                    Storage::disk('local-public')->makeDirectory($uploadDirectory . $banner->slug);
                }
            } else {
                if (!Storage::disk('local-public')->exists($uploadDirectory . $oldBanner->slug)) {
                    Storage::disk('local-public')->makeDirectory($uploadDirectory . $oldBanner->slug);
                }

                Storage::disk('local-public')->move($uploadDirectory . $oldBanner->slug, $uploadDirectory . $banner->slug);
            }

            $successMessage = trans('cms/forms.updatedSuccessfully', ['entity' => trans_choice('cms/forms.entityPages', 1)]);

            $datatable->setup($banner, $request->input('table'), $this->datatables[$request->input('table')], true);
            $datatable->setOption('url', \Locales::route($this->route, implode('/', $slugs)));
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

    public function upload(Request $request, FineUploader $uploader, $chunk = null)
    {
        $uploader->watermark = false;
        $uploader->resize = false;
        $uploader->banner = true;

        $slugs = $request->session()->get('routeSlugs', []);
        $uploader->uploadDirectory = $this->uploadDirectory . DIRECTORY_SEPARATOR . trim(implode(DIRECTORY_SEPARATOR, $slugs), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . \Config::get('images.rootDirectory');
        if (!Storage::disk('local-public')->exists($uploader->uploadDirectory)) {
            Storage::disk('local-public')->makeDirectory($uploader->uploadDirectory);
        }

        if ($chunk) {
            $response = $uploader->combineChunks();
        } else {
            $response = $uploader->handleUpload();
        }

        if (isset($response['success']) && $response['success'] && isset($response['fileName'])) {
            $directory = asset('upload/' . str_replace(DIRECTORY_SEPARATOR, '/', $uploader->uploadDirectory) . '/' . $response['uuid']);

            $response['file'] = '<a class="popup" href="' . asset($directory . '/' . $response['fileName']) . '">' . \HTML::image($directory . '/' . \Config::get('images.thumbnailSmallDirectory') . '/' . $response['fileName']) . '</a>';

            $image = new BannerImage;
            $image->file = $response['fileName'];
            $image->uuid = $response['uuid'];
            $image->extension = $response['fileExtension'];
            $image->size = $response['fileSize'];
            $image->order = BannerImage::where('banner_id', $request->input('id'))->max('order') + 1;
            $image->banner_id = $request->input('id');
            $image->save();

            $response['id'] = $image->id;
        }

        return response()->json($response, $uploader->getStatus())->header('Content-Type', 'text/plain');
    }

    public function deleteImage(Request $request)
    {
        $table = $request->input('table') ?: $this->route;

        $view = \View::make('cms.' . $this->route . '.delete-image', compact('table'));
        $sections = $view->renderSections();
        return $sections['content'];
    }

    public function destroyImage(DataTable $datatable, BannerImage $image, Request $request)
    {
        $count = count($request->input('id'));

        $uuids = BannerImage::find($request->input('id'))->lists('banner_id', 'uuid');

        if ($count > 0 && $image->destroy($request->input('id'))) {
            $slugs = $request->session()->get('routeSlugs', []);
            $path = DIRECTORY_SEPARATOR . trim(implode(DIRECTORY_SEPARATOR, $slugs), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . \Config::get('images.rootDirectory') . DIRECTORY_SEPARATOR;
            foreach ($uuids as $uuid => $banner) {
                Storage::disk('local-public')->deleteDirectory($this->uploadDirectory . $path . $uuid);
            }

            $datatable->setup(BannerImage::where('banner_id', $banner), $request->input('table'), $this->datatables[$request->input('table')], true);
            $datatable->setOption('url', \Locales::route($this->route, implode('/', $slugs)));
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

    public function editImage(Request $request, $id = null)
    {
        $image = BannerImage::findOrFail($id);

        $table = $request->input('table') ?: $this->route;

        $view = \View::make('cms.' . $this->route . '.edit-image', compact('image', 'table'));
        $sections = $view->renderSections();
        return $sections['content'];
    }

    public function updateImage(DataTable $datatable, BannerImageRequest $request)
    {
        $image = BannerImage::findOrFail($request->input('id'))->first();

        if ($image->update($request->all())) {
            $slugs = $request->session()->get('routeSlugs', []);

            $successMessage = trans('cms/forms.updatedSuccessfully', ['entity' => trans_choice('cms/forms.entityImages', 1)]);

            $image = $image->where('banner_id', $image->banner_id);

            $datatable->setup($image, $request->input('table'), $this->datatables[$request->input('table')], true);
            $datatable->setOption('url', \Locales::route($this->route, implode('/', $slugs)));
            $datatables = $datatable->getTables();

            return response()->json($datatables + [
                'success' => $successMessage,
                'closePopup' => true
            ]);
        } else {
            $errorMessage = trans('cms/forms.editError', ['entity' => trans_choice('cms/forms.entityImages', 1)]);
            return response()->json(['errors' => [$errorMessage]]);
        }
    }

}
