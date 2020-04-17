<?php

namespace App\Http\Controllers\Buyer;
use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class BuyerController extends ApiController
{
    public function __construct() {
        parent::__construct();
        $this->middleware('scope:read-general')->only(['show']);  
        $this->middleware('can:view,buyer')->only(['show']);  
    }    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->adminOrDie();
        
        $buyers = Buyer::has('transactions')->get();
        return $this->showAll($buyers);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Buyer $buyer)
    {
        //Policy
        //$this->authorize('view', $buyer);  
        
        $buyer = Buyer::has('transactions')->findOrFail($buyer->id);
        return $this->showOne($buyer);
    }
}
