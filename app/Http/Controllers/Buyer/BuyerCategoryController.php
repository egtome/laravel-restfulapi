<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class BuyerCategoryController extends ApiController
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
        #Laravel Eloquent Eager Loading
        #Show me categories only. Use collapse() to show a unique collection of collections
        #Avoid repeated values and empty values
        $categories = $buyer->transactions()
                ->with('product.categories')
                ->get()
                ->pluck('product.categories')
                ->collapse()
                ->unique('id')
                ->values();
        return $this->showAll($categories);        
    }
}