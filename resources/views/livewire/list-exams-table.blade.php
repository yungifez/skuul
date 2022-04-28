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