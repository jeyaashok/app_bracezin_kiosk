<?php

namespace User\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class UserDetail extends BaseModel
{
    protected $table = 'user_detail';

    protected $isCachable = false;

    protected $cachePrefix = 'user_detail';

    protected $cacheCooldownSeconds = 86400;

    protected $fillable = ['user_id', 'code', 'initial', 'surname', 'firstname', 'lastname', 'father_name', 'mother_name', 'mobile',
        'home_phone', 'emergency_phone', 'company_name', 'company_register_no', 'gst_number', 'vat_number', 'ein_number', 'office_phone',
        'designation', 'department', 'qualification', 'business_brand_name', 'business_category', 'office_phone', 'authority_person', 'authority_email', 'authority_mobile',
        'gender', 'date_of_birth', 'age', 'blood_group', 'marital_status',
        'address_doorno', 'address_line1', 'address_line2', 'landmark', 'area', 'city', 'state', 'country', 'zipcode', 'latitude', 'longitude', 'address', 'geo_location',
        'image_api', 'json', 'short_note', 'detail_about'];

    protected $casts = [
        'date_of_birth' => 'datetime:Y-m-d',
    ];

    protected $appends = ['tableName'];

    protected $searchable = [
        'columns' => [
            'user_detail.father_name' => 1,
        ],
    ];

    protected function dateOfBirth(): Attribute
    {
        return Attribute::make(
            get: fn ($date_of_birth) => $date_of_birth ? (new Carbon($date_of_birth))->format('Y-m-d') : null,
            set: fn ($date_of_birth) => $date_of_birth ? (new Carbon($date_of_birth))->format('Y-m-d') : null,
        );
    }
}
