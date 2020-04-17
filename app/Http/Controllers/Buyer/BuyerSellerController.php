<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class BuyerSellerController extends ApiController
{
    public function __construct() {
        parent::__construct();
    }    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        $this->adminOrDie();
        
        #Laravel Eloquent Eager Loading
        //$sellers = $buyer->transactions()->with('product.seller')->get();
        #Show me sellers only, avoid duplicated sellers and also empty values...
        $sellers = $buyer->transactions()
                ->with('product.seller')
                ->get()
                ->pluck('product.seller')
                ->unique('id')
                ->values();
        return $this->showAll($sellers);        
    }
}
