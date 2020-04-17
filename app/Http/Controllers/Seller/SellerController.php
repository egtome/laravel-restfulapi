<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\ApiController;
use App\Seller;
use Illuminate\Http\Request;

class SellerController extends ApiController
{
    public function __construct() {
        parent::__construct();
        $this->middleware('scope:read-general')->only(['show']); 
        $this->middleware('can:view,seller')->only(['show']);         
    }    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->adminOrDie();
        
        $sellers = Seller::has('products')->get();
        return $this->showAll($sellers);
    }

 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Seller $seller)
    {       
        $seller = Seller::has('products')->findOrFail($seller->id);
        //Policy controller level
        //$this->authorize('view', $seller);         
        return $this->showOne($seller);
    }
    public function show2($id)
    {       
        $seller = Seller::has('products')->findOrFail($id);
        
        //Policy controller level
        $this->authorize('view', $seller);         
        return $this->showOne($seller);
    }
    
    #NAME ACCESSOR
    public function getNameAttribute($name){
        return strtoupper($name);
    }        
}
