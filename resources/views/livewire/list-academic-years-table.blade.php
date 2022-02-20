<div>
    <p class='text-bold'>Academic year list</p>
    <x-adminlte-datatable id="school-list-table" :heads="['S/N', 'start year','stop year','session displayed as', 'action', '', ]" class='text-capitalize' >
        @foreach($academicYears as $academicYear)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{ $academicYear->start_year}}</td>
                <td>{{ $academicYear->stop_year}}</td>
                <td>{{ $academicYear->start_year}} - {{$academicYear->stop_year}}</td>
                <td>
                    @livewire('dropdown-links', [
                        'links' => [
                            ['href' => route("academic-years.edit", $academicYear->id), 'text' => 'edit', 'icon' => 'fas fa-cog'],
                            ['href' => route("academic-years.show", $academicYear->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                        ],
                    ],)
                </td>
                <td>
                    @livewire('delete-modal', ['modal_id' => $academicYear->id ,"action" => route('academic-years.destroy', $academicYear->id), 'item_name' => $academicYear->start - $academicYear->stop])
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>
</div>