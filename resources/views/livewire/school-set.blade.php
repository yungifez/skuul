@role('super-admin')
    <div class="my-4 card">
        <div class="card-body">
             
            <form action="{{route('schools.setSchool')}}" method="POST" class="d-flex flex-column mx-auto mb-2">
                <x-adminlte-select name="school_id" label="Set working school branch" class="my-2">
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}" {{auth()->user()->school_id == $school->id ?'selected' : ''}}>{{$loop->iteration}}: {{ $school->name }} - {{$school->address}}</option>
                    @endforeach
                </x-adminlte-select>
                @csrf
                <x-adminlte-button label="Set school" theme="primary" icon="fas fa-key" class="col-lg-3 align-self-center" type="submit"/>
            </form>
            @livewire('help-button', ['target_id' => 'school-set-help', 'text' => "The selected school is the school that you'll be managing and viewing"])
        </div>
    </div>
@endrole