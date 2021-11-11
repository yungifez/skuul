<p class='text-bold'>School list</p>
<x-adminlte-datatable id="school-list-table" :heads="['S/N', 'Name','initials','Code' , 'address', '', '']" class='text-capitalize' >
    @foreach($schools as $school)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{ $school->name}}</td>
            <td>{{ $school->initials}}</td>
            <td>{{$school->code}}</td>
            <td>{{$school->address}}</td>
            <td>@livewire('dropdown-links', [
                'links' => [
                ['href' => route("schools.edit", $school->id), 'text' => 'Settings', 'icon' => 'fas fa-cog'],
                ['href' => route("schools.show", $school->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                ],
            ],)</td>
            <td>
                @livewire('delete-modal', ['modal_id' => $school->id ,"action" => route('schools.destroy', $school->id), 'item_name' => $school->name])
            </td>
        </tr>
    @endforeach
</x-adminlte-datatable>