<div class="card">
    <div class="card-header">
        <h4 class="card-title">Manage Timetable</h4>
    </div>
    <div class="card-body">
        {{--Create timeslot form--}}
        <form action="{{route('time-slots.store',$timetable->id)}}" method="post" class="col-12">
            <div class="row">
                <x-adminlte-input name="start_time" type="time" fgroup-class="col-md-6" placeholder="select a start time" label="Start time"/>
                <x-adminlte-input name="stop_time" type="time" fgroup-class="col-md-6" placeholder="select a start time" label="Stop time"/>
                @csrf
                <div class='col-12 my-2'>
                    <x-adminlte-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="col-md-3"/>
                </div>
            </div>
        </form>
        
        <div class="table-responsive my-3">
            @livewire('display-validation-error')
            <table class="table table-bordered overflow-auto" style="overflow-x: scroll"> 
                <thead>
                    <tr> 
                        <th scope="col" class="col-2">Time slots &#8594<br>Weekdays &#8595 
                        </th>
                        {{--table heading which disp;lays all the time slots--}}
                        @foreach ($timeSlots as $timeSlot)
                        <th scope="col" >
                            <p class="text-center">{{$timeSlot->start_time}} - {{$timeSlot->stop_time}}</p>
                            <div class=" col-12 d-flex justify-content-center">
                                {{--delete time slot modal--}}
                                @livewire('delete-modal', ['modal_id' => "timeslot-$timeSlot->id" ,"action" => route('time-slots.destroy',[$timetable->id, $timeSlot->id]), 'item_name' => "timeslot $timeSlot->start_time - $timeSlot->stop_time", 'button_class' => ''])
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                {{--creates a row for each day of the week--}}
                @foreach ($weekdays as $weekday)
                    <tr> 
                        <td scope="col" class="col-2">{{$weekday->name}}</th>
                        {{--displays the time slots for each day of the week--}}
                        @foreach ($timeSlots as $timeSlot)
                        @php
                            $pivot = $timeSlot->weekdays->find($weekday->id)?->timetableRecord;
                        @endphp
                        <td scope="col">
                            {{--FORM FOR Displaying for subjects--}}
                            <form action="{{route('timetables.records.create',[$timetable->id,$timeSlot->id])}}" method="POST" > 
                                @csrf
                                <input type="hidden" name="weekday_id" value="{{$weekday->id}}">
                                <input type="hidden" name="type" value="subject">
                                <x-adminlte-select name='id' id="subject" disable-feedback label="Subjects">
                                    <option value=""></option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{$subject->id}}"
                                            @selected(!is_null($pivot) && $pivot->timetable_recordable_type == "subject" && $pivot->timetable_recordable_id == $subject->id)
                                            >
                                            {{$subject->name}}
                                        </option>
                                    @endforeach
                                </x-adminlte-select> 
                                <x-adminlte-button label="Save" theme="primary" type="submit" class="col-12"/>
                            </form>
                            <form action="{{route('timetables.records.create',[$timetable->id,$timeSlot->id])}}" method="POST" > 
                                @csrf  
                                {{--gets associated records for time slots and day of the week--}} 
                                <input type="hidden" name="weekday_id" value="{{$weekday->id}}">
                                <input type="hidden" name="type" value="customTimetableItem">
                                <x-adminlte-select name='id' id="subject" disable-feedback label="Custom items" fgroup-class="my-3">
                                    <option value=""></option>
                                    @foreach ($customItems as $customItem)
                                        <option value="{{$customItem->id}}" 
                                            @selected(!is_null($pivot) && $pivot->timetable_recordable_type == "customTimetableItem" && $pivot->timetable_recordable_id == $customItem->id)
                                        >
                                            {{$customItem->name}}
                                        </option>
                                    @endforeach
                                </x-adminlte-select> 
                                <x-adminlte-button label="Save" theme="secondary" type="submit" class="col-12"/>
                            </form>
                        </td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>