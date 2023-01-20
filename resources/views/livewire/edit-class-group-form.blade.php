<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit class group {{$classGroup->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('class-groups.update', $classGroup->id)}}" method="POST">
            <x-display-validation-errors />
            <x-input id="name" name="name" label="Name" placeholder="Enter class group name" class="md:w-6/12" value="{{$classGroup->name}}"/>
            @csrf
            @method('PUT')
            <x-button label="Save Changes" theme="primary" icon="fas fa-pen" type="submit"/>
        </form>
    </div>
</div>