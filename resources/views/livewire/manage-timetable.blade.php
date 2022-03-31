<div>
    <h4 class="text-bold text-center">  Manage timetable</h4>
    <form action="{{route('timeslots.store',$timetable->id)}}" method="post" class="col-12">
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
        <table class="table table-bordered overflow-auto" style="overflow-x: scroll"> 
            <thead>
                <tr> 
                    <th scope="col" class="col-2">Time slots &#8594<br>Weekdays &#8595 </th>
                    @foreach ($timeSlots as $timeSlot)
                    <th scope="col">{{$timeSlot->start_time}} - {{$timeSlot->stop_time}}</th>
                    @endforeach
                </tr>
            </thead>
            @foreach ($weekdays as $weekday)
                <tr> 
                    <th scope="col" class="col-2">{{$weekday->name}}</th>
                    @foreach ($timeSlots as $timeSlot)
                        <th scope="col"></th>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>
</div>