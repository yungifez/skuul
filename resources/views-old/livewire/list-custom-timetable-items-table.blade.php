<div class="card">
    <div class="card-header">
        <h4 class="card-title">Custom timetable items</h4>
    </div>
    <div class="card-body">
        <x-adminlte-datatable id="school-list-table" :heads="['S/N', 'Name', 'action', '', ]" class='text-capitalize' bordered striped head-theme="dark" beautify>
            @foreach($customTimetableItems as $customTimetableItem)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{ $customTimetableItem->name}}</td>
                    <td>@livewire('dropdown-links', [
                        'links' => [
                        ['href' => route("custom-timetable-items.edit", $customTimetableItem->id), 'text' => 'edit', 'icon' => 'fas fa-cog'],
                        // ['href' => route("custom-timetable-items.show", $customTimetableItem->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                        ],
                    ],)</td>
                    <td>
                        @livewire('delete-modal', ['modal_id' => $customTimetableItem->id ,"action" => route('custom-timetable-items.destroy', $customTimetableItem->id), 'item_name' => $customTimetableItem->name])
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </div>
</div>