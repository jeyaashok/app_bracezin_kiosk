<?php

namespace Directory\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media extends BaseModel
{
    protected $table = 'media';

    protected $isCachable = false;

    protected $fillable = ['user_id', 'resource_id', 'resource_type', 'model_id', 'model_type', 'shared_by', 'document_type', 'name', 'filename', 'location', 'dirname', 'mime', 'size', 'extension', 'etag', 'disk', 'url', 'type', 'is_active', 'is_primary', 'is_favorite', 'is_local_server', 'created_by', 'updated_by'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'is_favorite' => 'boolean',
        'is_local_server' => 'boolean',
    ];

    protected $appends = ['tableName'];

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            $disk = $model->disk ?: config('filesystems.default', 'local');
            $location = $model->location ?: $model->url ?: $model->filename;

            if (Str::startsWith($location, ['http://', 'https://'])) {
                $location = parse_url($location, PHP_URL_PATH) ?: $location;
            }

            $location = ltrim((string) $location, '/');

            if ($location && Storage::disk($disk)->exists($location)) {
                Storage::disk($disk)->delete($location);
            }
        });
    }
}
