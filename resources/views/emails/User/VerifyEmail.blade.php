@component('mail::message')
# Welcome

Enter the digits below in the form provided on our WebSite
@component('mail::panel')
{{$token}}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
