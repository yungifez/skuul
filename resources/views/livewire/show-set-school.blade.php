@role('super-admin')
<div>
    <p class="text-muted">
        @if (auth()->user()->school != null)
            You are currently on {{auth()->user()->school->name}} - {{auth()->user()->school->address}}
        @else
            Please set a school
        @endif
    </p>
</div>
@endrole