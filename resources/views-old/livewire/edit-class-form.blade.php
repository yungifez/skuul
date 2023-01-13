<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit classs {{$myClass->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('classes.update', $myClass->id)}}" method="POST">
            @livewire('display-validation-error')
            <x-adminlte-input name="name" label="Class Name" placeholder="Enter class name" fgroup-class="col-md-6" value="{{$myClass->name}}" enable-old-support/>
            <x-adminlte-select name="class_group_id" class="col-md-6 m-2" >
                @foreach ($classGroups as $classGroup)
                    <option value="{{$classGroup->id}}" 
                        @if (old('class_group_id'))
                            {{$classGroup->id == old('class_group_id') ? 'selected' : ''}}  
                        @else
                            {{$classGroup->id == $myClass->class_group_id ? 'selected' : ''}}  
                        @endif 
                    >{{$classGroup->name}}</option>
                @endforeach
            </x-adminlte-select>
            @csrf
            @method('PUT')
            <x-adminlte-button label="Edit" theme="primary" icon="fas fa-key" type="submit"/>
        </form>
    </div>
</div>
