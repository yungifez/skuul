<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create semester in session {{auth()->user()->school->academicYear->name()}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('semesters.store')}}" method="POST">
            @livewire('display-validation-error')
            <x-adminlte-input name="name" label="Semester Name" placeholder="Enter semester name" fgroup-class="col-md-6"/>
            @csrf
            <div class='col-12 my-2'>
                <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>