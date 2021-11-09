@role('super-admin')
    <div class="d-flex flex-column">
        @livewire('help-button', ['target_id' => 'school-set-help', 'text' => "The selected school is the school that you'll be managing and viewing"])
        <x-adminlte-select name="school" label="Set current school">
            @foreach ($schools as $school)
                <option value="{{ $school->id }}">{{ $school->name }} - {{$school->address}}</option>
            @endforeach
        </x-adminlte-select>
        <x-adminlte-button label="Set school" theme="primary" icon="fas fa-key" class="col-lg-3 align-self-center"/>
    </div>
@endrole