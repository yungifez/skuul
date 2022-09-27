<div class="card">
    <div class="card-header">
        <h4 class="card-title">Teachers</h4>
    </div>
    <div class="card-body">
        <x-adminlte-datatable id="school-list-table" :heads="['S/N', 'Photo', 'Name','email','gender' , 'address', '', '']" class='text-capitalize' >
            @foreach($teachers as $teacher)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td><img src="{{$teacher->profile_photo_url}}" alt="" class="rounded-circle" height="50px" width="50px"></td>
                    <td>{{ $teacher->name}}</td>
                    <td>{{ $teacher->email}}</td>
                    <td>{{$teacher->gender}}</td>
                    <td>{{$teacher->address}}</td>
                    <td>@livewire('dropdown-links', [
                        'links' => [
                        ['href' => route("teachers.edit", $teacher->id), 'text' => 'Edit profile', 'icon' => 'fas fa-cog'],
                        ['href' => route("teachers.show", $teacher->id), 'text' => 'View profile', 'icon' => 'fas fa-eye'],
                        ],
                    ],)</td>
                    <td>
                        @livewire('delete-modal', ['modal_id' => $teacher->id ,"action" => route('teachers.destroy', $teacher->id), 'item_name' => $teacher->name])
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </div>
</div>
