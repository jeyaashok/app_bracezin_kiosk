<?php

namespace Security\Models;

use Security\Models\BaseModel;

class Qr extends BaseModel
{
    protected $table = 'qr';

    protected $isCachable = false;
    protected $cachePrefix = "qr";
    protected $cacheCooldownSeconds = 86400;

    protected $fillable = array('code', 'qr_code', 'resource_id','resource_type','is_refreshable','is_used');

    protected $casts = [
        'is_refreshable' => 'boolean',
        'is_used' => 'boolean'
    ];

    protected $appends = ['tableName'];

}
