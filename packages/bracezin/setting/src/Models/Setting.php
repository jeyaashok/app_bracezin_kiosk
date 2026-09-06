<?php

namespace Setting\Models;

class Setting extends BaseModel
{
    protected $table = 'setting';

    protected $fillable = ['title', 'slug', 'value', 'type', 'category', 'is_editable', 'description', 'created_by', 'updated_by'];

    protected $casts = [
        'is_editable' => 'boolean',
    ];

    protected $appends = ['tableName', 'type_name'];

    protected $searchable = [
        'columns' => [
            'setting.title' => 1,
            'setting.category' => 2,
            'setting.description' => 3,
        ],
    ];

    public function gettitleTypeNameAttribute()
    {
        switch ($this->type) {
            case 1:
                return $this->attributes['type_name'] = 'text';
            case 2:
                return $this->attributes['type_name'] = 'number';
            case 3:
                return $this->attributes['type_name'] = 'email';
            case 4:
                return $this->attributes['type_name'] = 'email.multiple';
            case 5:
                return $this->attributes['type_name'] = 'address';
            case 6:
                return $this->attributes['type_name'] = 'link';
            case 7:
                return $this->attributes['type_name'] = 'setting';
            case 8:
                return $this->attributes['type_name'] = 'boolean';
            case 9:
                return $this->attributes['type_name'] = 'imageurl';
            case 10:
                return $this->attributes['type_name'] = 'videourl';
            default:
                return $this->attributes['type_name'] = 'none';
        }
    }
}
