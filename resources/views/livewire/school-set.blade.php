@role('super-admin')
    <div class="">
        @livewire('help-button', ['target_id' => 'school-set-help', 'text' => "The selected school is the school that you'll be managing and viewing"])
        <form action="{{route('schools.setSchool')}}" method="POST" class="d-flex flex-column">
            <x-adminlte-select name="school_id" label="Set current school">
                @foreach ($schools as $school)
                    <option value="{{ $school->id }}" {{auth()->user()->school_id == $school->id ?'selected' : ''}}>{{$loop->iteration}}: {{ $school->name }} - {{$school->address}}</option>
                @endforeach
            </x-adminlte-select>
            @csrf
            <x-adminlte-button label="Set school" theme="primary" icon="fas fa-key" class="col-lg-3 align-self-center" type="submit"/>
        </form>
    </div>
@endrole