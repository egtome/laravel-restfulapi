<?php

namespace App\Http\Controllers\Seller;
use \Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Controllers\ApiController;
use App\{Seller,User,Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Access\AuthorizationException;
use App\Transformers\ProductTransformer;
class SellerProductController extends ApiController
{
    public function __construct() {
        parent::__construct();
        $this->middleware('transform.input:' . ProductTransformer::class)->only(['store','update']);
        $this->middleware('scope:manage-products')->except(['index']);
        $this->middleware('can:view,seller')->only(['index']);         
        $this->middleware('can:sale,seller')->only(['store']);         
        $this->middleware('can:edit-product,seller')->only(['update']);         
        $this->middleware('can:delete-product,seller')->only(['destroy']);         
    }    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        if(request()->user()->tokenCan('read-general') || request()->user()->tokenCan('manage-products')){
            $products = $seller->products;
            return $this->showAll($products);             
        }
        throw new AuthorizationException;
    }


    /**
     * Store seller product
     * need to use User model since Seller wont be able to create product for first time
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller, Product $product)
    {
        $rules = [
            'name' => 'required|min:5',
            'description' => 'required|min:5',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image'
        ];
        
        $this->validate($request,$rules);
        $data = $request->all();
        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['image'] = $request->image->store('');
        $data['seller_id'] = $seller->id;
        
        $product = Product::create($data);
        return $this->showOne($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $seller, Product $product)
    {
        $rules = [
            'name' => 'min:5',
            'description' => 'min:5',
            'quantity' => 'integer|min:1',
            'status' => 'in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT, 
            'image' => 'image'
        ];
        
        $this->validate($request,$rules);
        $this->checkSeller($seller, $product);

        $product->fill($request->only(['name','description','image','quantity']));
        
        if($request->has('status')){
            $product->status = $request->status;
            if($product->isAvailable() && $product->categories()->count() == 0){
                return $this->errorResponse('You cannot enable a product without categories associated', 409);
            }
        }
    
        if($request->hasFile('image')){
            Storage::delete($product->image);
            $product->image = $request->image->store('');
        }
        if($product->isClean()){
            return $this->errorResponse('Nothing to update', 422);
        }
        
        $product->save();
        return $this->showOne($product);
    }

    private function checkSeller(User $seller, Product $product){
        if($product->seller_id != $seller->id){
            throw new HttpException(422,'Product does not belong to seller');
        }        
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->checkSeller($seller, $product);
        Storage::delete($product->image);
        $product->delete();
        return $this->showOne($product);
    }
}
