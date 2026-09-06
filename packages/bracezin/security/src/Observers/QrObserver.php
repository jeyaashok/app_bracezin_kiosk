<?php

namespace Security\Observers;

use Security\Models\Qr;

class QrObserver
{
    /**
     * Handle the Role "creating" event.
     * @param  \App\Role  $qr
     * @return void
     */
    public function creating(Qr $qr) { }

    public function created(Qr $qr) { }

    /**
     * Handle the Role "updating" event.
     * @param  \App\Role  $qr
     * @return void
     */
    public function saving(Qr $qr) { }

    public function saved(Qr $qr) { }

    /**
     * Handle the Role "deleting" event.
     * @param  \App\Role  $qr
     * @return void
     */
    public function deleting(Qr $qr) { }

    public function delete(Qr $qr) { }

}
