<x-mail::message>
# The status of your application has changed

@switch(strtolower($status))
	@case('approved')
		Congratulations, your application has been approved. Log in to see new changes now.
		@break
	@case('application received')
		Your application has been received, further instructions would be sent to you.
		@break
	@case('under review')
		Your application is currently under review, you would be notified when a decision is made.
		@break
	@case('rejected')
		Your application was unfortunately rejected.
		@break
	@case('user action required')
		Your action is currently required, if you don't see the reason why contact the school for support.
		@break
	@default
	This email has been sent to you because your application status has changed to {{$status}}.
@endswitch

<x-mail::button url="{{route('dashboard')}}">
Log in
</x-mail::button>

@isset($reason)
  Reason/Message -  {{ $reason }} 
@endisset
<br>
{{ config('app.name') }}. This is an automated message.
</x-mail::message>
