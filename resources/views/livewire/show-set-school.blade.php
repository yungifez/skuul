@role('super-admin')
<div>
    @if (auth()->user()->school != null)
        <p class="font-weight-bold">You are currently on {{auth()->user()->school->name}} -- {{auth()->user()->school->address}}</p>
    @else
        <p class="font-weight-bold">Please set a school</p>
    @endif
    
</div>
@endrole