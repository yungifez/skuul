<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create Custom Timetable Item</h3>
    </div>
<div class="card-body">  
    <form action="{{route('custom-timetable-items.store')}}" method="POST" class="md:w-1/2">
       <x-display-validation-errors/>
        <x-input id="name" name="name" label="Custom Timetable Item Name" placeholder="Enter custom timetable item name" />
        @csrf
        <x-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-1/2"/>
    </form>
</div>
</div>