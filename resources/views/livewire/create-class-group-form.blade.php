<form action="{{route('class-groups.store')}}" method="POST">
    @livewire('display-validation-error')
    @livewire('help-button', ['target_id' => 'class-group-help', 'text' => 'Class groups are used in grouping different types or levels of classes. Eg Primary, Secondary etc.'])
    <x-adminlte-input name="name" label="Class Group Name" placeholder="Enter class group name" fgroup-class="col-md-6"/>
    @csrf
    <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit"/>
</form>