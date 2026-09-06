<?php

namespace Role\Models;

use Tji\Traits\SearchableTrait;

// use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Permission extends \Spatie\Permission\Models\Permission
{
    use SearchableTrait;

    // protected $isCachable = false;
    // protected $cachePrefix = "permission";
    // protected $cacheCooldownSeconds = 864000;

    protected $fillable = ['name', 'guard_name', 'module'];

    protected $appends = ['tableName'];

    protected $searchable = [
        'columns' => [
            'permissions.name' => 1,
            'permissions.module' => 2,
        ],
    ];

    /** Assign the dataType Attribuite. **/
    public function getTableNameAttribute()
    {
        return $this->attributes['tableName'] = $this->getTable();
    }
}
