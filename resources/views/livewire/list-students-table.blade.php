<div class="card">
    <div class="card-header">
        <h4 class="card-title">Student list</h4>
    </div>
    <div class="card-body">
        <x-adminlte-datatable id="school-list-table" :heads="['S/N','Photo', 'Name','Class','Section', 'email','gender' , 'address', '', '']" class='text-capitalize' >
            @foreach($students as $student)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td><img src="{{$student->profile_photo_url}}" alt="" class="rounded-circle" height="50px" width="50px"></td>
                    <td>{{ $student->name}}</td>
                    <td>@isset ($student->studentRecord->myClass)
                        {{$student->studentRecord->myClass->name}}
                    @endisset</td>
                    <td>@isset($student->studentRecord->section)
                        {{$student->studentRecord->section->name}}
                    @endisset</td>
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
</div>
