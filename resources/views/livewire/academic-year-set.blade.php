<div class="my-2">
    @livewire('help-button', ['target_id' => 'academic-year-set-help', 'text' => "Select an academic year for the school"])
    <form action="{{route('academic-years.set-academic-year')}}" method="POST" class="d-flex flex-column">
        @livewire('display-validation-error')
        <x-adminlte-select name="academic_year_id" label="Set current academic-year">
            @foreach ($academicYears as $academicYear)
                <option value="{{ $academicYear->id }}"> {{ $academicYear->start_year }} - {{$academicYear->stop_year}}</option>
            @endforeach
        </x-adminlte-select>
        @csrf
        <x-adminlte-button label="Set academic-year" theme="primary" icon="fas fa-key" class="col-lg-3 align-self-center" type="submit"/>
    </form>
</div>
