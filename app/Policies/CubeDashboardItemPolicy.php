<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\CubeDashboardItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CubeDashboardItemPolicy
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
        return $user->can(Permissions::BASLAKE_ORGANIZATIONS_ACCESS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CubeDashboardItem $cubeDashboardItem)
    {
        return $user->hasPermissionTo(Permissions::BASLAKE_ORGANIZATIONS_ACCESS, null, $cubeDashboardItem->cube->organization);
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
    public function update(User $user, CubeDashboardItem $cubeDashboardItem)
    {
        return $user->hasPermissionTo(Permissions::BASLAKE_ORGANIZATIONS_MANAGE, null, $cubeDashboardItem->cube->organization);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CubeDashboardItem $cubeDashboardItem)
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
    public function restore(User $user, CubeDashboardItem $cubeDashboardItem)
    {
        return $this->update($user, $cubeDashboardItem->cube->organization);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, CubeDashboardItem $cubeDashboardItem)
    {
        return false;
    }
}
