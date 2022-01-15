<div>
    <form action="{{route('subjects.update', $subject->id)}}" method="POST">
        <x-adminlte-input name="name" label="Subject Name" placeholder="Enter subject name" fgroup-class="col-md-6" value="{{$subject->name}}"/>
        <x-adminlte-input name="short_name" label="Subject Short Name" placeholder="Enter subject short name" fgroup-class="col-md-6" value="{{$subject->short_name}}"/>
        @csrf
        @method('PUT')
        <x-adminlte-select2 id="my_class" name="my_class_id[]" label="Add or remove subject from classes" :config='["allowClear" => true,"placeholder" => "You can select multiple classes...",]' multiple fgroup-class="col-md-6" enable-old-support>
            @foreach ($classes as $class)
                <option value="{{$class->id}}" {{$class->selected ? 'selected' : ''}}>{{$class->name}}</option>
            @endforeach
        </x-adminlte-select2>
        {{-- <div>
            <label>Remove subject from class</label>
            @foreach ($classesOfferingSubject as $class)
                <x-adminlte-input id="remove-class-{{$loop->iteration}}" type="checkbox" name="remove_class_id[]" value="{{$class->id}}" fgroup-class="col-md-3" igroup-size="sm">
                    <x-slot name="prependSlot">
                       <label for="remove-class-{{$loop->iteration}}">{{$class->name}}</label>
                    </x-slot>
                </x-adminlte-input>
            @endforeach
        </div> --}}
        <x-adminlte-button label="Edit" theme="primary" icon="fas fa-key" type="submit"/>
    </form>
</div>
