<?php

namespace App;
use App\Transformers\TransactionTransformer;
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use App\Buyer;
use App\Product;
class Transaction extends Model
{
    use SoftDeletes;
    public $transformer = TransactionTransformer::class;   
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'quantity',
        'buyer_id',
        'product_id'
    ];
    
    public function buyer(){
        return $this->belongsTo(Buyer::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
