@can('set academic year')
<div class="card">
    <div class="my-2 card-body">
        <form action="{{route('academic-years.set-academic-year')}}" method="POST" class="grid">
            <div>
                <x=display-validation-errors/>
            </div>
            <x-select id="name" name="academic_year_id" label="Set current academic-year" group-class="w-full">
                @foreach ($academicYears as $academicYear)
                    <option value="{{ $academicYear->id }}" {{$academicYear->id == auth()->user()->school->academic_year_id ? 'selected' : ''}}> {{ $academicYear->name()}}</option>
                @endforeach
            </x-select>
            @csrf
            <x-button label="Set academic-year" theme="primary" icon="fas fa-key" class="m-auto w-full lg:w-3/12" type="submit"/>
        </form>
    </div>
</div>
@endif


