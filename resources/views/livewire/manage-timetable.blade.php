<div class="card">
    <div class="card-header">
        <h4 class="card-title">Manage Timetable</h4>
    </div>
    <div class="card-body">
        {{--form for creating timeSlots--}}
        <x-display-validation-errors/>
        <x-loading-spinner/>
        <!--Adds scrolling offset-->
        <div class=" relative bottom-24"id="create-timetable-record" ></div>
        <div class="md:grid grid-cols-4 gap-2" >
            <x-select id="timeslot" name="timeSlot" label="Time Slot" wire:model.live="timeSlot">
                @isset($timeSlots)
                    @foreach ($timeSlots as $item)
                        <option value="{{$item['id']}}" > {{$item->name}}</option>
                    @endforeach 
                    @if ($timeSlots->isEmpty()) 
                        <option selected>Create Time Slot first</option>
                    @endif
                @endisset
            </x-adminlte-select>
            @if(!is_null($timeSlot))
                <form action="{{route('timetables.records.create',[$timeSlot])}}#create-timetable-record" method="POST" class="md:grid col-span-3 grid-cols-3 gap-2" >
                    @csrf
                    <x-select id="weekday-id" name="weekday_id" label="Day of week"  wire:model.live="weekday">
                        @isset($weekdays)
                            @foreach ($weekdays as $item)
                                <option value="{{$item['id']}}"> {{$item->name}}</option>
                            @endforeach
                        @endisset
                    </x-select>
                    <x-select id="type" name="type" label="Record Type" wire:model.live="type">
                        @isset($types)
                            @foreach ($types as $item)
                                <option value="{{$item}}"> {{str()->title(str()->snake($item, " "))}}</option>
                            @endforeach
                        @endisset
                    </x-select>
                    <x-select id="id" name="id" label="Subject/Custom Item">
                        <option  value="">Make Blank</option>
                        @isset($types)
                            @switch($type)
                                @case('subject')
                                    @isset($subjects)
                                        @foreach ($subjects as $subject)
                                            <option value="{{$subject['id']}}">{{$subject['name']}}</option>
                                        @endforeach
                                    @endisset
                                    @break
                                @case('customTimetableItem')
                                    @foreach ($customItems as $customTimetableItem)
                                        <option value="{{$customTimetableItem['id']}}">{{$customTimetableItem['name']}}</option>
                                    @endforeach
                                    @break
                                @default
                                    <option value="" disabled selected>Select a type</option>
                            @endswitch
                        @endisset
                    </x-select>
                    <x-button label="Attach" theme="primary" type="submit" class="w-full"/>
                </form>
            @endisset
        </div>
        <livewire:show-timetable :timetable="$timetable" :showDescription="false" :disableEmitCellInformationDetail="false"/>
        
        {{--Create timeslot form--}}
        <form action="{{route('time-slots.store')."#create-time-slot"}}" id="create-time-slot" method="post" class="my-3 md:grid grid-cols-3 w-full items-end gap-4 ">
            <h4 class="col-span-3 text-center text-xl">Create time slot</h4>
            <input type="hidden" name="timetable_id" value="{{$timetable->id}}">
            <x-input id="start_time" name="start_time" type="time" placeholder="select a start time" label="Start time"/>
            <x-input id="stop-time" name="stop_time" type="time" placeholder="select a start time" label="Stop time"/>
            @csrf

            <x-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="w-full"/>
        
        </form>

        <h4 class="text-center text-xl">Time Slots</h4>
        <livewire:datatable :model="App\Models\TimetableTimeSlot::Class"
        :filters="[
            ['name' => 'where' , 'arguments' => ['timetable_id' , $timetable->id]]
        ]"
        :columns="[
            ['name' => 'name'],
            ['property' => 'start_time'],
            ['property' => 'stop_time'],
            ['type' => 'delete', 'action' => 'time-slots.destroy', 'name' => 'delete']
        ]"/>
    </div>
    
    @push('scripts')
        <script>
            Livewire.on('timetableCellClicked', () => {
                console.log('hi');
                document.getElementById('create-timetable-record').scrollIntoView();
            })
        </script>
    @endpush
</div>