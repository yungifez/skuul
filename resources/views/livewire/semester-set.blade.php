<div class="my-2">
    @livewire('help-button', ['target_id' => 'semester-set-help', 'text' => "Select (set) current school semester"])
    <form action="{{route('semesters.set-semester')}}" method="POST" class="d-flex flex-column">
        @livewire('display-validation-error')
        <x-adminlte-select name="semester_id" label="Set current semester">
            @foreach ($semesters as $semester)
                <option value="{{ $semester->id }}" {{$semester->id == auth()->user()->school->semester_id ? 'selected' : ''}}> {{ $semester->name}}</option>
            @endforeach
        </x-adminlte-select>
        @csrf
        <x-adminlte-button label="Set semester" theme="primary" icon="fas fa-key" class="col-lg-3 align-self-center" type="submit"/>
    </form>
</div>
