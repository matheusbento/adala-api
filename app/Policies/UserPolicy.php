<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
      * Determine whether the user can view any models.
      *
      * @param  \App\Models\User  $user
      * @return \Illuminate\Auth\Access\Response|bool
      */
    public function viewAny(User $user)
    {
        return $user->can(Permissions::BASLAKE_ORGANIZATIONS_MANAGE);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $userContext)
    {
        return $user->can(Permissions::BASLAKE_ORGANIZATIONS_MANAGE);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can(Permissions::BASLAKE_ORGANIZATIONS_MANAGE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $userContext)
    {
        return $user->can(Permissions::BASLAKE_ORGANIZATIONS_MANAGE);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $userContext)
    {
        return $user->can(Permissions::BASLAKE_ORGANIZATIONS_MANAGE);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $userContext)
    {
        return $this->update($user, $userContext);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $userContext)
    {
        return false;
    }
}
