<form action="{{route('timetables.store')}}" method="POST">
    @csrf 
    <x-adminlte-input name="name" label="Timetable name" placeholder="Enter timetable name" fgroup-class="col-md-6"/>
    <x-adminlte-select name="class_id" label="Select class" fgroup-class="col-md-6" wire:model="class" wire:loading.attr="disabled" wire:target="class">
        @foreach ($classes as $item)
            <option value="{{$item['id']}}">{{$item['name']}}</option>
        @endforeach
    </x-adminlte-select>
    <x-adminlte-select name='subject_id' id="subject" label="Subject" wire:init="loadInitialSubjects" fgroup-class="col-md-6" enable-old-support >
        @isset($subjects)
            @foreach ($subjects as $subject)
                <option value="{{$subject['id']}}">{{$subject['name']}}</option>
            @endforeach
        @endisset
    </x-adminlte-select>
    <div class='col-12 my-2'>
        <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
    </div>
</form>