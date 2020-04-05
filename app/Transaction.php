<?php

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use App\Buyer;
use App\Product;
class Transaction extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'quantity',
        'buyer_id',
        'product_id'
    ];
    
    public function buyer(){
        $this->belongsTo(Buyer::class);
    }
    public function product(){
        $this->belongsTo(Product::class);
    }
}
