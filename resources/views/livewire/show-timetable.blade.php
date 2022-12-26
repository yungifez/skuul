{{--Written for css 2.1 support--}}
<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{ $timetable->name}}</h4>
    </div>
    <div class="card-body">
        @if ($showDescription == true)  
            @isset($timetable->description)
                <p>{{$timetable->description}}</p>
            @endisset
        @endif
        <div class="table-responsive my-3">
            <table class="table table-bordered overflow-auto" style="overflow-x: scroll"> 
                <thead>
                    <tr> 
                        <th scope="col" class="">
                            <p class="text-center">Time slots <span style="font-family: Dejavu Sans, sans-serif;"><br>&rarr;</span><br>Weekdays <span style="font-family: Dejavu Sans, sans-serif;">&darr;</span> </p>
                        </th>
                        {{--table heading which displays all the time slots--}}
                        @foreach ($timeSlots as $timeSlot)
                        <th scope="col" >
                            <p class="text-center">
                                {{$timeSlot->start_time}}
                                <br> - <br> 
                                {{$timeSlot->stop_time}}</p>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                {{--creates a row for each day of the week--}}
                @foreach ($weekdays as $weekday)
                    <tr> 
                        <td scope="col" ><p class=""><strong>{{$weekday->name}}</strong></p></td>
                        {{--displays the time slots for each day of the week--}}
                        @foreach ($timeSlots as $timeSlot)
                            <td scope="col" wire:click="emitCellInformationDetail({{$timeSlot->id}}, {{$weekday->id}})" wire:loading.class="prevent-click" >
                                <p class="print-small-text">
                                    @php
                                        $pivot = $timeSlot->weekdays->find($weekday->id)?->timetableRecord;
                                    @endphp
                                    @if (!is_null($pivot) && $pivot->timetable_recordable_type == "subject")
                                        {{$subjects->find($pivot->timetable_recordable_id)->name}}
                                    @elseif (!is_null($pivot) && $pivot->timetable_recordable_type == "customTimetableItem")
                                        {{$customItems->find($pivot->timetable_recordable_id)->name}}
                                    @endif
                                </p>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
        <style>
            .prevent-click {
                pointer-events: none;
            }
        </style>
    </div>
</div>