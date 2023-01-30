<div class="card">
    <form action="{{route('schools.setSchool')}}" method="POST" class="card-body">
        <x-display-validation-errors />
        <x-select name="school_id" id="set-school-form" label="Set working school branch">
            @foreach ($schools as $school)
                <option @selected(auth()->user()->school_id == $school->id) value="{{ $school->id }}" @selected(auth()->user()->school_id == $school->id)> {{ $school->name }} - {{$school->address}}</option>
            @endforeach
        </x-select>
        @csrf
        <div class="my-6 flex justify-center items-center">
            <x-button class="m-auto w-full lg:w-3/12" icon="fa fa-key">
                Set School
            </x-button>
        </div>
    </form>
</div>
