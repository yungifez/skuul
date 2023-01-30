<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create subject</h3>
    </div>
    <div class="card-body">
        <form action="{{route('subjects.store')}}" method="POST">
            @livewire('display-validation-error')
            <x-adminlte-input name="name" label="Subject Name" placeholder="Enter subject name" fgroup-class="col-md-6"/>
            <x-adminlte-input name="short_name" label="Subject short Name" placeholder="Enter subject short name" fgroup-class="col-md-6"/>
            <x-adminlte-select2 id="my_class" name="my_class_id" label="Add subject to class" :config='["placeholder" => "Select a class...",]' fgroup-class="col-md-6" enable-old-support>
                @foreach ($classes as $class)
                    <option value="{{$class->id}}">{{$class->name}}</option>
                @endforeach
            </x-adminlte-select2>
            <x-adminlte-select2 id="teacher_id" name="teachers[]" label="Add teachers to subject" :config='["placeholder" => "You can select multiple teachers...","allowClear" => true]' fgroup-class="col-md-6" enable-old-support multiple>
                @foreach ($teachers as $teacher)
                    <option value="{{$teacher->id}}">{{$teacher->name}}</option>
                @endforeach
            </x-adminlte-select2>
            @csrf
            <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit"/>
            @section('plugins.Select2', true)
        </form>
    </div>
</div>