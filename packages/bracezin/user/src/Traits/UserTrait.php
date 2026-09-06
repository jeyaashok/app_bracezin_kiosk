<?php

namespace User\Traits;

trait UserTrait
{
    // /********** RelationShip Functions *********///

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public function userDetail()
    {
        return $this->hasOne('User\Models\UserDetail');
    }

    public function detail()
    {
        return $this->hasOne('User\Models\UserDetail');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by')->withTrashed();
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\Models\User', 'updated_by')->withTrashed();
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\User', 'admin_id');
    }

    public function admins()
    {
        return $this->hasMany('App\Models\User', 'admin_id', 'id');
    }

    public function agent()
    {
        return $this->belongsTo('App\Models\User', 'agent_id');
    }

    public function agents()
    {
        return $this->hasMany('App\Models\User', 'agent_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\User', 'company_id');
    }

    public function companies()
    {
        return $this->hasMany('App\Models\User', 'company_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\User', 'customer_id');
    }

    public function customers()
    {
        return $this->hasMany('App\Models\User', 'customer_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\User', 'staff_id');
    }

    public function staffs()
    {
        return $this->hasMany('App\Models\User', 'staff_id', 'id');
    }

    // /********** Attribute Functions *********///
    /** Get the CreatedByName Attribuite. **/
    public function getCreatedByNameAttribute()
    {
        $createdBy = ($this->created_by && $this->has('createdBy')) ? $this->createdBy()->first() : $this->createdBy;

        return $this->attributes['createdByName'] = ($createdBy && $createdBy->username) ? ucfirst($createdBy->username) : null;
    }

    /** Get the UpdatedByName Attribuite. **/
    public function getUpdatedByNameAttribute()
    {
        $updatedBy = ($this->updated_by && $this->has('updatedBy')) ? $this->updatedBy()->first() : $this->updatedBy;

        return $this->attributes['updatedByName'] = ($updatedBy && $updatedBy->username) ? ucfirst($updatedBy->username) : null;
    }

    public function getFullAddressAttribute()
    {
        $address = '';
        $detail = $this->detail()->first();
        if ($detail && $detail->address_doorno) {
            $address .= $detail->address_doorno;
        }
        if ($detail && $detail->address_line_1) {
            $address .= ', '.$detail->address_line_1;
        }
        if ($detail && $detail->address_line_2) {
            $address .= ', '.$detail->address_line_2;
        }
        if ($detail && $detail->landmark) {
            $address .= ', '.$detail->landmark;
        }
        if ($detail && $detail->city) {
            $address .= ', '.$detail->city;
        }
        if ($detail && $detail->state) {
            $address .= ', '.$detail->state;
        }
        if ($detail && $detail->country) {
            $address .= ', '.$detail->country;
        }
        if ($detail && $detail->zipcode) {
            $address .= ', '.$detail->zipcode;
        }

        return $this->attributes['fullAddress'] = $address;
    }
}
