<div class="card">
    <div class="card-header">
        <h4 class="card-title"></h4>
    </div>
    <div class="card-body">
        <h3 class="text-center">Exam slots in {{$exam->name}}</h3>
        <x-adminlte-datatable id="exam-slot-list-table" :heads="['S/N','name', 'Description', 'Maximum mark', '', '']" class='text-capitalize' >
            @foreach($exam->examSlots as $examSlot)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$examSlot->name}}</td>
                    <td>{{$examSlot->description}}</td>
                    <td>{{$examSlot->total_marks}}</td>
                    <td>
                        @livewire('dropdown-links', [
                            'links' => [
                                ['href' => route("exam-slots.edit", [$exam->id, $examSlot->id]), 'text' => 'edit', 'icon' => 'fas fa-cog'],
                                ['href' => route("exam-slots.show", [$exam->id, $examSlot->id]), 'text' => 'View', 'icon' => 'fas fa-eye'],
                            ]
                        ],)
                    </td>
                    <td>
                        @livewire('delete-modal', ['modal_id' => $examSlot->id ,"action" => route('exam-slots.destroy', [$exam->id, $examSlot->id]), 'item_name' => $examSlot->name])
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    
    </div>
</div>