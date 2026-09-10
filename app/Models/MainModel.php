<?php

namespace App\Models;

// use Nicolaslopezj\Searchable\SearchableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Tji\Traits\TjiTrait;

class MainModel extends Model
{
    use HasFactory, Notifiable, SoftDeletes, TjiTrait;

    protected $appends = ['tableName', 'dataType'];

    protected $dates = ['deleted_at'];

    protected $perPage = 25;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $user = auth('admin')->user();
            if ($user) {
                $model->created_by = $user->id;
                $model->updated_by = $user->id;
            } else {
                // Temp fix for customer registration
                $model->created_by = 1;
                $model->updated_by = 1;
            }
        });
        static::updating(function ($model) {
            $user = auth('admin')->user();
            if ($user) {
                $model->updated_by = $user->id;
            }
        });
    }

    /** Assign the dataType Attribuite. **/
    public function getTableNameAttribute()
    {
        return $this->attributes['tableName'] = $this->getTable();
    }

    /** Assign the ClassName Attribuite. **/
    public function getClassNameAttribute()
    {
        return $this->attributes['className'] = get_class($this);
    }

    /** Get the CreatedByName Attribuite. **/
    public function getCreatedByNameAttribute()
    {
        $userName = @$this->createdBy()->first()->name;

        return $this->attributes['createdByName'] = ($userName) ? ucfirst($userName) : 'System Admin';
    }

    /** Get the UpdatedByName Attribuite. **/
    public function getUpdatedByNameAttribute()
    {
        $user = @$this->updatedBy()->first()->name;

        return $this->attributes['updatedByName'] = ($userName) ? ucfirst($userName) : 'System Admin';
    }

    /** Get the CreatedByAvatar Attribuite. **/
    public function getCreatedByAvatarAttribute()
    {
        $userAvatar = @$this->createdBy()->first()->avatar_url;

        return $this->attributes['createdByAvatar'] = ($userAvatar) ? $userAvatar : null;
    }
}
