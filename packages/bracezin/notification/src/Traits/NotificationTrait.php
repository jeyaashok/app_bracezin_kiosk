<?php

namespace Notification\Traits;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/******** Index *********/
trait NotificationTrait { 

    public function notification() {
        return $this->belongsTo('Notification\Models\Notify', 'notify_id');
    }

    public function notifications() {
        return $this->hasMany('Notification\Models\Notify');
    }
}
