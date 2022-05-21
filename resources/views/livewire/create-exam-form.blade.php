<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create exam </h3>
    </div>
    <div class="card-body">
        <form action="{{route('exams.store')}}" method="POST">
            @livewire('display-validation-error')
            <x-adminlte-input name="name" label="Exam Name" placeholder="Enter Exam name" fgroup-class="col-md-6"/>
            <x-adminlte-textarea name="description" label="Description" placeholder="Enter description" fgroup-class="col-md-6"/>
            <div class="col-md-6">
                <x-adminlte-input-date name="start_date" label="Start date" required :config="['format' => 'YYYY/MM/DD']" value="{{old('start_date')}}"/>
            </div>
            <div class="col-md-6">
                <x-adminlte-input-date name="stop_date" label="Stop date" required :config="['format' => 'YYYY/MM/DD']" value="{{old('stop_date')}}"/>
            </div>
            <x-adminlte-select name="semester_id" label="Select Semester" fgroup-class="col-md-6" wire:loading.attr="disabled" wire:target="semester">
                @foreach ($semesters as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            </x-adminlte-select>
            @csrf
            <div class='col-12 my-2'>
                <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>