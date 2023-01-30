<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit {{$semester->name}} in session {{auth()->user()->school->academicYear->name()}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('semesters.update', $semester->id)}}" method="POST">
            @livewire('display-validation-error')
            <x-adminlte-input name="name" label="Semester Name" placeholder="Enter semester name" fgroup-class="col-md-6" value="{{$semester->name}}"/>
            @csrf
            @method('PUT')
            <div class='col-12 my-2'>
                <x-adminlte-button label="Edit" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>