<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit timetable</h3>
    </div>
    <div class="card-body">
        <form action="{{route('timetables.update',$timetable)}}" method="POST">
            @csrf 
            @method('PUT')
            <x-adminlte-input name="name" label="Timetable name" placeholder="Enter timetable name" fgroup-class="col-md-6" value="{{$timetable->name}}"/>
            <x-adminlte-textarea name="description" label="Description" placeholder="Enter description" fgroup-class="col-md-6">
                {{$timetable->description}}
            </x-adminlte-textarea>
            <x-adminlte-select name="my_class_id" label="Select class" fgroup-class="col-md-6" wire:model="class" wire:loading.attr="disabled" wire:target="class" disabled>
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            </x-adminlte-select>
            <div class='col-12 my-2'>
                <x-adminlte-button label="Edit" theme="primary" icon="fas fa-pen" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>