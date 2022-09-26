<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{ $timetable->name}}</h4>
    </div>
    <div class="card-body">
        @isset($timetable->description)
            <p>{{$timetable->description}}</p>
        @endisset
        <div class="table-responsive my-3">
            <table class="table table-bordered overflow-auto" style="overflow-x: scroll"> 
                <thead>
                    <tr> 
                        <th scope="col" class="">
                            <p class="text-center">Time slots &#8594<br>Weekdays &#8595 </p>
                        </th>
                        {{--table heading which displays all the time slots--}}
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
                        <td scope="col" ><p class="text-center">{{$weekday->name}}</p></td>
                        {{--displays the time slots for each day of the week--}}
                        @foreach ($timeSlots as $timeSlot)
                            <td scope="col">
                                @if ($timeSlot->weekdays()->where('weekday_id',$weekday->id)->first() != null)
                                    {{$timeSlot->weekdays()->where('weekday_id',$weekday->id)->first()?->timetableRecord?->subject?->name}}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
    
    </div>
</div>