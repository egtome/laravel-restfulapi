<?php

namespace App;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use App\Product;
class Category extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'description'
    ];
    
    public function products(){
        return $this->belongsToMany(Product::class);
    }
    
    #NAME MUTATOR
    public function setNameAttribute($name){
        $this->attributes['name'] = strtoupper($name);
    }
    
    #NAME ACCESSOR
    public function getNameAttribute($name){
        return strtolower($name);
    }       
}
