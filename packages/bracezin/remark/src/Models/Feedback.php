<?php

namespace Remark\Models;

use Remark\Models\BaseModel;

class Feedback extends BaseModel
{
    protected $table = 'feedback';

    protected $isCachable = false;
    protected $cachePrefix = "feedback";
    protected $cacheCooldownSeconds = 3600;

    protected $fillable = array('resource_id','resource_type','feedback_id','user_id','title','description','rating');

    protected $casts = [
        
    ];

    protected $appends = ['tableName'];

    protected $searchable = [
        'columns' => [
            'feedback.title' => 1,
            'feedback.description' => 1,
            'users.name' => 2,
            'users.mobile' => 2,
            'users.email' => 2,
        ],
        'joins' => [
            'users' => ['user_id', 'users.id'],
        ]
    ];

    public function parentFeedback() {
        return $this->belongsTo('Remark\Models\Feedback', 'feedback_id');
    }

    public function childFeedbacks() {
        return $this->hasMany('Remark\Models\Feedback');
    }

}
