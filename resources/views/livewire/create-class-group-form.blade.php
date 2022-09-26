<div class="card">
        <div class="card-header">
            <h3 class="card-title">Create Class Group</h3>
        </div>
    <div class="card-body">  
        <form action="{{route('class-groups.store')}}" method="POST" class="mb-3">
            @livewire('display-validation-error')
            <x-adminlte-input name="name" label="Class Group Name" placeholder="Enter class group name" fgroup-class="col-md-6"/>
            @csrf
            <div class="col-md-3">
                <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-6"/>
            </div>
        </form>
        @livewire('help-button', ['target_id' => 'class-group-help', 'text' => 'Class groups are used in grouping different types or levels of classes. Eg Primary, Secondary etc.'])
    </div>
</div>