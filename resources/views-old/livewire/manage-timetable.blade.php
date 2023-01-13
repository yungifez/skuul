<div class="card">
    <div class="card-header">
        <h4 class="card-title">Manage Timetable</h4>
    </div>
    <div class="card-body">
        {{--form for creating timeSlots--}}
        <div class="row mb-3" id="create-timetable-record">
            @livewire('display-validation-error')
            <x-adminlte-select name="timeSlot" label="Time Slot"  fgroup-class="col-md-3" enable-old-support wire:model="timeSlot">
                @isset($timeSlots)
                    @foreach ($timeSlots as $item)
                        <option value="{{$item['id']}}" > {{$item->name}}</option>
                    @endforeach
                @endisset
            </x-adminlte-select>
            @if(!is_null($timeSlot))
                <form action="{{route('timetables.records.create',[$timetable->id,$timeSlot])}}" method="POST" class="row col-md-9" >
                    @csrf
                    <x-adminlte-select name="weekday_id" label="Day of week"  fgroup-class="col-md-4" enable-old-support wire:model="weekday">
                        @isset($weekdays)
                            @foreach ($weekdays as $item)
                                <option value="{{$item['id']}}"> {{$item->name}}</option>
                            @endforeach
                        @endisset
                    </x-adminlte-select>
                    <x-adminlte-select name="type" label="Timetable Item"  fgroup-class="col-md-4" enable-old-support wire:model="type">
                        @isset($types)
                            @foreach ($types as $item)
                                <option value="{{$item}}"> {{str()->title(str()->snake($item, " "))}}</option>
                            @endforeach
                        @endisset
                    </x-adminlte-select>
                    <x-adminlte-select name="id" label="Subject/Custom Item"  fgroup-class="col-md-4" enable-old-support>
                        <option value=""></option>
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
                    </x-adminlte-select>
                    <div class="col-12 row justify-content-end">
                        <x-adminlte-button label="Attach" theme="primary" type="submit" class="col-md-3"/>
                    </div>
                </form>
            @endisset
        </div>

        {{-- loading spinner --}}
        <div class="d-flex justify-content-center">
            <div wire:loading class="spinner-border" role="status">
                <p class="sr-only">Loading.....</p>
            </div>
        </div>
        @livewire("show-timetable", ['timetable' => $timetable, 'showDescription' => false, 'disableEmitCellInformationDetail' => false ], key('show-timetable'))
        
        {{--Create timeslot form--}}
        <form action="{{route('time-slots.store',$timetable->id)."#create-time-slot"}}" id="create-time-slot" method="post" class="col-12">
            <div class="row">
                <h4 class="col-12 text-center">Create time slot</h4>
                <x-adminlte-input name="start_time" type="time" fgroup-class="col-md-6" placeholder="select a start time" label="Start time"/>
                <x-adminlte-input name="stop_time" type="time" fgroup-class="col-md-6" placeholder="select a start time" label="Stop time"/>
                @csrf
                <div class='col-12 my-2'>
                    <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
                </div>
            </div>
        </form>

        <div class="my-3">
            <div class="table-responsive">
                <style>
                    #time-slot-list-table tr td,  #time-slot-list-table tr th {
                        vertical-align: middle;
                        text-align: center;
                    }
                </style>
                <table id="time-slot-list-table" style="width:100%" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width:%">
                                S/N
                            </th>
                            <th style="width:%">
                                Time interval
                            </th>
                            <th style="width:%">
                                
                            </th>
                        </tr>
                    </thead>
                    @foreach($timeSlots as $timeSlot)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{ $timeSlot->name}}</td>
                            <td>
                                @livewire('delete-modal', ['modal_id' => "timeslot-$timeSlot->id" ,"action" => route('time-slots.destroy',[$timetable->id, $timeSlot->id])."#time-slot-list-table", 'item_name' => "timeslot $timeSlot->start_time - $timeSlot->stop_time", 'button_class' => ''], key($timeSlot->id))
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    @push('js')
        <script>
            Livewire.on('timetableCellClicked', () => {
                document.getElementById('create-timetable-record').scrollIntoView();
            })
        </script>
    @endpush
</div>