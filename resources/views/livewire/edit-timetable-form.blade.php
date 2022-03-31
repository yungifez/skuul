<form action="{{route('timetables.update',$timetable)}}" method="POST">
    @csrf 
    @method('PUT')
    <x-adminlte-input name="name" label="Timetable name" placeholder="Enter timetable name" fgroup-class="col-md-6" value="{{$timetable->name}}"/>
    <x-adminlte-textarea name="description" label="Description" placeholder="Enter description" fgroup-class="col-md-6">
        {{$timetable->description}}
    </x-adminlte-textarea>
    <x-adminlte-select name="class_id" label="Select class" fgroup-class="col-md-6" wire:model="class" wire:loading.attr="disabled" wire:target="class">
        @foreach ($classes as $item)
            <option value="{{$item['id']}}">{{$item['name']}}</option>
        @endforeach
    </x-adminlte-select>
    <x-adminlte-select name='subject_id' id="subject" label="Subject" wire:init="loadInitialSubjects" fgroup-class="col-md-6" enable-old-support wire:model="subject" >
        @isset($subjects)
            @foreach ($subjects as $subject)
                <option value="{{$subject['id']}}" {{$subject['id']}}>{{$subject['name']}}</option>
            @endforeach
        @endisset
    </x-adminlte-select>
    <div class='col-12 my-2'>
        <x-adminlte-button label="Edit" theme="primary" icon="fas fa-pen" type="submit" class="col-md-3"/>
    </div>
</form>