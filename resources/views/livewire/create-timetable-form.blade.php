<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create timetanle</h3>
    </div>
    <div class="card-body">
        <form action="{{route('timetables.store')}}" method="POST">
            @csrf 
            <x-adminlte-input name="name" label="Timetable name" placeholder="Enter timetable name" fgroup-class="col-md-6"/>
            <x-adminlte-textarea name="description" label="Description" placeholder="Enter description" fgroup-class="col-md-6"/>
            <x-adminlte-select name="my_class_id" label="Select class" fgroup-class="col-md-6" wire:model="class" wire:loading.attr="disabled" wire:target="class">
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            </x-adminlte-select>
            <div class='col-12 my-2'>
                <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
            </div>
        </form>
    </div>
</div>