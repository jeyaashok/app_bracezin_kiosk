<?php

namespace Remark\Traits;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/******** Index *********/
trait RemarkTrait {

    public function comment() {
    	if($this->tableName && $this->tableName === 'users') {
    		return $this->hasMany('Remark\Models\Comment')->latest();
    	}
    	if($this->tableName && $this->tableName === 'comment') {
    		return $this->belongsTo('Remark\Models\Comment', 'comment_id');
    	}
    	return $this->morphOne('Remark\Models\Comment', 'resource');
    }

    public function comments() {
    	if($this->tableName && $this->tableName === 'users') {
    		return $this->hasMany('Remark\Models\Comment');
    	}
    	if($this->tableName && $this->tableName === 'comment') {
    		return $this->hasMany('Remark\Models\Comment');
    	}
        return $this->morphMany('Remark\Models\Comment', 'resource');
    }

    public function feedback() {
    	if($this->tableName && $this->tableName === 'users') {
    		return $this->hasMany('Remark\Models\Feedback')->latest();
    	}
    	if($this->tableName && $this->tableName === 'feedback') {
    		return $this->belongsTo('Remark\Models\Feedback', 'feedback_id');
    	}
    	return $this->morphOne('Remark\Models\Feedback', 'resource');
    }

    public function feedbacks() {
    	if($this->tableName && $this->tableName === 'users') {
    		return $this->hasMany('Remark\Models\Feedback');
    	}
    	if($this->tableName && $this->tableName === 'feedback') {
    		return $this->hasMany('Remark\Models\Feedback');
    	}
        return $this->morphMany('Remark\Models\Feedback', 'resource');
    }

}
