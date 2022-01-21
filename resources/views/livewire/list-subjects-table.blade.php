@if (!empty($classes))
    <p class='text-bold'>Subject list</p>
    @foreach ($classes as $class)
        <x-adminlte-card title="Sections under Class: {{$class->name}}" theme="secondary" icon=""  collapsible="collapsed">
            <x-adminlte-datatable id="school-list-table-{{$class->id}}" :heads="['S/N', 'Name','Short name', 'Teahers assigned', '', '']" class='text-capitalize' >
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
    <p class='text-bold'>Create subject first</p>
@endif
