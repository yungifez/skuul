<div>
    <p class='text-bold'>Timetables list 
    </p>
    {{--Select statement for switching classes--}}
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
        <x-adminlte-datatable id="timetable-list-table" :heads="['S/N', 'Name', 'Action', '']" Class='text-capitalize'>
            @foreach($timetables as $timetable)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{ $timetable->name}}</td>
                    <td>
                        @can('update timetable')
                            @livewire('dropdown-links', [
                                'links' => [
                                ['href' => route("timetables.edit", $timetable->id), 'text' => 'Edit', 'icon' => 'fas fa-pen'],
                                ['href' => route("timetables.manage", $timetable->id), 'text' => 'Manage', 'icon' => 'fas fa-cog'],
                                ['href' => route("timetables.show", $timetable->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                                ],
                            ],)
                        @elsecan('read timetable')
                            @livewire('dropdown-links', [
                                'links' => [
                                ['href' => route("timetables.show", $timetable->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                                ],
                            ],)
                        @endcan
                    </td>
                    <td>
                        @can('delete timetable')
                            @livewire('delete-modal', ['modal_id' => $timetable->id ,"action" => route('timetables.destroy', $timetable->id), 'item_name' => $timetable->name])
                        @endcan
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    @else
        <p class="text-center text-bold">No Timetable for this class at this time</p>
    @endisset
</div>
