<p class='text-bold'>School list</p>
<x-adminlte-datatable id="school-list-table" :heads="['S/N', 'Name','initials','Code' , 'address', 'action']" class='text-capitalize'>
    @foreach($schools as $school)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{ $school->name}}</td>
            <td>{{ $school->initials}}</td>
            <td>{{$school->code}}</td>
            <td>{{$school->address}}</td>
            <td>@livewire('dropdown-links', [
                'links' => [
                ['href' => 'schools.edit', 'text' => 'Settings', 'icon' => 'fas fa-cog'],
                ['href' => 'schools.show', 'text' => 'View', 'icon' => 'fas fa-eye'],
                ],
            ],)</td>
        </tr>
    @endforeach
</x-adminlte-datatable>