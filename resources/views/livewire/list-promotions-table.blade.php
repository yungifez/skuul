<div class="card">
    <div class="card-header">
        <h4 class="card-title">Promotion list</h4>
    </div>
    <div class="card-body">
        @livewire('help-button', ['target_id' => 'promotion-help-text', 'text' => "Reset button returns all students back to original class, make sure to verify all students promotion are to be reset before undergoing this action"])
        <p class='text-bold'>Promotion list for {{ "$academicYear->start_year - $academicYear->start_year"}}</p>
        <x-adminlte-datatable id="promotion-list-table" :heads="['S/N', 'From class','From Section','To class','To section', 'No of students','', '']" class='text-capitalize' >
            @foreach($promotions as $promotion)
                <tr>
                    <td>{{$loop->iteration}}</td>
                <td>{{ $promotion->oldClass->name}}</td>
                    <td>{{ $promotion->oldSection->name}}</td>
                    <td>{{$promotion->newClass->name}}</td>
                    <td>{{$promotion->newSection->name}}</td>
                <td>{{collect($promotion->students)->count()}}</td>
                    <td>@livewire('dropdown-links', [
                        'links' => [
                        ['href' => route("students.promotions.show", $promotion->id), 'text' => 'View promoted students', 'icon' => 'fas fa-eye'],
                        ],
                    ],)</td>
                    <td>
                        @livewire('delete-modal', ['modal_id' => $promotion->id ,"action" => route('students.promotions.reset', $promotion->id), 'item_name' => "would be reset, all students returned to the old class", 'button_label' => "Reset"])
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </div>
</div>