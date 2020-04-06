<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\User;
use App\Mail\UserMailChanged;
use Illuminate\Support\Facades\Mail;
class SendUserConfirmEmailListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = $event->user;
        #Failing Prone actions
        retry(5, function() use ($user){
            Mail::to($user)->send(new UserMailChanged($user));
        },100);
    }
}
