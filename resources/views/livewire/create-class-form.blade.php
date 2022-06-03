<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create Class</h3>
    </div>
    <div class="card-body">
        <form action="{{route('classes.store')}}" method="POST">
            @livewire('display-validation-error')
            <x-adminlte-input name="name" label="Class Name" placeholder="Enter class name" fgroup-class="col-md-6"/>
            <x-adminlte-select name="class_group_id" fgroup-class="col-md-6" label="Class Group">
                @foreach ($classGroups as $classGroup)
                    <option value="{{$classGroup->id}}">{{$classGroup->name}}</option>
                @endforeach
            </x-adminlte-select>
            @csrf
            <div class="col-md-3">
                <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-12" co/>
            </div>
        </form>
    </div>
</div>