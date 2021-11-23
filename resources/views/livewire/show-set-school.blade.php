@role('super-admin')
<div>
    <p class="font-weight-bold">You are currently on {{auth()->user()->school->name}}</p>
</div>
@endrole