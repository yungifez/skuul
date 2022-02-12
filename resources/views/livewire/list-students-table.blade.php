<div>
    <p class='text-bold'>Student list</p>
    <x-adminlte-datatable id="school-list-table" :heads="['S/N','Profile photo', 'Name','Class','Section', 'email','gender' , 'address', '', '']" class='text-capitalize' >
        @foreach($students as $student)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td><img src="{{$student->profile_photo_url}}" alt="" class="rounded-circle" height="50px" width="50px"></td>
                <td>{{ $student->name}}</td>
                <td>{{$student->studentRecord->myClass->name}}</td>
                <td>{{$student->studentRecord->section->name}}</td>
                <td>{{ $student->email}}</td>
                <td>{{$student->gender}}</td>
                <td>{{$student->address}}</td>
                <td>@livewire('dropdown-links', [
                    'links' => [
                    ['href' => route("students.edit", $student->id), 'text' => 'Manage profile', 'icon' => 'fas fa-pen'],
                    ['href' => route("students.show", $student->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                    ],
                ],)</td>
                <td>
                    @livewire('delete-modal', ['modal_id' => $student->id ,"action" => route('students.destroy', $student->id), 'item_name' => $student->name])
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>
</div>
