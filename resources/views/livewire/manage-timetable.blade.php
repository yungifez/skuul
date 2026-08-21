<div class="card">
    <div class="card-header">
        <h4 class="card-title">Manage Timetable</h4>
        <x-timetable-status-control :timetable="$timetable" />
    </div>
    <div class="card-body">
        {{--form for creating timeSlots--}}
        <x-display-validation-errors/>
        <x-loading-spinner/>
        <!--Adds scrolling offset-->
        <div class=" relative bottom-24"id="create-timetable-record" ></div>
        <div class="md:grid grid-cols-4 gap-2" >
            <div class="flex w-full flex-col gap-2">
                <april:label for="timeslot">Time Slot</april:label>
                <april:select id="timeslot" name="timeSlot" wire:model.live="timeSlot">
                @isset($timeSlots)
                    @foreach ($timeSlots as $item)
                        <option value="{{$item['id']}}" > {{$item->name}}</option>
                    @endforeach
                    @if ($timeSlots->isEmpty())
                        <option selected>Create Time Slot first</option>
                    @endif
                @endisset

                </april:select>
                @error('timeSlot')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror
            </div>
            @if(!is_null($timeSlot))
                <form action="{{route('timetables.records.create',[$timeSlot])}}#create-timetable-record" method="POST" class="md:grid col-span-3 grid-cols-3 gap-2" >
                    @csrf
                    <div class="flex w-full flex-col gap-2">
                        <april:label for="weekday-id">Day of week</april:label>
                        <april:select id="weekday-id" name="weekday_id" wire:model.live="weekday">
                        @isset($weekdays)
                            @foreach ($weekdays as $item)
                                <option value="{{$item['id']}}"> {{$item->name}}</option>
                            @endforeach
                        @endisset

                        </april:select>
                        @error('weekday_id')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex w-full flex-col gap-2">
                        <april:label for="type">Record Type</april:label>
                        <april:select id="type" name="type" wire:model.live="type">
                        @isset($types)
                            @foreach ($types as $item)
                                <option value="{{$item}}"> {{str()->title(str()->snake($item, " "))}}</option>
                            @endforeach
                        @endisset

                        </april:select>
                        @error('type')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex w-full flex-col gap-2">
                        <april:label for="id">Subject/Custom Item</april:label>
                        <april:select id="id" name="id">
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

                        </april:select>
                        @error('id')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                    <april:button type="submit" class="w-full">
                        Attach
                    </april:button>
                </form>
            @endisset
        </div>
        <livewire:show-timetable :timetable="$timetable" :showDescription="false" :disableEmitCellInformationDetail="false"/>

        {{--Create timeslot form--}}
        <form action="{{route('time-slots.store')."#create-time-slot"}}" id="create-time-slot" method="post" class="my-3 md:grid grid-cols-3 w-full items-end gap-4 ">
            <h4 class="col-span-3 text-center text-xl">Create time slot</h4>
            <input type="hidden" name="timetable_id" value="{{$timetable->id}}">
            <april:input-group id="start_time" name="start_time" type="time" placeholder="select a start time" label="Start time" />
            <april:input-group id="stop-time" name="stop_time" type="time" placeholder="select a start time" label="Stop time" />
            @csrf

            <april:button type="submit" class="w-full">
                <x-lucide-key class="mr-2 size-4" />
                Create
            </april:button>

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
