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
        <div class="overflow-scroll beautify-scrollbar ">
            <table class="border w-full my-4 table-auto"> 
                <thead>
                    <tr> 
                        <th class="text-center p-4 w-60 whitespace-nowrap">
                            <p >Time slots <span style="font-family: Dejavu Sans, sans-serif;"><br>&rarr;</span><br>Weekdays <span style="font-family: Dejavu Sans, sans-serif;">&darr;</span> </p>
                        </th>
                        {{--table heading which displays all the time slots--}}
                        @foreach ($timeSlots as $timeSlot)
                        <th scope="col" class="border p-4 w-60 whitespace-nowrap">
                            <p class="text-center ">
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
                        <td scope="col" class="p-4 border"><p class=""><strong>{{$weekday->name}}</strong></p></td>
                        {{--displays the time slots for each day of the week--}}
                        @foreach ($timeSlots as $timeSlot)
                            <td class="p-4 border w-60"
                            @if($disableEmitCellInformationDetail == false)
                                wire:click="emitCellInformationDetail({{$timeSlot->id}}, {{$weekday->id}})" wire:loading.class="prevent-click pointer-events-none"    
                            @endif>
                                <p class="print-small-text whitespace-nowrap">
                                    @php
                                        $pivot = $timeSlot->weekdays->find($weekday->id)?->timetableRecord;
                                    @endphp
                                    @if (!is_null($pivot) && $pivot->timetable_recordable_type == "App\Models\Subject")
                                        {{$subjects->find($pivot->timetable_recordable_id)?->name}}
                                    @elseif (!is_null($pivot) && $pivot->timetable_recordable_type ==  "App\Models\CustomItem")
                                        {{$customItems->find($pivot->timetable_recordable_id)?->name}}
                                    @endif
                                </p>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>