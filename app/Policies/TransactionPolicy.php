<?php

namespace App\Policies;

use App\Transaction;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use App\Traits\AdminPrivileges;

class TransactionPolicy
{
    use HandlesAuthorization, AdminPrivileges;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Transaction  $transaction
     * @return mixed
     */
    public function view(User $user, Transaction $transaction)
    {
        return ($user->id === $transaction->buyer->id || $user->id === $transaction->product->seller->id)
                ? Response::allow()
                : Response::deny(); 
    }
}
