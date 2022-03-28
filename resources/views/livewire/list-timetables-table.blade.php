<div>
    <p class='text-bold'>Timetables list 
    </p>
    @if (!auth()->user()->hasRole('student'))
        <form action="">
            <x-adminlte-select id="my_class" label="Select a class to see timetable"  fgroup-class="col-md-6" name="" wire:model="class">
                @foreach ($classes as $item)
                    <option value="{{$item['id']}}">{{$item['name']}}</option>
                @endforeach
            </x-adminlte-select>
        </form>
    @endif
    @isset($timetables)
        @foreach ($timetables as $timetable)
           
        @endforeach
    @endisset
   
</div>
