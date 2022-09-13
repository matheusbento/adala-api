<?php

namespace App\Policies;

use App\Models\Cube;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Constants\Permissions;

class CubePolicy
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
        return $user->can(Permissions::BASLAKE_CUBES_ACCESS);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cube  $cube
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Cube $cube)
    {
        return $user->hasPermissionTo(Permissions::BASLAKE_CUBES_ACCESS, null, $cube->organization);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can(Permissions::BASLAKE_CUBES_MANAGE);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cube  $cube
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Cube $cube)
    {
        return $user->hasPermissionTo(Permissions::BASLAKE_CUBES_MANAGE, null, $cube->organization);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cube  $cube
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Cube $cube)
    {
        return $user->can(Permissions::BASLAKE_CUBES_MANAGE);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cube  $cube
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Cube $cube)
    {
        return $this->update($user, $cube);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cube  $cube
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Cube $cube)
    {
       return false;
    }
}
