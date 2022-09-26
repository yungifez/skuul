<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit subject {{$subject->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('subjects.update', $subject->id)}}" method="POST">  
            @livewire('display-validation-error')
            <x-adminlte-input name="name" label="Subject Name" placeholder="Enter subject name" fgroup-class="col-md-6" value="{{$subject->name}}"/>
            <x-adminlte-input name="short_name" label="Subject Short Name" placeholder="Enter subject short name" fgroup-class="col-md-6" value="{{$subject->short_name}}"/>
            <x-adminlte-select2 id="teacher_id" name="teachers[]" label="Add teachers to class" :config='["placeholder" => "You can select multiple teachers...","allowClear" => true]' fgroup-class="col-md-6" enable-old-support multiple>
                @foreach ($teachers as $teacher)
                    <option value="{{$teacher->id}}" {{in_array($teacher->id,$assignedTeachersId) ? 'selected' : ''}}>{{$teacher->name}}</option>
                @endforeach
            </x-adminlte-select2>
            @csrf
            @method('PUT')
            <x-adminlte-button label="Edit" theme="primary" icon="fas fa-key" type="submit"/>
        </form>
    </div>
</div>
