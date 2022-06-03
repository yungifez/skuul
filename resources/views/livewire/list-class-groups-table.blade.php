<div class="card">
    <div class="card-header">
        <h4 class="card-title">Class Groups</h4>
    </div>
    <div class="card-body">
        <x-adminlte-datatable id="school-list-table" :heads="['S/N', 'Name', 'action', '', ]" class='text-capitalize' >
            @foreach($classGroups as $classGroup)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{ $classGroup->name}}</td>
                    <td>@livewire('dropdown-links', [
                        'links' => [
                        ['href' => route("class-groups.edit", $classGroup->id), 'text' => 'edit', 'icon' => 'fas fa-cog'],
                        ['href' => route("class-groups.show", $classGroup->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                        ],
                    ],)</td>
                    <td>
                        @livewire('delete-modal', ['modal_id' => $classGroup->id ,"action" => route('class-groups.destroy', $classGroup->id), 'item_name' => $classGroup->name])
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </div>
</div>