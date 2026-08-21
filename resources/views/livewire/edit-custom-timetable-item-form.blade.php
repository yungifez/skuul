<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit {{$customTimetableItem->name}}</h3>
    </div>
<div class="card-body">
    <form action="{{route('custom-timetable-items.update', $customTimetableItem->id)}}" method="POST" class="md:w-1/2">
        <x-display-validation-errors/>
        <april:input-group id="name" name="name" label="Custom Timetable Item Name" placeholder="Enter custom timetable item name" value="{{$customTimetableItem->name}}" />
        @csrf
        @method('PUT')
        <april:button type="submit" class="w-full md:w-1/2">
            <x-lucide-settings class="mr-2 size-4" />
            Edit
        </april:button>
    </form>
</div>
</div>