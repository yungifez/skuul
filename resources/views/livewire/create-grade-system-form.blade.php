<div class="card">
    <div class="card-header">
        <h3 class="card-title"Create grade></h3>
    </div>
    <div class="card-body">
        <form action="{{route('grade-systems.store')}}" method="post">
            <x-adminlte-input name="name" label="Name" placeholder="Grade name eg A1" fgroup-class="col-md-6" enable-old-support/>
            <x-adminlte-input name="remark" label="Remark" placeholder="Grade remark eg Excellent" fgroup-class="col-md-6" enable-old-support/>
            <x-adminlte-input type="number" name="grade_from" label="From" placeholder="Grade from eg 10" fgroup-class="col-md-6" enable-old-support/>
            <x-adminlte-input type="number" name="grade_till" label="Till" placeholder="grade till eg 20" fgroup-class="col-md-6" enable-old-support/>
            <x-adminlte-select name="class_group_id" fgroup-class="col-md-6 mx-1" label="Class Group" enable-old-suppport>
                @foreach ($classGroups as $classGroup)
                    <option value="{{$classGroup->id}}">{{$classGroup->name}}</option>
                @endforeach
            </x-adminlte-select>
            <div class='col-12 my-2'>
                <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
            </div>
            @csrf
        </form>
    </div>
</div>