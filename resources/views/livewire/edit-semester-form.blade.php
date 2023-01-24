<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit {{$semester->name}} in session {{auth()->user()->school->academicYear->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('semesters.update', $semester->id)}}" method="POST" class="md:w-6/12">
            <x-display-validation-errors/>
            <x-input id="name" name="name" label="Semester Name" placeholder="Enter semester name" value="{{$semester->name}}"/>
            @csrf
            @method('PUT')
            <x-button label="Edit" theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-1/2"/>
        </form>
    </div>
</div>