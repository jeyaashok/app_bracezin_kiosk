<?php

namespace Remark\Observers;

use Remark\Models\Comment;

class CommentObserver
{
    /**
     * Handle the Role "creating" event.
     * @param  \App\Role  $comment
     * @return void
     */
    public function creating(Comment $comment) { }

    public function created(Comment $comment) { }

    /**
     * Handle the Role "updating" event.
     * @param  \App\Role  $comment
     * @return void
     */
    public function saving(Comment $comment) { }

    public function saved(Comment $comment) { }

    /**
     * Handle the Role "deleting" event.
     * @param  \App\Role  $comment
     * @return void
     */
    public function deleting(Comment $comment) { }

    public function delete(Comment $commente) { }

}
