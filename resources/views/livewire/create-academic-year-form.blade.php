<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create Academic Year</h3>
    </div>
    <div class="card-body">
        <form action="{{route('academic-years.store')}}" autocomplete="off" method="POST">
            @livewire('display-validation-error')
            <div class="col-md-6">
                <x-adminlte-input-date name="start_year" label="Start year" required  :config="['format' => 'YYYY']" value="{{old('start_year')}}"/>
            </div>
            <div class="col-md-6">
                <x-adminlte-input-date name="stop_year" label="Stop year" required  :config="['format' => 'YYYY']" value="{{old('stop_year')}}"/>
            </div>
            @csrf
            <div class='col-12 my-2'>
                <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>
