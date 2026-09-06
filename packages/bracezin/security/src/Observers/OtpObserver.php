<?php

namespace Security\Observers;

use Security\Models\Otp;

class OtpObserver
{
    /**
     * Handle the Role "creating" event.
     * @param  \App\Role  $otp
     * @return void
     */
    public function creating(Otp $otp) { }

    public function created(Otp $otp) { }

    /**
     * Handle the Role "updating" event.
     * @param  \App\Role  $otp
     * @return void
     */
    public function saving(Otp $otp) { }

    public function saved(Otp $otp) { }

    /**
     * Handle the Role "deleting" event.
     * @param  \App\Role  $otp
     * @return void
     */
    public function deleting(Otp $otp) { }

    public function delete(Otp $otp) { }

}
