<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit {{$customTimetableItem->name}}</h3>
    </div>
<div class="card-body">  
    <form action="{{route('custom-timetable-items.update', $customTimetableItem->id)}}" method="POST" class="mb-3">
        @livewire('display-validation-error')
        <x-adminlte-input name="name" label="Custom Timetable Item Name" placeholder="Enter custom timetable item name" fgroup-class="col-md-6" value="{{$customTimetableItem->name}}" enable-old-support/>
        @csrf
        @method('PUT')
        <div class="col-md-3">
            <x-adminlte-button label="Edit" theme="primary" icon="fas fa-cog" type="submit" class="col-md-6"/>
        </div>
    </form>
</div>
</div>