<?php

namespace Remark\Models;

use Remark\Models\BaseModel;

class Comment extends BaseModel
{
    protected $table = 'comment';

    protected $isCachable = false;
    protected $cachePrefix = "comment";
    protected $cacheCooldownSeconds = 3600;

    protected $fillable = array('resource_id', 'resource_type', 'comment_id','user_id','title', 'description','rating');
  
    protected $appends = ['tableName'];

    protected $searchable = [
        'columns' => [
            'comment.title' => 1,
            'comment.description' => 1,
            'users.name' => 2,
            'users.mobile' => 2,
            'users.email' => 2,
        ],
        'joins' => [
            'users' => ['user_id', 'users.id'],
        ]
    ];

    public function parentComment() {
        return $this->belongsTo('Remark\Models\Comment', 'comment_id');
    }

    public function childComments() {
        return $this->hasMany('Remark\Models\Comment');
    }

    public function comments() {
        return $this->hasMany('Remark\Models\Comment');
    }

}
