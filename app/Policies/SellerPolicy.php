<?php

namespace App\Policies;

use App\Seller;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use App\Traits\AdminPrivileges;

class SellerPolicy
{
    use HandlesAuthorization, AdminPrivileges; 
    /**
     * Determine whether the user can view product / s.
     *
     * @param  \App\User  $user
     * @param  \App\Seller  $seller
     * @return mixed
     */
    public function view(User $user, Seller $seller)
    {
        #Detailed response example
        return $user->id === $seller->id
                ? Response::allow()
                : Response::deny();        
    }

    /**
     * Determine whether the user can sell products.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function sale(User $user, Seller $seller)
    {
        return $user->id === $seller->id
                ? Response::allow()
                : Response::deny(); 
    }
    
    /**
     * Determine whether the user can edit products.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function editProduct(User $user, Seller $seller)
    {
        return $user->id === $seller->id
                ? Response::allow()
                : Response::deny(); 
    }
    
    /**
     * Determine whether the user can create products.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function deleteProduct(User $user, Seller $seller)
    {
        return $user->id === $seller->id
                ? Response::allow()
                : Response::deny(); 
    }

}
