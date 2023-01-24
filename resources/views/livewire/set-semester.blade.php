<div class="card">
    <form action="{{route('semesters.set-semester')}}" method="POST" class="card-body">
        <x-display-validation-errors />
        <x-select name="semester_id" id="set-semester-form" label="Change School Semester">
            @foreach ($semesters as $semester)
                <option @selected(auth()->user()->school->semester_id == $semester->id) value="{{ $semester->id }}"> {{ $semester->name }}</option>
            @endforeach
        </x-select>
        @csrf
        <div class="my-6 flex justify-center items-center">
            <x-button class="m-auto w-full lg:w-3/12" icon="fa fa-key">
                Set semester
            </x-button>
        </div>
    </form>
</div>

