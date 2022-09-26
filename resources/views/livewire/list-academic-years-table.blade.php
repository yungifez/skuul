<div class="card">
    <div class="card-header">
        <h4 class="card-title">Academic year list</h4>
    </div>
    <div class="card-body">
        <x-adminlte-datatable id="school-list-table" :heads="['S/N','duration', 'action', '', ]" class='text-capitalize' >
            @foreach($academicYears as $academicYear)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{ $academicYear->name()}}</td>
                    <td>
                        @livewire('dropdown-links', [
                            'links' => [
                                ['href' => route("academic-years.edit", $academicYear->id), 'text' => 'edit', 'icon' => 'fas fa-cog'],
                                ['href' => route("academic-years.show", $academicYear->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                            ],
                        ],)
                    </td>
                    <td>
                        @livewire('delete-modal', ['modal_id' => $academicYear->id ,"action" => route('academic-years.destroy', $academicYear->id), 'item_name' => $academicYear->start - $academicYear->stop])
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </div>
</div>