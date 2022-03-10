<form action="{{route('semesters.update', $semester->id)}}" method="POST">
    <p>Edit {{$semester->name}} in session {{auth()->user()->school->academicYear->name()}}</p>
    @livewire('display-validation-error')
    <x-adminlte-input name="name" label="Semester Name" placeholder="Enter semester name" fgroup-class="col-md-6" value="{{$semester->name}}"/>
    @csrf
    @method('PUT')
    <div class='col-12 my-2'>
        <x-adminlte-button label="Edit" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
    </div>
</form>