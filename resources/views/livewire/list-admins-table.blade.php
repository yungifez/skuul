<div class="card">
    <div class="card-header">
        <h4 class="card-title">Admins</h4>
    </div>
    <div class="card-body">
        <x-adminlte-datatable id="school-list-table" :heads="['S/N', 'Name','email','gender' , 'address', '', '']" class='text-capitalize' bordered striped head-theme="dark" beautify>
            @foreach($admins as $admin)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td style="text-transform: none">{{ $admin->email}}</td>
                    <td>{{$admin->gender}}</td>
                    <td>{{$admin->address}}</td>
                    <td>@livewire('dropdown-links', [
                        'links' => [
                        ['href' => route("admins.edit", $admin->id), 'text' => 'Edit profile', 'icon' => 'fas fa-cog'],
                        ['href' => route("admins.show", $admin->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                        ],
                    ],)</td>
                    <td>
                        @livewire('delete-modal', ['modal_id' => $admin->id ,"action" => route('admins.destroy', $admin->id), 'item_name' => $admin->name])
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </div>
</div>
