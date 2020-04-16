<?php

namespace App\Http\Controllers\Category;

use App\Category;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Transformers\CategoryTransformer;
class CategoryController extends ApiController
{
    public function __construct() 
    {
        $this->middleware('client.credentials')->only(['index','show']);
        $this->middleware('auth:api')->except(['index','show']);
        $this->middleware('transform.input:' . CategoryTransformer::class)->only(['store','update']);
        $this->middleware('scope:read-general')->only(['index','show']); 
    }            
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return $this->showAll($categories);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Category $category)
    {
        $rules = [
            'name' => 'required|min:5',
            'description' => 'required|min:5',
        ];
        
        $this->validate($request,$rules);
        $created = Category::create($request->all());
        return $this->showOne($created,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->showOne($category);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $rules = [
            'name' => 'min:5',
            'description' => 'min:5',
        ];
        $this->validate($request,$rules);

        $category->fill($request->only(['name','description']));
        if($category->isClean()){
            return $this->errorResponse('Nothing to update', 422);
        }
        $category->save();
        return $this->showOne($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->showOne($category);
    }
}
