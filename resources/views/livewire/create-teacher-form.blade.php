<form action="{{route('teachers.store')}}" method="POST" enctype="multipart/form-data">
    @livewire('create-user-fields', ['role' => 'teacher']) 
    <div class='col-12 my-2'>
        <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
    </div>
</form>