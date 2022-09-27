<div class="card">
    <div class="card-body">
        <form action="{{route('academic-years.update',$academicYear)}}" autocomplete="off" method="POST">
            @livewire('display-validation-error')
            <div class="col-md-6">
                <x-adminlte-input-date name="start_year" label="Start year" required  :config="['format' => 'YYYY']" value="{{$academicYear->start_year}}"></x-adminlte-input>
            </div>
            <div class="col-md-6">
                <x-adminlte-input-date name="stop_year" label="Stop year" required  :config="['format' => 'YYYY']" value="{{$academicYear->stop_year}}"></x-adminlte-input>
            </div>
            @csrf
            @method('PUT')
            <div class='col-12 my-2'>
                <x-adminlte-button label="Edit" theme="primary" icon="fas fa-pen" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>
