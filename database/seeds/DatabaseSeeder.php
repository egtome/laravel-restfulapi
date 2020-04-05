<?php

use App\User;
use App\Category;
use App\Product;
use App\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        //Delete all data in tables before running
        User::truncate();
        Category::truncate();
        Product::truncate();
        Transaction::truncate();
        DB::table('category_product')->truncate();
        
        factory(User::class,100)->create();
        factory(Category::class,50)->create();
        
        factory(Product::class,100)->create()->each(
            function($product){
                $categories = Category::all()->random(rand(1,3))->pluck('id');
                $product->categories()->attach($categories);
            });
        
        factory(Transaction::class,100)->create();
    }
}
