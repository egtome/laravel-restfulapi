<?php

namespace App\Policies;

use App\User;
use App\Product;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use App\Traits\AdminPrivileges;

class ProductPolicy
{
    use HandlesAuthorization, AdminPrivileges; 
    /**
     * Determine whether the user can addCategory.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function addCategory(User $user, Product $product)
    {
        return $user->id === $product->seller->id
                ? Response::allow()
                : Response::deny(); 
    }

    /**
     * Determine whether the user can deleteCategory.
     *
     * @param  \App\User  $user
     * @param  \App\product  $product
     * @return mixed
     */
    public function deleteCategory(User $user, Product $product)
    {
        return $user->id === $product->seller->id
                ? Response::allow()
                : Response::deny(); 
    }
}
