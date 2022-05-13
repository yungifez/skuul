<div>
    <p class='text-bold'>Exam list for semester {{ auth()->user()->school->semester->name}} </p>
    <x-adminlte-datatable id="school-list-table" :heads="['S/N','name', 'start date', 'stop date', '', '']" class='text-capitalize' >
        @foreach($exams as $exam)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$exam->name}}</td>
                <td>{{$exam->start_date}}</td>
                <td>{{$exam->stop_date}}</td>
                <td>
                    @livewire('dropdown-links', [
                        'links' => [
                            ['href' => route("exams.edit", $exam->id), 'text' => 'edit', 'icon' => 'fas fa-cog'],
                            ['href' => route("exams.show", $exam->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                            ['href' => route("exam-slots.index", $exam->id), 'text' => 'Manage exam slots', 'icon' => 'fas fa-cog'],
                            ['href' => route("exam-slots.create", $exam->id), 'text' => 'Create exam slots', 'Create exam slot', 'icon' => 'fas fa-key'],
                        ],
                    ],)
                </td>
                <td>
                    @livewire('delete-modal', ['modal_id' => $exam->id ,"action" => route('exams.destroy', $exam->id), 'item_name' => $exam->name])
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>
</div>