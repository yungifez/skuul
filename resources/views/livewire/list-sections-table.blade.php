@if (!empty($myClasses))
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Section list</h4>
    </div>
    <div class="card-body">
        @foreach ($myClasses as $myClass)
            <div>
                <x-adminlte-card title="Sections under Class: {{$myClass->name}}" theme="secondary" icon=""  collapsible="collapsed">
                    <x-adminlte-datatable id="sections-list-table-{{$myClass->id}}" :heads="['S/N', 'Name', '', '']" class='text-capitalize'>
                    @foreach ($myClass->sections->all() as $section)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$section->name}}</td>
                            <td>@livewire('dropdown-links', [
                                'links' => [
                                ['href' => route("sections.edit", $section->id), 'text' => 'edit', 'icon' => 'fas fa-cog'],
                                ['href' => route("sections.show", $section->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                                ],
                            ],)</td>
                            <td>
                                @livewire('delete-modal', ['modal_id' => $section->id ,"action" => route('sections.destroy', $section->id), 'item_name' => $section->name])
                            </td>
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
                </x-adminlte-card>
            </div>
        @endforeach
    </div>
</div>
   

@else
    <p>No classes and sections created in this school</p>
@endif
