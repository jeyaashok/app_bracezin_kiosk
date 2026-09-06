<?php
namespace Notification\Observers;

use Notification\Models\Notify;

class NotifyObserver
{
    /**
     * Handle the Role "creating" event.
     * @param  \App\Role  $notify
     * @return void
     */
    public function creating(Notify $notify) { }
    public function created(Notify $notify) { }

    /**
     * Handle the Role "updating" event.
     * @param  \App\Role  $notify
     * @return void
     */
    public function saving(Notify $notify) { }
    public function saved(Notify $notify) { }

    /**
     * Handle the Role "deleting" event.
     * @param  \App\Role  $notify
     * @return void
     */
    public function deleting(Notify $notify) { }
    public function delete(Notify $notify) { }

}
