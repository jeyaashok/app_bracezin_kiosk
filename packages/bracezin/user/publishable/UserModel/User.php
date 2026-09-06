<?php

namespace App\Models;

class User extends AuthModel
{
    protected $table = 'users';

    protected $guard = 'admin';

    protected $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'username', 'email', 'mobile', 'password', 'remember_token', 'is_sysAdmin', 'is_active', 'do_change_password', 'do_reset_password', 'is_email_verified', 'is_mobile_verified', 'email_verified_at', 'mobile_verified_at', 'is_sound_notify', 'is_desktop_notify', 'is_web_notify', 'default_lang', 'type', 'force_logout_at'];

    protected $dates = [
        'email_verified_at',
        'mobile_verified_at',
        'force_logout_at',
        'deleted_at',
    ];

    protected $appends = ['tableName', 'nameBase', 'code', 'profilePercentage', 'imageUrl', 'fullAddress', 'otp'];

    protected $searchable = [
        'columns' => [
            'name' => 1,
            'username' => 1,
            'email' => 1,
            'mobile' => 1,
        ],
    ];

    protected $casts = [
        'is_sysAdmin' => 'boolean',
        'is_active' => 'boolean',
        'do_change_password' => 'boolean',
        'do_reset_password' => 'boolean',
        'is_email_verified' => 'boolean',
        'is_mobile_verified' => 'boolean',
        'is_sound_notify' => 'boolean',
        'is_desktop_notify' => 'boolean',
        'is_web_notify' => 'boolean',
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
        'force_logout_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /** Get Table Name */
    public function getTableNameAttribute()
    {
        return $this->attributes['tableName'] = 'users';
    }

    /** Get the percentage of profile updation Attribuite. **/
    public function getProfilePercentageAttribute()
    {
        $isFilled = static function ($value): bool {
            if (is_string($value)) {
                return trim($value) !== '';
            }

            return ! is_null($value);
        };

        $detail = $this->relationLoaded('detail') ? $this->detail : $this->detail()->first();

        $userFields = [
            $this->name,
            $this->email,
            $this->mobile,
            $this->avatar_url,
        ];

        $detailFields = [
            optional($detail)->code,
            optional($detail)->company_name,
            optional($detail)->company_register_no,
            optional($detail)->vat_number,
            optional($detail)->ein_number,
            optional($detail)->address_line1,
            optional($detail)->city,
            optional($detail)->state,
            optional($detail)->country,
            optional($detail)->zipcode,
        ];

        $allFields = array_merge($userFields, $detailFields);
        $totalFields = count($allFields);
        $completedFields = count(array_filter($allFields, $isFilled));
        $percentage = ($totalFields > 0) ? (int) round(($completedFields / $totalFields) * 100) : 0;
        $percentage = max(10, min(100, $percentage));

        return $this->attributes['profilePercentage'] = $percentage;
    }

    protected function getNameBaseAttribute()
    {
        $nameBase = $this->name ?: $this->username ?: $this->mobile ?: $this->email;

        return $this->attributes['nameBase'] = @$nameBase;
    }

    protected function getCodeAttribute()
    {
        $code = $this->detail?->code;

        return $this->attributes['code'] = @$code;
    }

    public function getOtpAttribute()
    {
        $otp = $this->otp();
        $otpValue = ($otp && isset($otp->value)) ? $otp->value : null;

        return $this->attributes['otp'] = $otpValue;
    }
}
