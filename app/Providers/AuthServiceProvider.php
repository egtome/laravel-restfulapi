<?php

namespace App\Providers;

use App\{Buyer, Seller, User, Transaction, Product};
use App\Policies\{BuyerPolicy, SellerPolicy, UserPolicy, TransactionPolicy, ProductPolicy};
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
         Buyer::class => BuyerPolicy::class,
         Seller::class => SellerPolicy::class,
         User::class => UserPolicy::class,
         Transaction::class => TransactionPolicy::class,
         Product::class => ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        Gate::define('admin-power', function($user){
            return $user->isAdmin();
        });
        
//        Gate::define('another-power', function($test){
//            return $user->isInspector();
//        });
        
        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addMinutes(30));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        Passport::enableImplicitGrant();
        
        //Scopes
        Passport::tokensCan([
            'purchase-products' => 'Create a new transaction',
            'manage-products' => 'Read, Create, Edit and Delete products',
            'manage-account' => 'Read your account data. If you are admin user, '
                                 . 'also Create and Edit your account data. '
                                 . 'Your password cannot be readed and your account cannot be deleted.',
            'read-general' => 'Grant readonly access to all sections'
        ]);
    }
}
