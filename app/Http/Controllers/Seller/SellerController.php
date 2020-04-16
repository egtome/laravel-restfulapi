<?php

namespace App\Http\Controllers\Seller;
use App\Seller;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class SellerController extends ApiController
{
    public function __construct() {
        parent::__construct();
        $this->middleware('scope:read-general')->only(['show']);         
    }    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellers = Seller::has('products')->get();
        return $this->showAll($sellers);
    }

 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $seller = Seller::has('products')->findOrFail($id);
        return $this->showOne($seller);
    }
    
    #NAME ACCESSOR
    public function getNameAttribute($name){
        return strtoupper($name);
    }        
}
