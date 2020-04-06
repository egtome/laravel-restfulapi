@component('mail::message')
# Welcome, {{$user->name}}

Please verify your email address, use the button below:

@component('mail::button', ['url' => route('verify',$user->verification_token)])
Verify Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
