<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit grade {{$grade->name}}</h3>
    </div>
    <div class="card-body">
        <form action="{{route('grade-systems.update' ,$grade->id)}}" method="post">
            <x-adminlte-input name="name" label="Name" placeholder="Grade name eg A1" fgroup-class="col-md-6" value="{{$grade->name}}"/>
            <x-adminlte-input name="remark" label="Remark" placeholder="Grade remark eg Excellent" fgroup-class="col-md-6" value="{{$grade->remark}}"/>
            <x-adminlte-input type="number" name="grade_from" label="From" placeholder="Grade from eg 10" fgroup-class="col-md-6" value="{{$grade->grade_from}}"/>
            <x-adminlte-input type="number" name="grade_till" label="Till" placeholder="grade till eg 20" fgroup-class="col-md-6" value="{{$grade->grade_till}}"/>
            <x-adminlte-select name="class_group_id" fgroup-class="col-md-6 mx-1" label="Class Group" wire:model="classGroup">
                @foreach ($classGroups as $classGroup)
                    <option value="{{$classGroup->id}}">{{$classGroup->name}}</option>
                @endforeach
            </x-adminlte-select>
            <div class='col-12 my-2'>
                <x-adminlte-button label="Edit" theme="primary" icon="fas fa-pen" type="submit" class="col-md-3"/>
            </div>
            @csrf
            @method("PUT")
        </form>
    </div>
</div>