<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit exam {{$exam->id}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('exams.update',$exam)}}" autocomplete="off" method="POST">
            @livewire('display-validation-error')
            <x-adminlte-input name="name" label="Exam Name" placeholder="Enter semester name" fgroup-class="col-md-6" value="{{$exam->name}}"/>
            <x-adminlte-textarea name="description" label="Description" placeholder="Enter description" fgroup-class="col-md-6">{{$exam->description}}</x-adminlte-textarea>
        
            <div class="col-md-6">
                <x-adminlte-input-date name="start_date" label="Start date" required  :config="['format' => 'YYYY/MM/DD']" value="{{$exam->start_date}}"></x-adminlte-input>
            </div>
            <div class="col-md-6">
                <x-adminlte-input-date name="stop_date" label="Stop date" required  :config="['format' => 'YYYY/MM/DD']" value="{{$exam->stop_date}}"></x-adminlte-input>
            </div>
            <x-adminlte-select name="semester_id" label="Select Semester" fgroup-class="col-md-6" wire:loading.attr="disabled" wire:target="semester">
                @foreach ($semesters as $semester)
                    <option value="{{$semester['id']}}" {{$semester->id == $exam->semester_id ? 'selected': ''}}> {{$semester['name']}}</option>
                @endforeach
            </x-adminlte-select>
            @csrf
            @method('PUT')
            <div class='col-12 my-2'>
                <x-adminlte-button label="Edit" theme="primary" icon="fas fa-pen" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>
