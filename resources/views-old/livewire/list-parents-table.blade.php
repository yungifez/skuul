<div class="card">
    <div class="card-header">
        <h4 class="card-title">Parents</h4>
    </div>
    <div class="card-body">
        <x-adminlte-datatable id="school-list-table" :heads="['S/N', 'Name','email', '', '']" class='text-capitalize' bordered striped head-theme="dark" beautify>
            @foreach($parents as $parent)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$parent->name}}</td>
                    <td>{{$parent->email}}</td>
                    <td>@livewire('dropdown-links', [
                        'links' => [
                        ['href' => route("parents.edit", $parent->id), 'text' => 'Edit profile', 'icon' => 'fas fa-cog'],
                        ['href' => route("parents.show", $parent->id), 'text' => 'View profile', 'icon' => 'fas fa-eye'],
                        ['href' => route("parents.assign-students", $parent->id), 'text' => 'Assign students', 'icon' => 'fas fa fa-users'],
                        ],
                    ],)</td>
                    <td>
                        @livewire('delete-modal', ['modal_id' => $parent->id ,"action" => route('parents.destroy', $parent->id), 'item_name' => $parent->name])
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </div>
</div>
