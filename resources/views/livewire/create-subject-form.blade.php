<form action="{{route('subjects.store')}}" method="POST">
    @if ($errors->any())
        <div class="alert alert-danger col-12">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <x-adminlte-input name="name" label="Subject Name" placeholder="Enter subject name" fgroup-class="col-md-6"/>
    <x-adminlte-input name="short_name" label="Subject short Name" placeholder="Enter subject short name" fgroup-class="col-md-6"/>
    <x-adminlte-select2 id="my_class" name="my_class_id[]" label="Add subject to classes" :config='["allowClear" => true,"placeholder" => "You can select multiple classes...",]' multiple fgroup-class="col-md-6" enable-old-support>
        @foreach ($classes as $class)
            <option value="{{$class->id}}">{{$class->name}}</option>
        @endforeach
    </x-adminlte-select2>
    @csrf
    <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit"/>
    @section('plugins.Select2', true)
</form>