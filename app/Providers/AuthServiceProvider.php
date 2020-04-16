<?php

namespace App\Providers;

use App\Buyer;
use App\Policies\BuyerPolicy;
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
         //'App\Buyer' => 'App\Policies\BuyerPolicy',
         Buyer::class => BuyerPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
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
