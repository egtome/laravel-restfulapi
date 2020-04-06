<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\{Product,User,Transaction};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\NewTransactionEvent;

class ProductBuyerTransactionController extends ApiController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $rules = [
            'quantity' => 'required|integer|min:1'
        ];
        $this->validate($request,$rules);
        
        if($buyer->id == $product->seller_id){
            return $this->errorResponse('Buyer must be different than seller', 409);
        }
        if(!$buyer->isVerified()){
            return $this->errorResponse('Buyer must be a verified user', 409);
        }
        if(!$product->seller->isVerified()){
            return $this->errorResponse('Seller must be a verified user', 409);
        }
        if(!$product->isAvailable()){
            return $this->errorResponse('Product is not available', 409);
        }
        if($product->quantity < $request->quantity){
            return $this->errorResponse('No quantity enough', 409);
        }
        
        return DB::transaction(function() use ($request, $product, $buyer){
            $product->quantity -= $request->quantity;
            $product->save();
            
            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id
            ]);
            //Check stock and availability after transaction
            return $this->afterTransaction($transaction);
        });
        
    }
    
    private function afterTransaction($transaction){
        event(new NewTransactionEvent($transaction));
        return $this->showOne($transaction,201);
    }
    
}
