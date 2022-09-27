<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create School</h3>
    </div>
    <div class="card-body">
        <form action="{{route('schools.store')}}" method="POST" >
            @livewire('display-validation-error')
            <x-adminlte-input name="name" placeholder="Enter name of school" label="School Name" enable-old-support fgroup-class="col-md-6 mb-3"/>
            <x-adminlte-textarea name="address" placeholder="Enter school branch address" label="School Address" enable-old-support fgroup-class="col-md-6 my-3"/>
            <x-adminlte-input name="initials" placeholder="Enter school initials" label="School initials" enable-old-support fgroup-class="col-md-6 my-3"/>    
            @csrf
            <x-adminlte-button label="  Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
        </form>
    </div>
</div>