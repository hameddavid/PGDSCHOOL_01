@component('mail::message')
# Don't Share With Anyone

Enter this digits to reset your password
@component('mail::panel')
{{$token}}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
