<?php

namespace App\Policies;

use App\Buyer;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use App\Traits\AdminPrivileges;
class BuyerPolicy
{
    use HandlesAuthorization, AdminPrivileges;
    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Buyer  $buyer
     * @return mixed
     */
    public function view(User $user, Buyer $buyer)
    {
        return $user->id === $buyer->id
                ? Response::allow()
                : Response::deny();         
    }
    
    /**
     * Determine whether the user can purchase.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function purchase(User $user, Buyer $buyer)
    {
        return $user->id === $buyer->id;
    }    

}
