@role('super-admin')
<div>
    <p class="text-gray-600 dark:text-gray-200 text-xs md:text-base my-2">
        @if (auth()->user()->school != null)
            You are currently on {{auth()->user()->school->name}} - {{auth()->user()->school->address}}
        @else
            Please set a school
        @endif
    </p>
</div>
@endrole