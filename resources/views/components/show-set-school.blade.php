@if (auth()->user()->isPlatformAdmin() || auth()->user()->schoolMemberships()->active()->count() > 1)
<div>
    <p class="text-gray-600 dark:text-gray-200 text-xs md:text-base my-2">
        @if (current_school() !== null)
            You are currently on {{current_school()->name}} - {{current_school()->address}}
        @else
            Please set a school
        @endif
    </p>
</div>
@endif
