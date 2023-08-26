<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit timetable</h3>
    </div>
    <div class="card-body">
        <form action="{{route('timetables.update',$timetable)}}" method="POST" class="md:w-1/2">
            @csrf 
            @method('PUT')
            <x-input id="name" name="name" label="Timetable name" placeholder="Enter timetable name"  value="{{$timetable->name}}"/>
            <x-textarea id="description" name="description" label="Description" placeholder="Enter description" >
                {{$timetable->description}}
            </x-textarea>
            <x-select id="class" name="my_class_id" label="Select class"  wire:model.live="class" wire:loading.attr="disabled" wire:target="class" disabled>
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            </x-select>
            <div class='col-12 my-2'>
                <x-button label="Edit" theme="primary" icon="fas fa-pen" type="submit" class="w-full md:w-1/2"/>
            </div>
        </form>
    </div>
</div>