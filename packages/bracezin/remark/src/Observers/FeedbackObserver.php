<?php

namespace Remark\Observers;

use Remark\Models\Feedback;

class FeedbackObserver
{
    /**
     * Handle the Role "creating" event.
     * @param  \App\Role  $feedback 
     * @return void
     */
    public function creating(Feedback $feedback) { }

    public function created(Feedback $feedback) { }

    /**
     * Handle the Role "updating" event.
     * @param  \App\Role  $feedback 
     * @return void
     */
    public function saving(Feedback $feedback) { }

    public function saved(Feedback $feedback) { }

    /**
     * Handle the Role "deleting" event.
     * @param  \App\Role  $feedback 
     * @return void
     */
    public function deleting(Feedback $feedback) { }

    public function delete(Feedback $feedback) { }

}
