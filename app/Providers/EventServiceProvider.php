<?php

namespace App\Providers;

use App\Events\NewTransactionEvent;
use App\Listeners\CheckProductAvailabilityListener;
use App\Events\UserCreatedEvent;
use App\Listeners\SendUserWelcomeEmailListener;
use App\Events\ConfirmEmailEvent;
use App\Listeners\SendUserConfirmEmailListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        NewTransactionEvent::class => [
            CheckProductAvailabilityListener::class,
        ],
        UserCreatedEvent::class => [
            SendUserWelcomeEmailListener::class,
        ],
        ConfirmEmailEvent::class => [
            SendUserConfirmEmailListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
