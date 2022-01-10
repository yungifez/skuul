<div>
    <p class='text-bold'>teacher list</p>
    <x-adminlte-datatable id="school-list-table" :heads="['S/N', 'Name','email','gender' , 'address', '', '']" class='text-capitalize' >
        @foreach($teachers as $teacher)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{ $teacher->name}}</td>
                <td>{{ $teacher->email}}</td>
                <td>{{$teacher->gender}}</td>
                <td>{{$teacher->address}}</td>
                <td>@livewire('dropdown-links', [
                    'links' => [
                    ['href' => route("teachers.edit", $teacher->id), 'text' => 'Settings', 'icon' => 'fas fa-cog'],
                    ['href' => route("teachers.show", $teacher->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                    ],
                ],)</td>
                <td>
                    @livewire('delete-modal', ['modal_id' => $teacher->id ,"action" => route('teachers.destroy', $teacher->id), 'item_name' => $teacher->name])
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>
</div>
