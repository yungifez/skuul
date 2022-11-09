<x-mail::message>
# The status of your application has changed
@if ($status == 'approved')
    Congratulations, your application has been approved. Log in to see new changes now.
@else
    This email has been sent to you because your application status has changed to {{$status}}.
@endif

<x-mail::button url="{{route('dashboard')}}">
Log in
</x-mail::button>

@isset($reason)
  Reason/Message -  {{ $reason }} 
@endisset
<br>
{{ config('app.name') }}
</x-mail::message>
