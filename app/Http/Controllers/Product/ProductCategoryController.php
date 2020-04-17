<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\{Product,Category};
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    public function __construct() 
    {
        //parent::__construct();
        $this->middleware('client.credentials')->only(['index']);
        $this->middleware('auth:api')->except(['index']);
        $this->middleware('scope:manage-products')->except(['index']);  
        $this->middleware('scope:read-general')->only(['index']);        
        $this->middleware('can:add-category,product')->only(['update']);        
        $this->middleware('can:delete-category,product')->only(['destroy']);        
    }     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $categories = $product->categories;
        return $this->showAll($categories); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product, Category $category)
    {
        //attach (add one), sync (add one and delete all previous), syncWithoutDetaching (add new one, keep previous and AVOID duplicates)
        $product->categories()->syncWithoutDetaching([$category->id]);
        return $this->showAll($product->categories);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Category $category)
    {
        //$r = $product->categories()->find([$category->id,$product->id])->count();
        //dd($r);
        //$product->categories()->delete([$category->id]);
       
        if(!$product->categories()->find($category->id)){
            return $this->errorResponse('Category does not exist for provided product', 404);
        }
        $product->categories()->detach($category->id);
        return $this->showAll($product->categories);
    }
}
