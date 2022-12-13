<div class="card">
    <div class="card-header">
        <h4 class="card-title">Student list</h4>
    </div>
    <div class="card-body">
        <x-adminlte-datatable id="school-list-table" :heads="['S/N', 'Name','Class','Section', 'email','gender' , 'address', '', '']" class='text-capitalize' bordered striped head-theme="dark" beautify>
            @foreach($students as $student)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{ $student->name}}</td>
                    <td>@isset ($student->studentRecord->myClass)
                        {{$student->studentRecord->myClass->name}}
                    @endisset</td>
                    <td>@isset($student->studentRecord->section)
                        {{$student->studentRecord->section->name}}
                    @endisset</td>
                    <td style="text-transform: none">{{ $student->email}}</td>
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
