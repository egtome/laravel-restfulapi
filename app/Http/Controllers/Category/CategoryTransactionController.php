<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class CategoryTransactionController extends ApiController
{
    public function __construct() {
        parent::__construct();
        $this->middleware('scope:read-general')->only(['index']);        
    }    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        $this->adminOrDie();
        
        $transactions = $category->products()
                ->whereHas('transactions')
                ->with('transactions')
                ->get()
                ->pluck('transactions')
                ->collapse();
        return $this->showAll($transactions); 
    }
}