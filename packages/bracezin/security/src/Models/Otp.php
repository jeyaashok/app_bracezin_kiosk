<?php

namespace Security\Models;

use Security\Models\BaseModel;

class Otp extends BaseModel
{
    protected $table = 'otp';

    protected $isCachable = false;
    protected $cachePrefix = "otp";
    protected $cacheCooldownSeconds = 86400;

    protected $fillable = array('value', 'resource_id', 'resource_type', 'expired_at');

    protected $casts = [ ];

    protected $appends = ['tableName'];

}
