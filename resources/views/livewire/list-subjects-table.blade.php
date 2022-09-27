<div class="card">
    <div class="card-header">
        <h4 class="card-title">Subject List</h4>
    </div>
    <div class="card-body">
        @if (!empty($classes))
        
            @foreach ($classes as $class)
                <x-adminlte-card title="Subjects under Class: {{$class->name}}" theme="primary" icon=""  collapsible="false">
                    <x-adminlte-datatable id="school-list-table-{{$class->id}}" :heads="['S/N', 'Name','Short name', 'Teachers assigned', '', '']" class='text-capitalize ' >
                        @foreach($class->subjects as $subject)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$subject->name}}</td>
                                <td>{{$subject->short_name}}</td>
                                <td>{{$subject->teachers()->count()}}</td>
                                <td>@livewire('dropdown-links', [
                                    'links' => [
                                    ['href' => route("subjects.edit", $subject->id), 'text' => 'Edit', 'icon' => 'fas fa-cog'],
                                    ['href' => route("subjects.show", $subject->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                                    ],
                                ],)</td>
                                <td>
                                    @livewire('delete-modal', ['modal_id' => $subject->id ,"action" => route('subjects.destroy', $subject->id), 'item_name' => $subject->name])
                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </x-adminlte-card>
            @endforeach
        @else
            <p class='text-bold'>Create Class first</p>
        @endif
    </div>
</div>
