<div class="card">
    <div class="card-body">
        <form action="{{route('academic-years.update',$academicYear)}}" autocomplete="off" method="POST" class="md:w-6/12">
                <x-display-validation-errors/>
                <x-input-year id="start-year" name="start_year" label="Start year" required value="{{$academicYear->start_year}}"/>
                <x-input-year id="stop-year" name="stop_year" label="Stop year" required value="{{$academicYear->stop_year}}"/>
            @csrf
            @method('PUT')
            <div class='col-12 my-2'>
                <x-button id="start-year" label="Edit" icon="fas fa-pen" type="submit" class="w-full md:w-6/12"/>
            </div>
        </form>
    </div>
</div>
