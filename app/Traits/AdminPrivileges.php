<?php

namespace App\Traits;

trait AdminPrivileges
{
    public function before($user, $ability)
    {
        if($user->isAdmin()){
            return true;
        }
    }  
}

