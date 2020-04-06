<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Product;
class CheckProductAvailabilityListener
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $product_id = $event->transaction->product_id;
        $this->checkAvailability($product_id);
    }
    
    protected function checkAvailability($product_id){
        $product = new Product;
        $p = $product->find($product_id);
        if($p->quantity == 0 && $p->status == Product::AVAILABLE_PRODUCT){
            $p->status = Product::UNAVAILABLE_PRODUCT;
            $p->save();
        }
    }
}
