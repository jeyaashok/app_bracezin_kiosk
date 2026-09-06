<?php

namespace Directory\Traits;

use Arr;
use Carbon\Carbon;
use ErrorResponse;
use Media;
use Storage;
use Str;
use Sys;
use Validator;

trait DirectoryTrait
{
    // /********** Relationship Functions *********///
    /** Get the Avatar Image */
    public function media()
    {
        if ($this->media_id && $this->media_id > 0) {
            return $this->belongsTo('Directory\Models\Media', 'media_id');
        } else {
            return $this->morphOne('Directory\Models\Media', 'resource')->where('document_type', null)->where('is_primary', '=', 1)->latest();
        }
    }

    public function userMedia()
    {
        return $this->morphOne('Directory\Models\Media', 'model');
    }

    public function image()
    {
        return $this->media();
    }

    public function avatar()
    {
        return $this->media();
    }

    public function medias()
    {
        return $this->morphMany('Directory\Models\Media', 'resource');
    }

    public function images()
    {
        return $this->medias();
    }

    public function allMedias()
    {
        return $this->medias()->get('url')->toArray();
    }

    public function allImages()
    {
        return $this->medias()->get('url')->toArray();
    }

    // /********** Attribute Functions *********///
    /** Get Image Attribuite Values */
    public function getImageUrlAttribute()
    {
        $image = $this->image()->first();

        return $this->attributes['imageUrl'] = $this->resolveMediaUrl($image);
    }

    protected static function resolveMediaDisk($disk = null)
    {
        $disk = $disk ?: config('filesystems.default', 'local');

        if (! in_array($disk, ['local', 'public', 's3'], true)) {
            $disk = 's3';
        }

        return $disk;
    }

    protected static function normalizeMediaLocation($location = null)
    {
        if (! $location) {
            return null;
        }

        $location = trim((string) $location);
        if ($location === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $location)) {
            $location = parse_url($location, PHP_URL_PATH) ?: $location;
        }

        $location = ltrim($location, '/');
        if (Str::startsWith($location, 'storage/')) {
            $location = Str::after($location, 'storage/');
        }

        return ltrim($location, '/');
    }

    protected static function resolveMediaMime($file = null, $fallback = 'application/octet-stream')
    {
        if (is_string($file)) {
            if (preg_match('/^data:([^;]+);base64,/', $file, $matches)) {
                return $matches[1];
            }

            if (is_file($file)) {
                $mime = @mime_content_type($file);
                if ($mime) {
                    return $mime;
                }
            }
        }

        if (is_object($file) && method_exists($file, 'getMimeType')) {
            $mime = $file->getMimeType();
            if ($mime) {
                return $mime;
            }
        }

        return $fallback;
    }

    protected static function resolveMediaExtension($file = null, $mime = null, $fallback = 'jpg')
    {
        if (is_string($file)) {
            if (preg_match('/\.([a-zA-Z0-9]+)$/', $file, $matches)) {
                return $matches[1];
            }

            if (preg_match('/^data:([^;]+);base64,/', $file, $matches)) {
                $mime = $matches[1];
            }
        }

        if ($mime) {
            $parts = explode('/', $mime);
            if (count($parts) > 1 && $parts[1]) {
                return $parts[1];
            }
        }

        return $fallback;
    }

    protected static function resolveMediaSize($file = null)
    {
        if (is_string($file)) {
            if (preg_match('/^data:[^;]+;base64,/', $file)) {
                $payload = substr($file, strpos($file, ',') + 1);

                return (int) (strlen(rtrim($payload, '=')) * 3 / 4);
            }

            if (is_file($file)) {
                return (int) @filesize($file);
            }
        }

        if (is_object($file) && method_exists($file, 'getSize')) {
            return (int) $file->getSize();
        }

        return null;
    }

    protected static function resolveMediaContents($file = null)
    {
        if (is_string($file)) {
            if (preg_match('/^data:[^;]+;base64,/', $file)) {
                $payload = substr($file, strpos($file, ',') + 1);

                return base64_decode($payload, true);
            }

            if (is_file($file)) {
                return file_get_contents($file);
            }

            return null;
        }

        if (is_object($file)) {
            if (method_exists($file, 'getRealPath') && $file->getRealPath()) {
                return file_get_contents($file->getRealPath());
            }

            if (method_exists($file, 'getPathname') && $file->getPathname()) {
                return file_get_contents($file->getPathname());
            }
        }

        return null;
    }

    protected function resolveMediaUrl($item = null)
    {
        if (! $item) {
            return null;
        }

        $disk = self::resolveMediaDisk(@$item->disk);
        $location = self::normalizeMediaLocation(@$item->location ?: @$item->url);

        if (! $location) {
            return @$item->url ?: null;
        }

        if ($disk === 'local') {
            return @$item->id ? route('show.media', ['id' => $item->id]) : (@$item->url ?: null);
        }

        try {
            if (Storage::disk($disk)->exists($location)) {
                return Storage::disk($disk)->url($location);
            }
        } catch (\Throwable $exception) {
            return @$item->url ?: null;
        }

        return @$item->url ?: null;
    }

    /** Store Image then Return stored image id */
    public function scopeStoreImage($query, $input)
    {
        $media = Media::storeImage($input, $this->getTable(), $this->id);

        return $media;
    }

    /** Update Image then Return stored image id */
    public function scopeUpdateImage($query, $input)
    {
        $media = Media::updateImage($input, $this->getTable(), $this->id);

        return $media;
    }

    /** Delete Stored Image */
    public function deleteImage($query)
    {
        $medias = Media::getAllImages($this->getTable(), $this->id);
        foreach ($medias as $media) {
            $this->deleteS3Media($media);
            Media::deleteImage($media->id);
        }

    }

    public function deleteS3Media($item)
    {
        $disk = self::resolveMediaDisk(@$item->disk);
        $location = self::normalizeMediaLocation(@$item->location ?: @$item->url);

        if ($item && $location && is_null($item->shared_by)) {
            try {
                if (Storage::disk($disk)->exists($location)) {
                    Storage::disk($disk)->delete($location);
                    if ($disk === 's3') {
                        // $this->deleteCloudFront($location);
                    }
                }
            } catch (\Throwable $exception) {
                return;
            }
        }
    }

    public function deleteCloudFront($filePath) {}

    public function getProofsAttribute()
    {
        $proofs = null;
        if ($this && $this->tableName) {
            $proofKeys = @config('directoryConfig.append_proofs')[$this->tableName];
            switch ($this->tableName) {
                case 'partner':
                case 'diagnose':
                    $proofs = $this->medias()
                        ->whereIn('document_type', array_filter($proofKeys))
                        ->get()
                        ->keyBy('document_type')
                        ->map(function ($media) {
                            return $this->resolveMediaUrl($media);
                        })
                        ->toArray();
                    break;
                default:
                    break;
            }
        }
        $proofs = (is_array($proofs) && count($proofs) === 0) ? null : $proofs;

        return $this->attributes['proofs'] = $proofs;
    }

    public function storeMedia($input = null)
    {
        $media = null;
        $disk = self::resolveMediaDisk(@$input['disk']);
        $input['tableName'] = $this->tableName;
        $input['tableId'] = $this->id;
        $modelId = $input['model_id'] = Arr::has($input, 'model_id') ? $input['model_id'] : @Sys::authUser()->id;
        $modelType = $input['model_type'] = Arr::has($input, 'model_type') ? $input['model_type'] : @Sys::authUser()->tableName;
        $fileData = Arr::has($input, 'file_data') ? $input['file_data'] : null;
        $base64_image = @$fileData ?: null;
        if (is_null($base64_image)) {
            return null;
        }
        $fileName = @$input['file_name'] ?: $this->tableName.'_'.$this->id.'_'.(string) Carbon::now()->format('Y_M_d_H_i_s');
        $mime = @$input['file_mime'] ?: self::resolveMediaMime($base64_image);
        $extension = @$input['file_extension'] ?: self::resolveMediaExtension($base64_image, $mime);
        $size = @$input['file_size'] ?: self::resolveMediaSize($base64_image);
        $type = $input['type'] = ($mime) ? Media::getTypeByFileType($mime) : 'document';
        $documentType = @$input['document_type'] ?: null;
        $storeType = @$input['store_type'] ?: 'replace';
        if ($documentType) {
            $input['file_name'] = @$input['file_name'] ?: $fileName;
            $input['file_name'] = @$input['document_type'] ? Str::slug($input['document_type'], '-') : $fileName;
            $this->mediaValidation($input);
        }
        $existingMedia = null;
        if ($storeType && $storeType === 'replace') {
            $checkData = [
                'resource_id' => $this->id,
                'resource_type' => $this->tableName,
                'is_primary' => 1,
            ];
            if ($documentType != null) {
                $checkData['document_type'] = $documentType;
            } else {
                $checkData['null'] = 'document_type|true';
            }
            $existingMedia = @Media::getExistingMediaByResource($checkData);
            if ($existingMedia && $existingMedia->id) {
                $this->deleteS3Media($existingMedia);
            }
        }
        $s3StoreLocation = $this->storeAndGetS3Url($input, true);
        if (! $s3StoreLocation) {
            return $media;
        }
        $s3Url = $this->resolveMediaUrl((object) [
            'id' => null,
            'disk' => $disk,
            'location' => $s3StoreLocation,
            'url' => null,
        ]) ?: $s3StoreLocation;
        $storeData = [
            'resource_id' => $this->id,
            'resource_type' => $this->tableName,
            'model_id' => @$modelId,
            'model_type' => @$modelType,
            'document_type' => @$documentType,
            'name' => $fileName,
            'filename' => $fileName,
            'location' => $s3StoreLocation,
            'url' => $s3Url,
            'type' => @$type,
            'mime' => @$mime,
            'disk' => $disk,
            'etag' => null,
            'extension' => @$extension,
            'size' => (int) @$size,
            'is_active' => 1,
            'is_primary' => ($storeType && $storeType === 'replace') ? 1 : 0,
            'is_local_server' => in_array($disk, ['local', 'public'], true) ? 1 : 0,
        ];
        if ($existingMedia && $existingMedia->id) {
            $media = Media::updateMedia($existingMedia->id, $storeData);
        } else {
            $media = Media::storeMedia($storeData);
        }

        if ($media && $media->id) {
            $media->url = $this->resolveMediaUrl($media);
            if ($media->url !== @$storeData['url']) {
                Media::updateMedia($media->id, ['url' => $media->url]);
            }
        }

        return $media;
    }

    public static function storeAndGetS3Url($input = null, $returnPath = false)
    {
        $file = @$input['file_data'] ?: null;
        if (is_null($file)) {
            return null;
        }
        $disk = self::resolveMediaDisk(@$input['disk']);
        $modelId = @$input['model_id'] ?: @Sys::authUser()->id;
        $modelType = @$input['model_type'] ?: @Sys::authUser()->tableName;
        $tableName = @$input['tableName'] ?: null;
        $tableId = @$input['tableId'] ?: null;
        $fileName = @$input['file_name'] ?: $this->tableName.'_'.$this->id.'_'.(string) Carbon::now()->format('Y_M_d_H_i_s');
        $folderLocation = $tableName.'/'.Carbon::now()->format('Y').'/'.Carbon::now()->format('M').'/';
        $fileLocation = ($modelId) ? $folderLocation.$modelId.'_user/'.$fileName : $folderLocation.$tableId.'_table/'.$fileName;
        $mime = @$input['file_mime'] ?: self::resolveMediaMime($file);
        $extension = @$input['file_extension'] ?: self::resolveMediaExtension($file, $mime);
        $extension = (! is_null($extension) && $extension !== '') ? '.'.$extension : '.jpg';
        $fileLocation = $fileLocation.$extension;
        $s3Url = null;
        if ($file) {
            $folderPath = dirname($fileLocation);
            $storedLocation = $fileLocation;
            $contents = self::resolveMediaContents($file);
            $visibility = in_array($disk, ['public', 's3'], true) ? 'public' : null;

            Storage::disk($disk)->makeDirectory($folderPath);
            if (! is_null($contents)) {
                Storage::disk($disk)->put($storedLocation, $contents, $visibility ? ['visibility' => $visibility] : []);
            } else {
                Storage::disk($disk)->putFileAs($folderPath, $file, basename($storedLocation), $visibility ? ['visibility' => $visibility] : []);
            }

            if ($disk === 'local') {
                $s3Url = $storedLocation;
            } else {
                $s3Url = Storage::disk($disk)->url($storedLocation);
            }

            return ($returnPath) ? $storedLocation : $s3Url;
        }

        return $s3Url;
    }

    public function mediaValidation($input)
    {
        $tableName = @$input['tableName'] ?: null;
        $documentType = @$input['document_type'] ?: null;
        $fileName = @$input['file_name'] ?: null;
        $modelId = @$input['model_id'] ?: @Sys::authUser()->id;
        if ($modelId) {
            $validator = Validator::make($input, [
                'file_data' => 'required',
                'file_name' => 'required',
                'store_type' => 'required',
            ]);
        } else {
            $validator = Validator::make($input, [
                'file_data' => 'required',
                'file_name' => 'required',
                'store_type' => 'required',
                'document_type' => 'required',
            ]);
        }
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            throw new ErrorResponse($error, 405, 'info');
        }

        // Check File Size
        $file = Arr::has($input, 'file_data') ? $input['file_data'] : null;
        $size = @$input['file_size'] ?: self::resolveMediaSize($file);
        $sizeMb = ($size) ? $size / 1048576 : null;
        if ($sizeMb && $sizeMb > 5) {
            throw new ErrorResponse('File Size should not exceeded of 5MB.', 405, 'info');
        }

        return $input;
    }
}
