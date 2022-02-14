<div class="my-3">
    <p class='text-bold'>Promotion list for{{ "$academicYear->start_year - $academicYear->start_year"}}</p>
    <x-adminlte-datatable id="promotion-list-table" :heads="['S/N', 'From class','From Section','To class','To section', '', '']" class='text-capitalize' >
        @foreach($promotions as $promotion)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{ $promotion->oldClass->name}}</td>
                <td>{{ $promotion->oldSection->name}}</td>
                <td>{{$promotion->newClass->name}}</td>
                <td>{{$promotion->newSection->name}}</td>
                <td>@livewire('dropdown-links', [
                    'links' => [
                    ['href' => route("schools.edit", $promotion->id), 'text' => 'Settings', 'icon' => 'fas fa-cog'],
                    ['href' => route("schools.show", $promotion->id), 'text' => 'View', 'icon' => 'fas fa-eye'],
                    ],
                ],)</td>
                <td>
                    @livewire('delete-modal', ['modal_id' => $promotion->id ,"action" => route('schools.destroy', $promotion->id), 'item_name' => $promotion->name])
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>
</div>