<div>
    <p class='text-bold'>Subject list</p>
    <x-adminlte-datatable id="school-list-table" :heads="['S/N', 'Name','Short name', ['label'=>'Class', ], 'Teahers assigned', '', '']" class='text-capitalize' >
        @foreach($subjects as $subject)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$subject->name}}</td>
                <td>{{$subject->short_name}}</td>
                <td>{{$subject->myClass->name}}</td>
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
</div>
