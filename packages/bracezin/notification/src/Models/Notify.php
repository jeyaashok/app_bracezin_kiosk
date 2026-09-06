<?php

namespace Notification\Models;

use Notification\Models\BaseModel;

class Notify extends BaseModel
{
    protected $table = 'notify'; 
    protected $isCachable = false;
    protected $cachePrefix = "notify";
    protected $cacheCooldownSeconds = 3600;

    protected $fillable = array('user_id', 'type', 'title','description','icon','color','is_notified','is_read','is_sound_notify','is_web_notify','is_desktop_notify');

    protected $casts = [
        'is_read' => 'boolean',
        'is_notified' => 'boolean',
        'is_sound_notify' => 'boolean',
        'is_desktop_notify' => 'boolean',
        'is_web_notify' => 'boolean'
    ];

    protected $appends = ['tableName'];

    protected $searchable = [
        'columns' => [
            'notify.title' => 1,
            'notify.description' => 2,
            'users.name' => 3,
            'users.mobile' => 3,
            'users.email' => 3,
        ],
        'joins' => [
            'users' => ['user_id', 'users.id'],
        ]
    ];

}
