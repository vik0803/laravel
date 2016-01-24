<?php

namespace App\Services;

use Illuminate\Http\Request;
use Storage;
use File;

class FineUploader
{
    const IMAGE_EXTENSIONS = ['jpg', 'png', 'gif', 'jpeg'];
    const ALL_EXTENSIONS = [];

    public $request;
    public $status = 200;
    public $allowedExtensions = [];
    public $sizeLimit = null;
    public $inputName = 'qqfile';
    public $chunksDirectory = 'chunks';
    public $chunksPath;
    public $chunksDisk = 'local-private';
    public $uploadDirectory;
    public $uploadPath;
    public $uploadDisk = 'local-public';

    public $chunksCleanupProbability = 0.001; // Once in 1000 requests on avg
    public $chunksExpireIn = 604800; // One week

    protected $uploadName;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->allowedExtensions = $this::IMAGE_EXTENSIONS;
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
        $this->uploadName = $this->getName();
        $chunksDirectory = $this->chunksDirectory . DIRECTORY_SEPARATOR . $uuid;
        $totalParts = (int)$this->request->input('qqtotalparts', 1);

        $uploadFile = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid, $this->getUploadName()]);

        if (!Storage::disk($this->uploadDisk)->exists($uploadFile)) {
            Storage::disk($this->uploadDisk)->makeDirectory(dirname($uploadFile));
        }

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

        // $file = Storage::disk($this->uploadDisk)->get($uploadFile);
        // make thumbnail ?

        return [
            'success'=> true,
            'uuid' => $uuid,
            'fileName' => $this->getUploadName(),
            'fileExtension' => File::extension($this->getUploadName()),
            'fileSize' => Storage::disk($this->uploadDisk)->size($uploadFile),
        ];
    }

    /**
     * Process the upload.
     * @param string $name Overwrites the name of the file.
     */
    public function handleUpload($name = null)
    {
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
            $uploadFile = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $uuid, $name]);

            if ($uploadFile) {
                $uploadDirectory = dirname($uploadFile);
                $this->uploadName = basename($uploadFile);

                if (!Storage::disk($this->uploadDisk)->exists($uploadDirectory)) {
                    Storage::disk($this->uploadDisk)->makeDirectory($uploadDirectory);
                }

                if (($response = $file->move($this->uploadPath . $uploadDirectory, $uploadFile)) !== false) {
                    return [
                        'success'=> true,
                        'uuid' => $uuid,
                        'fileName' => $response->getFilename(),
                        'fileExtension' => $response->getExtension(),
                        'fileSize' => $response->getSize(),
                    ];
                }
            }

            return ['error' => trans('cms/fineuploader.errorSave')];
        }
    }

    /**
     * Deletes all file parts in the chunks directory for files uploaded
     * more than chunksExpireIn seconds ago
     */
    protected function cleanupChunks()
    {
        foreach (Storage::disk($this->chunksDisk)->directories($this->chunksDirectory) as $dir) {
            $path = $this->chunksDirectory . DIRECTORY_SEPARATOR . $dir;

            if (time() - filemtime($this->chunksPath . $path) > $this->chunksExpireIn) {
                Storage::disk($this->chunksDisk)->deleteDirectory($path);
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
