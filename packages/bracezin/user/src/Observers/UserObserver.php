<?php

namespace User\Observers;

use App\Models\User;
use App\Role;
use Person;

class UserObserver
{
    /**
     * Handle the Role "creating" event.
     *
     * @param  Role  $user
     * @return void
     */
    public function creating(User $user) {}

    public function created(User $user)
    {
        $this->storeUserDetail($user);
        $this->roleInit($user);
    }

    /**
     * Handle the Role "updating" event.
     *
     * @param  Role  $user
     * @return void
     */
    public function saving(User $user) {}

    public function saved(User $user)
    {
        $this->storeUserDetail($user);
        $this->roleInit($user);
    }

    /**
     * Handle the Role "deleting" event.
     *
     * @param  Role  $user
     * @return void
     */
    public function deleting(User $user) {}

    public function delete(User $user) {}

    public function storeUserDetail(User $user)
    {
        $input = request()->all();
        Person::updateOrCreateUserDetail($user, $input);
    }

    public function roleInit(User $user)
    {
        if ($user && $user->type && ($user->type == 'customer' || $user->type == 'company' || $user->type == 'agent' || $user->type == 'admin' || $user->type == 'staff')) {
            $user->assignRole($user->type);
        }
    }
}
