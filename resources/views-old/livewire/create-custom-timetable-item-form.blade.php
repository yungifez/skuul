<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create Custom Timetable Item</h3>
    </div>
<div class="card-body">  
    <form action="{{route('custom-timetable-items.store')}}" method="POST" class="mb-3">
        @livewire('display-validation-error')
        <x-adminlte-input name="name" label="Custom Timetable Item Name" placeholder="Enter custom timetable item name" fgroup-class="col-md-6" enable-old-support/>
        @csrf
        <div class="col-md-3">
            <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-6"/>
        </div>
    </form>
    @livewire('help-button', ['target_id' => 'class-group-help', 'text' => 'Custom timetable items are items that can be added to timetables other than subjects eg lunch breaks etc.'])
</div>
</div>