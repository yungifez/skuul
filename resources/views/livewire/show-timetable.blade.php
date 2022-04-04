<div>
   <h4 class="text-center">{{ $timetable->name}}</h4>
    @isset($timetable->description)
        <p>{{$timetable->description}}</p>
    @endisset
    <div class="table-responsive my-3">
        <table class="table table-bordered overflow-auto" style="overflow-x: scroll"> 
            <thead>
                <tr> 
                    <th scope="col" class="col-2">Time slots /<br>Weekdays
                    </th>
                    {{--table heading which disp;lays all the time slots--}}
                    @foreach ($timeSlots as $timeSlot)
                    <th scope="col" >
                        <p class="text-center">{{$timeSlot->start_time}} - {{$timeSlot->stop_time}}</p>
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
                        <td scope="col">
                            @php
                                $record = $timeSlot->weekdays()->where('weekday_id',$weekday->id)->where('weekday_id',$weekday->id)->latest()->first();
                            @endphp
                            @isset ($record)
                                <p class="text-center">{{$this->subjects->find($record->timetableRecord->subject_id )->name}}</p>
                            @endisset
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>

</div>