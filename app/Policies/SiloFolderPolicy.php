<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\SiloFolder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SiloFolderPolicy
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
        return $user->can(Permissions::BASLAKE_DATASETS_ACCESS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SiloFolder  $folder
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SiloFolder $folder)
    {
        return $user->hasPermissionTo(Permissions::BASLAKE_DATASETS_ACCESS, null, $folder->organization);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can(Permissions::BASLAKE_DATASETS_MANAGE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SiloFolder  $folder
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, SiloFolder $folder)
    {
        return $user->hasPermissionTo(Permissions::BASLAKE_DATASETS_MANAGE, null, $folder->organization);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SiloFolder  $folder
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SiloFolder $folder)
    {
        return $user->can(Permissions::BASLAKE_DATASETS_MANAGE);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SiloFolder  $folder
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, SiloFolder $folder)
    {
        return $this->update($user, $folder);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SiloFolder  $folder
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, SiloFolder $folder)
    {
       return false;
    }
}
