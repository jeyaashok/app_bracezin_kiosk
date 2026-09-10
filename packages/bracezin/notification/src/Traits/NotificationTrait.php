<?php

namespace Notification\Traits;

/******** Index *********/
trait NotificationTrait
{
    public function notification()
    {
        return $this->belongsTo('Notification\Models\Notify', 'notify_id');
    }

    public function notifyRecords()
    {
        return $this->hasMany('Notification\Models\Notify');
    }
}
