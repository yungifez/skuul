<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create semester in session {{auth()->user()->school->academicYear->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('semesters.store')}}" method="POST" class="md:w-1/2">
            <x-display-validation-errors/>
            <x-input id="name" name="name" label="Semester Name" placeholder="Enter semester name"/>
            @csrf
            <div class='col-12 my-2'>
                <x-button label="Create" icon="fas fa-key" type="submit" class="w-full md:w-1/2"/>
            </div>
        </form>
    </div>
</div>