<div class="card">
    <div class="card-header">
        <h4 class="card-title">Class list</h4>
    </div>
    <div class="card-body">
        <x-adminlte-datatable id="class-list-table" :heads="['S/N', 'Name', 'Group', 'Action', '']" Class='text-capitalize'>
            @foreach($myClasses as $myClass)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{ $myClass->name}}</td>
                    <td>{{$myClass->classGroup->name}}</td>
                    <td>@livewire('dropdown-links', [
                        'links' => [
                        ['href' => route("classes.edit", $myClass->id), 'text' => 'edit', 'icon' => 'fas fa-cog'],
                        ['href' => route("classes.show", $myClass->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                        ],
                    ],)</td>
                    <td>
                        @livewire('delete-modal', ['modal_id' => $myClass->id ,"action" => route('classes.destroy', $myClass->id), 'item_name' => $myClass->name])
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    
    </div>
</div>
