<?php

namespace App\Services;

use Illuminate\Http\Request;
use Storage;
use File;

class FineUploader
{
    public $request;
    public $status = 200;
    public $allowedExtensions = [];
    public $sizeLimit = null;
    public $inputName = 'qqfile';
    public $chunksDirectory;
    public $chunksPath;
    public $chunksDisk = 'local-private';
    public $uploadDirectory;
    public $uploadPath;
    public $uploadDisk = 'local-public';
    public $thumbnail = true;
    public $watermark = true;
    public $resize = true;
    public $slider = false;
    public $banner = false;
    public $icon = false;

    public $chunksCleanupProbability = 0.001; // Once in 1000 requests on avg
    public $chunksExpireIn = 604800; // One week

    protected $uploadName;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->chunksDirectory = \Config::get('images.chunksDirectory');
        $this->allowedExtensions = \Config::get('images.extensions');
        $this->chunksPath = $this->getDiskPath($this->chunksDisk);
        $this->uploadPath = $this->getDiskPath($this->uploadDisk);
    }

    /**
     * Get the original filename
     */
    public function getName()
    {
        $name = null;
        if ($this->request->has('qqfilename')) {
            $name = $this->request->input('qqfilename');
        } elseif ($this->request->hasFile($this->inputName)) {
            $name = $this->request->file($this->inputName)->getClientOriginalName();
        }

        return $name;
    }

    /**
     * Get the name of the uploaded file
     */
    public function getUploadName()
    {
        return $this->uploadName;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function combineChunks()
    {
        $uuid = $this->request->input('qquuid');
        $chunksDirectory = $this->chunksDirectory . DIRECTORY_SEPARATOR . $uuid;
        if (Storage::disk($this->chunksDisk)->exists($chunksDirectory)) {
            $this->uploadName = $this->getName();
            $totalParts = (int)$this->request->input('qqtotalparts', 1);

            $rootDirectory = $this->uploadDirectory . DIRECTORY_SEPARATOR . $uuid;
            if (!Storage::disk($this->uploadDisk)->exists($rootDirectory)) {
                Storage::disk($this->uploadDisk)->makeDirectory($rootDirectory);
            }

            $uploadDirectory = $rootDirectory . DIRECTORY_SEPARATOR . \Config::get('images.originalDirectory');
            if (!Storage::disk($this->uploadDisk)->exists($uploadDirectory)) {
                Storage::disk($this->uploadDisk)->makeDirectory($uploadDirectory);
            }

            $uploadFile = $uploadDirectory . DIRECTORY_SEPARATOR . $this->getUploadName();

            $destination = fopen($this->uploadPath . $uploadFile, 'wb');

            for ($i = 0; $i < $totalParts; $i++) {
                $source = fopen($this->chunksPath . $chunksDirectory . DIRECTORY_SEPARATOR . $i, 'rb');
                stream_copy_to_stream($source, $destination);
                fclose($source);
            }

            fclose($destination);

            Storage::disk($this->chunksDisk)->deleteDirectory($chunksDirectory);

            if (!is_null($this->sizeLimit) && Storage::disk($this->uploadDisk)->size($uploadFile) > $this->sizeLimit) {
                Storage::disk($this->uploadDisk)->delete($uploadFile);
                $this->status = 413;
                return ['success' => false, 'uuid' => $uuid, 'preventRetry' => true];
            }

            $size = $this->processUploaded($rootDirectory, $this->getUploadName());

            return [
                'success'=> true,
                'uuid' => $uuid,
                'fileName' => $this->getUploadName(),
                'fileExtension' => File::extension($this->getUploadName()),
                'fileSize' => $size,
            ];
        }
    }

    /**
     * Process the upload.
     * @param string $name Overwrites the name of the file.
     */
    public function handleUpload($name = null)
    {
        clearstatcache();

        if (File::isWritable($this->chunksPath . $this->chunksDirectory) && 1 == mt_rand(1, 1 / $this->chunksCleanupProbability)) {
            $this->cleanupChunks();
        }

        // Check that the max upload size specified in class configuration does not exceed size allowed by server config
        if ($this->toBytes(ini_get('post_max_size')) < $this->sizeLimit || $this->toBytes(ini_get('upload_max_filesize')) < $this->sizeLimit) {
            $neededRequestSize = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            return ['error' => trans('cms/fineuploader.errorServerMaxSize', ['size' => $neededRequestSize]), 'preventRetry' => true];
        }

        if (!File::isWritable($this->uploadPath . $this->uploadDirectory) && !is_executable($this->uploadPath . $this->uploadDirectory)) {
            return ['error' => trans('cms/fineuploader.errorUploadDirectoryNotWritable'), 'preventRetry' => true];
        }

        $type = $this->request->server('HTTP_CONTENT_TYPE', $this->request->server('CONTENT_TYPE'));

        if (!$type) {
            return ['error' => trans('cms/fineuploader.errorUpload')];
        } else if (strpos(strtolower($type), 'multipart/') !== 0) {
            return ['error' => trans('cms/fineuploader.errorMultipart')];
        }

        $file = $this->request->file($this->inputName);
        $size = $this->request->input('qqtotalfilesize', $file->getSize());

        if (is_null($name)) {
            $name = $this->getName();
        }

        if (is_null($name) || empty($name)) {
            return ['error' => trans('cms/fineuploader.errorFileNameEmpty')];
        }

        if (empty($size)) {
            return ['error' => trans('cms/fineuploader.errorFileEmpty')];
        }

        if (!is_null($this->sizeLimit) && $size > $this->sizeLimit) {
            return ['error' => trans('cms/fineuploader.errorFileSize'), 'preventRetry' => true];
        }

        $ext = strtolower(File::extension($name));
        $name = File::name($name) . '.' . $ext; // use lowercase extension
        $this->uploadName = $name;

        if ($this->allowedExtensions && !in_array($ext, array_map('strtolower', $this->allowedExtensions))) {
            $these = implode(', ', $this->allowedExtensions);
            return ['error' => trans('cms/fineuploader.errorFileExtension', ['extensions' => $these]), 'preventRetry' => true];
        }

        $totalParts = (int)$this->request->input('qqtotalparts', 1);
        $uuid = $this->request->input('qquuid');

        if ($totalParts > 1) { // chunked upload
            $partIndex = (int)$this->request->input('qqpartindex');

            if (!File::isWritable($this->chunksPath . $this->chunksDirectory) && !is_executable($this->chunksPath . $this->chunksDirectory)){
                return ['error' => trans('cms/fineuploader.errorChunksDirectoryNotWritable'), 'preventRetry' => true];
            }

            $chunksDirectory = $this->chunksDirectory . DIRECTORY_SEPARATOR . $uuid;

            if (!Storage::disk($this->chunksDisk)->exists($chunksDirectory)) {
                Storage::disk($this->chunksDisk)->makeDirectory($chunksDirectory);
            }

            $file->move($this->chunksPath . $chunksDirectory, $partIndex);

            return ['success' => true, 'uuid' => $uuid];
        } else { // non-chunked upload
            $rootDirectory = $this->uploadDirectory . DIRECTORY_SEPARATOR . $uuid;
            if (!Storage::disk($this->uploadDisk)->exists($rootDirectory)) {
                Storage::disk($this->uploadDisk)->makeDirectory($rootDirectory);
            }

            $uploadDirectory = $rootDirectory . DIRECTORY_SEPARATOR . \Config::get('images.originalDirectory');
            if (!Storage::disk($this->uploadDisk)->exists($uploadDirectory)) {
                Storage::disk($this->uploadDisk)->makeDirectory($uploadDirectory);
            }

            if (($response = $file->move($this->uploadPath . $uploadDirectory, $this->getUploadName())) !== false) {
                $size = $this->processUploaded($rootDirectory, $this->getUploadName());

                return [
                    'success'=> true,
                    'uuid' => $uuid,
                    'fileName' => $response->getFilename(),
                    'fileExtension' => $response->getExtension(),
                    'fileSize' => $size,
                ];
            }

            return ['error' => trans('cms/fineuploader.errorSave')];
        }
    }

    protected function processUploaded($directory, $filename) {
        $file = $this->uploadPath . $directory . DIRECTORY_SEPARATOR . \Config::get('images.originalDirectory') . DIRECTORY_SEPARATOR . $filename;

        $img = \Image::make($file);

        if ($this->resize) {
            if ($img->width() > \Config::get('images.imageMaxWidth') || $img->height() > \Config::get('images.imageMaxHeight')) {
                $img->resize(\Config::get('images.imageMaxWidth'), \Config::get('images.imageMaxHeight'), function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            if ($this->watermark) {
                $img->insert(\Config::get('images.watermarkImage'), \Config::get('images.watermarkPosition'), \Config::get('images.watermarkOffsetX'), \Config::get('images.watermarkOffsetY'));
            }
        } elseif ($this->banner) {
            $img->fit(\Config::get('images.bannerWidth'), \Config::get('images.bannerHeight'), function ($constraint) {
                $constraint->upsize();
            });
        } elseif ($this->icon) {
            $img->fit(\Config::get('images.iconWidth'), \Config::get('images.iconHeight'), function ($constraint) {
                $constraint->upsize();
            });
        }

        $img->save($this->uploadPath . $directory . DIRECTORY_SEPARATOR . $filename, \Config::get('images.quality'));
        $size = $img->filesize();

        if ($this->thumbnail) {
            $thumbnailDirectory = $directory . DIRECTORY_SEPARATOR . \Config::get('images.thumbnailSmallDirectory');
            if (!Storage::disk($this->uploadDisk)->exists($thumbnailDirectory)) {
                Storage::disk($this->uploadDisk)->makeDirectory($thumbnailDirectory);
            }

            $thumb = \Image::make($file);
            $thumb->fit(\Config::get('images.thumbnailSmallWidth'), \Config::get('images.thumbnailSmallHeight'), function ($constraint) {
                $constraint->upsize();
            });

            if ($thumb->width() < \Config::get('images.thumbnailSmallWidth') || $thumb->height() < \Config::get('images.thumbnailSmallHeight')) {
                $thumb->resizeCanvas(\Config::get('images.thumbnailSmallWidth'), \Config::get('images.thumbnailSmallHeight'), 'center', false, \Config::get('images.thumbnailCanvasBackground'));
            }

            $thumb->save($this->uploadPath . $thumbnailDirectory . DIRECTORY_SEPARATOR . $filename);
        }

        if ($this->slider) {
            $sliderDirectory = $directory . DIRECTORY_SEPARATOR . \Config::get('images.sliderDirectory');
            if (!Storage::disk($this->uploadDisk)->exists($sliderDirectory)) {
                Storage::disk($this->uploadDisk)->makeDirectory($sliderDirectory);
            }

            $slider = \Image::make($file);
            $slider->fit(\Config::get('images.sliderWidth'), \Config::get('images.sliderHeight'), function ($constraint) {
                $constraint->upsize();
            });

            $slider->save($this->uploadPath . $sliderDirectory . DIRECTORY_SEPARATOR . $filename);
        }

        return $size;
    }

    /**
     * Deletes all file parts in the chunks directory for files uploaded
     * more than chunksExpireIn seconds ago
     */
    protected function cleanupChunks()
    {
        foreach (Storage::disk($this->chunksDisk)->directories($this->chunksDirectory) as $dir) {
            $path = $this->chunksDirectory . DIRECTORY_SEPARATOR . $dir;

            if ($time = @filemtime($this->chunksPath . $path)) {
                if (time() - $time > $this->chunksExpireIn) {
                    Storage::disk($this->chunksDisk)->deleteDirectory($path);
                }
            }
        }
    }

    /**
     * Converts a given size with units to bytes.
     * @param string $str
     */
    protected function toBytes($str)
    {
        $val = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    protected function getDiskPath($disk = null)
    {
        if ($disk) {
            return Storage::disk($disk)->getDriver()->getAdapter()->getPathPrefix();
        } else {
            return Storage::getDriver()->getAdapter()->getPathPrefix();
        }
    }
}
