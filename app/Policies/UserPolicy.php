<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use App\Traits\AdminPrivileges;

class UserPolicy
{
    use HandlesAuthorization, AdminPrivileges;
    
    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $tokenUser
     * @param  \App\User  $model
     * @return mixed
     */
    public function view(User $tokenUser, User $model)
    {
        return $tokenUser->id === $model->id
                ? Response::allow()
                : Response::deny(); 
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $tokenUser
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $tokenUser, User $model)
    {
        return $tokenUser->id === $model->id
                ? Response::allow()
                : Response::deny(); 
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $tokenUser
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $tokenUser, User $model)
    {
        return $tokenUser->token()->client->personal_access_client && $tokenUser->id === $model->id
                ? Response::allow()
                : Response::deny(); 
    }

}

