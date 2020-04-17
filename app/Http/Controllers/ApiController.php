<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;
use App\Traits\ApiResponser;
class ApiController extends Controller
{
    
    use ApiResponser;
    public function __construct() {
        $this->middleware('auth:api');
    }
    
    protected function adminOrDie(){
        if(Gate::denies('admin-power')){
            throw new AuthorizationException('Admin privileges needed.');
        }
        return true;
    }    
}
