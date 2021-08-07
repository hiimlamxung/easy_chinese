<?php

namespace App\Policies\Admin;

use App\User;
use App\Models\Admins\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any admins.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the admin.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Admins\Admin  $admin
     * @return mixed
     */
    public function view(User $user, Admin $admin)
    {
        //
    }

    /**
     * Determine whether the user can create admins.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the admin.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Admins\Admin  $admin
     * @return mixed
     */
    public function update(Admin $guard, Admin $admin)
    {
        return $guard->id == $admin->id;
    }

    /**
     * Determine whether the user can delete the admin.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Admins\Admin  $admin
     * @return mixed
     */
    public function delete(User $user, Admin $admin)
    {
        //
    }

    /**
     * Determine whether the user can restore the admin.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Admins\Admin  $admin
     * @return mixed
     */
    public function restore(User $user, Admin $admin)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the admin.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Admins\Admin  $admin
     * @return mixed
     */
    public function forceDelete(User $user, Admin $admin)
    {
        //
    }
}
