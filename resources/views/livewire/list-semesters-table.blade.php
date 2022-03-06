<div>
    <p class='text-bold'>Semester List for {{auth()->user()->school->academicYear->name()}}</p>
    <x-adminlte-datatable id="Semester-list-table" :heads="['S/N', 'Name', 'Action', '']" Class='text-capitalize'>
        @foreach($semesters as $semester)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{ $semester->name}}</td>
                <td>@livewire('dropdown-links', [
                    'links' => [
                    ['href' => route("semesters.edit", $semester->id), 'text' => 'edit', 'icon' => 'fas fa-cog'],
                    ['href' => route("semesters.show", $semester->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                    ],
                ],)</td>
                <td>
                    @livewire('delete-modal', ['modal_id' => $semester->id ,"action" => route('semesters.destroy', $semester->id), 'item_name' => $semester->name])
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>

</div>
